<?php
session_start();

header("Content-type: application/json; charset=utf-8");

//Améliorations possibles : passer la connexion DB en $_SESSION

try
{
    $pdo = new PDO('mysql:host=db5000134522.hosting-data.io;port=3306;dbname=dbs129553;', 'dbu281826', 'Dfs15@15');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} 
catch(Exception $e) 
{
    print_r($e);
    retour_json(false, "Impossible de se connecter à la base de données");
    
}

$connexion_courante = Auth($pdo);
$_SESSION['db'] = $connexion_courante;




//Les fonctions :

function Auth($pdo)
{
    if(isset($_GET['token']))
    {
        $token = html_entity_decode($_GET['token']);
        //premièrement, je vais récupérer la session qui est passé en paramètre.
        $req = $pdo->prepare("SELECT * FROM session WHERE token = :token ;");
        $req->bindParam(':token', $token);
        $req->execute();

        $session = $req->fetch();
        if(empty($session))
        {
            //Soit le token est bidon.. Soit il n'éxiste plus (AFK ou c'est Logout).
            retour_json(false, "Votre session n'éxiste pas/plus.");
        }
        else
        {
            //Dans le doute, je vais vérifier qu'il n'y ait qu'une seul Session pour cet utilisateur.
            //Si il y en a plusieurs, je vais juste bloquer l'authentification.
            $req = $pdo->prepare("SELECT * FROM session WHERE id_utilisateur = :id_utilisateur ;");
            $req->bindParam(':id_utilisateur', $session['id_utilisateur']);
            $req->execute();

            $sessions_utilisateur = $req->fetchAll();
            $nbSessions_utilisateur = count($sessions_utilisateur);
            if($nbSessions_utilisateur == null)
            {
                retour_json(false, "Plusieurs sessions pour un utilisateur. Suspension du compte.");
            }
            else
            {
                //sinon, la série de test à l'air OK. On va pouvoir authentifier la connexion, mettre à jour l'utilisation celle-ci et passer à la requête.
                // $req = $pdo->prepare("UPDATE `session` SET `dateUse` = CURRENT_TIMESTAMP WHERE `id` = :id ;  ");
                // $req->bindParam(':id', $session['id']);
                // $req->execute();

                //Mise à jour des Logs :
                //Je vais décomposer l'URL de la requête pour en déduire la modifiction qui et faite.

                //récupération de l'adresse IP.
                $ipConnexion = '';
                if (getenv('HTTP_CLIENT_IP'))
                    $ipConnexion = getenv('HTTP_CLIENT_IP');
                else if(getenv('HTTP_X_FORWARDED_FOR'))
                    $ipConnexion = getenv('HTTP_X_FORWARDED_FOR');
                else if(getenv('HTTP_X_FORWARDED'))
                    $ipConnexion = getenv('HTTP_X_FORWARDED');
                else if(getenv('HTTP_FORWARDED_FOR'))
                    $ipConnexion = getenv('HTTP_FORWARDED_FOR');
                else if(getenv('HTTP_FORWARDED'))
                    $ipConnexion = getenv('HTTP_FORWARDED');
                else if(getenv('REMOTE_ADDR'))
                    $ipConnexion = getenv('REMOTE_ADDR');
                else
                    $ipConnexion = 'UNKNOWN';


                $infoURL = explode('/', $_SERVER["SCRIPT_NAME"]);
                // var_dump($infoURL);die;
                $nomTable = strtoupper($infoURL[3]);
                $nomAction = strtoupper(substr($infoURL[4], 0, -4));
                // $nomAction = strtoupper($infoURL[4]);
                $mention = false;
                $idAction = null;
                switch ($nomAction) {
                    case 'CREATE':
                        if(isset($_GET['id']))
                        {
                            $nomAction = "MODIFICATION";
                            $action = $nomAction.' '.$nomTable;
                            $mention = true;
                            $important = 1;
                        }
                        else
                        {
                            $nomAction = "CREATION";
                            $action = $nomAction.' '.$nomTable;
                            $mention = true;
                            $important = 1;
                        }
                        break;

                    case 'DELETE':
                        $nomAction = "SUPPRESSION";
                        $action = $nomAction.' '.$nomTable;
                        $mention = true;
                        $important = 1;
                        break;
                    
                    default:
                        $mention = false;
                        break;
                }
                //Maintenant que tout est bon, j'insère la ligne de log (uniquement si elle vaut le coup d'être mentionnée)
                if($mention)
                {
                    $req = $pdo->prepare("INSERT INTO `logs` (`id`, `action`, `important`) VALUES (NULL, :action, :important); ");
                    $req->bindParam(':action', $action);
                    $req->bindParam(':important', $important);
                    $req->execute();
                }

                //C'est terminé, tout est bon.
                $_SESSION['id_utilisateur'] = $session['id_utilisateur'];
                return true;
            }
        }
        
    }
    else
    {
        retour_json(false, "Connexion refusée.", "Il n'y a pas de Token");  
    }
}

function retour_json($success, $msg, $results = NULL)
{
    $retour["success"] = $success;
    $retour["message"] = $msg;
    $retour["results"] = $results;
    
    echo(json_encode($retour));die;
}

function generatePassword($chaine)
{
    $options = [
        'cost' => 9,
    ];
    return password_hash($chaine, PASSWORD_DEFAULT, $options);
}
