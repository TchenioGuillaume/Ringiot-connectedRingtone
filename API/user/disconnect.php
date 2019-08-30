<?php
include '../header.php';


try
{
    $req = $pdo->prepare("DELETE FROM session WHERE token = :token;");
    $req->bindParam(':token', $_GET['token']);
    $req->execute();

        
    retour_json(true, "Déconnecté.");
}
catch(PDOException $e) 
{
    retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
