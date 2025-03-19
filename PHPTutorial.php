<!DOCTYPE html>
<html>

<body>
    <?php
    $txt = "W3Schools.com";
    echo "I love $txt!<br/>";
    echo phpversion();
    //classes, functions, and user-defined functions are not case-sensitive
    echo "<br/>Hello World!<br>";
    echo "Hello World!<br>";
    echo "Hello World!<br>";
    # but all variable names are case-sensitive!
    $color = "red";
    echo "My car is " . $color . "<br/>";
    /*echo "My car is".$COLOR."<br/>";
    echo "My car is".$coLOR."<br/>";*/
    $x = 5;
    $y = 4;
    echo $x + $y . "<br/>";

    //to get the data type
    var_dump($x);
    var_dump($x + $y * 2);
    var_dump(3.14);
    var_dump([2, 3, 56]);
    var_dump(NULL);
    echo "<br/>";

    //assign the same value to multiple variables
    $x = $y = $z = "Fruit";
    echo $x . "<br/>";
    echo $y . "<br/>";
    echo $z . "<br/>";

    //Variables Scope

    $x = 5; // global scope

    function myTest()
    {
        // using x inside this function will generate an error
        echo "<p>Variable x inside function is: variable x</p>";
        $e = 10;
        echo "<p>Variable y inside function is: $e</p>";
    }
    myTest();
    echo "<p>Variable x outside function is: $x</p>";
    // using x outside the function will generate an error
    echo "<p>Variable e outside function is: variable e</p>";

    //global keyword to access a global variable in a fuction
    $x = 5;
    $y = 10;

    function myTest1()
    {
        global $x, $y;
        echo "<p>Variable x and y inside function is: $x and $y</p>";
    }

    myTest1();
    echo "<p>Variable x and y outside function is: $x and $y</p>";

    //global variables in an array
    $x = 5;
    $y = 10;
    function myTest2()
    {
        $GLOBALS['y'] = $GLOBALS['x'] + $GLOBALS['y'];
    }

    myTest2();
    echo $y;
    echo "<br/>";
    $name = 'Linus';
    function myTest3()
    {
        $GLOBALS['name'] = 'Tobias';
    }
    myTest3();
    echo $name;

    // when we want a local variable NOT to be deleted when a function is completed
    // we use the static keyword
    function myTest4()
    {
        static $x = 0;
        echo $x . '<br/>';
        $x++;
    }
    echo "<br/>";
    myTest4();
    myTest4();
    myTest4();
    

    ?>
</body>

</html>