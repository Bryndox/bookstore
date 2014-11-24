<?php
require ("connection.php");
$con=new createConnection();
$con->connectToDatabase();
$fraction=0.5;


//get list pf books user has rated
$query="SELECT bookId
        FROM ratings 
        WHERE userId='455024'";

$result=$con->performQuery($query);
$numberBooks=$result->num_rows;
if($numberBooks==0){
   die("No user by that id"); 
}
$string=array();
while($row=$result->fetch_array(MYSQLI_ASSOC)){
    
    array_push($string, $row["bookId"]);
}
$value=implode(",",$string);


//get other users who have rated the same books
$query="SELECT  rating, userId, COUNT(bookId)
        FROM ratings 
        WHERE bookId IN (".$value.")   
        GROUP BY userID
        ORDER BY COUNT(bookID) DESC";

$result=$con->performQuery($query);
$counter=1;


//perform correlation
while($counter<=100){
    $row=$result->fetch_array(MYSQLI_ASSOC);
    echo $row['rating']."    ".$row['userId']."    ".$row['COUNT(bookId)']."</br>";
    $counter++;
}




?>