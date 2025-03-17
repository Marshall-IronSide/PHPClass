<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Compétences Info</title>
</head>
<body>
<?php
if(isset($_POST["ident"]) && isset($_POST["lang"]) &&isset($_POST["competent"])) 
{
echo "<table border=\"1\"><tr><th> Récapitulatif de votre fiche d'information personnelle</th></tr><tr><td>";
$nom=$_POST["ident"][0];
$prenom=$_POST["ident"][1];
$age=$_POST["ident"][2];
$lang = $_POST["lang"];
$competent=$_POST["competent"];
echo"Vous êtes :<b> $prenom ", stripslashes($nom) ,"</b><br />Vous avez <b>$age ans </b> ";
echo "<br />Vous parlez :";
echo "<ul>";
foreach($lang as $valeur)
{
echo " <li> $valeur </li>";
}
echo "</ul>";
echo "Vous avez des compétences informatiques en :";
echo "<ul>";
foreach($competent as $valeur)
{
echo "<li> $valeur </li> ";
}
echo "</ul> </td></tr>";
}
else
{
echo"<script type=\"text/javascript\">" ;
echo "alert('Cochez au moins une compétence!!');"; 
echo "window.history.back();"; 	
echo "</script>";
}
?>
</body>
</html>