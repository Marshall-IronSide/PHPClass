<?php
echo"<h1>Hello</h1>";
echo"Aujourd'hui nous somme a la deuxieme seance du cours de PHP.";
$auteur = "IronSide";
echo"<p>Cette page a été implémenté par Sir $auteur.</p>";
$nombre1 = 17;
$nombre2 = 3;
$addition = $nombre1+$nombre2;
$soustraction = $nombre1-$nombre2;
$rmultiplication = $nombre1*$nombre2;
$division = $nombre1/$nombre2;
$exponnentiel = $nombre1**$nombre2;
$racine_carre = sqrt($nombre1*$nombre2);
echo"Le resultat de l'addition est: " .$addition."<br/>";
echo"Le resultat de la soustraction est: " .$soustraction."<br/>";
echo"Le resultat de la multiplication est: " .$rmultiplication."<br/>";
echo"Le resultat de la division est: " .$division."<br/>";
echo"Le resultat de l'exponnentiel est: " .$exponnentiel."<br/>";
echo"Le resultat de la racine carrée est: " .$racine_carre."<br/>";

?>