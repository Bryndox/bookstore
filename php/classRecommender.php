<?php

require_once "classNearestNeighbour.php";
require_once "connection.php";
class recommender{
    var $nearestNeighbours=array();
    var $ratingThreshold=3;
    var $connection;
    var $userAverages=array();
    var $finalRecommendations=array();
    var $userId;
    var $recommendationThreshhold=100;
    
    
    
    
    //constructor
    function __construct(){
        
        //connect to database
         $conn=new createConnection();
       
        if($conn){
            $this->connection=$conn;
            $this->connection->connectToDatabase();
        } 
        
        //set the array of nearest neighbours
       // $this->nearestNeighbours=$nearestNeighbours;
    }//end constructor 
    
    
    
    /*-------------Function to get recommendations-----------*/
    function getRecommendations($userId, $neighbourhoodSize){
        
        $this->userId=$userId;
        //get nearest neighbours
        $nearestNeighbour=new nearestNeighbour(); 
      
        $this->nearestNeighbours=$nearestNeighbour->getNearestNeighbours($userId, $neighbourhoodSize);
       
        
        //user with that ID does not exist
        if($this->nearestNeighbours == null){
           
            return null;   
        }
        // die($neighbourhoodSize);
       
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
         
        
        /*foreach($bookArray as $key=>$value){
            $this->getPredictedRatinggg($value, $userString);
        }*/
        
       
         $bookString=implode("," , $bookArray);
        $this->getPredictedRating($bookString, $userString);
        
            
        
        arsort($this->finalRecommendations);
        array_slice($this->finalRecommendations, 0, 90);
        
       
        return $this->finalRecommendations;
    
        
        
    }//end of getRecommendations
    
    
    
    
    /*function to get predicted ratings
    function getPredictedRatinggg($bookId, $userString){
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
    }//end of getPredictedRating*/
    
    
    
    
    
    function getPredictedRating($bookString, $userString){
        $query="SELECT bookId, userId, rating
                FROM ratings 
                WHERE userId IN(".$userString.")
                AND bookId IN(".$bookString.")
                ORDER BY bookId";
        
        $result=$this->connection->performQuery($query);
        $upper=0;
        $lower=0;
        $currentBook=-1;
        $userAverage=$this->userAverages[$this->userId];
        
        while($row=$result->fetch_array(MYSQLI_ASSOC)){
             if($row['bookId'] == $currentBook || $currentBook == -1){
                
                 $upper+=$this->nearestNeighbours[$row["userId"]] * ($row["rating"]-$this->userAverages[$row["userId"]]);
                 $lower+=abs($this->nearestNeighbours[$row["userId"]]);
                 $currentBook=$row['bookId'];
            }
            else{
                 $prediction=$userAverage+($upper/$lower);
                 if($prediction>5){
                     $prediction=5.0;                     
                 }
                    else if($prediction<0){
                        $prediction=0.0; 
                    }
                
                 $this->finalRecommendations[$currentBook]=$prediction;
                  $upper=0;
                  $lower=0;
                  $currentBook=$row['bookId'];
                
                
                 $upper+=$this->nearestNeighbours[$row["userId"]] * ($row["rating"]-$this->userAverages[$row["userId"]]);
                 $lower+=abs($this->nearestNeighbours[$row["userId"]]);
                
            }
        }
           
    }//end of getPredictedRatings
    
    
    
    
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
    
    //function to get nearest neighbours
    function getNeighbours(){
        return $this->nearestNeighbours;
        
        
    }
    
    
}//end of class recommender
?>