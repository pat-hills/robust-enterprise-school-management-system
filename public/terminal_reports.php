<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Mark.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/User.php';
require_once '../classes/Pupil.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Guardian.php';
require_once '../classes/SMS.php';
 

confirm_logged_in();

$classes = new Classes();
$institutionDetail = new InstitutionDetail();
$mark = new Mark();
$academicTerm = new AcademicTerm();
$subjectCombination = new SubjectCombination();
$user = new User();
$pupil = new Pupil();
$classMembership = new ClassMembership();
$guardian = new Guardian();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);

$splitAccessPages = explode("//", $getUserDetails["access_pages"]);
for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("terminal_reports", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["selected_class_name_results"])) {
    $_SESSION["selected_class_name_results"] = "";
}
?>

<div class="container">
    <?php require_once '../includes/breadcrumb_for_terminal_report.php'; ?>

    <div class="row">
        <div class="span12">
            <legend style="color: #4F1ACB;"><strong>Terminal Reports</strong></legend> 

            <div class="spacer"></div>

            <form class="form-horizontal" method="post">
                <fieldset>
                    <?php
                    $show_form = TRUE;
                    if (isset($_POST["show_results"])) {
                        $validator = new FormValidator();
                        $validator->addValidation("selected_academic_term", "dontselect=select term", "Please, select Academic Term.");
                        $validator->addValidation("selected_class_name", "dontselect=select class", "Please, select Class.");

                        $selected_term = trim(escape_value($_POST["selected_academic_term"]));
                        $_SESSION["selected_academic_term_results"] = $selected_term;

                        $selected_class = trim(escape_value($_POST["selected_class_name"]));
                        $_SESSION["selected_class_name_results"] = $selected_class;

                        if ($validator->ValidateForm()) {
//                                $class = trim(escape_value($_POST["class_name"]));
                        } else {
                            echo "<div class='row'>";
                            echo "<div class='span12'>";
                            echo "<div class='alert alert-error'>";
                            echo "<button type='button' class='close' data-dismiss='alert'></button>";
                            echo "<ul type = 'none'>";
                            echo "<li>Please, select both Academic Term and Class.</li>";
                            echo "</ul>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        $selected_term = "";
                        $selected_class = "";
                    }

                    $institution_set = $institutionDetail->getInstitutionDetails();

                    $getClasses = $classes->getClasses();
                    $academic_terms = $academicTerm->getLastThreeAcademicTerms();

//                    prepare terminal report for sms
                    $show_form_2 = TRUE;
                    if (isset($_POST["sms_exam_results"])) {
                        $validator = new FormValidator();

                        $x = 1;
                        $y = 0;
                        $prev = "";
                        $getClassTotalScores = $mark->getClassTotalScores($_SESSION["selected_class_name_results"], $_SESSION["selected_academic_term_results"]);
                        foreach ($getClassTotalScores as $studentSMS) {
                            if ($prev == $studentSMS["totalScores"]) {
                                $position = $y;
                                $classPosition = ordinal($position);
                            } else {
                                $position = $x;
                                $classPosition = ordinal($position);
                                $y = $x;
                            }
                            $prev = $studentSMS["totalScores"];
                            $x++;

                            $getClassTotal = $classMembership->getClassTotal($_SESSION["selected_class_name_results"], $_SESSION["selected_academic_term_results"]);
                            $getStudentData = $pupil->getPupilById($studentSMS["pupil_id"]);
                            $student = htmlentities($getStudentData["other_names"] . " " . $getStudentData["family_name"]);

                            $getGuardianDetails = $guardian->getGuardianByPupilId($studentSMS["pupil_id"]);
                            $phone_number = htmlentities($getGuardianDetails["telephone_1"]);
                            $phoneNumberSMS = formatPhoneNumbers($phone_number);

                            $getMarksByClass = $mark->getMarksByClass($_SESSION["selected_class_name_results"]);
                            $classAverage = number_format($getMarksByClass["classAverageMark"], 2, ".", ",");

                            $getStatus = $classMembership->getClassMembershipPupilId($studentSMS["pupil_id"]);
                            $getSubjectCombinationName = $subjectCombination->getSubjectCombination($getStatus["subject_combination_name"]);
                            $countSubjects = explode("**", $getSubjectCombinationName["subject_name"]);
                            $totalSubjectsPerClass = count($countSubjects);

                            $getMark = $mark->getMarks($studentSMS["pupil_id"]);
                            $getStudentAverageMark = $getMark["totalMarks"] / $totalSubjectsPerClass;
                            $studentAverageMark = number_format($getStudentAverageMark, 2, ".", ",");

                            $getTotalScoresByStudentID = $mark->getTotalScoresByStudentID($_SESSION["selected_class_name_results"], $_SESSION["selected_academic_term_results"], $studentSMS["pupil_id"]);
                            $studentTotalExamScore = number_format($getTotalScoresByStudentID["totalScores"], 0, ".", ",");
                            $overallScore = $totalSubjectsPerClass * 100;

                            $smsTagName = $institution_set["sms_tag_name"];

                            $message = trim("Dear Sir/Madam, find herewith " . strtoupper($student) . "'s Terminal Report summary. NUMBER ON ROLL: " . $getClassTotal["classTotal"] . "; POSITION IN CLASS: " . $classPosition . "; CLASS AVERAGE MARK: " . $classAverage . "; STUDENT AVERAGE MARK: " . $studentAverageMark . "; TOTAL EXAM SCORE: " . $studentTotalExamScore . " out of " . $overallScore . " marks. Thank you.");
                            $sms = new SMS("121.241.242.114", "8080", "hap1-utech", "21utech4", $smsTagName, $message, $phoneNumberSMS, "0", "1");

                            if ($validator->ValidateForm()) {
                                $sms->submit();
                            }
                        }
                    }

                    if (TRUE == $show_form) {
                        ?>
                        <div class="row">
                            <div class="span12">
                                <table class="table table-bordered table-condensed">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; padding: 10px;" colspan="10">
                                                <select name="selected_academic_term">
                                                    <option value="select term">--Select Academic Term--</option>
                                                    <?php
                                                    foreach ($academic_terms as $term) {
                                                        ?>
                                                        <option value="<?php echo htmlentities($term["academic_year"] . "/" . $term["term"]); ?>"><?php echo htmlentities($term["academic_year"] . "/" . $term["term"]); ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>

                                                <select name="selected_class_name" class="select">
                                                    <option value="select class">--Select Class--</option>
                                                    <?php
                                                    foreach ($getClasses as $class) {
                                                        ?>
                                                        <option value="<?php echo htmlentities($class["class_name"]); ?>"><?php echo htmlentities($class["class_name"]); ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>

                                                <button type="submit" name="show_results" class="btn">Show Exam Results for Students in the selected class</button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">STUDENT ID</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">STUDENTS</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">CLASS</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">SEX</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 12%;">ACADEMIC TERM</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">TOTAL SCORE</td>
                                            <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">POSITION</td>
                                             
                                        </tr>
                                        <?php
                                        $i = 1;
                                        $x = 1;
                                        $y = 0;
                                        $prev = "";
                                        $getClassTotalScores = $mark->getClassTotalScores($selected_class, $selected_term);
                                        while ($getTotalScore = mysqli_fetch_assoc($getClassTotalScores)) {
                                            $getStudentById = $pupil->getPupilById($getTotalScore["pupil_id"]);
                                            ?>
                                            <tr>
                                                <td style="text-align: center;"><?php echo $i++; ?></td>
                                                <td style="text-align: center;"><?php echo $getStudentById["pupil_id"]; ?></td>
                                                <td><?php echo $getStudentById["other_names"] . " " . $getStudentById["family_name"]; ?></td>
                                                <td style="text-align: center;"><?php echo $getTotalScore["class"]; ?></td>
                                                <td style="text-align: left;"><?php echo $getStudentById["sex"]; ?></td>
                                                <td style="text-align: center;"><?php echo $getTotalScore["academic_term"]; ?></td>
                                                <td style="text-align: center;"><?php echo $getTotalScore["totalScores"]; ?></td>
                                                <td style="text-align: center;">
                                                    <?php
                                                    if ($prev == $getTotalScore["totalScores"]) {
                                                        $position = $y;
                                                        echo ordinal($position);
                                                    } else {
                                                        $position = $x;
                                                        echo ordinal($position);
                                                        $y = $x;
                                                    }
                                                    $prev = $getTotalScore["totalScores"];
                                                    $x++;


                                                   ?>
                                                </td>
                                                
                                                
                                                 </tr>
                                                 
                                                     
                                            <?php
                                            
                                           
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
    <?php
    
                                            
}
?>
                </fieldset>
                <a href="print_terminal_reports.php" class="btn btn-primary" target="_blank">Generate Terminal Reports</a>
                <a href="print_broadsheet.php" class="btn btn-info" target="_blank" style="margin-left: 20px;">Generate <strong><?php echo $_SESSION["selected_class_name_results"]; ?> Broadsheet</strong></a>                
                <button type="submit" name="sms_exam_results" class="btn btn-warning" style="margin-left: 20px;">Send Examination Results to Parents/Guardians</button> </br></br></br>
                <a href="class_continous_assessment.php" style="margin-left:0px;" class="btn btn-warning" >CLICK HERE FOR CONTINUOUS ASSESSMENT</a>
                <legend class="legend"></legend>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

