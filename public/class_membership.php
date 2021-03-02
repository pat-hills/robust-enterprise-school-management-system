<?php
require_once '../includes/header.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Subject.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$subject = new Subject();
$subjectCombination = new SubjectCombination();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("class_membership", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["pupil_id"], $_SESSION["class_admitted_to"])) {
    $_SESSION["pupil_id"] = "";
    $_SESSION["class_admitted_to"] = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("subject_combination_name", "dontselect=--Select One--", "Please, select Subject Combination.");

        $subject_combination_name = trim(ucwords(escape_value($_POST["subject_combination_name"])));

        if ($validator->ValidateForm()) {
            $classMembership->updateClassMembersDetails();
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

    $institution_set = $institutionDetail->getInstitutionDetails();
    $subject_set = $subject->getSubjectsBySchoolNumber($institution_set["school_number"]);
    $subject_combination_set = $subjectCombination->getSubjectCombinationBySchoolNumber($institution_set["school_number"]);
    $getStudentId = $classMembership->getClassMembershipById($_SESSION["pupil_id"]);

    if (TRUE == $show_form) {
        ?>

        <div class="alert alert-info">
            <?php
            echo "<strong>STEP 4 of 4:</strong>";
            echo "<ul type='square'>";
            echo "<li>Select <strong>SUBJECT COMBINATION</strong> in the form below.</li>";
            echo "</ul>";
            ?>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Admit Student to a class</strong></legend>    

                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2" type="text" value="<?php echo $_SESSION["pupil_id"]; ?>" disabled>
                                <input class="span2" type="hidden" name="pupil_id" value="<?php echo $_SESSION["pupil_id"]; ?>">
                            </div>
                        </div>

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"] ?>">

                        <div class="control-group">
                            <label class="control-label" for="class_name">Class</label>
                            <div class="controls">
                                <input class="span1" type="text" value="<?php echo $_SESSION["class_admitted_to"]; ?>" disabled>
                                <input class="span1" type="hidden" name="class_name" value="<?php echo $_SESSION["class_admitted_to"]; ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="subject_combination_name">Subject Combination</label>
                            <div class="controls">
                                <select name="subject_combination_name" class="select">
                                    <option value="<?php
                                    if (isset($subject_combination_name)) {
                                        echo $subject_combination_name;
                                    } else {
                                        echo "--Select One--";
                                    }
                                    ?>">
                                                <?php
                                                if (isset($subject_combination_name)) {
                                                    echo $subject_combination_name;
                                                } else {
                                                    echo "--Select One--";
                                                }
                                                ?>
                                    </option>
                                    <?php
                                    while ($subject_combinations = mysqli_fetch_assoc($subject_combination_set)) {
                                        ?>
                                        <option value="<?php echo $subject_combinations["subject_combination_name"]; ?>"><?php echo $subject_combinations["subject_combination_name"]; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" id="submit" class="btn">Save</button>
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

