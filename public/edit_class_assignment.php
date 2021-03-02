<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/Classes.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Subject.php';
require_once '../classes/TeacherClass.php';

confirm_logged_in();

$classes = new Classes();
$subject = new Subject();
$user = new User();
$teacherClass = new TeacherClass();
$institutionDetail = new InstitutionDetail();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_class_assignment", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $autoID = trim(escape_value(urldecode(getURL())));
    $subr = substr($autoID, 32);
} else {
    redirect_to("class_assignment.php");
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("subject_name", "req", "Please, fill in the Subject name.");

        if ($validator->ValidateForm()) {
            $updateTeacherClass = $teacherClass->updateTeacherClass($subr);
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
    
    $getTeachingClasses = $teacherClass->getTeachingClassesByAutoId($subr);
    
    $institutionSet = $institutionDetail->getInstitutionDetails();
    $getClasses = $classes->getClasses();
    $getSubjects = $subject->getSubjects();
    
    $getUserID = $getTeachingClasses["user_id"];
    $getUserById = $user->getUserById($getUserID);
    
    $getSubjectByTeacherId = $teacherClass->getSubjectByTeacherId($getUserID);
    $getTeacherById = $user->getTeacherById($getUserID);
    $getUsers = $user->getTeachers();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Assigning Subjects and Classes to Teachers</strong></legend>
                        <div class="spacer"></div>
                        <table class="table table-condensed table-margin-bottom">
                            <tr>
                                <td>
                                    <div class="control-group">
                                        <label class="control-label" for="name">Name</label>
                                        <div class="controls">
                                            <input type="text" name="name" value="<?php echo $getTeachingClasses["name"]; ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="subject_name">Subject</label>
                                        <div class="controls">
                                            <select name="subject_name">
                                                <option value="<?php echo $getTeachingClasses["subject_name"]; ?>"><?php echo $getTeachingClasses["subject_name"]; ?></option>
                                                <?php
                                                while ($subjects = mysqli_fetch_assoc($getSubjects)) {
                                                    ?>
                                                    <option value="<?php echo ucwords($subjects["subject_name"]); ?>"><?php echo ucwords($subjects["subject_name"]); ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>

                                <!--table second column starts-->
                                <td>
                                    <div class="row">
                                        <div class="span3">
                                            <table class="table table-bordered table-condensed">
                                                <tbody>
                                                    <tr>
                                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 3%; text-align: center;">S/N</td>
                                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 5%; text-align: center;">Select</td>
                                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">Class Name</td>
                                                    </tr>

                                                    <?php
                                                    $i = 1;
                                                    while ($class = mysqli_fetch_assoc($getClasses)) {
                                                        ?>
                                                        <tr>
                                                            <td style="text-align: center;"><?php echo $i++; ?></td>
                                                            <td style="text-align: center; padding-bottom: 5px;"><input type="checkbox" name="class_names[]" value="<?php echo $class["class_name"]; ?>"></td>
                                                            <td style="text-align: center;"><?php echo $class["class_name"]; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!--table second row ends-->
                            <tr>
                                <td>
                                    <div class="spacer"></div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn">Save</button>
                                            <a href="class_assignment.php" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
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


