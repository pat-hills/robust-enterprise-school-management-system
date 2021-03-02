<?php
include '../classes/BillType.php';
include '../includes/config.php';
include '../classes/InstitutionDetail.php';


$billType = new BillType();

if(isset($_GET['id'])){
    $get_id = $_GET['id'];
    
    $id = $billType->getTypeById($get_id);
    
    while ($row = mysql_fetch_array($id)) {
        
        $bill_id = $row[0];
        $bill_typeName = $row[1];
        $bill_desc = $row[2];
    }
    
    
    if(isset($_POST['update'])){
        
        
        $bill_type_id = trim(($_POST["bill_type_id"]));
        $bill_type_name = trim(ucwords(($_POST["bill_type_name"])));
        $bill_type_description = trim(ucwords(($_POST["bill_type_description"])));
       //  $school_number = trim(($_POST["school_number"]));

        
       
            
             $billType->updateBillType($bill_type_id);    

    
    header("Location:student_bill_type_register.php");
        
    }
    
    
    
    
    
}



?>



<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <form method="post" action="">
        
            
           <input type="text" name="bill_type_name" value="<?php echo $bill_typeName;  ?>" placeholder="Bill Name"/> </br>
            
           <input type="text" name="bill_type_id" value="<?php echo $bill_id; ?>" placeholder="Bill Initials"/> </br>
            
             
              
            <textarea  name="bill_type_description"  placeholder="Bill Description"> <?php echo $bill_desc; ?>  </textarea> </br>
                
                   
                  
                  <button type="submit" name="update" value="Register">UPDATE</button>
            
       
            
            
            
        </form>
    </body>
</html>
