<?php
require_once '../includes/config.php';
require_once '../includes/connection.php';

if(isset ($_POST['search_term'])){
    $search_term = mysql_real_escape_string(htmlentities(trim($_POST['search_term'])));
    if(!empty ($search_term)){
       
       // $now = date("Y-m-d");
        $search = mysql_query("SELECT * FROM book WHERE name_of_book LIKE '%$search_term%' AND deleted = 'NO'");  
        if($search==FALSE){
            die (mysql_error());
        }
        $count = mysql_num_rows($search);
        if($count==0){
            
            echo '<script type=text/javascript>';
            
          echo  'alert("BOOK NOT AVAILABLE");';
            
            echo '</script>';
            
        }
         for($i=0;$i<$count;++$i){
             $row = mysql_fetch_array($search);
             
             echo  "<a style='color:black;text-decoration:none;font-size:15px;' href=library_tray.php?id=$row[0]>".$row['name_of_book']."-".$row['subject_of_book'].'</a>'.'</br>'.'</br>';
         }
    }
    
}

?>