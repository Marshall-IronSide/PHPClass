<!DOCTYPE html>
<html>

<body>
    <?php
    //When using double quotes, variables can be inserted to the string.
    //When using single quotes, variables have to be inserted using the.
    $txt1 = "Learn PHP";
    $txt2 = "W3Schools.com";

    echo "<h2>  $txt1  </h2>";
    echo '<p>Study PHP at ' . $txt2 . '</p>';

    #print 
    print "<h2>PHP is Fun!</h2>";
    print "Hello world!<br>";
    print "I'm about to learn PHP!";
    //Double quoted string literals perform operations for special characters
    //Single quoted string literals returns the string as it is
    echo "<br/>";
    //get the lengh of a string
    echo strlen("Hello world!");

    //count the number of words in a string
    echo "<br/>";
    echo str_word_count("Hello, world!");
    echo "<br/>";

    //Search for the text "world" in the string "Hello world!"
    echo strpos("Hello, world!", "world");

    //upper case
    $x = "Hello World!";
    echo "<br/>";
    echo strtoupper($x);

    //lowercase
    $x = "Hello World!";
    echo "<br/>";
    echo strtolower($x);
    //replaces some characters with some other characters in a string
    $x = "Hello World!";
    echo "<br/>";
    echo str_replace("World", "Dolly", $x);
    //Reverse a String
    $x = "Hello, world";
    echo "<br/>";
    echo strrev($x);
    //remove whitespace
    $x = "Hello, world!";
    echo "<br/>";
    echo trim($x);

    //Convert String into Array
    $x = "Hello world";
    $y = explode(" ", $x);
    echo "<br/>";
    print_r($y);

    //String Concatenation
    echo "<br/>";
    $x = "Hello ";
    $y = "World!";
    echo $x . $y;

    $x = 5;
    $y = 10;
    $z = "$x . $y";
    echo "<br/>";
    echo "$z";
    $x = 5;
    $y = 10;
    $z = $x . $y;
    echo "<br/>";
    echo "$z";

    //return a range of characters
    echo "<br/>";
    $x = "Hello World!";
    echo substr($x, 6, 5);
    echo "<br/>";
    $x = "Hello World!";
    echo substr($x, -5, 3);
    echo "<br/>";
    $x = "Hi, how are you?";
    echo substr($x, 5, -3);

    //escape character
    echo "<br/>";
    echo "We are the so-called \"Vikings\" from the north.";

    ?>
</body>

</html>