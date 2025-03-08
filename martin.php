<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.1//" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="https://www.w3.org/1999/xhtml/" xml:lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
    <title>Tableau d'amortissement</title>
</head>

<body>
    <div>
        <?php
            if (isset($_POST["capital"]) && isset($_POST["taux"]) && isset($_POST["duree"])) 
            {
                $capital =$_POST["capital"];
                $taux =$_POST["taux"]/100/12;
                $duree =$_POST["duree"]*12;
                $assur =$_POST["assur"]*$capital*0.00035;
                $mens = ($capital * $taux) / (1 - pow(1 + $taux, -$duree));
                echo "<h3>Pour un prêt de $capital F CFA à {$_POST['taux']}% sur {$_POST['duree']} ans, la mensualité est de " . round($mens, 2) . " F CFA hors assurance.</h3>";
                echo "<h4>Tableau d'amortissement du prêt</h4>";
                echo "<table border=\"1\"> <tr><th>Mois </th><th>Capital restant</ th><th> Mensualité Hors Ass.</th><th>Amortissement </ th><th> Intérêt</th><th> Assurance</th><th>Mensualité Ass.cis </ th>";
//Boucle d'affichage des lignes du tableau
for($i=1;$i<=$duree;$i++)
{
$int=$capital*$taux;
$amort=$mens-$int;
echo "<tr>";
echo "<td>$i</td>";
echo "<td>",round($capital,2),"</td>";
echo "<td>",round($mens,2),"</td>";
echo "<td>",round($amort,2),"</td>";
echo "<td>",round($int,2),"</td>";
echo "<td>$assur</td>";
echo "<td>",round($mens+$assur,2),"</td>";
echo "</tr>";
$capital=$capital-$amort;
}
echo "</table>";
}
else
{
header("Location:form4.php");
}
?>
</div>
</body>
</html>