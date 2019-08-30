<?php
include "../header.php";

if(isset($_POST['pass']) && isset($_POST['mail']) && isset($_POST['login']) && isset($_POST['id_rasp']))
{
    $hashPass = generatePassword($_POST['pass']);

    try
    {
        $req = $pdo->prepare("INSERT INTO utilisateur (id, login, password, email, id_rasp) VALUES (NULL, :login, :hashPass, :mail, :id_rasp);");
        $req->bindParam(':login', $_POST['login']);
        $req->bindParam(':hashPass', $hashPass);
        $req->bindParam(':mail', $_POST['mail']);
        $req->bindParam(':id_rasp', $_POST['id_rasp']);
        $req->execute();
    }
    catch(PDOException $e) 
    {
        retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    }
    

    retour_json(true, "Insertion effectuée avec succès.");
}
else
{
    retour_json(false, "Impossible d'éxecuter le script :", "Des informations sont manquantes.");
}
?>