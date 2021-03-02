<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Mark.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Admission.php';
require_once '../classes/Comment.php';
require_once '../classes/User.php';
require_once '../classes/SubjectCombination.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$mark = new Mark();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$academicTerm = new AcademicTerm();
$admission = new Admission();
$comment = new Comment();
$user = new User();
$subjectCombination = new SubjectCombination();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("show_terminal_reports", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["pupil_id"])) {
    redirect_to("list_classmembers.php");
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_terminal_1.php';

    $institutionDetail_set = $institutionDetail->getInstitutionDetails();

    $getAcademicTerm = $academicTerm->getActivatedTerm();
    $getAcademicYear = $getAcademicTerm["academic_year"];
    $academicYear = str_replace("/", "", $getAcademicYear);
    $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("remark", "req", "Please, select Remark.");

        if ($validator->ValidateForm()) {
            $comment->insertHeadComment();
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
                <form method="post">

                    <div class="spacer"></div>

                    <?php
                    $getStudentDetails = $pupil->getPupilById($_SESSION["pupil_id"]);
                    $getBoardingStatus = $admission->getAdmissionById($_SESSION["pupil_id"]);
                    $getClassTotal = $classMembership->getClassTotal($getBoardingStatus["class_admitted_to"], $get_academic_term);
                    $getTerminalSettings = $comment->getTerminalSettings($institutionDetail_set["school_number"]);
//                    $getMarks = $mark->getMarksDetails($_SESSION["pupil_id"]);
                    $getHeadRemark = $comment->getHeadRemark($_SESSION["pupil_id"], $institutionDetail_set["school_number"]);

//                    counts total Subjects per class
                    $getSubjectCombinationName = $subjectCombination->getSubjectCombination($getClassTotal["subject_combination_name"]);
                    $countSubjects = explode("**", $getSubjectCombinationName["subject_name"]);
                    $totalSubjectsPerClass = count($countSubjects);

                    $getContinuousAssessmentDetails = $mark->getContinuousAssessmentDetails($_SESSION["pupil_id"]);
                    ?>
                    <div class="row">
                        <div class="span12">
                            <table class="table table-condensed table-bordered">
                                <thead>
                                    <tr>
                                        <td style="width: 50%;">
                                            <table class="table table-condensed table-bordered">
                                                <tr>
                                                    <td style="width: 130px; font-weight: 600; text-align: right;">Student:</td><td><?php echo $getStudentDetails["other_names"] . " " . $getStudentDetails["family_name"]; ?></td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">ID Number:</td><td><?php echo $getStudentDetails["pupil_id"]; ?></td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Status:</td><td><?php echo $getBoardingStatus["boarding_status"]; ?></td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Class:</td><td><?php echo $getBoardingStatus["class_admitted_to"]; ?></td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Gender:</td><td><?php echo $getStudentDetails["sex"]; ?></td>
                                                </tr>
                                            </table>
                                        </td>

                                        <td style="width: 50%;">
                                            <table class="table table-condensed table-bordered">
                                                <tr>
                                                    <td style="font-weight: 600; text-align: right; width: 180px;">Academic Term:</td><td><?php echo str_replace("/", "", str_replace("$getAcademicYear", "", $get_academic_term)); ?></td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Class Average Mark:</td>

                                                    <td>
                                                        <?php
                                                        $getMarksByClass = $mark->getMarksByClass($getBoardingStatus["class_admitted_to"]);
                                                        $classAverage = $getMarksByClass["classAverageMark"];

                                                        echo number_format($classAverage, 0, ".", ",");
                                                        ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Student's Average Mark:</td>

                                                    <td>
                                                        <?php
                                                        $getMark = $mark->getMarks($getStudentDetails["pupil_id"]);
                                                        $getStudentAverageMark = $getMark["totalMarks"] / $totalSubjectsPerClass;

                                                        echo number_format($getStudentAverageMark, 0, ".", ",");
                                                        ?>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Number on Roll:</td><td><?php echo $getClassTotal["classTotal"]; ?></td>
                                                </tr>

                                                <tr>
                                                    <td style="font-weight: 600; text-align: right;">Position in Class:</td>
                                                    <td>
                                                        <?php
                                                        $scores = array();
                                                        $getTotalScores = $mark->getTotalScore($getBoardingStatus["class_admitted_to"], $_SESSION["pupil_id"]);
                                                        while ($row = mysqli_fetch_assoc($getTotalScores)) {
                                                            $scores[] = $row;
                                                            $finalScores = $row["totalScore"];
                                                        }
                                                        
                                                        $getClassPosition = $mark->getClassPositions($getBoardingStatus["class_admitted_to"]);
                                                        foreach ($getClassPosition as $ranking) {
                                                            if ($finalScores === $ranking["totalScore"]) {
                                                                echo ordinal($ranking["classPosition"]);
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">
                                            <table class="table table-condensed table-bordered">
                                                <tr>
                                                    <td style="text-align: center; font-weight: 600;"><br />SUBJECTS</td>
                                                    <td style="text-align: center; width: 10%; font-weight: 600;">CLASS MARKS(<?php echo $getTerminalSettings["class_score_point"]; ?>%)</td>
                                                    <td style="text-align: center; width: 10%; font-weight: 600;">EXAM MARKS(<?php echo $getTerminalSettings["exam_score_point"]; ?>%)</td>
                                                    <td style="text-align: center; width: 10%; font-weight: 600;">TOTAL MARKS(<?php echo ($getTerminalSettings["class_score_point"] + $getTerminalSettings["exam_score_point"]); ?>%)</td>
                                                    <td style="text-align: center; width: 10%; font-weight: 600;">SUBJECT POSITIONS</td>
                                                    <td style="text-align: center; width: 10%; font-weight: 600;"><br />GRADES</td>
                                                    <td style="text-align: center; width: 10%; font-weight: 600;"><br />REMARKS</td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <?php
//                                                        subjects
                                                        foreach ($getContinuousAssessmentDetails as $subject) {
                                                            echo $subject["subject"] . "<br />";
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="text-align: center;">
                                                        <?php
//                                                        class score
                                                        foreach ($getContinuousAssessmentDetails as $classScore) {
                                                            echo number_format($classScore["thirty_percent"], 0, ".", ",") . "<br />";
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="text-align: center;">
                                                        <?php
//                                                        exam score
                                                        foreach ($getContinuousAssessmentDetails as $examScore) {
                                                            echo number_format($examScore["seventy_percent"], 0, ".", ",") . "<br />";
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="text-align: center;">
                                                        <?php
//                                                        total score
                                                        foreach ($getContinuousAssessmentDetails as $totalScore) {
                                                            echo number_format($totalScore["total"], 0, ".", ",") . "<br />";
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="text-align: center;">
                                                        <!--position-->
                                                        <?php
                                                        $getSubjectPositions = $mark->getSubjectPositions($getBoardingStatus["class_admitted_to"], $_SESSION["pupil_id"]);
                                                        foreach ($getSubjectPositions as $subjectPosition) {
                                                            echo ordinal($subjectPosition["position"]) . "<br />";
                                                        }
                                                        ?>
                                                    </td>

                                                    <td style="text-align: center;">
                                                        <?php
//                                                        grade
                                                        foreach ($getContinuousAssessmentDetails as $grade) {
                                                            echo $grade["grade"] . "<br />";
                                                        }
                                                        ?>
                                                    </td>

                                                    <td>
                                                        <!--remarks-->
                                                        <?php
                                                        foreach ($getContinuousAssessmentDetails as $remark) {
                                                            echo $remark["remark"] . "<br />";
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                <input type="hidden" name="school_number" value="<?php echo $institutionDetail_set["school_number"]; ?>">
                                <input type="hidden" name="pupil_id" value="<?php echo $getStudentDetails["pupil_id"]; ?>">

                                <table class="table table-condensed table-bordered">
                                    <tr>
                                        <td style="width: 20%;">HEAD TEACHER'S REMARK</td>

                                        <td>
                                            <select class="span5" name="remark">
                                                <option value="<?php
                                                if (isset($getHeadRemark["remark"])) {
                                                    echo $getHeadRemark["remark"];
                                                } else {
                                                    echo "--Select Remark--";
                                                }
                                                ?>"><?php
                                                            if (isset($getHeadRemark["remark"])) {
                                                                echo $getHeadRemark["remark"];
                                                            } else {
                                                                echo "--Select Remark--";
                                                            }
                                                            ?></option>
                                                <?php
                                                $getRemarks = $comment->getRemarks($institutionDetail_set["school_number"]);
                                                foreach ($getRemarks as $value) {
                                                    ?>
                                                    <option value = "<?php echo $value["comment"]; ?>"><?php echo $value["comment"]; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>

                                                                                                                                                                                    <!--<input type="text" class="span6" name="remark2" placeholder="Please, type REMARK here if you don't want any of the suggested REMARKS." />-->
                                        </td>
                                    </tr>
                                </table>

                                <div class="control-group">
                                    <div class="controls">
                                        <input type="submit" name="submit" style="margin-left: 50%;" value="Save" class="btn btn-primary" />
                                    </div>
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
