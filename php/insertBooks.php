<?php
include ('connection.php');

$connection=new createConnection();
$connection->connectToDatabase();
echo "<html><body>";
/*
$myfile=fopen("movies.txt", "r") or die("Unable to open file!");
echo "<html><body>";


$count=0;
while(!feof($myfile)){
    $line=fgets($myfile);
    $pos1=strpos($line,',');
    $id=substr($line,0,$pos1);
    $pos2=strpos($line,',',$pos1+1);
    $year=substr($line,$pos1+1,$pos2-$pos1-1);
    $name=substr($line,$pos2+1);    
    $count++;
    
$sql='INSERT INTO books (bookId, year, name) VALUES ('.$id.','.$year.',"'.$name.'")';
$connection->performQuery($sql);
}
echo $count." books have been inserted into the database.";
*/

ob_end_flush();
$base=10000000;
for ($x=101; $x<=1000; $x++) {
    $bookName="mv_".substr($base+$x,1).".txt";
    
    $myfile=fopen("../training_set/".$bookName, "r") or die("Unable to open file ".$bookName);
    $bookId=fgets($myfile);
    $bookId=substr($bookId,0,strlen($bookId)-2);

    while(!feof($myfile)){
        $line=fgets($myfile);
        $pos1=strpos($line,',');
        $id=substr($line,0,$pos1);
        $pos2=strpos($line,',',$pos1+1);
        $rating=substr($line,$pos1+1,$pos2-$pos1-1);
        $date=substr($line,$pos2+1);    
        
    //echo "ID: ".$id.". RATING: ".$rating.". DATE: ".$date."</br>";
        if($id && $date && $rating){
        $sql='INSERT INTO ratings (bookId, userId, date, rating) VALUES ('.$bookId.','.$id.',"'.$date.'",'.$rating.')';
        $connection->performQuery($sql);
        }
    }
    echo "Book ".$bookId." complete </br>";
    fclose($myfile);
    
}

   


echo "</body></html>";

//$connection->closeConnection();
?>