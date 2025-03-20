<html>
    <body>
        <form action="Form1.php" method="post">
            <fieldset>
                <legend>User informations</legend>
            Name: <input type="text" name="name"><br/>
            Email: <input type="text" name="email"><br/>
           Gender: <input type="radio" value="Male" name="sexe">Male
            <input type="radio" value="Female" name="sexe">Female<br/>
            About you :<textarea name="comment" rows="5" cols="40"></textarea><br/>
            Powers: 
            Superstrength<input type="checkbox" name="cap">
            Superspeed<input type="checkbox" name="cap">
            Invisibility <input type="checkbox" name="cap"><br/>
            <input type="submit" value="Send">
            <input type="reset" value = "cancel">
            </fieldset>
        </form>
    </body>
</html>