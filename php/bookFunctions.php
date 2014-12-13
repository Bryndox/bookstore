
<?php
require ('connection.php');
require ('classNearestNeighbour.php');
require ('classRecommender.php');
$connection=new createConnection();
$connection->connectToDatabase();
$rows_per_page=1000;
$return=array();

switch ($_POST['action']) {
    
    //get all books
    case "GET_ALL_BOOKS":
       if(!isset($_POST['page'])){
           $_POST['page']=1;           
       }
        $page=$_POST['page'];
        $startRow=(($page-1)*$rows_per_page);
        $query="SELECT *
                FROM books";
     
        $result=$connection->performQuery($query);
        $no_pages=ceil(($result->num_rows)/$rows_per_page);
        
        $query="SELECT *
                FROM books
                LIMIT ".$startRow.", ".$rows_per_page;
        $result=$connection->performQuery($query);     
     
        
    
        $data='<table class="table-fill">
                        <thead>
                        <tr>
                        <th style="width:10%" class="text-left">BOOK ID</th>
                        <th style="width:10%" class="text-left">YEAR</th>
                        <th style="width:50%" class="text-left">NAME</th>
                        </tr>
                        </thead>   
                        </tbody>';
    
        while($row=$result->fetch_array(MYSQLI_ASSOC)){
        $data.='<tr>
                                       <td class="text-left">'.$row["bookId"].'</td>
                                       <td class="text-left">'.$row["year"].'</td>
                                       <td class="text-left">'.$row["name"].'</td>
                                       </tr> ';      
        }
    
        $data.= '</tbody>
                 </table>';
        $return["data"] = $data;
        $return["pages"]=$no_pages;
        echo json_encode($return);
        break; 
   
    

    
    
    //get book ratings
    case "GET_RATINGS":
        if(!isset($_POST['page'])){
           $_POST['page']=1;           
        }
        $page=$_POST['page'];
        $startRow=(($page-1)*$rows_per_page);
        $value=$_POST["value"];
        $option=$_POST["option"];
        
        
        if($option=="book"){
            $sql="SELECT * 
             FROM ratings
             WHERE bookId=".$value."
             LIMIT ".$startRow.", ".$rows_per_page;
            
            
            $query="SELECT * 
             FROM ratings
             WHERE bookId=".$value;
        }
        else if($option=="user"){
            $sql="SELECT * 
             FROM ratings
             WHERE userId=".$value;
            
             $query="SELECT * 
             FROM ratings
             WHERE userId=".$value."
             LIMIT ".$startRow.", ".$rows_per_page;
        }
        else{
        die("Wrong option");
        
        }
    
        $result=$connection->performQuery($query);
         $return["rows"]=$result->num_rows;
        $no_pages=ceil(($result->num_rows)/$rows_per_page);
        $result=$connection->performQuery($sql);
        if($result->num_rows >0){
            
            $data='<table class="table-fill">
                        <thead>
                        <tr>
                        <th style="width:2%" class="text-left">RATING ID</th>
                        <th style="width:5%" class="text-left">BOOK ID</th>
                        <th style="width:5%" class="text-left">USER ID</th>
                        <th style="width:5%" class="text-left">DATE</th>
                        <th style="width:5%" class="text-left">RATING</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">';
            
            while($row=$result->fetch_array(MYSQLI_ASSOC)){
                $data.='<tr>
                        <td class="text-left">'.$row["ratingId"].'</td>
                        <td class="text-left">'.$row["bookId"].'</td>
                        <td class="text-left">'.$row["userId"].'</td>
                        <td class="text-left">'.$row["date"].'</td>
                        <td class="text-left">'.$row["rating"].'</td>
                        </tr>';
            }
            $data.='</tbody>
                    </table>';
            
            $return["data"]=$data;
            $return["pages"] = $no_pages;
            echo json_encode($return);
        }
    
        else{
            $return["data"]="<p>No ".$option." has that ID</p>";
            $return["rows"]=0;
            echo json_encode($return);
        }
    
    
    break;
    
    
    case "GET_RECOMMENDATIONS":
        $nearestNeighbours=array();
        $recommendations=array();
        $result=array();
    
    //variables for timing
        
        if(isset($_POST['userId']) && isset($_POST['neighbourhoodSize'])){    
            $userId= $_POST['userId'];       
            $neighbourhoodSize=$_POST['neighbourhoodSize'];
            
        }
        else
            die ('Value(s) not set');
        
         $query="SELECT *
                FROM ratings
                WHERE userId=".$userId;
        $result=$connection->performQuery($query);
    
        if($result->num_rows==0){
            $return["data"]="ERROR_USER_NOT_FOUND";
            
        }
        else{
        //get recommendations
        $recommender=new recommender();
       
        $recommendations=$recommender->getRecommendations($userId, $neighbourhoodSize);
        
        $nearestNeighbours=$recommender->getNeighbours();
        
        
        //prepare recommendations table output
        $data='<table class="table-fill">
                        <thead>
                        <tr>
                        <th style="width:2%" class="text-left">NO.</th>
                        <th style="width:15%" class="text-left">BOOK ID</th>
                        <th style="width:10%" class="text-left">YEAR</th>
                        <th style="" class="text-left">TITLE</th>
                        <th style="width: 15%" class="text-left">PREDICTED RATING</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">';
            $counter=0;
            foreach($recommendations as $key => $value){
                $counter++;
                
                     $query="SELECT *
                        FROM books
                        WHERE bookId=".$key;
                $result=$connection->performQuery($query);
                $row=$result->fetch_array(MYSQLI_ASSOC);
                $data.='<tr>
                        <td class="text-left">'.$counter.'</td>
                        <td class="text-left">'.$row["bookId"].'</td>
                        <td class="text-left">'.$row["year"].'</td>
                        <td class="text-left">'.$row["name"].'</td>
                        <td class="text-left">'.$value.'</td>
                        </tr>';
   
                
                        }
    
            $data.='</tbody>
                    </table>';
            
    
    
            //prepare nearest neighbours output
            $dataNeigh='<table class="table-fill">
                        <thead>
                        <tr>
                        <th style="width:1%" class="text-left">NO.</th>
                        <th style="width:5%" class="text-left">USER ID</th>
                        <th style="width:10%" class="text-left">SIMILARITY SCORE</th>
                        </tr>
                        </thead>
                        <tbody class="table-hover">';
    
               $counter=0;
                foreach($nearestNeighbours as $key=> $value){
                    $counter++;
                    
                        $dataNeigh.='
                            <tr>
                                <td class="text-left">'.$counter.'</td>
                                <td class="text-left">'.$key.'</td>
                                <td class="text-left">'.$value.'</td>
                                </tr>
                                ';
                    
                    
                }
    
    
    
             $dataNeigh.='</tbody>
                    </table>';
    
            $return["data"]=$data;
            $return["dataNeigh"]=$dataNeigh;
        }
            //echo $data;
            echo json_encode($return);
        
            //echo sizeof($recommendations);*/
        
            break;
        
    default:
        echo "Wrong option";
        

}
       


?>
   