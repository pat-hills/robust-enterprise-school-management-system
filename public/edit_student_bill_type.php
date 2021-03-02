<?php
include '../classes/BillType.php';
include '../includes/config.php';
include '../classes/InstitutionDetail.php';

$billType = new BillType();


$bill_type_id = trim(($_POST["bill_type_id"]));
        $bill_type_name = trim(ucwords(($_POST["bill_type_name"])));
        $bill_type_description = trim(ucwords(($_POST["bill_type_description"])));
       //  $school_number = trim(($_POST["school_number"]));

        
       
            
             $billType->updateBillType($bill_type_id);    

    
    header("Location:student_bill_type_register.php");
    
        
        
   

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
