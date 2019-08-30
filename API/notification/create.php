<?php
$_GET['token'] = $_POST['token'];
include "../header.php";

if(!isset($_POST['url']))
{
    $directory = '../pictures'; 
    $url = array_diff(scandir($directory, SCANDIR_SORT_DESCENDING), array('..', '.'));
    $url = "https://tchenioguillaume.fr/iot/pictures/".$url[0];
    $_POST['url'] = " ";
}
else
{
    $url = "https://tchenioguillaume.fr/iot/pictures/".$_POST['url'];
}

if(isset($_POST['url']) && isset($_POST['id_rasp'])) 
{

    try
    {
        $req = $pdo->prepare("INSERT INTO notification (id, date, url, id_rasp) VALUES (NULL, CURRENT_TIMESTAMP, :url, :id_rasp);");
        $req->bindParam(':url', $url);
        $req->bindParam(':id_rasp', $_POST['id_rasp']);
        $req->execute();
    }
    catch(PDOException $e) 
    {
        retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    }
    
    // $req = $pdo->prepare("SELECT ip FROM raspberry WHERE id=:id_rasp;");
    // $req->bindParam(':id_rasp', $_POST['id_rasp']);
    // $req->execute();

    // $ip=$req->fetchAll();
    
    // $finalData['ip'] = $ip[0]['ip'];
    // $finalData['url'] = $_POST['url'];

    retour_json(true, "Insertion effectuée avec succès.");
}
else
{
    retour_json(false, "Impossible d'éxecuter le script :", "Des informations sont manquantes.");
}
?>