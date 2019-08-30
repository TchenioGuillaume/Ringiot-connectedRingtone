<?php
include '../header.php';

//La méthode GET récupère des données en fonction des paramètres envoyés.

//exemple : Ici, si l'utilisateur entre un ID : il a toutes les informations d'un Raspberry en particulier.
//          Si il n'entre aucun paramètre : il récupère les informations de tous les Rasberrys (Cette fonctionnalitée est désactivée car dangereuse).
try
{
    switch (isset($_GET)) 
    {
        case isset($_GET['id']):
            $req = $pdo->prepare("SELECT * FROM raspberry WHERE id = :id;");
            $req->bindParam(':id', $_GET['id']);
            $req->execute();
            break;
    
        default:
            // $req = $pdo->prepare("SELECT * FROM raspberry");
            // $req->execute();
            break;
    } 
}
catch(PDOException $e) 
{
    //Si la requête n'a pas fonctionné (erreur SQL) elle est renvoyée à l'utilisateur.
    retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}

$datas=$req->fetchAll();

$results["nb"] = count($datas);
$results["datas"] = $datas;
retour_json(true, "Liste des contacts", $results);