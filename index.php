<?php
session_start();
if (isset($_POST['login']) && isset($_POST['password'])) {
    if ($_POST['login'] == "IronSide" && $_POST['password'] == "07052000") {
        $_SESSION['access'] = "oui";
        $_SESSION['login'] = $_POST['login'];
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.1//" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="https://www.w3.org/1999/xhtml/" xml:lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
    <title>Ouvrir Session</title>
</head>

<body>
    <div>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <fieldset>
                <legend>Accès reservé aux persones autorisées : Identifiez vous!</legend>
                <p>
                    <label for="login">Login:</label>
                    <input type="text" name="login" id="login" required />
                </p>
                <p>
                    <label for="password">Pass: &nbsp;</label>
                    <input type="password" name="password" />
                </p>
                <p>
                    <input type="submit" name="envoe" value="Entrez" />
                </p>
            </fieldset>
        </form>
        Visitez les pages du site <br />
        <ul>
            <li>
                <a href="https://esagnde.org/">Informations sur l'école</a>
            </li>
        </ul>
    </div>
</body>

</html>