<?php
//session_start();
include ('connection.php');
//include ('errorcodes.php');
class userManager
{
    var $userId;
    var $firstname;
    var $lastname;
    var $email;
    var $username;
    var $password;
    var $connection;
    var $sessionTime=15;
    
    function __construct() 
    { 
        //create connection to database
        $conn=new createConnection();
        
        if($conn){
            $this->connection=$conn;
            $this->connection->connectToDatabase();
        }
    } 
    
    
    //function to login
    function login($username, $password){
        
        $this->username=$username;
        $this->password=$password;

        $query='SELECT * FROM users
                WHERE username="'.$this->username.'"';
        $result=$this->connection->performQuery($query);
        
        //No user by that username
        if($result->num_rows==0){
            echo "ERR_WRONG_USER";   
        }
        else{
            $row=$result->fetch_array(MYSQLI_ASSOC);
            
            //successful login
            if($this->password==$row["password"]){
                echo "SUCCESS_LOGIN";
                $this->userId=$row["userId"];
                
                //Start user session
                $this->createSession();
            }
            
            //wrong password
            else
                echo "ERR_WRONG_PASS";

            } 
    }

    
    //signup function
    function signUp($firstname, $lastname, $username, $email, $password){
       
        //check if usernmae exists
        $query="SELECT username 
                FROM users
                WHERE username='".$username."'";
        
        $result=$this->connection->performQuery($query);
        
        //username exists
        if($result->num_rows>0){
            echo "ERROR_USERNAME_EXISTS";
        }
        
        //username doesnt exist, register
        else{
        
           $query="INSERT INTO users (firstname, lastname, username,email, password)
                    VALUES ('".$firstname."', '".$lastname."', '".$username."','".$email."','".$password."')";
            $result=$this->connection->performQuery($query);
            if($result){
                $query="SELECT * 
                        FROM users
                        WHERE username='".$username."'";
                $result=$this->connection->performQuery($query);
                $row=$result->fetch_array(MYSQLI_ASSOC);
                $this->userId=$row["userId"];
                $this->username=$row["username"];
                
                $this->createSession();
                echo "SUCCESS_USER_REGISTERED";
            }
            else{
                echo "ERROR_SIGNUP";
            }
        }
    }

    
    
    function logout(){
        //Destroy Session variale
        session_destroy();
        echo "SUCCESS_LOGOUT";
        
    }
    
    function createSession(){
        # Initialize variables
		$_SESSION['id']=$this->userId;
        $_SESSION['username']=$this->username;
		$_SESSION['timeout']=time();
        
    }
    
    
    function destroySession(){
        
        
    }
    
    //function to check if session is valid
    function isSessionValid(){
        if(isset($_SESSION['id'])){
           $time=$_SESSION['timeout'];  
            
            //check if time has expired
            if($time > (time()-(60 * $this->sessionTime))){
                
                $_SESSION['timeout']=time();
                return true;
            }
            else
                return false;
        }
        else
            return false;        
    }
    
    
}

?>