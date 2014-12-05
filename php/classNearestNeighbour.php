<?php

require_once "connection.php";
class nearestNeighbour{
    
    var $booksArray=array();
    var $bookString=array();
    var $userArray=array();
    var $userString=array();
    var $uservalues=array();
    var $finalscore=array();
    var $connection;
    var $userId;
    var $userLimit=1000;
    
    /*-----------------constructor------------------------------*/
    function __construct(){
        
        //connect to database
         $conn=new createConnection();
       
        if($conn){
            $this->connection=$conn;
            $this->connection->connectToDatabase();
        }
    }
        
        
        
    /*----------Function to get the nearest neighbours-----------*/
    function getNearestNeighbours($userId){
        $this->userId=$userId;

        //get list of books user has rated together with the ratings
        $query="SELECT bookId, rating
                FROM ratings 
                WHERE userId=".$userId;

        $result=$this->connection->performQuery($query);
        $numberBooks=$result->num_rows;
        
        //if userId does not exist in database
        if($numberBooks==0){
           die ("No user by that id"); 
        }
        
         //if userId exists in database
        else{
            
                while($row=$result->fetch_array(MYSQLI_ASSOC)){
                     //populate array of books that user has rated 
                    array_push($this->booksArray, $row["bookId"]);
                    
                    //create associative array of each book the user has rated and its respective rating value
                    $this->uservalues[$row['bookId']]=$row['rating'];
                }
            
        }  
        
        //Create comma separated string of books
        $this->bookString=implode(",",$this->booksArray);
        
        echo "Books rated: ".$this->bookString."</br>";
        
        //get other users who have rated the same boooks
        $query="SELECT  userId, COUNT(bookId)
        FROM ratings 
        WHERE bookId IN (".$this->bookString.")   
        GROUP BY userID
        ORDER BY COUNT(bookID) DESC
        LIMIT 0, ".$this->userLimit;
        
        $result=$this->connection->performQuery($query);
        $numberUsers=$result->num_rows;
        
        //if no user have rated that book
        if($numberUsers==0){
           die ("No usesr have rated that book"); 
        }
        
         //if users have rated that book
        else{
            
                while($row=$result->fetch_array(MYSQLI_ASSOC)){
                     //populate array of these users
                    array_push($this->userArray, $row["userId"]);
            }  
            $this->userString=implode(",", $this->userArray);
            echo "Users: ".$this->userString."</br>";
        }
        
        //get these users ratings
        $query="SELECT  rating, bookId, userId
                FROM ratings 
                WHERE bookId IN (".$this->bookString.")   
                AND userId IN (".$this->userString.")
                ORDER BY userId DESC";
    
        
        $result=$this->connection->performQuery($query);

        $workingArray=array();
        $currentUser=-1;
        while($row=$result->fetch_array(MYSQLI_ASSOC)){
             if($row['userId'] == $currentUser || $currentUser == -1){
                 
                 //Store book id and corresponding rating for this user. To be used for computing correlation
                 $workingArray[$row['bookId']]=$row['rating'];
                 $currentUser=$row['userId'];
    
             }
            else{

                //compute averages
                $sum1=0;
                $sum2=0;

                //calculate totals
                foreach($workingArray as $key => $val){
                   $sum1+=$val;  //total of this users ratings
                   $sum2+=$this->uservalues[$key];  //total of initial users ratings
                }  

                //average of this user
                $pvA=$sum1/sizeof($workingArray);

                //average of initial user
                $puA=$sum2/sizeof($workingArray);

                //compute correlation
                $upper=0;
                $lowerU=0;
                $lowerV=0;

                 foreach($workingArray as $key => $val){
                   $upper+=(($workingArray[$key]-$pvA)*($this->uservalues[$key]-$puA)); 
                   $lowerV+=pow(($val-$pvA),2);
                   $lowerU+=pow(($this->uservalues[$key]-$puA),2);
                     //echo $key." => ".$uservalues[$key]."    ";
                } 

                $totalLower=sqrt($lowerV)*sqrt($lowerU);
                if($totalLower==0){
                    $score=0;
                }

                else{
                    $score=($upper)/$totalLower;

                }


                //insert user's correlation score to finalScore array
                $this->$finalscore[$currentUser]=$score;
               // echo "User ".$currentUser.": ".$score."</br>";

                //clear contents of working array and initialize with next user
                $workingArray=array();
                $workingArray[$row['bookId']]=$row['rating'];

                //set currentuser to be the next user
                $currentUser=$row['userId'];

            }//end else
        }//end while

    
    
    }
}


?>