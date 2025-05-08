<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Calculatrice en ligne</title>
</head>

<body>
    <?php
    if (isset($_POST['calcul']) && isset($_POST['nombre1']) && isset($_POST['nombre2'])) {
        $nombre1 = $_POST['nombre1'];
        $nombre2 = $_POST['nombre2'];
        $operation = $_POST['calcul'];

        switch ($_POST['calcul']) {
            case 'Addition x + y':
                $resultat = $nombre1 + $nombre2;
                break;
            case 'Division x/y':
                if ($nombre2 != 0) {
                    $resultat = $nombre1 / $nombre2;
                } else {
                    echo "Erreur : Division par zéro !";
                    exit;
                }
                break;
            case 'Multiplication x * y':
                $resultat = $nombre1 * $nombre2;
                break;
            case 'Soustraction x - Y':
                $resultat = $nombre1 - $nombre2;
                break;
            case 'Puissance x ^ y':
                $resultat = pow($nombre1, $nombre2);
                break;

            default:
                echo "Erreur : Opération non valide !";
                exit;
        }
    }
    else {
        echo "<h3>Entrer deux nombres</h3>";
    }
    ?>
    <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post">
        <fieldset>
            <legend><b>Calculatrice en ligne</b></legend>
            <table>
                <tbody>
                    <tr>
                        <th>Nombre X</th>
                        <td><input type="text" name="nombre1" size="10" value="<?= isset($nombre1) ? $nombre1 : '' ?>" /></td>
                    </tr>
                    <tr>
                        <th>Nombre Y</th>
                        <td><input type="text" name="nombre2" size="10" value="<?= isset($nombre2) ? $nombre2 : '' ?>" /></td>
                    </tr>
                    <td>Résultat</td>
                    <td><input type="text" name="resultat" size="10" value="<?= isset($resultat) ? $resultat : '' ?>"readonly/></td>
                    <tr>
                        <th>Choisisez</th>
                        <td><input type="submit" name="calcul" value="Addition x + y" /></td>
                        <td><input type="submit" name="calcul" value="Division x/y" /></td>
                        <td><input type="submit" name="calcul" value="Multiplication x * y" /></td>
                        <td><input type="submit" name="calcul" value="Soustraction x - Y" /></td>
                        <td><input type="submit" name="calcul" value="Puissance x ^ y" /></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
</body>

</html>