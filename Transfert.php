<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Transfert de fichiers</title>
</head>

<body>
    <form action="<?= $_SERVER["PHP_SELF"]?>" method="post" enctype="multipart/form-data">
        <fieldset>
            <input type="hidden" name="MAX_FILE_SIZE" value="100000" />
            <legend><b>Transfert de fichiers</b></legend>
            <table>
                <tbody>
                    <tr>
                        <th>
                            Fichier
                        </th>
                        <td>
                            <input type="file" name="fich" accept="image/jpg" size="50" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Clic!
                        </th>
                        <td><input type="submit" value="Envoi"></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </form>
    <?php
        if(isset ($_FILES['fich'])) 
        {
           echo "Taille maximale autorisée:",$_POST['MAX_FILE_SIZE']," octets<hr/>";
           echo "<b>Clé et valeurs du tableau \$_FILES</b><br/>";
        
           foreach ($_FILES['fich'] as $cle => $valeur) 
           {
              echo "cle:$cle valeur:$valeur<br/>";
           }
           $result=move_uploaded_file($_FILES['fich']['tmp_name'],"imageimass.jpg");
        
            if($result==TRUE)
            {
           echo "<hr/<big>Le fichier a été transféré avec succès</big>";
            }
            else
            {
            echo "<br/>Erreur de transfert numéro ",$_FILES['fich']['Error'];
            }
        }
    ?>
</body>

</html>