<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/InstitutionDetail.php';
//require_once '../classes/SubjectCombination.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$institutionDetail = new InstitutionDetail();
//$subjectCombination = new SubjectCombination();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("boarding_status", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($class_name)) {
    $class_name = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_boarding_status.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("update_class_boarding_status", "dontselect=Select Class", "Please, select <strong>CLASS</strong> to update Boarding Status of Students.");

        $class_name = trim(ucwords(escape_value($_POST["update_class_boarding_status"])));
//        $_SESSION["previous_class_name"] = $class_name;

        if ($validator->ValidateForm()) {
//            redirect_to("promotion.php");
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

    $class_set = $classes->getClasses();
    $class_membership_set = $classMembership->getClassMembersByClassName($class_name);
    $institution_set = $institutionDetail->getInstitutionDetails();

    if (TRUE == $show_form) {
        ?>

        <div class="spacer"></div>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="center-table">
                        <div class="span8">
                            <div class="span2" style="margin-left: 200px;">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>
                                            <select name="update_class_boarding_status" class="select">
                                                <option value="Select Class">--Select CLASS--</option>
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
                                            <div class="span2">
                                                <button type="submit" name="submit" class="btn btn-block btn-danger">Show Class Members</button>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="span12">
                        <?php
                        if (isset($_POST["submit_boarding_status"]) && !empty($_POST["academic_term"])) {
                            $classMembership->studentsToBeUpdatedBoardingStatus();
                        }
                        ?>
                        <form class="form-horizontal" method="post">
                            <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                            <button type="submit" name="submit_boarding_status" class="btn">Export 
                                <?php
                                if (isset($class_name)) {
                                    echo $class_name;
                                } else {
                                    echo "";
                                }
                                ?> Students to Next Term
                            </button>

                            <a href="boarding_status.php" class="btn btn-danger" style="margin-left: 20px;">Cancel</a>

                            <table class="table table-condensed table-bordered margin-left table-striped">
                                <thead class="table-head-format">
                                    <tr>
                                        <td colspan="7" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($class_name)) {
                                                echo $class_name;
                                            } else {
                                                echo "";
                                            }
                                            ?> STUDENTS
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="7">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 3%; text-align: center; font-weight: 600;">S/N</td>
                                        <td style="text-align: center; width: 10%; font-weight: 600;">ID NUMBERS</td>
                                        <td style="text-align: center; font-weight: 600;">STUDENTS</td>
                                        <td style="text-align: center; font-weight: 600; width: 5%;">SEX</td>
                                        <td style="text-align: center; font-weight: 600; width: 15%;">BOARDING STATUS</td>
    <!--                                        <td style="text-align: center; font-weight: 600; width: 20%;">SUBJECT COMBINATION</td>
                                        <td style="text-align: center; font-weight: 600; width: 5%;">CLASS</td>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;

                                    while ($members = mysqli_fetch_assoc($class_membership_set)) {
//                                        $getClassSubjectCombination = $subjectCombination->getClassSubjectCombination($_SESSION["selected_promoted_class"]);
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo htmlentities($members["pupil_id"]); ?>
                                                <input type="hidden" name="pupil_id[]" value="<?php echo htmlentities($members["pupil_id"]); ?>">
                                                <input type="hidden" name="subject_combination" value="<?php echo htmlentities($members["subject_combination_name"]); ?>">
                                                <input type="hidden" name="next_term_class" value="<?php echo htmlentities($members["class_name"]); ?>">
                                                <input type="hidden" name="academic_term" value="<?php echo $_SESSION["boarding_status_term"]; ?>">
                                            </td>
                                            <td>
                                                <?php
                                                $getNames = $pupil->getPupilById($members["pupil_id"]);
                                                echo $getNames["other_names"] . " " . $getNames["family_name"];
                                                ?>
                                            </td>
                                            <td style="text-align: left;">
                                                <?php
                                                $getSex = $pupil->getPupilById($members["pupil_id"]);
                                                echo $getSex["sex"];
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $admissions = $admission->getAdmissionById($members["pupil_id"]);
                                                echo $admissions["boarding_status"];
                                                ?>
                                            </td>

        <!--                                            <td><?php // echo htmlentities($members["subject_combination_name"]);  ?></td>
                                                        <select name="subject_combination[]">
                                                            <option value="<?php // echo htmlentities($getClassSubjectCombination["subject_combination_name"]);  ?>"><?php // echo htmlentities($getClassSubjectCombination["subject_combination_name"]);  ?></option>
                                                            <option value="<?php // echo htmlentities($members["subject_combination_name"]);  ?>"><?php // echo htmlentities($members["subject_combination_name"]);  ?></option>
                                                        </select>

                                                    <td><?php // echo htmlentities($members["class_name"]);  ?></td>-->
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
