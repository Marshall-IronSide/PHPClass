<?php
echo "Il \"était\" une fois <br/>"; //permet de mettre d'autres "" ex:Il "était" une fois
echo 'Il m\'arrive d\'être super génial <br/>';
echo date("d/m/y")."<br/>"; //pour afficher la date du jour ex:24/02/25
echo date("d/m/Y")."<br/>";// le Y maj affiche l'année entièrement ex: 24/02/2025
echo date("d/m/Y H:i:s")."<br>"; // H:i:s affihche l'heure ex: 24/02/2025 09:57:49
echo date("d/m/Y H \h\\e\u\\r\\e\s:i\m\i\\n\u\\t\\e\s:s\s\\e\c\o\\n\d\\e\s")."<br>";// on a 24/02/2025 10 heures:05minutes:01secondes
echo date("d/m/Y H:i:s")." heures :".date("i")." minutes ".date("s")." secondes <br>";//même résultat que la ligne 7
$NeLe =mktime(1,5,0,02,29,2000) ;
echo "Je suis née un ".date("L",$NeLe)."<br/>";
function calcul($nbre1,$nbre2,$nbre3)
{
    $result = $nbre1*$nbre2+$nbre3;
    return $result;
}
echo calcul(10,2,3)."<br/>";
function TVA($montant){
    $result = $montant*1.18;
    return $result;
}
echo TVA(10000)."<br/>";
$i = 0;
while ($i < 10){
    echo "Je ne dois pas coller mon chewing-gum sous la table"."<br/>";
    $i++;
}

for($i = 0;$i<10;$i++){
    echo "No yapping in class"."<br/>";
}
$var = array(
    "nom" => " Iron",
    "Prénom" => " Side",
    "Adresse" => " Boulevard des armées chez les soeur"
);

foreach($var as $key => $valeur){   //foreach est utilisé pour les tableaux $key( nom,Prénom et Adresse) et $valeur(Iron,Side et Boulevard des armées chez les soeur)
    echo $key.":".$valeur."<br/>";
}
function IronSide($note)
{
  return $note;   
}
$result = IronSide(9);
if($result<10){
    echo "Tu n'as pas la moyenne"."<br/>";
}
elseif($result == 10){
    echo "T'as tout juste la myenne"."<br/>";
}
else{
    echo "Bravo!! t'as plus que la moyenne"."<br/>";
}
?>