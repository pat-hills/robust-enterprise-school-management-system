<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Bill.php';
require_once '../classes/Payments.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Photo.php';
require_once '../classes/User.php';
require_once '../classes/Pupil.php';
require_once '../classes/Admission.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$bill = new Bill();
$payment = new Payments();
$academicTerm = new AcademicTerm();
$photo = new Photo();
$user = new User();
$pupil = new Pupil();
$admission = new Admission();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("single_student_billing", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($pupil_id, $getStudentClass["boarding_status"], $getStudentClass["class_admitted_to"], $getAllDayBillsPayable["dayBillsPayable"], $getAllBoardingBillsPayable["boardingBillsPayable"])) {
    $pupil_id = "";
    $getStudentClass["boarding_status"] = "";
    $getStudentClass["class_admitted_to"] = "";
    $getAllDayBillsPayable["dayBillsPayable"] = "";
    $getAllBoardingBillsPayable["boardingBillsPayable"] = "";
//    $_SESSION["single_billing_pupil_id"] = "";
}

$getAcademicTerm = $academicTerm->getActivatedTerm();
$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_ledger.php';

    $show_form_2 = TRUE;
    if (isset($_POST["save_fees"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "req", "Please, click <strong>Cancel</strong> and fill in the <strong>Student ID</strong>.");
        $validator->addValidation("amount", "req", "Please, all fields are <strong>required</strong>. Click <strong>Cancel</strong> to refresh the page.");
        $validator->addValidation("amount", "num", "Please, enter <strong>Positive Amount</strong> only. Click <strong>Cancel</strong> to refresh the page.");
        $validator->addValidation("transaction_type", "dontselect=--Select One--", "Please, all fields are <strong>required</strong>. Click <strong>Cancel</strong> to refresh the page.");
        $validator->addValidation("bill_item", "dontselect=--Select One--", "Please, all fields are <strong>required</strong>. Click <strong>Cancel</strong> to refresh the page.");
//        $validator->addValidation("bill_type", "dontselect=select one", "Please, select <strong>Bill Type</strong>.");

        $amount = trim(escape_value($_POST["amount"]));
        $transaction_type = trim(escape_value($_POST["transaction_type"]));
        $bill_item = trim(escape_value($_POST["bill_item"]));

//        $_SESSION["single_billing_amount"] = $amount;

        if ($validator->ValidateForm()) {
            $bill->insertSingleStudentBill();
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

    if (TRUE == $show_form_2) {
        $show_form = TRUE;
        if (isset($_POST["submit"])) {
            $validator = new FormValidator();
            $validator->addValidation("pupil_id", "req", "Please, fill in the <strong>STUDENT ID</strong>.");

            $pupil_id = trim(escape_value($_POST["pupil_id"]));
//            $_SESSION["single_billing_pupil_id"] = $pupil_id;

            $getStudentDetails = $pupil->getPupilById($pupil_id);
            $getStudentClass = $admission->getAdmissionById($pupil_id);

            if ($validator->ValidateForm()) {
//            $bill->insertClassBill();
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

        $institution_set = $institutionDetail->getInstitutionDetails();
        $getBillTypes = $bill->getBillType($institution_set["school_number"]);
        $billItems = $bill->getBillItemBySchoolNumber($institution_set["school_number"]);
        $getStudentPhoto = $photo->getPhotoById($pupil_id);
        ?>
        <div class="row">
            <div class="span12">
                <?php
                if (TRUE == $show_form) {
                    ?>
                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <legend style="color: #4F1ACB;"><strong>Single Student Billing</strong></legend>

                            <div style="margin-left: 200px; margin-top: 20px;">
                                <div class="control-group">
                                    <label class="control-label" for="pupil_id">Student ID</label>
                                    <div class="controls">
                                        <input class="span2" type="text" name="pupil_id" autocomplete="off" autofocus 
                                               value="<?php
                                               if (isset($pupil_id)) {
                                                   echo $pupil_id;
                                               }
                                               ?>">

                                        <button type="submit" name="submit" class="btn">Load Student Ledger</button>
                                        <a href="single_student_billing.php" class="btn large btn-danger">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>

                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <legend style="color: #4F1ACB;"></legend> 

                            <div class="row">
                                <div class="span12">
                                    <table class="table table-condensed">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <label class="control-label" for="amount">Amount</label>
                                                                        <div class="controls">
                                                                            <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>" />
                                                                            <input type="hidden" name="pupil_id" value="<?php echo $pupil_id; ?>" />
                                                                            <input class="span2" type="text" name="amount" autocomplete="off" value="<?php
                                                                            if (isset($amount)) {
                                                                                echo $amount;
                                                                            }
                                                                            ?>" />
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <label class="control-label" for="transaction_type">Transaction Type</label>
                                                                        <div class="controls">
                                                                            <select name="transaction_type" class="select">
                                                                                <option value="--Select One--">
                                                                                    <?php
                                                                                    if (isset($transaction_type)) {
                                                                                        echo $transaction_type . " Entry";
                                                                                    } else {
                                                                                        echo "--Select One--";
                                                                                    }
                                                                                    ?> 
                                                                                </option>
                                                                                <option value="Credit">Credit Entry</option>
                                                                                <option value="Debit">Debit Entry</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <label class="control-label" for="bill_item">Bill Item</label>
                                                                        <div class="controls">
                                                                            <select name="bill_item" class="select">
                                                                                <option value="--Select One--">
                                                                                    <?php
                                                                                    if (isset($bill_item)) {
                                                                                        echo $bill_item;
                                                                                    } else {
                                                                                        echo "--Select One--";
                                                                                    }
                                                                                    ?>
                                                                                </option>
                                                                                <?php
                                                                                foreach ($billItems as $value) {
                                                                                    ?>
                                                                                    <option value="<?php echo htmlentities($value["name"]) ?>"><?php echo htmlentities($value["name"]) ?></option>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="spacer"></div>

                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="save_fees" class="btn" onclick="return confirm('Do you want to EXTRA-BILL this student? If YES, click OK, otherwise click CANCEL')">Save</button>
                                                            <a href="single_student_billing.php" class="btn btn-danger">Cancel</a>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <table class="table table-condensed table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <div class="controls">
                                                                            <img src="<?php echo "../" . $getStudentPhoto["photo_url"]; ?>" width="155">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="student">Student</label>
                                                                    <div class="controls">
                                                                        <input class="span4" type="text" name="student" autocomplete="off" disabled 
                                                                               value="<?php
                                                                               if (isset($getStudentDetails["other_names"], $getStudentDetails["family_name"])) {
                                                                                   echo $getStudentDetails["other_names"] . " " . $getStudentDetails["family_name"];
                                                                               }
                                                                               ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="sex">Sex</label>
                                                                    <div class="controls">
                                                                        <input class="span2" type="text" name="sex" autocomplete="off" disabled 
                                                                               value="<?php
                                                                               if (isset($getStudentDetails["sex"])) {
                                                                                   echo $getStudentDetails["sex"];
                                                                               }
                                                                               ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="class">Class</label>
                                                                    <div class="controls">
                                                                        <input class="span2" type="text" name="class" autocomplete="off" disabled 
                                                                               value="<?php
                                                                               if (isset($getStudentClass["class_admitted_to"])) {
                                                                                   echo $getStudentClass["class_admitted_to"];
                                                                               }
                                                                               ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="boarding_status">Status</label>
                                                                    <div class="controls">
                                                                        <input class="span2" type="text" name="boarding_status" autocomplete="off" disabled
                                                                               value="<?php
                                                                               if (isset($getStudentClass["boarding_status"])) {
                                                                                   echo $getStudentClass["boarding_status"];
                                                                               }
                                                                               ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="fees_paid">Total Fees Paid, GH&cent;</label>
                                                                    <div class="controls">
                                                                        <input class="span2" type="text" name="fees_paid" autocomplete="off" disabled value="<?php
//                                                                        $getAllFeesPaid = $bill->getTotalFeesPaid($pupil_id);
//                                                                        echo number_format($getAllFeesPaid["allFeesPaid"], 2, ".", ",");
                                                                        $getAllFeesPaid = $bill->getTotalFeesPaid($pupil_id);
                                                                        $getPTAFeePaid = $bill->getPTAFeesPaid($pupil_id);
                                                                        echo number_format(($getAllFeesPaid["allFeesPaid"] + $getPTAFeePaid["ptaFeesPaid"]), 2, ".", ",");
                                                                        ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="balance">Balance, GH&cent;</label>
                                                                    <div class="controls">
                                                                        <input class="span2" type="text" name="balance" autocomplete="off" disabled value="<?php
                                                                        $getFeesPayable = $bill->getStudentAccount($pupil_id);
                                                                        $getTotalFeesPaid = $bill->getTotalFeesPaid($pupil_id);
                                                                        $getSingleBillAmount = $bill->getSingleBillingAmount($pupil_id);
                                                                        $singleBillAmount = $getSingleBillAmount["billAmount"];

//                                                                        $getBalance = number_format(($getFeesPayable["allFeesPayable"] - $getTotalFeesPaid["allFeesPaid"]) + $singleBillAmount, 2, ".", ",");
                                                                        $getPTAAll = $bill->getPTAFeesAll($pupil_id);
                                                                        $getPTAFeesPaid = $bill->getPTAFeesPaid($pupil_id);
                                                                        $getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalFeesPaid["allFeesPaid"] + $getPTAFeesPaid["ptaFeesPaid"])), 2, ".", ",");
                                                                        
                                                                        if ($getBalance <= 0) {
                                                                            if ($getBalance == 0) {
                                                                                echo $balance = "-";
                                                                            } else {
                                                                                echo str_replace("-", "", $getBalance) . " Credit";
                                                                            }
                                                                        } else {
                                                                            echo $getBalance . " Debit";
                                                                        }
                                                                        
//                                                                        if ($getBalance <= 0) {
//                                                                            echo str_replace("-", "", $getBalance) . " Credit";
//                                                                        } else {
//                                                                            echo $getBalance . " Debit";
//                                                                        }
                                                                        ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

