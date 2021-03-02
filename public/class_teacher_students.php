<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/User.php';

confirm_logged_in();

$pupil = new Pupil();
$classMembership = new ClassMembership();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("class_teacher_students", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["class_teacher_students"])) {
    redirect_to("teacher_comment.php");
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_list_student_1.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "dontselect=Select Student", "Please, select <strong>STUDENT</strong>.");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $_SESSION["pupil_id"] = $pupil_id;

        if ($validator->ValidateForm()) {
            redirect_to("teacher_terminal_report.php");
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

    $getClassMember = $classMembership->checkCommentByClassName($_SESSION["class_teacher_students"]);

    if (TRUE == $show_form) {
        ?>
        <div class="spacer"></div>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="row">
                        <div class="center-table">
                            <div class="span4" style="margin-left: 130px;">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>
                                            <select name="pupil_id" class="span4">
                                                <option value="Select Student">--Select Student--</option>

                                                <?php
                                                while ($members = mysqli_fetch_assoc($getClassMember)) {
                                                    $getStudentDetails = $pupil->getPupilById($members["pupil_id"]);
                                                    ?>
                                                    <option value="<?php echo htmlentities($members["pupil_id"]); ?>"><?php echo htmlentities($getStudentDetails["pupil_id"] . " -- " . $getStudentDetails["other_names"] . " " . $getStudentDetails["family_name"]); ?></option>
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

