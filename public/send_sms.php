<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Guardian.php';
require_once '../classes/Pupil.php';
require_once '../classes/Admission.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/SMS.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$user = new User();
$classMembership = new ClassMembership();
$guardian = new Guardian();
$pupil = new Pupil();
$admission = new Admission();
$academicTerm = new AcademicTerm();

//$getAcademicTerm = $academicTerm->getActivatedTerm();
//$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("send_sms", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $getUniqueUrlString = trim(escape_value(urldecode(getURL())));
    $getStudentID = $pupil->getPupilByUniqeUrlString($getUniqueUrlString);
} else {
    redirect_to("student_sms.php");
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_send_sms.php';

    $institution_set = $institutionDetail->getInstitutionDetails();
    $class_membership_set = $classMembership->getClassMembersByClassName($_SESSION["sms_class_list"]);
    $getGuardianDetails = $guardian->getGuardianByPupilId($getStudentID["pupil_id"]);
    $smsTagName = $institution_set["sms_tag_name"];

    $show_form = TRUE;
    if (isset($_POST["submit"]) && !empty($_POST["message"])) {
        $validator = new FormValidator();
        $validator->addValidation("parent", "req", "Please, fill in the parent's/guardian's name.");
        $validator->addValidation("message", "req", "Please, type your message.");

        $parent = trim(escape_value($_POST["parent"]));

        $phone_number = trim(escape_value($_POST["phone_number"]));
        $phoneNumberSMS = formatPhoneNumbers($phone_number);

        $message = trim($_POST["message"]);
        $sms = new SMS("121.241.242.114", "8080", "hap1-multy18", "abcd12", $smsTagName, $message, $phoneNumberSMS, "0", "1");

        if ($validator->ValidateForm()) {
//            $response = sendSMS($phone_number, $message);
            $sms->submit();
        }

//        redirect_to("send_sms_on.php");

//        if ($response) {
//            redirect_to("send_sms_on.php");
//        }
    }

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>SMS Platform</strong></legend>                        

                        <input type="hidden" name="phone_number" value="<?php echo htmlentities($getGuardianDetails["telephone_1"]); ?>">

                        <div class="control-group">
                            <label class="control-label" for="parent">Parent/Guardian</label>
                            <div class="controls">
                                <input class="span4" type="hidden" name="parent" value="<?php echo htmlentities(htmlentities($getGuardianDetails["guardian_family_name"])); ?>" />
                                <input class="span4" type="text" value="<?php echo htmlentities(htmlentities($getGuardianDetails["guardian_other_names"] . " " . $getGuardianDetails["guardian_family_name"])); ?>" disabled />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="message">Message</label>
                            <div class="controls">
                                <textarea rows="3" class="span4" maxlength="320" autofocus name="message"></textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Send SMS</button>
                                <a href="student_sms.php" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="spacer"></div>

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
                                        <td style="text-align: center; font-weight: 600;">PARENT/GUARDIAN</td>
                                        <td style="text-align: center; font-weight: 600; width: 12%;">ACTION</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;

                                    while ($members = mysqli_fetch_assoc($class_membership_set)) {
                                        $getGuardianDetails = $guardian->getGuardianByPupilId($members["pupil_id"]);
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
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

