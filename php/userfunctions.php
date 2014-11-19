<?php
session_start();
include ('userManager.php');


$userMan=new userManager();

switch ($_POST['action']) {
    
    //Login
    case "login":
        if(isset($_POST['username']) && isset($_POST['password'])){
            $userMan->login($_POST['username'], $_POST['password']);
        }
        else
            echo "No match";
        break;
    
    
    //Logout
    case "logout":
        $userMan->logout();
        break;
    
    //Register
    case "register":
        if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['username']) && isset ($_POST['email']) && isset($_POST['password'])){
            $firstname=$_POST['firstname'];
            $lastname=$_POST['lastname'];
            $username=$_POST['username'];
            $email=$_POST['email'];
            $password=$_POST['password'];
            
            $userMan->signUp($firstname, $lastname, $username, $email, $password);
        }
        else
            echo "No match";
        
        break;
    
    
    default:
        echo "Wrong option";
}




?>