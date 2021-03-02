<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Library
 *
 * @author SEBASTIAN
 */
class Library {
    //put your code here
    
    
    
      public function library_last_id_check(){
        
         $query = mysql_query("SELECT MAX(id_of_book) FROM book ORDER BY id_of_book LIMIT 1") or die(mysql_error());
   if($query){
       if(mysql_num_rows($query)>0){
           
            while ($row = mysql_fetch_array($query)) {
        $last_user_id =($row["MAX(id_of_book)"]);
        
          return $last_user_id;
    }
           
       }
  
 }
        
        
    }
    
    
    
       public function generate_user_id(){
        
        $code_start = "0001";
        
        $year = date("Y");
        
        $sub_year = substr($year, -2);
       
        
       if($this->library_last_id_check() != NULL){
           
           $last_user_id = $this->library_last_id_check();
           
           $exploded_part = explode("/", $last_user_id);
           
           $code_part = $exploded_part[2] + 1;
           $prject_name_part = $exploded_part[0];
           $year_part = $exploded_part[1];
           
           $new_increment_user_id = $code_part;
           
           if(strlen($new_increment_user_id)<=1){
             $new_increment_user_id =  "000".$new_increment_user_id;
           }elseif (strlen($new_increment_user_id)<=2) {
                $new_increment_user_id = "00".$new_increment_user_id;
            }elseif(strlen($new_increment_user_id)<=3)   {
                $new_increment_user_id = "0".$new_increment_user_id;
            }  else {
                strlen($new_increment_user_id) >= 5;
            }  
            $new_code = $prject_name_part."/"."$sub_year"."/".$new_increment_user_id;
            
       }  else {
              
            $new_code = "LIB"."/"."$sub_year"."/"."$code_start";
           
          // $new_increment_user_id = "AGROFARM"."/"."$sub_year"."/"."$code_start";
       }
       
       
       
       return $new_code;
        
        
    }
    
 
    
    
      public function getAllSubjects() {
    
    $category_name = mysql_query("SELECT * FROM subjects");
   if ($category_name){
       if (mysql_num_rows($category_name) > 0){  
   while($row = mysql_fetch_array($category_name)){
        echo  '<option value="'.$row['subject_name'].'">'.$row['subject_name'].'</option>';
   }
       }
       }
    
}


public function populateBooks(){
    
    
    $book_id = clean($_POST['book_id']);
    
     $book_name = clean($_POST['book_name']);   
     
         $author = clean($_POST['author']);
        $edition = clean($_POST['edition']);
        
          $pages = clean($_POST['pages']);  
        $color = clean($_POST['color']);
        
            $quantity = clean($_POST['quantity']);
            
            
                $subject = clean($_POST['subject']);
                
                    $category = clean($_POST['category']);
                    
                    
mysql_query("INSERT INTO book(id_of_book,name_of_book,author_of_book,"
        . "category_of_book,subject_of_book,edition_of_book,total_pages_of_book,"
        . "color_of_book,quantity,date_populated,time_populated) VALUES".
        "('$book_id','$book_name','$author','$category','$subject','$edition','$pages','$color','$quantity',NOW(),NOW())") or die(mysql_error());                 

redirect_to("books.php");


}


   public function getBooks() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM book ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY quantity DESC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }
    
    
     public function getTrayRecords() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM tray ";
        $query .= "WHERE collection_status = 'YES' ";
        $query .= "ORDER BY id DESC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }
    
     public function getTrayRecordsforToday() {
        global $connection;
        
        
         $now = date("Y-m-d");
        
       
        
       // $date = date("Y-m-d");

        $query = "SELECT * ";
        $query .= "FROM tray ";
        $query .= "WHERE collection_status = 'YES'";
        $query .= "ORDER BY id DESC";

        $query_results = mysqli_query($connection, $query) or die(mysql_error());
        confirm_query($query_results);

        return $query_results;
    }
    
    
    
    
     public function getBookHash($url_hash) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM book ";
        $query .= "WHERE id_of_book = '{$url_hash}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query) or die(mysql_error());
        confirm_query($query_results);

        if ($user = mysqli_fetch_assoc($query_results)) {
            return $user;
        } else {
            return NULL;
        }
    }
    
      public function check_existing_book($username){
        
        $username = mysql_real_escape_string($username);
        
        $query_to_check_user = mysql_query("SELECT * FROM book WHERE id_of_book='$username'") or die(mysql_error());
        
        if($query_to_check_user){
            
            if(mysql_num_rows($query_to_check_user) > 0){
               
                while ($row = mysql_fetch_array($query_to_check_user)) {
                    return $row;
                }
            }  else {
                
            
                return NULL;
            }
        }
        
        
    }
    
    
    public function updateBook(){
        
         $book_id = clean($_POST['book_id']);
    
     $book_name = clean($_POST['book_name']);   
     
         $author = clean($_POST['author']);
        $edition = clean($_POST['edition']);
        
          $pages = clean($_POST['pages']);  
        $color = clean($_POST['color']);
        
            $quantity = clean($_POST['quantity']);
            
            
                $subjectt = clean($_POST['subjectt']);
                
                    $category = clean($_POST['category']);
                    
                    
                    mysql_query("UPDATE book SET name_of_book='$book_name',author_of_book='$author',category_of_book='$category',subject_of_book='$subjectt',edition_of_book='$edition',total_pages_of_book='$pages',color_of_book='$color',quantity='$quantity' WHERE id_of_book ='$book_id'") or die(mysql_error());              
        
            redirect_to("books.php");
        
    }
    
    
    public function deleteBook(){
        
        $url =  $_SESSION['deleted_id'];
        
        mysql_query("UPDATE book SET deleted='YES' WHERE id_of_book='$url'") or die(mysql_error());
        
        redirect_to("books.php");
        
    }
    
     public function clearStudent(){
        
        $url =  $_SESSION['clear_id'];
        
        mysql_query("UPDATE tray SET collection_status='NO' WHERE id_of_student='$url'") or die(mysql_error());
        
        redirect_to("library_records.php");
        
    }
    
    
        public function getStudentDetails($url_hash) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM puplis ";
        $query .= "WHERE pupil_id = '{$url_hash}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query) or die(mysql_error());
        confirm_query($query_results);

        if ($user = mysqli_fetch_assoc($query_results)) {
            return $user;
        } else {
            return NULL;
        }
    }
    
    
       public function check_existing_student($username){
        
        $username = mysql_real_escape_string($username);
        
        $query_to_check_user = mysql_query("SELECT * FROM pupils WHERE pupil_id='$username'") or die(mysql_error());
        
        if($query_to_check_user){
            
            if(mysql_num_rows($query_to_check_user) > 0){
               
                while ($row = mysql_fetch_array($query_to_check_user)) {
                    return $row;
                }
            }  else {
                
            
                return NULL;
            }
        }
        
        
    }
    
    
        public function check_student_classname($username){
        
        $username = mysql_real_escape_string($username);
        
        $query_to_check_user = mysql_query("SELECT * FROM class_membership WHERE pupil_id='$username'") or die(mysql_error());
        
        if($query_to_check_user){
            
            if(mysql_num_rows($query_to_check_user) > 0){
               
                while ($row = mysql_fetch_array($query_to_check_user)) {
                    return $row;
                }
            }  else {
                
            
                return NULL;
            }
        }
        
        
    }
    
    
      public function check_student_photo($username){
        
        $username = mysql_real_escape_string($username);
        
        $query_to_check_user = mysql_query("SELECT * FROM photos  WHERE pupil_id='$username' AND deleted = 'NO'") or die(mysql_error());
        
        if($query_to_check_user){
            
            if(mysql_num_rows($query_to_check_user) > 0){
               
                while ($row = mysql_fetch_array($query_to_check_user)) {
                    return $row;
                }
            }  else {
                
            
                return NULL;
            }
        }
        
        
    }
    
         public function getAllTrayRecords(){
        
       // $username = mysql_real_escape_string($username);
             
            $now = date("Y-m-d");
        
        $query_to = mysql_query("SELECT * FROM tray WHERE collection_status='YES'") or die(mysql_error());
        
        if($query_to){
            
            if(mysql_num_rows($query_to) > 0){
               
                while ($row_tray = mysql_fetch_array($query_to)) {
                    return $row_tray;
                }
            } else {
                
            
                return NULL;
            }
        }
        
        
    }
    
           public function getAllTrayLimit50(){
        
       // $username = mysql_real_escape_string($username);
             
            $now = date("Y-m-d");
        
        $query_to = mysql_query("SELECT * FROM tray WHERE collection_status='YES' ORDER BY id DESC LIMIT 2") or die(mysql_error());
        
        if($query_to){
            
            if(mysql_num_rows($query_to) > 0){
               
                while ($row_tray = mysql_fetch_array($query_to)) {
                    return $row_tray;
                }
            } else {
                
            
                return NULL;
            }
        }
        
        
    }
    
    
    
    
    
    public function populate_tray(){
        
        
       
        
        
        
        $_SESSION['std_family_name'] ;
   $_SESSION['std_other_name'] ;
   $_SESSION['std_number'] ;
   $_SESSION['std_class'] ;
   $_SESSION['boarding_status'];
   
   $full_name_std = $_SESSION['std_other_name']." ".$_SESSION['std_family_name'] ;
   $std_no = $_SESSION['std_number'];
   $std_class = $_SESSION['std_class'];
        
        $id_of_book = $_POST['id_of_book'];
        $name_of_book = $_POST['book_name'];
        $author = $_POST['author'];
        
        $quantity_collected = $_POST['quantity_collected'];
        
        
         
        if($id_of_book==""||$name_of_book==""||$author==""){
            
            $this->searchBeforeBanner();
        } else{
        
        
        
        
        mysql_query("INSERT INTO tray(id_of_book,name_of_book,author_of_book,id_of_student,name_of_student,class_of_student,date_collected,time_collected,date_time_collected) VALUES"
                . "('$id_of_book','$name_of_book','$author','$std_no','$full_name_std','$std_class',NOW(),NOW(),NOW())") or die(mysql_error());
        
        
        
        
       $query_book_by_id = $this->check_existing_book($id_of_book);
         
         $query_book_id_quantity = $query_book_by_id['quantity'];
         
         $remain_book_quantity = $query_book_id_quantity - $quantity_collected;
         
         $count = $this->countBookOnTrayById($id_of_book);
         
         $this->updateBookAfterTray($id_of_book, $remain_book_quantity, $count);
         
         
         redirect_to("clear_tray.php");
        } 
         
         
        
        
    }
    
    
    
    public function updateBookAfterTray($id,$quantity,$times){
       
         mysql_query("UPDATE book SET quantity='$quantity',no_of_times_collected='$times' WHERE id_of_book ='$id'") or die(mysql_error());              
        
        
        
    }
    
    
    public  function countBookOnTrayById($id){
        
        
         $query = mysql_query("SELECT COUNT(name_of_book) FROM tray WHERE id_of_book = '$id' LIMIT 1") or die(mysql_error());
   if($query){
       if(mysql_num_rows($query)>0){
           
            while ($row = mysql_fetch_array($query)) {
        $last_user_id =($row["COUNT(name_of_book)"]);
        
          return $last_user_id;
    }
           
       }
  
 }
        
    }
    
    
     public function searchBeforeBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-danger'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>Please Search Details,</strong> Before Saving!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    
    
}
