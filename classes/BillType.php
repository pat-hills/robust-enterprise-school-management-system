<?php

 

class BillType {
    
    
    
    
    public function insertBillType(){
        
       
        
        $bill_type_id = trim(($_POST["bill_type_id"]));
        $bill_type_name = trim(ucwords(($_POST["bill_type_name"])));
        $bill_type_description = trim(ucwords(($_POST["bill_type_description"])));
         $school_number = trim(($_POST["school_number"]));
         
         
           $query = "INSERT INTO student_bill_type (";
            $query .= "student_bill_type_id, bill_type_name,bill_type_description,school_number";
            $query .= ") VALUES (";
            $query .= "'{$bill_type_id}', '{$bill_type_name}','{$bill_type_description}','{$school_number}'";
            $query .= ")";

            $query_result = mysql_query($query);
            
            return $query_result;
           // confirm_query($query_result);

//            if ($query_result) {
//                $this->saveBillItemSuccessBanner();
//            }
        
         
        
    }
    
    public function  getBillTypesBySchoolNumber ($school_number){
        
        $query  = "SELECT *";
         $query.= "FROM student_bill_type";
         $query .=" WHERE deleted='NO' ";
         $query.="AND school_number='{$school_number}'";
         $query.="ORDER BY bill_type_name";
         
         $query_result = mysql_query($query);
         return $query_result;
     }
     
     
     public function deleteBillType($bill_id){
         $query = "DELETE";
         $query.="FROM student_bill_type";
         $query.="WHERE student_bill_type_id='{$bill_id}'";
         
         $query_result = mysql_query($query);
         
         return $query_result;
         
         if($query_result){
             header("Location:student_bill_type_register.php");
         }
         
     }
     
     public function updateBillType($bill_type_id){
         
         
         $bill_type_id = trim(($_POST["bill_type_id"]));
        $bill_type_name = trim(ucwords(($_POST["bill_type_name"])));
        $bill_type_description = trim(ucwords(($_POST["bill_type_description"])));
        
         
        
         
         
         $query = "UPDATE";
         $query.="student_bill_type";
         $query.="SET bill_type_name='{$bill_type_name}',bill_type_description='{$bill_type_description}'";
         $query.="WHERE student_bill_type_id='{$bill_type_id}'";
         $query_result = mysql_query($query);
         return $query_result;
         
//         if($query_result){
//             header("Location:student_bill_type_register.php");
//         }
//         
     }
     
     public function getTypeById($id){
         
         $query  = "SELECT *";
         $query.= "FROM student_bill_type";
         $query .=" WHERE deleted='NO' ";
         $query.="AND student_bill_type_id='{$id}'";
        // $query.="ORDER BY bill_type_name";
         
         $query_result = mysql_query($query);
         return $query_result;
         
     }
}

 
?>
