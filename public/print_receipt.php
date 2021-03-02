<?php
 
ob_start();

require_once '../includes/header.php';
require_once '../tcpdf/tcpdf.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/Admission.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';
require_once '../classes/Bill.php';
require_once '../classes/Payments.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/ToWords.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$photo = new Photo();
$bill = new Bill();
$payment = new Payments();
$academicTerm = new AcademicTerm();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("print_receipt", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$getAcademicTerm = $academicTerm->getActivatedTerm();
$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

$getInstitutionDetail = $institutionDetail->getInstitutionDetails();
$getSchool_name = $getInstitutionDetail["school_name"];
$getSchoolMotor = $getInstitutionDetail["school_motor"];
$getPostalAddress = $getInstitutionDetail["postal_address"];
$getTelephoneNumbers = $getInstitutionDetail["telephone_1"] . " " . $getInstitutionDetail["telephone_2"] . " " . $getInstitutionDetail["telephone_3"];

$getTransactionDetails = $payment->getTransactionDetails($_SESSION["pupil_id_ledger"]);
$getPTATransactionDetails = $payment->getPTATransactionDetails($_SESSION["pupil_id_ledger"]);

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
    }

    // Page footer
    public function Footer() {
        $payment = new Payments();
        $getTransactionDetails = $payment->getTransactionDetails($_SESSION["pupil_id_ledger"]);
        $feesReceivedBy = $getTransactionDetails["fees_received_by"];
        // Position at 15 mm from bottom
        $this->SetY(-34);
        // Set font
        $this->SetFont('helvetica', '', 11);
        // Page number
//        date_default_timezone_set("Africa/Accra");
//        $this->Cell(0, 10, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
//        $takeNote = "<strong><u>FOOTNOTES:</u></strong>";
        $horizontalLine = "<hr />";
        $space = "<p></p>";
        $msg = "<strong>Received by:</strong> " . $feesReceivedBy;
        $sign = "<strong>Signature:</strong> ...................................................";
        $company = "<strong><em>Software by ANIDROL GHANA: +233 26-764-2898 / +233 24-774-5156</em></strong>";

        $this->writeHTML($horizontalLine, true, 0, true, 0);
//        $this->writeHTML($takeNote, true, 0, true, 0);
//        $this->writeHTML($space, true, 0, true, 0);
        $this->writeHTML($msg, true, 0, true, 0);
        $this->writeHTML($space, true, 0, true, 0);
        $this->writeHTML($space, true, 0, true, 0);
        $this->writeHTML($sign, true, 0, true, 0);
        $this->writeHTML($company, true, 0, true, 0, 'R');
    }

}

$pdf = new MYPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . "/lang/eng.php")) {
    require_once(dirname(__FILE__) . "/lang/eng.php");
    $pdf->setLanguageArray($l);
}

// set font
$pdf->SetFont('times', '', 18.5);

// add a page
$pdf->AddPage();
$pdf->setJPEGQuality(75);

//$getTransactionDetails = $payment->getTransactionDetails($_SESSION["pupil_id_ledger"]);

$iD = $getTransactionDetails["receipt_no"];
if (empty($iD)) {
    $transactionID = "____________________";
} else {
    $transactionID = $getTransactionDetails["receipt_no"];
}

$transaction_date = date("d-m-Y", strtotime($getTransactionDetails["date"]));
if ($transaction_date === "01-01-1970") {
    $transactionDate = "____________________";
} else {
    $transactionDate = date("d-m-Y", strtotime($transaction_date));
}

$time = $getTransactionDetails["time"];
if (empty($time)) {
    $transactionTime = "____________________";
} else {
    $transactionTime = $getTransactionDetails["time"];
}

$feesPaidBy = $getTransactionDetails["fees_paid_by"];

$chequeDraftNumber = $getTransactionDetails["mode_of_payment_number"];
if (empty($chequeDraftNumber)) {
    $number = "________________";
} else {
    $number = $chequeDraftNumber . " ";
}

$transactionAmount = $getTransactionDetails["amount"];
$lastPTAAmount = $getPTATransactionDetails["amount"];
$feesPlusPTA = $transactionAmount + $lastPTAAmount;
$toWords = new ToWords($transactionAmount);
//$toWords = new ToWords($feesPlusPTA);

$getClassStudentById = $classMembership->getClassMembershipById($_SESSION["pupil_id_ledger"]);
$getStudentData = $pupil->getPupilById($_SESSION["pupil_id_ledger"]);

$getStudentName = $getStudentData["other_names"] . " " . $getStudentData["family_name"];
//if (empty($getStudentName)) {
//    $studentName = "____________________";
//} else {
//    $studentName = $getStudentName;
//}

$studentID = $getStudentData["pupil_id"];
if (empty($studentID)) {
    $getStudentID = "____________________";
} else {
    $getStudentID = $getStudentData["pupil_id"];
}

$className = $getClassStudentById["class_name"];
if (empty($className)) {
    $getClassName = "____________________";
} else {
    $getClassName = $getClassStudentById["class_name"];
}

//    $getGender = $getStudentData["sex"];
//    $getBoardingStatus = $value["boarding_status"];

$getSingleBillAmount = $bill->getSingleBillingAmount($_SESSION["pupil_id_ledger"]);
$singleBillAmount = $getSingleBillAmount["billAmount"];

$getFeesPayable = $bill->getStudentAccount($_SESSION["pupil_id_ledger"]);
$getTotalFeesPaid = $bill->getTotalFeesPaid($_SESSION["pupil_id_ledger"]);
//$getBalance = number_format((($getFeesPayable["allFeesPayable"] - $getTotalFeesPaid["allFeesPaid"]) + $singleBillAmount), 2, ".", ",");
//$getPTAAll = $bill->getPTAFeesAll($_SESSION["pupil_id_ledger"]);
//$getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalFeesPaid["allFeesPaid"])), 2, ".", ",");
$getPTAAll = $bill->getPTAFeesAll($_SESSION["pupil_id_ledger"]);
$getPTAFeesPaid = $bill->getPTAFeesPaid($_SESSION["pupil_id_ledger"]);
$getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalFeesPaid["allFeesPaid"] + $getPTAFeesPaid["ptaFeesPaid"])), 2, ".", ",");

if ($getBalance <= 0) {
    if ($getBalance == 0) {
        $fees_dued = "_______________";
    } else {
        $fees_dued = "GH&cent;" . str_replace("-", "", $getBalance) . " Credit";
    }
} else {
    $fees_dued = "GH&cent;" . $getBalance;
}

//if ($getBalance <= 0) {
//    $fees_dued = str_replace("-", "", $getBalance) . " credit";
//}  else {
//    $fees_dued = $getBalance;
//} 
//    table
$getStudentPhoto = $photo->getPhotoById($_SESSION["pupil_id_ledger"]);
$studentPhoto = "../" . $getStudentPhoto["photo_url"];

$getLogoID = $photo->getSchoolLogoID();
$getSchoolLogo = $photo->getSchoolLogo($getLogoID["logo_id"]);
$schoolLogo = "../" . $getSchoolLogo["photo_url"];

$heading = "<span>GHANA EDUCATION SERVICE</span>"
        . "<h1>" . $getSchool_name . "</h1>"
        . "<span>" . $getPostalAddress . "</span><br />"
        . "<span>" . $getTelephoneNumbers . "</span>";

$pdf->Image($studentPhoto, 243, 7, 49, 52, '', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->Image($schoolLogo, 5, 7, 45, 52, '', '', '', true, 150, '', false, false, 0, false, false, false);

$tbl_head_heading = '<table cellspacing = "0" cellpadding = "5">';
$tbl_foot_heading = '</table>';
$tbl_heading = '';

$tbl_heading .= '
    <tbody>
      <tr>
         <td style="text-align: center; background-color: #666; color: #fff;" colspan="4">' . $heading . '</td>
      </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_heading . $tbl_heading . $tbl_foot_heading, true, false, true, false, '');

$schoolFeesReceipt = "<strong>" . "SCHOOL FEES RECEIPT" . "</strong>";
$pdf->writeHTML($schoolFeesReceipt, true, 0, true, 0, 'C');

$className = "Class: <strong>" . $getClassName . "</strong>";
$pdf->writeHTML($className, true, 0, true, 0, 'R');

$paymentDate = "Date: <strong>" . $transactionDate . "</strong>";
$pdf->writeHTML($paymentDate, true, 0, true, 0, 'R');

$paymentDate = "Time: <strong>" . $transactionTime . "</strong>";
$pdf->writeHTML($paymentDate, true, 0, true, 0, 'R');

$receiptNumber = "Transaction ID: <strong>" . $transactionID . "</strong>";
$pdf->writeHTML($receiptNumber, true, 0, true, 0, 'R');

$studentID = "Student ID: <strong>" . $getStudentID . "</strong>";
$pdf->writeHTML($studentID, true, 0, true, 0, 'R');

$tbl_head_title = '<table cellspacing = "0" cellpadding = "2" border = "0">';
$tbl_foot_title = '</table>';
$tbl_title = '';

$tbl_title .= '
    <tbody>
      <tr style="color: #000;">
         <!--<td style="text-align: right; width: 10%;">&nbsp;</td>-->
         <td style="text-align: left; width: 90%;">' . "<strong>Received from</strong> " . $getStudentName . '</td>
         <td style="text-align: right; width: 10%;">&nbsp;</td>
      </tr>
      
      <tr style="color: #000;">
         <!--<td style="text-align: right; width: 10%;">&nbsp;</td>-->
         <td style="text-align: left; width: 90%;">' . "<strong>The sum of</strong>" . $toWords->words . '</td>
         <td style="text-align: right; width: 10%;">&nbsp;</td>
      </tr>
      
      <tr style="color: #000;">
         <!--<td style="text-align: right; width: 10%;">&nbsp;</td>-->
         <td style="text-align: left; width: 90%;">' . "<strong>Being</strong> part/full payment for school fees" . '</td>
         <td style="text-align: right; width: 10%;">&nbsp;</td>
      </tr>
      
      <tr style="color: #000;">
         <!--<td style="text-align: right; width: 10%;">&nbsp;</td>-->
         <td style="text-align: left; width: 90%;">' . "<strong>Cheque/Draft Number:</strong> " . $number . "<strong> Balance: </strong>" . $fees_dued . '</td>
         <td style="text-align: right; width: 10%;">&nbsp;</td>
      </tr>
      
      <tr style="color: #000;">
         <!--<td style="text-align: right; width: 10%;">&nbsp;</td>-->
         <td style="text-align: left; width: 90%;">' . "<strong>Amount Paid:</strong> GH&cent;" . number_format($transactionAmount, 2, ".", ",") . '</td>
         <td style="text-align: right; width: 10%;">&nbsp;</td>
      </tr>
      
      <tr style="color: #000;">
         <!--<td style="text-align: right; width: 10%;">&nbsp;</td>-->
         <td style="text-align: center; width: 90%;">' . "<em>Thank you!</em>" . '</td>
         <td style="text-align: right; width: 10%;">&nbsp;</td>
      </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





