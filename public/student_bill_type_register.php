<?php
include '../classes/BillType.php';
include '../includes/config.php';
include '../classes/InstitutionDetail.php';
//include '../includes/functions.php';


$billType = new BillType();
$institution_detail = new InstitutionDetail();

$get_sch_number = $institution_detail->getInstitutionDetails();


if(isset($_POST['register'])){
    
    
    $bill_type_id = trim(($_POST["bill_type_id"]));
        $bill_type_name = trim(ucwords(($_POST["bill_type_name"])));
        $bill_type_description = trim(ucwords(($_POST["bill_type_description"])));
         $school_number = trim(($_POST["school_number"]));
         
         
         $billType->insertBillType();
    
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
        <form method="post">
        
            
             <input type="text" name="bill_type_name" placeholder="Bill Name"/> </br>
            
            <input type="text" name="bill_type_id" placeholder="Bill Initials"/> </br>
            
             
              
            <textarea  name="bill_type_description" placeholder="Bill Description">   </textarea> </br>
                
                <input type="text" name="school_number" value="<?php echo $get_sch_number['school_number']; ?>" placeholder="School Number"/> </br>
                  
                  
                  <button type="submit" name="register" value="Register">Register</button>
            
       
            
            
            
        </form>
        
        
        <div>
            
            <table>
                <tr>
                    <td>
                        S/N
                    </td>
                    
                    <td>
                        Bill ID
                    </td>
                    
                    <td>
                        Bill Name
                    </td>
                    
                    <td>
                        Bill Description
                    </td>
                    
                    <td>
                       ACTION
                    </td>
                </tr>
                
                <?php
                $a=1;
                $get_all_bill_types = $billType->getBillTypesBySchoolNumber($get_sch_number['school_number']);
                
                while ($row = mysql_fetch_array($get_all_bill_types)) {
                    
                    echo "<tr>";
                    echo "<td>".$a++."</td>";
                    
                    echo "<td>".$row['student_bill_type_id']."</td>";
                    echo "<td>".$row['bill_type_name']."</td>"; 
                    
                     echo "<td>".$row['bill_type_description']."</td>";
                    
                    
                     echo "<td>"."<a  href=edit_bill_type.php?id=$row[0]>"."Edit"."</a>"."|"."<a href=delete_bill_type.php?id=$row[0]>"."Delete"."</a>"."</td>";
                    
               
                    
                    
                    echo "</tr>";
    
                    }
                
                
                
                ?>
                
            </table>
            
            
        </div>
        
    </body>
</html>
