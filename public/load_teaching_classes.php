<?php
require_once '../includes/header.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Classes.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/User.php';

confirm_logged_in();

$classMembership = new ClassMembership();
$classes = new Classes();
$subjectCombination = new SubjectCombination();
$user = new User();

if (!isset($_SESSION["teaching_subjects"])) {
    redirect_to("teachers_continuous_assessment.php");
}

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("load_teaching_classes", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_load_teaching_classes.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=Select Class", "Please, select <strong>CLASS</strong>.");

        $class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $_SESSION["class_name"] = $class_name;

        if ($validator->ValidateForm()) {
            redirect_to("continuous_assessment_entries.php");
        } else {
            $get_errors = $validator->GetErrors();

            foreach ($get_errors as $input_field_name => $error_msg) {
                echo "<div class='row'>";
                echo "<div class='span12'>";
                echo "<div class='alert alert-error'>";
                echo "<button type='button' class='close' data-dismiss='alert'></button>";
                echo "<ul type = 'none'>";
                echo "<li>$error_msg</li>";
                echo "</ul>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
    }

//    $getClassMembers = $classMembership->getClassMembersByName($_SESSION["class_name"]);
//    $getSubjectCombinationName = $subjectCombination->getSubjectCombinationByName($getClassMembers["subject_combination_name"]);
    
    $getUser = $user->getUserByUsername($_SESSION["username"]);
    $getTeacherSubjects = $classes->getTeacherSubjects($getUser["user_id"], $_SESSION["teaching_subjects"]);

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
                                                <option value="Select Class">--Select Class--</option>

                                                <?php
                                               while ($getTeachingClasses = mysqli_fetch_assoc($getTeacherSubjects)) {
                                                    $splitTeachingClasses = explode("-", $getTeachingClasses["class_names"]);

                                                    for ($i = 0; $i < count($splitTeachingClasses); $i++) {
                                                        ?>
                                                <option value="<?php echo htmlentities($splitTeachingClasses[$i]); ?>"><?php echo htmlentities($splitTeachingClasses[$i]); ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?> 

                                            </select>
                                        </td>

                                        <td>
                                            <div class="span2">
                                                <button type="submit" name="submit" class="btn btn-block">Show Class Members</button>
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
