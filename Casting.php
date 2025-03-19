<?php

// cast string to int
$x = "23465.768";
$y = (int)$x; 
echo $y;

$a = 5;
$b = 5.34;
$c = "hello";
$d = true;
$e = NULL;

$a = (string) $a;
$b = (string) $b;
$c = (string) $c;
$d = (string) $d;
$e = (string) $e;
var_dump($a);
var_dump($b);
var_dump($c);
var_dump($d);
var_dump($e);

$a = 5;
$b = 5.34;
$c = "hello";
$d = true;
$e = NULL;

$a = (array) $a;
$b = (array) $b;
$c = (array) $c;
$d = (array) $d;
$e = (array) $e;
var_dump($a);
var_dump($b);
var_dump($c);
var_dump($d);
var_dump($e);

//Converting Objects into Arrays
class car {
    public $color;
    public $model;
    public function __construct($color, $model) {
        $this->color = $color;
        $this->model = $model;
}
public function __message() {
return "My car is a".$this ->color ." ". $this->model ."!";
}
}
$myCar = new car("red","volvo");
$myCar = (array) $myCar;
echo "<br/>";
var_dump($myCar);

//cast to object
$a = 5;
$b = 5.34;
$c = "hello";
$d = true;
$e = NULL;

$a = (object) $a;
$b = (object) $b;
$c = (object) $c;
$d = (object) $d;
$e = (object) $e;
echo"<br/>";
var_dump($a);
var_dump($b);
var_dump($c);
var_dump($d);
var_dump($e);

//Associative arrays converts into objects with the keys as property names and values as property values
$a = array("Volvo", "BMW", "Toyota"); // indexed array
$b = array("Peter"=>"35", "Ben"=>"37", "Joe"=>"43"); // associative array

$a = (object) $a;
$b = (object) $b;
echo"<br/>";

var_dump($a);
var_dump($b);
?>