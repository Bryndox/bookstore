
<?php
include ('connection.php');
$connection=new createConnection();
$connection->connectToDatabase();
$rows_per_page=1000;
$return=array();

switch ($_POST['action']) {
    
    //Login
    case "GET_ALL_BOOKS":{
       if(!isset($_POST['page'])){
           $_POST['page']=1;           
       }
        $page=$_POST['page'];
        $startRow=(($page-1)*$rows_per_page);
    
        $query="SELECT bookId
                FROM books";
        $result=$connection->performQuery($query);
        $no_pages=ceil(($result->num_rows)/$rows_per_page);
        
        $query="SELECT *
                FROM books
                LIMIT ".$startRow.", ".$rows_per_page;
        $result=$connection->performQuery($query);     
    
        
       
     
        $return["pages"] = $no_pages;
    
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
        echo json_encode($return);
        break; 
   
    }

    
    
    //get book ratings
    case "GET_RATINGS":
        $value=$_POST["value"];
        $option=$_POST["option"];
        $sql="";
        if($option=="book"){
            $sql="SELECT * 
             FROM ratings
             WHERE bookId=".$value;
        }
        else if($option=="user"){
            $sql="SELECT * 
             FROM ratings
             WHERE userId=".$value;
        }
        else{
        die("Wrong option");
        
        }
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
            $return["rows"]=$result->num_rows;
            echo json_encode($return);
        }
    
        else{
            $return["data"]="<p>No ".$option." has that ID</p>";
            $return["rows"]=0;
            echo json_encode($return);
        }
    
    
    break;
    
        
    default:
        echo "Wrong option";
        
}

       


?>
   