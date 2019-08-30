<?php
include '../header.php';

//Ici, je vérifie que l'ID est bien passé puis je delete la ligne qui as cet ID... Rien de spécial.

if(isset($_GET['id']))
{
    try
    {
        $req = $pdo->prepare("DELETE FROM raspberry WHERE id = :id;");
        $req->bindParam(':id', $_GET['id']);
        $req->execute();
        retour_json(true, "Suppression terminée.");
    }
    catch(PDOException $e) 
    {
        retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    }
}
else
{
    retour_json(false, "ID invalide.");
}

