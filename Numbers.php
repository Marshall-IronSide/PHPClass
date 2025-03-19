<?php
$a = 5;
$b = 5.34;
$c = "25";
//verify the type
var_dump($a);
var_dump($b);
var_dump($c);
//Check if the type of a variable is integer
echo"<br/>";
var_dump(is_int($a));
var_dump(is_int($b));
var_dump(is_int($c));
//Check if the type of a variable is integer
echo"<br/>";
var_dump(is_float($a));
var_dump(is_float($b));
var_dump(is_float($c));
//Infinity value that is larger than PHP_FLOAT_MAX
echo"<br/>";
$x = 1.9e411;
var_dump($x);
var_dump(is_infinite($x));
//NaN stands for Not a Number.
echo"<br/>";
$y = acos(8);
var_dump($y);
var_dump(is_nan($y));
//Numerical Strings
echo"<br/>";
$x = 5985;
$y = "5985";
$a = "59.85" + 100;
$b = "Hello";
$c = 0xf4c3b00c;//hexadecimal form
$d = " 0xf4c3b00c";
var_dump(is_numeric($x));//returns true if the variable is a number or a numeric string
var_dump(is_numeric($y));
var_dump(is_numeric($a));
var_dump(is_numeric($b));
var_dump(is_numeric($c));
var_dump(is_numeric($d));
// Cast float to int
echo"<br/>";
$x = 23465.768;
$int_cast = (int)$x;
echo $int_cast;
?>