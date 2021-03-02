<?php
require_once '../includes/config.php';
//require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Mark.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$mark = new Mark();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$academicTerm = new AcademicTerm();
$user = new User();

if (!isset($_SESSION["class_name"], $_SESSION["teaching_subjects"])) {
    redirect_to("teachers_continuous_assessment.php");
}

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("continuous_assessment_entries", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_continuous_assessment_entries.php';

    $institutionDetail_set = $institutionDetail->getInstitutionDetails();
    $classMembers = $classMembership->getClassMembersByClassName($_SESSION["class_name"]);

    $getAcademicTerm = $academicTerm->getActivatedTerm();
    $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
//        $validator->addValidation("class_work[]", "req", "Please, fill in all the Exam Scores.");

        if ($validator->ValidateForm()) {
            $mark->replaceContinuousAssessmentMarks();
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

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">

                    <div class="spacer"></div>

                    <p>Subject: <strong><?php echo strtoupper($_SESSION["teaching_subjects"]); ?></strong></p>
                    <table class="table table-condensed table-bordered margin-left table-striped">
                        <thead class="table-head-format">
                            <tr>
                                <td colspan="8" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;"><?php echo $_SESSION["class_name"]; ?> CLASS MEMBERS</td>
                            </tr>

                            <tr>
                                <td style="text-align: center; width: 3%;"><br />S/N</td>
                                <td style="text-align: center; width: 10%;"><br />ID NUMBERS</td>
                                <td style="text-align: center;"><br />STUDENTS</td>
<!--                                <td style="text-align: center; width: 13%">TASK 1<br />INDIVIDUAL TEST 1 (15 marks)</td>
                                <td style="text-align: center; width: 11%">TASK 2<br />INDIVIDUAL PROJECT WORK (15 marks)</td>
                                <td style="text-align: center; width: 13%">TASK 3<br />INDIVIDUAL TEST 2 (15 marks)</td>-->
                                <td style="text-align: center; width: 10%">TASK 4<br />CLASS SCORE (100 MARKS)</td>
                                <td style="text-align: center; width: 15%"><br />END OF TERM EXAM (100 MARKS)</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($classMembers as $classMember) {
                                $getAssessmentMarksById = $mark->getContinuousAssessmentMarksById($classMember["pupil_id"], $_SESSION["teaching_subjects"],$get_academic_term);
                                ?>
                                <tr>
                                    <td style="text-align: center;"><?php echo $i++; ?></td>
                                    <td style="text-align: center;">
                                        <input type="hidden" name="pupil_id[]" value="<?php echo $classMember["pupil_id"]; ?>"><?php echo $classMember["pupil_id"]; ?>
                                        <input type="hidden" name="class" value="<?php echo $_SESSION["class_name"]; ?>" />
                                        <input type="hidden" name="school_number" value="<?php echo $institutionDetail_set["school_number"]; ?>" />
                                        <input type="hidden" name="subject" value="<?php echo $_SESSION["teaching_subjects"]; ?>" />
                                        <input type="hidden" name="academic_term"  value="<?php echo $get_academic_term; ?>" />
                                    </td>
                                    <td>
                                        <?php $pupils = $pupil->getPupilById($classMember["pupil_id"]); ?>
                                        <?php  echo $pupils["other_names"] . " " . $pupils["family_name"]; ?>
<!--                                    <td>
                                        <input class="span1" type="text" name="indiv_test[]" style="margin-left: 38px; margin-bottom: 3px;" autocomplete="off" value="<?php echo $getAssessmentMarksById["indiv_test"]; ?>" autofocus />
                                    </td>

                                    <td>
                                        <input class="span1" type="text" name="project_work[]" style="margin-left: 27px; margin-bottom: 3px;" autocomplete="off" value="<?php echo $getAssessmentMarksById["project_work"]; ?>" />
                                    </td>-->
                                    
                                    <td>
                                        <input class="span1" type="text" name="class_test[]" style="margin-left: 38px; margin-bottom: 3px;" autocomplete="off" value="<?php echo $getAssessmentMarksById["class_test"]; ?>" />
                                    </td>
                                    
<!--                                    <td>
                                        <input class="span1" type="text" name="group_work[]" style="margin-left: 21px; margin-bottom: 3px;" autocomplete="off" value="<?php echo $getAssessmentMarksById["group_work"]; ?>" />
                                    </td>-->


                                    <td>
                                        <input class="span1" type="text" name="exam_work[]" style="margin-left: 45px; margin-bottom: 3px;" autocomplete="off" value="<?php echo $getAssessmentMarksById["exam_work"]; ?>" />
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <input type="submit" name="submit" value="Save" class="btn btn-danger" />
                    <a href="teachers_continuous_assessment.php" class="btn btn-warning" style="margin-left: 20px;">Select another <strong>Subject</strong> to enter marks</a>
                    <a href="load_teaching_classes.php" class="btn btn-info" style="margin-left: 20px;">Select another <strong>Class</strong> to enter marks</a>
                    <div class="spacer"></div>
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
