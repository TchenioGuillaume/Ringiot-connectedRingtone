<?php
include '../header.php';

//La méthode MODIFY modifie la ligne qui comporte l'ID passé en paramètre.

//C'est un peu un mélange entre la méthode CREATE et GET dans le sens ou il faut passer les paramètres de la méthode CREATE mais aussi ceux de la méthode GET
//(au moins l'ID)
if(isset($_POST['name']) && isset($_POST['ip']) && isset($_GET['id']))
{
    try
    {
        $req = $pdo->prepare("UPDATE raspberry SET name = :name, ip = :ip WHERE id = :id;");

        //Passer les paramètres.
        $req->bindParam(':name', $_POST['name']);
        $req->bindParam(':ip', $_POST['ip']);
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

//Sur la modification on envois pas les données à l'utilisateur, il viens de les saisir...