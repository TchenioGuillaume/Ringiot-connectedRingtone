<?php

header('Content-Type: application/json');

try
{
    $pdo = new PDO('mysql:host=db5000134522.hosting-data.io;port=3306;dbname=dbs129553;', 'dbu281826', 'Dfs15@15');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} 
catch(Exception $e) 
{
    echo($e);
    retour_json(false, "Impossible de se connecter à la base de données");
}

function retour_json($success, $msg, $results = NULL)
{
    $retour["success"] = $success;
    $retour["message"] = $msg;
    $retour["results"] = $results;
    // var_dump($retour);
    echo(json_encode($retour));die;
}


//La fonction login
function verifyPost()
{
    $errors=array();
    if(empty($_POST['login']))
    {
        array_push($errors, "Le login n'est pas renseigné.");
    }
    if(empty($_POST['pass']))
    {
        array_push($errors, "Le mot de passe n'est pas rensigné.");
    }

    if(!empty($errors))
    {
        retour_json(false,"Impossible d'éxecuter la requête.",$errors);
        return false;
    }
    else
    {
        return true;
    }
}

function generatePassword($chaine)
{
    $options = [
        'cost' => 9,
    ];
    return password_hash($chaine, PASSWORD_DEFAULT, $options);
}



//Au login, l'utilisateur récupère un token d'identification qui le suivra jusque son logout.
//Après 2h d'inactivité, le token d'identification disparait.
//Dans l'idéal, il me faudrait une procédure stockée en BDD pour automatiquement supprimer le tuple de la table "session" (c'est fait :D)
//Le token serait généré en fonction de l'ID de l'utilisateur et du TimeStamp actuel. (tkt, il est généré automatiquement par PHP..)

//Premièrement, je vérifie que j'ai toutes les données dont j'ai besoin.
if(verifyPost())
{
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

    //Je récupère l'utilisateur qui appartiens à l'addresse login qui a été renseigné.
    $req = $pdo->prepare("SELECT * FROM utilisateur WHERE login = :login OR email = :login;");
    $req->bindParam(':login', $_POST['login']);
    $req->execute();

    $data = $req->fetch();
    //Si j'ai au moins un résultat dans mon $data et que le mot de passe correspond.
    if(!empty($data) && password_verify($_POST['pass'], $data['password']))
    {
        //Je vérifie que le hash du mot de passe soit à jour.
        if(password_needs_rehash($data['password'], PASSWORD_DEFAULT, array('cost' => 9)))
        {
            //Si il est pas à jour, je le regénère.
            $pass = generatePassword($_POST['pass']);
            //Puis je l'ajoute en BDD.
            $req = $pdo->prepare("UPDATE `utilisateur` SET `pass` = :pass WHERE `id` = :id;");
            $req->bindParam(':id', $data['id']);
            $req->bindParam(':pass', $pass);
        }

        //Je créé une session et retourne les informations de l'utilisateur avec son Token de Session.
        //Création du Token de Session (en fonction de l'adresse login) :
        $token_session = generatePassword($data['login']);
        //Création de la Session :
        try
        {
            $req = $pdo->prepare("INSERT INTO `session` (`id`, `dateCrea`, `dateModif`, `id_utilisateur`, `token`) VALUES (NULL,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,:id_utilisateur,:token);");
            $req->bindParam(':id_utilisateur', $data['id']);
            $req->bindParam(':token', $token_session);
            $req->execute();
        }
        catch(PDOException $e) 
        {
            retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
            

        //Je retire le mot de passe des informations à retourner.
        //unset($data['password'], $data['4']);
        
        //J'ajoute le token de la session initialisée aux informations à retourner.
        $data["token"] = $token_session;

        //J'insère la connexion dans les logs.

            

        $req = $pdo->prepare("INSERT INTO `logs` (`id`, `action`, `important`) VALUES (NULL, :action, :important); ");
        // $nbSessions++;
        $important = 0;
        $action = "LOGIN SUCCESS";
        $req->bindParam(':action', $action);
        $req->bindParam(':important', $important);
        $req->execute();

        //Je termine le login.
        retour_json(true, "Logged In.", $data);

    }
    else
    {
        retour_json(false, "Mot de passe ou login incorrect.");
    }
}