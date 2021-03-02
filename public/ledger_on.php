<?php
ob_start();

require_once '../includes/config.php';
//require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Bill.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/Admission.php';
require_once '../classes/Payments.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Photo.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$bill = new Bill();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$payment = new Payments();
$academicTerm = new AcademicTerm();
$photo = new Photo();
$user = new User();

if (!isset($pupil_id, $getStudentClass["boarding_status"], $getStudentClass["class_admitted_to"], $getAllDayBillsPayable["dayBillsPayable"], $getAllBoardingBillsPayable["boardingBillsPayable"], $_SESSION["pupil_id_ledger"])) {
    $pupil_id = "";
    $getStudentClass["boarding_status"] = "";
    $getStudentClass["class_admitted_to"] = "";
    $getAllDayBillsPayable["dayBillsPayable"] = "";
    $getAllBoardingBillsPayable["boardingBillsPayable"] = "";
    $_SESSION["pupil_id_ledger"] = "";
}

$getAcademicTerm = $academicTerm->getActivatedTerm();
$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("ledger_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_ledger.php';

    $payment->saveFeesPaySuccessBanner();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "req", "Please, fill in the <strong>Student ID</strong>.");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $_SESSION["pupil_id_ledger"] = $pupil_id;

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
    ?>
    <div class="row">
        <div class="span12">
            <?php
            if (TRUE == $show_form) {
                ?>
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Student Ledger Details</strong></legend>

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
                                    <a href="ledger.php" class="btn large btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <form class="form-horizontal" method="post">
                    <fieldset>
                        <?php
                        $show_form_2 = TRUE;
                        if (isset($_POST["save_fees"])) {
                            $validator = new FormValidator();
                            $validator->addValidation("pupil_id", "req", "Please, click <strong>Cancel</strong> and fill in the <strong>Student ID</strong>.");
                        $validator->addValidation("amount", "req", "Please, all <strong>fields</strong> are required. Click <strong>Cancel</strong> to refresh the page.");
                           $validator->addValidation("amount", "num", "Please, enter <strong>Positive Amount</strong> only. Click <strong>Cancel</strong> to refresh the page.");
                            $validator->addValidation("fees_paid_by", "req", "Please, all <strong>fields</strong> are required. Click <strong>Cancel</strong> to refresh the page..");
                            $validator->addValidation("mode_of_payment", "dontselect=--Select One--", "Please, select <strong>Mode of Payment</strong>.");

                            $amount = trim(escape_value($_POST["amount"]));
                            $fees_paid_by = trim(ucwords(escape_value($_POST["fees_paid_by"])));
                            $mode_of_payment = trim(escape_value($_POST["mode_of_payment"]));
                            $mode_of_payment_number = trim(escape_value($_POST["mode_of_payment_number"]));

                            if ($validator->ValidateForm()) {
                                $payment->insertFeePayments();
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
                        $getStudentDetails = $pupil->getPupilById($pupil_id);
                        $getStudentClass = $admission->getAdmissionById($pupil_id);

                        if (TRUE == $show_form_2) {
                            ?>
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
                                                                            <input class="span2" type="text" name="amount" autocomplete="off"
                                                                                   value="<?php
                                                                                   if (isset($amount)) {
                                                                                       echo $amount;
                                                                                   }
                                                                                   ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <label class="control-label" for="mode_of_payment">Mode of Payment</label>
                                                                        <div class="controls">
                                                                            <select name="mode_of_payment" class="select">
                                                                                <option value="--Select One--">
                                                                                    <?php
                                                                                    if (isset($mode_of_payment)) {
                                                                                        echo $mode_of_payment;
                                                                                    } else {
                                                                                        echo "--Select One--";
                                                                                    }
                                                                                    ?> 
                                                                                </option>
                                                                                <option value="Bank Draft">Bank Draft</option>
                                                                                <option value="Cash">Cash</option>
                                                                                <option value="Cheque">Cheque</option>
                                                                                <!--<option value="Scholarship">Scholarship</option>-->
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr> 

                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <label class="control-label" for="mode_of_payment_number">Cheque/Draft Number</label>
                                                                        <div class="controls">
                                                                            <input class="span3" type="text" name="mode_of_payment_number" autocomplete="off" value="<?php
                                                                            if (isset($mode_of_payment_number)) {
                                                                                echo $mode_of_payment_number;
                                                                            }
                                                                            ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <label class="control-label" for="fees_paid_by">Fees paid by</label>
                                                                        <div class="controls">
                                                                            <input class="span4" type="text" name="" autocomplete="off"
                                                                                   value="<?php
                                                                                   if (isset($getStudentDetails["other_names"], $getStudentDetails["family_name"])) {
                                                                                       echo $getStudentDetails["other_names"] . " " . $getStudentDetails["family_name"];
                                                                                   } elseif (isset($fees_paid_by)) {
                                                                                       echo $fees_paid_by;
                                                                                   }
                                                                                   ?>" disabled>

                                                                            <input class="span4" type="hidden" name="fees_paid_by" autocomplete="off"
                                                                                   value="<?php
                                                                                   if (isset($getStudentDetails["other_names"], $getStudentDetails["family_name"])) {
                                                                                       echo $getStudentDetails["other_names"] . " " . $getStudentDetails["family_name"];
                                                                                   } elseif (isset($fees_paid_by)) {
                                                                                       echo $fees_paid_by;
                                                                                   }
                                                                                   ?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <button type="submit" name="save_fees" class="btn">Save</button>
                                                            <a href="ledger.php" class="btn btn-danger">Cancel</a>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <a href="print_receipt.php" target="_blank" class="btn btn-primary">Generate Receipt</a>
                                                            <!--<a href="print_student_bill.php" target="_blank" class="btn btn-large btn-info">Generate Bill</a>-->
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <a href="print_payment_history.php" target="_blank" class="btn btn-warning">Generate School Fees Statement</a>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="control-group">
                                                        <div class="controls">
                                                            <a href="print_pta_history.php" target="_blank" class="btn btn-info">Generate PTA Fees Statement</a>
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
                                                                        //$getAllFeesPaid = $bill->getTotalFeesPaid($pupil_id);
                                                                        //$getPTAFeePaid = $bill->getPTAFeesPaid($pupil_id);
                                                                        //echo number_format(($getAllFeesPaid["allFeesPaid"] + $getPTAFeePaid["ptaFeesPaid"]), 2, ".", ",");
                                                                        
                                                                         $get_total_amount_paid = $bill ->getTotalAmountPaid($pupil_id);
                                                                         
                                                                         echo number_format(($get_total_amount_paid["total_amount_paid"]), 2, ".", ",");
                                                                         
                                                                        
                                                                        ?>">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <label class="control-label" for="balance">Balance, GH&cent;</label>
                                                                    <div class="controls">
                                                                        <input class="span2" type="text" name="balance" autocomplete="off" disabled value="<?php
                                                                        
                                                                        $getSingleBillAmount = $bill->getSingleBillingAmount($pupil_id);
                                                                      $singleBillAmount = $getSingleBillAmount["billAmount"];

                                                                        $getFeesPayable = $bill->getStudentAccount($pupil_id);
                                                                        $getFeesPayable["allFeesPayable"];
                                                                        $getPTAAll = $bill->getPTAFeesAll($pupil_id);
                                                                          $getPTAAll["ptaFeesAll"];
                                                                      // $get_credit = $payment ->getCredit($pupil_id); 
                                                                      //$get_credit["accumulatedCredit"];
                                                                     $overall_debit = ($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]);
                                                                        
                                                                        
                                                                        
                                                                     $getTotalFeesPaid = $bill->getTotalFeesPaid($pupil_id);
                                                                    
                                                                       $getPTAFeesPaid = $bill->getPTAFeesPaid($pupil_id);
                                                                       
                                                                       $overall_paid = $getTotalFeesPaid["allFeesPaid"] +  $getPTAFeesPaid["ptaFeesPaid"];
                                                                       
                                                                       
                                                                       
                                                                       $get_balance_paid = $overall_debit - $overall_paid;
                                                                       
                                                                       
                                                                     
                                                                     
                                                                     
                                                                     
                                                                        if($get_balance_paid){
                                                                              if($get_balance_paid==0){
                                                                                  echo $get_balance_paid." -"."";
                                                                          }elseif ($get_balance_paid<0) {
                                                                               echo str_replace("-", "", $get_balance_paid) . " CREDIT";
                                                                            }  else {
                                                                                
                                                                               echo $get_balance_paid."  DEBIT";
                                                                                
                                                                            }
                                                                       } 
                                                                        
                                                                        
                                                                        
                                                                        
                                                                        
                                                                        
                            
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

