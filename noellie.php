    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.1//" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

    <html xmlns="https://www.w3.org/1999/xhtml/" xml:lang="fr">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
        <title>Calcul de prêts</title>

    </head>

    <body>
        <h3>Prêts</h3>
        <form method="POST" action="martin.php">
            <fieldset>
                <legend>Les caractéristiques de votre prêt</legend>
                <table>
                    <tr>
                        <td>Montant</td>
                        <td><input type="text" name="capital"></td>
                    </tr>
                    <tr>
                        <td>Taux</td>
                        <td><input type="text" name="taux" /></td>
                    </tr>
                    <tr>
                        <td>Durée en année</td>
                        <td><input type="text" name="duree" /></td>
                    </tr>
                    <tr>
                        <td>Assurance</td>
                        <td>OUI :<input type="radio" name="assur" checked="checked" value="1" />&nbsp;NON :<input type="radio" name="assur" value="1" />&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type="reset" name="" value="Effacer" /></td>
                        <td><input type="submit" name="" value="Calculer" /></td>
                    </tr>
                </table>
            </fieldset>
    </body>

    </html>