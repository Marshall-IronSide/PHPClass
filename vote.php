<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML1.1//" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="https://www.w3.org/1999/xhtml/" xml:lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset= iso-8859-1" />
    <title>sondage en ligne :voter foot</title>
</head>
<body style="background-color:#a0a1a1";>
    <form action="<?=$_SERVER['PHP_SELF']?>"method="post">
    <fieldset>
        <legend><b>voter pour votre joueur préféré!</b></legend>
        <p>
            <?php
            $joueurs=array("Mbap"=>"MBAPPE","ronal"=>"Ronaldo","mes"=>"messi");
            ?>
            MBAPPE<input type="radio" name="vote" value="Mbap"/><br/>
            RONALDO<input type="radio" name="vote" value="ronal"/><br/> 
            MESSI<input type="radio" name="vote" value="mes"/><br/>
            <input type="submit" value="Voter"/>
            <input type="submit" value="afficher les resultats" name="affiche"/>
        </p>
    </fieldset>
    </form>
    <?php
    if(isset($_POST["vote"]))
    {
        $vote=$_POST["vote"];
        echo"<h2>Merci pour votre vote pour ".$joueurs[$vote]."</h2>";
        if(file_exists("votes.txt"))
        {
            if($id_file=fopen("Votes,txt","a"))
            {
                flock($id_file,2);
                fwrite($id_file,$vote."\n");
                flock($id_file,3);
                fclose($id_file);
            }
            else
            {
                echo"fichier inaccessible";
            }
        }
        else
        {
            $id_file=fopen("votes.txt","w");
            fwrite($id_file,$vote."\n");
            fclose($id_file);
        }
    }
    else
    {
        echo"<h2>completer le formulaire puis cliquer sur voter!</h2>";
    }
    $result=array("Mbappé"=>0,"Ronaldo"=>0,"Messi"=>0);
    if(isset($_POST["affiche"]))
    {
        if($id_file=fopen("votes.txt","r"))
        {
            while($ligne=fread($id_file,6))
            {
                switch($ligne)
                {
                    case"mbap\n":
                    $result["Mbappé"]++;
                    break;
                    case"ronal\n":
                    $result["Ronaldo"]++;
                    break;
                    case"mes\n":
                    $result["Messi"]++;
                    break;
                     default;
                    break;
                }
            }
            fclose($id_file);
        }
        $totalVotes = $result["Mbappé"] + $result["Ronaldo"] + $result["Messi"];
        if ($totalVotes > 0) {
            $total = $totalVotes/100;
            $tri=$result;
            arsort($tri);
            echo"<div style=\"border-style:double\">";
            echo"<h3>les resultats de votes</h3>";
            foreach($tri as $nom=>$score)
            {
                $i=2;
                $i++;
                echo"<h4>$i<sup>e</sup>: ",$nom,"a $score voix soit ",number_format($score/$total,2),"%</h4>";
            }
            echo"</div>";
        } else {
            echo "<div style=\"border-style:double\">";
            echo "<h3>Aucun vote n'a encore été enregistré</h3>";
            echo "</div>";
        }
    }
    ?>
    </body>
    </html>