<?php
include "../header.php";

//La méthode CREATE permet de créer un raspberry.

//Si tous les champs de la table sont passés (donc ici name et ip), on créé la ligne, sinon, on retourne une erreur.
if(isset($_POST['name']) && isset($_POST['ip']))
{
    try
    {
        //D'abord on passe le template de la requête en SQL (note : on met ID pour le principe et on lui attribus une valeur à 'NULL' pour laisser l'auto incrémentation se faire).
        $req = $pdo->prepare("INSERT INTO raspberry (id, name, ip) VALUES (NULL, :name, :ip);");

        //Ensuite on assigne les valeurs au template (:name prend donc la valeur du $_POST['name']).
        $req->bindParam(':name', $_POST['name']);
        $req->bindParam(':ip', $_POST['ip']);
        
        //On éxectue la requête SQL.
        $req->execute();

        //Une fois que tout ça est terminé, on retourne l'info à l'utilisateur : La création est terminée/
        retour_json(true, "Insertion effectuée avec succès.");
    }
    catch(PDOException $e) 
    {
        //Si la requête n'a pas fonctionné (erreur SQL) elle est renvoyée à l'utilisateur.
        retour_json(false, "Impossible d'éxecuter la requête", ['message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    }


}
else
{
    retour_json(false, "Impossible d'éxecuter le script :", "Des informations sont manquantes.");
}
?>