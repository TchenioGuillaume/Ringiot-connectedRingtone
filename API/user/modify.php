<?php
include '../header.php';

//La méthode MODIFY modifie la ligne qui comporte l'ID passé en paramètre.

//C'est un peu un mélange entre la méthode CREATE et GET dans le sens ou il faut passer les paramètres de la méthode CREATE mais aussi ceux de la méthode GET
//(au moins l'ID)
if(isset($_POST['login']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['DND']) && isset($_POST['id_rasp'])  && isset($_GET['id']))
{
    try
    {
        $req = $pdo->prepare("UPDATE utilisateur SET login = :login, password = :password, email = :email, DND = :DND, id_rasp = :id_rasp WHERE id = :id;");
        
        //Passer les paramètres.
        $req->bindParam(':login', $_POST['login']);
        $req->bindParam(':password', $_POST['password']);
        $req->bindParam(':email', $_POST['email']);
        $req->bindParam(':DND', $_POST['DND']);
        $req->bindParam(':id_rasp', $_POST['id_rasp']);
        $req->bindParam(':id', $_GET['id']);
        
        //On éxectue la requête SQL.
        $req->execute();

        //Une fois que tout ça est terminé, on retourne l'info à l'utilisateur : La création est terminée/
        retour_json(true, "Modification effectuée avec succès.");
    }
    catch(PDOException $e) 
    {
        //Si la requête n'a pas fonctionné (erreur SQL) elle est renvoyée à l'utilisateur.
        retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    }
}
else
    {
        retour_json(false, "Paramètres manquants.");
    }

//Sur la modification on envois pas les données à l'utilisateur, il viens de les saisir...