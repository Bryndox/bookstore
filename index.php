<?php
session_start();
include ('php/userManager.php');

$userMan=new userManager();

/*Determine if session exists*/
//Load home page if session exists
if($userMan->isSessionValid()){
   include ('home.html');

}

//Load signup page if session exists
else
    include ('signup.html');



?>