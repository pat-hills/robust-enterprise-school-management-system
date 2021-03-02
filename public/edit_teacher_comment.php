<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/User.php';
require_once '../classes/FormMaster.php';

confirm_logged_in();

$classes = new Classes();
$user = new User();
$formMaster = new FormMaster();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_teacher_comment", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_edit_comment_1.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "req", "Please, select a <strong>CLASS</strong>.");

        $class_name = trim(escape_value($_POST["class_name"]));
        $_SESSION["class_teacher_members"] = $class_name;

        if ($validator->ValidateForm()) {
            redirect_to("class_teacher_members.php");
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

    $getUser = $user->getUserByUsername($_SESSION["username"]);
    $getTeacherClasses = $classes->getTeacherClasses($getUser["user_id"]);

    $name = $getUser["other_names"] . " " . $getUser["family_name"];
    $getClassTeacher = $formMaster->getClassTeacherByFullName($name);

//    $getClasses = $classes->getClasses();

    if (TRUE == $show_form) {
        ?>
        <div class="spacer"></div>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="row">
                        <div class="center-table">
                            <div class="span4" style="margin-left: 195px;">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>
                                            <select name="class_name">
                                                <option value="">--Select Class--</option>

                                                <?php
//                                                while ($getTeachingClasses = mysqli_fetch_assoc($getTeacherClasses)) {
//                                                    $splitTeachingClasses = explode("-", $getTeachingClasses["class_names"]);
//
//                                                    for ($i = 0; $i < count($splitTeachingClasses); $i++) {
//                                                        
                                                ?>
                                                <!--<option value="//<?php // echo htmlentities($splitTeachingClasses[$i]); ?>"><?php // echo htmlentities($splitTeachingClasses[$i]); ?></option>-->
                                                //<?php
//                                                    }
//                                                }

                                                foreach ($getClassTeacher as $value) {
                                                    ?>
                                                    <option value="<?php echo htmlentities($value["class_name"]); ?>"><?php echo htmlentities($value["class_name"]); ?></option>
                                                    <?php
                                                }
                                                ?> 
                                            </select>
                                        </td>

                                        <td>
                                            <div class="span2">
                                                <button type="submit" name="submit" class="btn btn-block">Next</button>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
                <legend style="color: #4F1ACB;"></legend>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';
