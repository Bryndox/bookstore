<?php

class createConnection
{
    var $host="localhost";
    var $username="root";
    var $password="rocker";
    var $database="bookstore";
    var $myConn;
    
    //function to connect to database
    function connectToDatabase(){
         $conn=new mysqli($this->host, $this->username, $this->password, $this->database);
        
        //testing the connection
        if($conn->connect_error){
            die("Cannot connect to database: ".$conn->connect_error);
        }
        $this->myConn=$conn;        
    } 
    
    //function to run queries
    function performQuery($sql){
        
        //insert into database
        $result=$this->myConn->query($sql);
        if($result){
            return $result;
        }
        else{
            die ("Error: ".$sql." :: ".$this->myConn->error);  
        }
    }
    
    
    
    //function to close connection to database
    function closeConnection(){
        $this->myConn->close();
        echo "Connection closed";
        
    }
    
    
}

?>