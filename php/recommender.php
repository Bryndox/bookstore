<?php
require ("connection.php");

//variables
$con=new createConnection();
$con->connectToDatabase();
$fraction=0.5;
$string=array();
$string2=array();
$uservalues=array();
$finalscore=array();
$pu=0;
$puA=0;
$pv=0;
$pvA=0;
$pu2=0;
$pv2=0;
$pcvalue0;

//get list of books user has rated together with the ratings
$query="SELECT bookId, rating
        FROM ratings 
        WHERE userId='1788036'";

$result=$con->performQuery($query);
$numberBooks=$result->num_rows;
if($numberBooks==0){
   die("No user by that id"); 
}

while($row=$result->fetch_array(MYSQLI_ASSOC)){
    
    array_push($string, $row["bookId"]);
    $uservalues[$row['bookId']]=$row['rating'];
}

$value=implode(",",$string);

/*foreach ($uservalues as $key => $value) {
 echo $value."</br>";
}*/
echo "Books rated: ".$value."</br>";
//get other users who have rated the same books
$query="SELECT  userId, COUNT(bookId)
        FROM ratings 
        WHERE bookId IN (".$value.")   
        GROUP BY userID
        ORDER BY COUNT(bookID) DESC
        LIMIT 0, 1000";

$result=$con->performQuery($query);

 //create an array of these users
 while($row=$result->fetch_array(MYSQLI_ASSOC)){
     array_push($string2, $row["userId"]);
 }
$value2=implode(",",$string2);
echo "Users: ".$value2."</br>";
//get ratings of these users
$query="SELECT  rating, bookId, userId
        FROM ratings 
        WHERE bookId IN (".$value.")   
        AND userId IN (".$value2.")
        ORDER BY userId DESC";


$result=$con->performQuery($query);

$workingArray=array();
$currentUser=-1;
while($row=$result->fetch_array(MYSQLI_ASSOC)){
     if($row['userId'] == $currentUser || $currentUser == -1){
         $workingArray[$row['bookId']]=$row['rating'];
         $currentUser=$row['userId'];
       //  echo $row['userId']."  ".$row['bookId']."  ".$row['rating']."  ".$uservalues[$row['bookId']]."</br>";
     }
    else{

        //compute averages
        $sum1=0;
        $sum2=0;
        foreach($workingArray as $key => $val){
           $sum1+=$val; 
           $sum2+=$uservalues[$key];
        }  
        $pvA=$sum1/sizeof($workingArray);
        $puA=$sum2/sizeof($workingArray);
        
        //compute correlation
        $upper=0;
        $lowerU=0;
        $lowerV=0;
       // echo "User ".$currentUser.": ";
         foreach($workingArray as $key => $val){
           $upper+=(($workingArray[$key]-$pvA)*($uservalues[$key]-$puA)); 
           $lowerV+=pow(($val-$pvA),2);
           $lowerU+=pow(($uservalues[$key]-$puA),2);
             //echo $key." => ".$uservalues[$key]."    ";
        } 
        
      $score=($upper)/(sqrt($lowerV)*sqrt($lowerU));
        $lw=(sqrt($lowerV)*sqrt($lowerU));
        echo "User ".$currentUser.": ".$score."</br>";
       // echo '</br>';
        $workingArray=array();
        $workingArray[$row['bookId']]=$row['rating'];
        $currentUser=$row['userId'];
    }
 }

/*
//perform correlation
while($counter<=100){
    $row=$result->fetch_array(MYSQLI_ASSOC);
    echo $row['sum(rating)']."    ".$row['userId']."    ".$row['COUNT(bookId)']."</br>";
    $counter++;
}*/




?>