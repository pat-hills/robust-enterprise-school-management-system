<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Classes.php';

confirm_logged_in();

$pupil = new Pupil();
$classMembership = new ClassMembership();
$user = new User();
$classes = new Classes();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_remark", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_edit_remark.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=Select Class", "Please, select <strong>CLASS</strong>.");

        $class_name= trim(escape_value($_POST["class_name"]));
        $_SESSION["class_nam"] = $class_name;

        if ($validator->ValidateForm()) {
            redirect_to("edit_head_teacher_comment.php");
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

//    $getClassMember = $classMembership->editHeadRemarkByClassName($_SESSION["class_nam"]);
//    $getUser = $user->getUserByUsername($_SESSION["username"]);
//    $getTeacherClasses = $classes->getTeacherClasses($getUser["user_id"]);
    $getClasses = $classes->getClasses();

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
                                            <select name="class_name" class="span4">
                                                <option value="Select Class">--Select Class--</option>

                                                <?php
                                                while ($classes = mysqli_fetch_assoc($getClasses)) {
                                                    ?>
                                                    <option value="<?php echo $classes["class_name"]; ?>"><?php echo $classes["class_name"]; ?></option>
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

