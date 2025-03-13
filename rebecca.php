<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.1//" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="https://www.w3.org/1999/xhtml/" xml:lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
    <title>Liste à chois multiple</title>
</head>

<body>
    <form method="post" action="erwin">
        <fieldset>
            <legend>
                Recherche d'emplois: complétez la fiche
            </legend>
            <div>
                <span>Nom<input type="text" name="ident[]" />
                    prénom <input type="text" name="ident[]" />
                    age <input type="text" name="ident[]" />
                    <br /><br />
                    Langues pratiquées<br />
                    <select name="lang[]" multiple="multiple">
                        <option value="français"> français</option>
                        <option value="anglais"> anglais</option>
                        <option value="allemand"> allemand</option>
                        <option value="espagnol"> espagnol</option>
                    </select><br /><br />
                    Compétences informatiques<br />
                    XHTML<input type="checkbox" name="competent[]" value="XHTML" />
                    PHP<input type="checkbox" name="competent[]" value="PHP" />
                    MySQL<input type="checkbox" name="competent[]" value="MySQL" />
                    ASP.Net<input type="checkbox" name="competent[]" value="ASP.Net" />
                </span><br /><br />
                <input type="reset" value="EFFACER" />
                PHP 5 174
                <input type="submit" value="ENVOI" />
            </div>
        </fieldset>
    </form>
</body>

</html>