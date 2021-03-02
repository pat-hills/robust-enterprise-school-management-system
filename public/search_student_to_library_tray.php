<?php 
require_once '../includes/header.php';
require_once '../classes/User.php';

require_once '../classes/Library.php';

$student_name = "";

$student_class = "";


$student_pic = "";

$family_std = "";

$other_std = "";

$stud_number = "";

$status_std = "";


$std_photo = "";


if(getURL()){
    
    $id = trim(ucwords(escape_value(urldecode(getURL())))); 
    
    
    $lib = new Library();
    
   $get_students = $lib ->check_existing_student($id);
   
   $get_std_number = $lib->check_student_classname($id);
   
   $family_std = $get_students['family_name'];
   
   $other_std = $get_students["other_names"];
   
   $stud_number = $get_std_number["pupil_id"];
    
   $student_class = $get_std_number['class_name'];
   
   $status_std = $get_std_number["boarding_status"];
   
   
   $_SESSION['std_family_name'] = $family_std;
   $_SESSION['std_other_name'] = $other_std;
   $_SESSION['std_number'] = $stud_number;
   $_SESSION['std_class'] = $student_class;
   $_SESSION['boarding_status'] = $status_std;
   
   
   $std_photo = $lib ->check_student_photo($id);
    
    
}








confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("search_student_to_library_tray", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_library_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "req", "Please, fill in the <strong>ID Number</strong> before you click the <strong>FIND</strong> button!");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
                   $_SESSION["pupil_id"] = strtoupper($pupil_id);


        if ($validator->ValidateForm()) {
            redirect_to("display_data.php");
        } else {
            $get_errors = $validator->GetErrors();

            foreach ($get_errors as $input_field_name => $error_msg) {
                echo "<div class='row'>";
                echo "<div class='span12'>";
                echo "<div class='alert alert-error'>";
                echo "<ul type = 'none'>";
                echo "<li>$error_msg</li>";
                echo "</ul>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
    }

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: red;"><strong>Confirm Student Details</strong></legend>
                        <div class="wrapper">
                            <div class="control-group">
                                <label class="control-label" for="pupil_id">Search Student</label>
                                <div class="controls">
                                    <input id="searchh" placeholder="Search Student" class="span2" type="text" name="pupil_id" autocomplete="off" autofocus>
                                    
                                     <div id="resultss">    </div>
            
            <script type="text/javascript" src="js/jquery.js"></script>
            <script type="text/javascript" src="js/searchh.js"></script>
            
                                </div>
                            </div>
                            
                            
                            
                             <div class="control-group">
                                        <label class="control-label" for="author">Student Name</label>
                                        <div class="controls">
                                            <input class="span3" type="text" required="true" name="author" autocomplete="off" value="<?php
                                             echo  $family_std." ".$other_std;
                                            ?>" />
                                        </div>
                                    </div>
                            
                            
                          
                             <div class="control-group">
                                        <label class="control-label" for="author">Index Number</label>
                                        <div class="controls">
                                            <input class="span3" type="text" required="true" name="author" autocomplete="off" value="<?php
                                             echo  $stud_number;
                                            ?>" />
                                        </div>
                                    </div>
                            
                            
                               <div class="control-group">
                                        <label class="control-label" for="author">Class</label>
                                        <div class="controls">
                                            <input class="span3" type="text" required="true" name="author" autocomplete="off" value="<?php
                                             echo  $student_class;
                                            ?>" />
                                        </div>
                                    </div>
                            
                            
                               <div class="control-group">
                                        <label class="control-label" for="author">Status</label>
                                        <div class="controls">
                                            <input class="span3" type="text" required="true" name="author" autocomplete="off" value="<?php
                                             echo  $status_std;
                                            ?>" />
                                        </div>
                                    </div>
                            
                            
                              <div class="control-group" >
                                        <label class="control-label" for="author">Photo</label>
                                        <div class="controls" style="width:150px; height: 150px; background: #bababa;  border-radius: 55px;">
                                            <img style="width:150px; height: 150px; border-radius: 55px;" src="<?php echo $std_photo["photo_url"] ?>" />   
                                        </div>
                                    </div>
                            

                            <div class="control-group">
                                <div class="controls">
                                    <a style="text-decoration: none;" href="library_tray.php" class="btn-large btn-success">Get Book For Student</a>
                                    <a href="search_student_to_library_tray.php" class="btn large btn-danger">Clear</a>
                                </div>
                            </div>
                            
                            
                            
                            
                          
                            
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

