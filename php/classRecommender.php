<?php

require_once "classNearestNeighbour.php";
require_once "connection.php";
class recommender{
    var $nearestNeighbours;
    var $ratingThreshold=3;
    var $connection;
    var $userAverages=array();
    var $finalRecommendations=array();
    var $userId;
    
    
    
    //constructor
    function __construct(){
        
        //connect to database
         $conn=new createConnection();
       
        if($conn){
            $this->connection=$conn;
            $this->connection->connectToDatabase();
        } 
    }//end constructor 
    
    
    
    /*-------------Function to get recommendations-----------*/
    function getRecommendations($userId){
        
        $this->userId=$userId;
        //get nearest neighbours
        $nearestNeighbour=new nearestNeighbour();        
        $this->nearestNeighbours=$nearestNeighbour->getNearestNeighbours($userId);
        
        if($this->nearestNeighbours == null){
            die ("no user by that Id");   
        }
        
        
        //find books neighbours have rated more that threshhold
        $userArray=array();
        $userString=array();
        foreach($this->nearestNeighbours as $key=>$value){
            array_push($userArray, $key);
        }
        
        //create comma separated string of users
        $userString=implode("," , $userArray);
      
        $query="SELECT DISTINCT bookId
                FROM ratings
                WHERE userId IN (".$userString.") 
                AND rating >= ".$this->ratingThreshold ;
        
        $result=$this->connection->performQuery($query);
        
        //store the books in an array
         $bookArray=array();
        while($row=$result->fetch_array(MYSQLI_ASSOC)){
           array_push($bookArray,$row['bookId']);            
        }//end while
        
       
        //remove books that the user has already rated
        $userRatedBooks=$nearestNeighbour->getUserRatedBooks();
        $bookArray=array_diff($bookArray, $userRatedBooks);
         
        //compute predicted rating
        //compute averages
        $this->computeUserAverages($userString);
        
        
        foreach($bookArray as $key=>$value){
            $this->getPredictedRating($value, $userString);
        }
         
        arsort($this->finalRecommendations);
        foreach($this->finalRecommendations as $key=>$value){
            echo "BookId: ".$key."   Predicted: ".$value."</br>"; 
        }
    
        
        
    }//end of getRecommendations
    
    
    
    
    //function to get predicted ratings
    function getPredictedRating($bookId, $userString){
        $query="SELECT bookId, userId, rating
                FROM ratings
                WHERE userId IN(".$userString.")
                AND bookId =".$bookId;
        
        $result=$this->connection->performQuery($query);
        $upper=0;
        $lower=0;
        $userAverage=$this->userAverages[$this->userId];
       while($row=$result->fetch_array(MYSQLI_ASSOC)){
            $upper+=($this->nearestNeighbours[$row["userId"]] * ($row["rating"]-$this->userAverages[$row["userId"]]));
            $lower+=abs($this->nearestNeighbours[$row["userId"]]);
        }
    
        //copy the book id and its predicted ratings to the array
        $this->finalRecommendations[$bookId]=($userAverage+($upper/$lower));
    }//end of getPredictedRating
    
    
    
    
    
    //function to compute user Averages
    function computeUserAverages($userString){
        $query="SELECT AVG(rating), userId
                FROM ratings
                WHERE userId IN (".$userString.",".$this->userId.")
                GROUP BY userId";
        
        $result=$this->connection->performQuery($query);
        
        while($row=$result->fetch_array(MYSQLI_ASSOC)){
            
            //insert average values in averages array
            $this->userAverages[$row['userId']]=$row['AVG(rating)'];
            
        }//end of while
        
        
    }//end of computeUserAverages
    
    
    
    
}//end of class recommender
?>