<?php
session_start();
include ('../php/userManager.php');


$userMan=new userManager();

/*if(isset($_POST['username']) && isset($_POST['password']))
{
    $userMan->login($_POST['username'], $_POST['password']);
    
}*/


    echo "No match";
?>
