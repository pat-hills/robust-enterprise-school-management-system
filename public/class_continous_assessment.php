<?php
//require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/Subject.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Mark.php';
include '../includes/config.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$subject_class = new SubjectCombination();
$subjects = new Subject();
$academic_term = new AcademicTerm();
$marks = new Mark();

$get_activated_academic_term = $academic_term->getActivatedTerm();
$get_current_term = $get_activated_academic_term['academic_year']."/".$get_activated_academic_term['term'];

//$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
//$splitAccessPages = explode("//", $getUserDetails["access_pages"]);
//
//for ($i = 0; $i < count($splitAccessPages); $i++) {
//    if (!in_array("class_continous_assessment", $splitAccessPages, TRUE)) {
//        redirect_to("logout_access.php");
//    }
//}

if (!isset($_SESSION["class_list"], $class_name)) {
    $_SESSION["class_list"] = "";
    $class_name = "--Select Class--";
}elseif (!isset ($_SESSION['class_subjects'],$class_subjects)) {
    $_SESSION['class_subjects'] ="";
    $class_subjects = "--Select Subject--";
}

 
?>

<div class="container">

    <?php
    require_once '../includes/breadcrumb_class_menu.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=--Select Class--", "Please, select <strong>CLASS</strong> to view its members.");

        $class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $class_subjects = trim(ucwords(escape_value($_POST["class_subjects"])));
        $_SESSION["class_list"] = $class_name;
        $_SESSION["class_subjects"] = $class_subjects;

        if ($validator->ValidateForm()) {
    //redirect_to("class_menu.php");
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

    $class_membership_set = $classMembership->getClassMembersDetailsByClassName($class_name);
    $class_set = $classes->getClasses();
    $subjectOfClass = $subject_class->getSubjectCombinationByName($_SESSION['class_list']);
     $get_subjects = $subjects->getSubjects();
     
    // $get_assess_by_total = $marks ->getContinuousAssessmentTotals($class_name,$class_subjects,$get_current_term);
    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="center-table">
                        <div class="span8">
                            <div class="span2" style="margin-left: 200px;">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>
                                            <select name="class_name" class="select">
                                                <option value="<?php
                                                if (isset($class_name)) {
                                                    echo $class_name;
                                                } else {
                                                    echo "--Select Class--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($class_name)) {
                                                                echo $class_name;
                                                            } else {
                                                                echo "--Select Class--";
                                                            }
                                                            ?>
                                                </option>
                                                <?php
                                                while ($classes = mysqli_fetch_assoc($class_set)) {
                                                    ?>
                                                    <option value="<?php echo $classes["class_name"]; ?>"><?php echo $classes["class_name"]; ?></option>
                                                    <?php
                                                }
                                                ?> 
                                            </select>
                                        </td>
                                        
                                        <td>
                                            <select name="class_subjects">
                                                <option value="<?php
                                                if (isset($class_subjects)) {
                                                    echo $class_subjects;
                                                } else {
                                                    echo "--Select Subject--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($class_subjects)) {
                                                                echo $class_subjects ;
                                                            } else {
                                                                echo "--Select Subject--";
                                                            }
                                                            ?>
                                                </option>
                                                
                                                 <?php
                                                while ($subject_got = mysqli_fetch_assoc($get_subjects)) {
                                                    ?>
                                                    <option value="<?php echo $subject_got["subject_name"]; ?>"><?php echo $subject_got["subject_name"]; ?></option>
                                                    <?php
                                                }
                                                ?> 
                                                
                                                
                                                
                                            </select>
                                        </td>

                                        <td>
                                            <div class="span2">
                                                <button type="submit" name="submit" class="btn btn-block btn-danger">Show Class Assesstment</button>
                                            </div>
                                        </td>
                                    </tr>
                                    
 
                                    
                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <!--printing buttons-->
                <div class="row">
                    <div class="span12">
                         

                        <a href="print_continuous_assessment.php" target="_blank" class="btn btn-primary" style="margin-left: 20px; margin-top: 10px;">Print 
                            <?php
                            if (isset($_SESSION["class_list"])) {
                                echo $_SESSION["class_list"];
                            }  elseif (isset ($_SESSION["class_subjects"])) {
                                echo $_SESSION["$class_subjects;"];
                            }
                            ?>
                            Continuous Assessment
                        </a>

                        <form class="form-horizontal" method="post">
                            <table class="table table-condensed table-bordered margin-left table-striped">
                                <thead class="table-head-format">
                                    <tr>
                                        <td colspan="6" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($_SESSION["class_list"])) {
                                                echo $_SESSION["class_list"];
                                            } else {
                                                echo "";
                                            }
                                            ?> STUDENTS:: <?php if(isset($class_subjects)) echo $class_subjects; ?> CONTINUOUS ASSESSMENT
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 3%; text-align: center; font-weight: 600;">S/N</td>
                                        <td style="text-align: center; width: 10%; font-weight: 600;">ID NUMBERS</td>
                                        <td style="text-align: center; font-weight: 600; width: 30%;">STUDENTS</td>
                                        <td style="text-align: center; font-weight: 600; width: 5%;">CLASS MARKS(100 MARKS)</td>
<!--                                        <td style="text-align: center; font-weight: 600; width: 5%;">TASK 2</td>-->
<!--                                        <td style="text-align: center; font-weight: 600; width: 5%;">TASK 3</td>-->
<!--                                        <td style="text-align: center; font-weight: 600; width: 5%;">TASK 4</td>-->
                                        <td style="text-align: center; font-weight: 600; width: 5%;">EXAM(100 MARKS)</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                       while ($members = mysql_fetch_array($class_membership_set)){
                                    //while ($class_members = mysqli_fetch_array($get_assess_by_total)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="text-align: center;"><?php 
                                             
                                            //$members_from_clas = $classMembership->getClassMembersDetailsByClassName($class_members["class"]);
                                            
                                            echo $members["pupil_id"]; ?>
                                            
                                            </td>
                                            <td>
                                                <?php
                                                $getNames = $pupil->getPupilById($members["pupil_id"]);
                                                echo $getNames["other_names"] . " " . $getNames["family_name"];
                                                
                                                   $get_score_by_subjects = $marks->getClassMarks($members["pupil_id"], $class_subjects);
                                                ?>
                                            </td>
<!--                                            <td style="text-align: left;">
                                                <?php
                                                //$getSex = $pupil->getPupilById($members["pupil_id"]);
                                                //echo $getSex["sex"];
                                            
                                                //$get_score_by_subjects = $marks->getSubjectScores($class_name, $members["pupil_id"], $class_subjects, $get_current_term);
                                                //echo $get_score_by_subjects["indiv_test"];
                                                ?>
                                            </td>-->
<!--                                            <td>
                                                <?php
                                                //$admissions = $admission->getAdmissionById($members["pupil_id"]);
                                                //echo $admissions["boarding_status"];
                                              //  echo $get_score_by_subjects["project_work"];
                                                ?>
                                            </td>-->
                                            <td><?php 
                                            
                                            
                                                echo $get_score_by_subjects["class_test"];
                                            //echo $members["subject_combination_name"];
                                            
                                            ?>
                                            
                                            
                                            </td>
                                            
<!--                                            <td><?php 
                                            
                                            
                                                //echo $get_score_by_subjects["group_work"];
                                            //echo $members["subject_combination_name"];
                                            
                                            ?>
                                            
                                            
                                            </td>-->
                                            
                                            <td><?php 
                                            
                                            
                                                echo $get_score_by_subjects["exam_work"];
                                            //echo $members["subject_combination_name"];
                                            
                                            ?>
                                            
                                            
                                            </td>
                                            
                                            
                                            
                                            
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php
require_once '../includes/footer.php';
