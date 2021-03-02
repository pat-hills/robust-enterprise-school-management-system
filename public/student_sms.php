<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Guardian.php';
require_once '../classes/AcademicTerm.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$guardian = new Guardian();
$academicTerm = new AcademicTerm();

//$getAcademicTerm = $academicTerm->getActivatedTerm();
//$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("student_sms", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["sms_class_list"])) {
    $_SESSION["sms_class_list"] = "--Select Class--";
//    $sms_class_name = "--Select Class--";
}
?>

<div class="container">

    <?php
    require_once '../includes/breadcrumb_sms.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=--Select Class--", "Please, select <strong>CLASS</strong> to view its members.");

        $sms_class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $_SESSION["sms_class_list"] = $sms_class_name;

        if ($validator->ValidateForm()) {
//            redirect_to("class_menu.php");
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

    $class_membership_set = $classMembership->getClassMembersByClassName($_SESSION["sms_class_list"]);
    $class_set = $classes->getClasses();

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
                                                if (isset($_SESSION["sms_class_list"])) {
                                                    echo $_SESSION["sms_class_list"];
                                                } else {
                                                    echo "--Select Class--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($_SESSION["sms_class_list"])) {
                                                                echo $_SESSION["sms_class_list"];
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

                <!--printing buttons-->
                <div class="row">
                    <div class="span12">
                        <form class="form-horizontal" method="post">
                            <table class="table table-condensed table-bordered margin-left table-striped">
                                <thead class="table-head-format">
                                    <tr>
                                        <td colspan="7" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($_SESSION["sms_class_list"])) {
                                                echo $_SESSION["sms_class_list"];
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
                                        <td style="text-align: center; font-weight: 600; width: 12%;">STATUS</td>
                                        <td style="text-align: center; font-weight: 600;">PARENTS/GUARDIANS</td>
                                        <td style="text-align: center; font-weight: 600; width: 12%;">ACTION</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;

                                    while ($members = mysqli_fetch_assoc($class_membership_set)) {
                                        $getGuardianDetails = $guardian->getGuardianByPupilId($members["pupil_id"]);
                                        $_SESSION["sms_pupil_id"] = $getGuardianDetails["pupil_id"];
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="text-align: center;"><?php echo $members["pupil_id"]; ?></td>
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
                                            <td style="text-align: left;"><?php echo htmlentities($getGuardianDetails["guardian_other_names"] . " " . $getGuardianDetails["guardian_family_name"]); ?></td>
                                            <td><a href="send_sms.php?id=<?php echo urlencode(htmlentities($getNames["unique_url_string"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Type Message</a></td>
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
