<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Bill.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Guardian.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/SMS.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$bill = new Bill();
$academicTerm = new AcademicTerm();
$guardian = new Guardian();
$institutionDetail = new InstitutionDetail();

$getAcademicTerm = $academicTerm->getActivatedTerm();
$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];
//$getNextAcademicTerm = $academicTerm->getNextAcademicTerm();
//$getLastAcademicTerm = $getNextAcademicTerm["academic_year"] . "/" . $getNextAcademicTerm["term"];

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("sms_class_bills", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["sms_class_lists"])) {
    $_SESSION["sms_class_lists"] = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_sms.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=--Select Class--", "Please, select <strong>CLASS</strong> to view its members.");

        $class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $_SESSION["sms_class_lists"] = $class_name;

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

    $show_form_2 = TRUE;
    if (isset($_POST["submit_bulk_bills"])) {
        $validator = new FormValidator();

        $getClassStudents = $classMembership->getClassMembersByClassName($_SESSION["sms_class_lists"]);
        foreach ($getClassStudents as $studentSMS) {
            $institution_set = $institutionDetail->getInstitutionDetails();

            $getSingleBillAmount = $bill->getSingleBillingAmount($studentSMS["pupil_id"]);
            $singleBillAmount = $getSingleBillAmount["billAmount"];

            $getFeesPayable = $bill->getStudentAccount($studentSMS["pupil_id"]);
            $getTotalFeesPaid = $bill->getTotalFeesPaid($studentSMS["pupil_id"]);

            $getTermFees = $bill->getTermFees($studentSMS["pupil_id"], $get_academic_term);
//            $termFees = number_format($getTermFees["termFees"], 2, ".", ",");
            $getPTA = $bill->getPTAFees($studentSMS["pupil_id"], $get_academic_term);
            $termFees = number_format(($getTermFees["termFees"] + $getPTA["ptaFees"]), 2, ".", ",");
//            $getBalanceBF = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount) - ($getTotalFeesPaid["allFeesPaid"] + $termFees)), 2, ".", ",");
            $getPTAAll = $bill->getPTAFeesAll($studentSMS["pupil_id"]);
            $getPTAFeesPaid = $bill->getPTAFeesPaid($studentSMS["pupil_id"]);
            $getBalanceBF = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalFeesPaid["allFeesPaid"] + $getPTAFeesPaid["ptaFeesPaid"])), 2, ".", ",");
//            $totalFees = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount) - $getTotalFeesPaid["allFeesPaid"]), 2, ".", ",");
            $totalFees = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"] + $termFees) - ($getTotalFeesPaid["allFeesPaid"] + $getPTAFeesPaid["ptaFeesPaid"])), 2, ".", ",");
            if ($totalFees <= 0) {
                if ($totalFees == 0) {
                    $balanceCarriedForward = "-";
                } else {
                    $balanceCarriedForward = "GHS" . str_replace("-", "", $totalFees) . " in credit";
                }
            } else {
                $balanceCarriedForward = "GHS" . $totalFees;
            }

            if ($getBalanceBF <= 0) {
                if ($getBalanceBF == 0) {
                    $balanceBF = "-";
                } else {
                    $balanceBF = "GHS" . str_replace("-", "", $getBalanceBF) . " in credit";
                }
            } else {
                $balanceBF = "GHS" . $getBalanceBF . " in arrears";
            }

            $getGuardianDetails = $guardian->getGuardianByPupilId($studentSMS["pupil_id"]);
            $parent = htmlentities($getGuardianDetails["guardian_family_name"]);

            $getStudentData = $pupil->getPupilById($studentSMS["pupil_id"]);
            $student = htmlentities($getStudentData["other_names"] . " " . $getStudentData["family_name"]);

            $phone_number = htmlentities($getGuardianDetails["telephone_1"]);
            $phoneNumberSMS = formatPhoneNumbers($phone_number);

            $smsTagName = $institution_set["sms_tag_name"];
            $message = trim("Dear Sir/Madam, find herewith " . strtoupper($student) . "'s School Fees. BALANCE b/f: " . $balanceBF . "; NEXT TERM FEES: " . "GHS" . $termFees . "; TOTAL FEES: " . $balanceCarriedForward);

            $sms = new SMS("121.241.242.114", "8080", "hap1-utech", "21utech4", $smsTagName, $message, $phoneNumberSMS, "0", "1");

            if ($validator->ValidateForm()) {
//                $response = sendSMS($phone_number, $message);
                $sms->submit();
            }
        }

//        redirect_to("sms_class_bills_on.php");
//        if ($response) {
//            redirect_to("sms_class_bills_on.php");
//        }
    }

    $class_membership_set = $classMembership->getClassMembersByClassName($_SESSION["sms_class_lists"]);
    $class_set = $classes->getClasses();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="center-table">
                        <div class="span8">
                            <div class="span2" style="margin-left: 145px;">
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
                                            <div class="span3">
                                                <button type="submit" name="submit" class="btn btn-block btn-danger">Show Student Bills</button>
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
                                        <td colspan="8" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($_SESSION["sms_class_lists"])) {
                                                echo $_SESSION["sms_class_lists"];
                                            } else {
                                                echo "";
                                            }
                                            ?> STUDENTS' BILL
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="8">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 3%; text-align: center; font-weight: 600;">S/N</td>
                                        <td style="text-align: center; width: 10%; font-weight: 600;">ID NUMBERS</td>
                                        <td style="text-align: center; font-weight: 600;">STUDENTS</td>
                                        <td style="text-align: center; font-weight: 600; width: 5%;">SEX</td>
                                        <td style="text-align: center; font-weight: 600; width: 12%;">STATUS</td>
                                        <td style="text-align: center; font-weight: 600; width: 12%;">BALANCE b/f, GH&cent;</td>
                                        <td style="text-align: center; font-weight: 600; width: 12%;">TERM FEES, GH&cent;</td>
                                        <td style="text-align: center; font-weight: 600; width: 12%;">TOTAL FEES, GH&cent;</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;

                                    while ($members = mysqli_fetch_assoc($class_membership_set)) {
                                        $getSingleBillAmount = $bill->getSingleBillingAmount($members["pupil_id"]);
                                        $singleBillAmount = $getSingleBillAmount["billAmount"];

                                        $getFeesPayable = $bill->getStudentAccount($members["pupil_id"]);
                                        $getTotalFeesPaid = $bill->getTotalFeesPaid($members["pupil_id"]);

                                        $getTermFees = $bill->getTermFees($members["pupil_id"], $get_academic_term);
//                                        $termFees = number_format($getTermFees["termFees"], 2, ".", ",");
                                        $getPTA = $bill->getPTAFees($members["pupil_id"], $get_academic_term);
                                        $termFees = number_format(($getTermFees["termFees"] + $getPTA["ptaFees"]), 2, ".", ",");
//                                        $getBalanceBF = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount) - ($getTotalFeesPaid["allFeesPaid"] + $termFees)), 2, ".", ",");
                                        $getPTAAll = $bill->getPTAFeesAll($members["pupil_id"]);
                                        $getPTAFeesPaid = $bill->getPTAFeesPaid($members["pupil_id"]);
                                        $getBalanceBF = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalFeesPaid["allFeesPaid"] + $getPTAFeesPaid["ptaFeesPaid"])), 2, ".", ",");
//                                        $totalFees = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount) - $getTotalFeesPaid["allFeesPaid"]), 2, ".", ",");
                                        $totalFees = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"] + $termFees) - ($getTotalFeesPaid["allFeesPaid"] + $getPTAFeesPaid["ptaFeesPaid"])), 2, ".", ",");
                                        if ($totalFees <= 0) {
                                            if ($totalFees == 0) {
                                                $balanceCarriedForward = "";
                                            } else {
                                                $balanceCarriedForward = str_replace("-", "", $totalFees) . " CR";
                                            }
                                        } else {
                                            $balanceCarriedForward = $totalFees;
                                        }

                                        if ($getBalanceBF <= 0) {
                                            if ($getBalanceBF == 0) {
                                                $balanceBF = "-";
                                            } else {
                                                $balanceBF = str_replace("-", "", $getBalanceBF) . " CR";
                                            }
                                        } else {
                                            $balanceBF = $getBalanceBF . " DR";
                                        }
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
                                            <td style="text-align: right;"><?php echo $balanceBF; ?></td>
                                            <td style="text-align: right;"><?php echo $termFees; ?></td>
                                            <td style="text-align: right;"><?php echo $balanceCarriedForward; ?></td>

                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </form>

                        <?php
                        if (TRUE == $show_form_2) {
                            ?>
                            <div class="row">
                                <div class="span12">
                                    <form class="form-horizontal" method="post">
                                        <fieldset>
                                            <div class="spacer-min"></div>

                                            <div class="controls">
                                                <button type="submit" name="submit_bulk_bills" class="btn" style="margin-left: 250px;">Send Bills to Parents/Guardians</button>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <legend class="legend"></legend>
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
