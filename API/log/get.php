<?php
include '../header.php';


//Les différentes façons de chercher une (ou plusieurs) entité.
switch (isset($_GET)) 
{
    case isset($_GET['id']):
        // $req = $pdo->prepare("SELECT * FROM utilisateur WHERE id=:id;");
        // $req->bindParam(':id', $_GET['id']);
        // $req->execute();
        break;

    default:
        $req = $pdo->prepare("SELECT * FROM logs");
        $req->execute();
        // retour_json(false, "Impossible d'éxecuter la commande. (Il manque des arguments)");
        break;
}


$datas=$req->fetchAll();

$results["nb"] = count($datas);
$results["datas"] = $datas;
retour_json(true, "Liste des logs", $results);