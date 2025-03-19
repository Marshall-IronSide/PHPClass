<?php
//define(name, value);
define("GREETINGS","Hello everybody");
echo GREETINGS;
echo("<br/>");
const MYCAR = "Volvo";
echo MYCAR;
//const cannot be created inside another block scope, like inside a function or inside an if statement.
//define can be .

//create an Array constant 
echo ("<br/>");
define ("Cars",["Mclaren","Redbull","Mercedes","Ferrari","Aston Martin"]);
echo Cars[1];

//Constants are automatically global
define("MORNING","Hello everybody");
function myTest(){
    echo MORNING;
}
echo ("<br/>");
myTest();
?>