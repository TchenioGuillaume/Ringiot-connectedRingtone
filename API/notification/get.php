<?php
include '../header.php';

if(isset($_POST['token']))
{
    $_GET['token'] = $_POST['token'];
}

//Les différentes façons de chercher une (ou plusieurs) entité.
switch (isset($_GET)) 
{
    case isset($_GET['id_rasp']):
        $req = $pdo->prepare("SELECT * FROM notification WHERE id_rasp=:id_rasp ORDER BY date;");
        $req->bindParam(':id_rasp', $_GET['id_rasp']);
        $req->execute();
        break;

    default:
        retour_json(false, "Impossible d'éxecuter la commande. (Il manque des arguments)");
        break;
}

$datas=$req->fetchAll();

// $results["nb"] = count($datas);
// $i=0;
// foreach ($datas as $data) 
// {
//     $req = $pdo->prepare("SELECT * FROM notification WHERE id_rasp=:id_rasp AND date=:date ORDER BY date;");
//     $req->bindParam(':id_rasp', $_GET['id_rasp']);
//     $req->bindParam(':date', $data['date']);
//     $req->execute();

//     $newDatas=$req->fetchAll();

//     var_dump($newDatas);die;
//     $i++;
// }


$results["datas"] = $datas;
retour_json(true, "Liste des notifications", $results);