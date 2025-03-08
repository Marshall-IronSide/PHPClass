<?php 
echo "Information de l'internaute"."<br/>";
$prenom=$_POST["prenom"];
$nom=$_POST["nom"];
$email=$_POST["email"];
echo "Bonjour ".$prenom." ".$nom.",<br/>
Vous avez été enregistré avec votre adresse mail: " .$email 
?>