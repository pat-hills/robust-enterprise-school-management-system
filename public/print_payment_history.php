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
    if (!in_array("print_payment_history", $splitAccessPages, TRUE)) {
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



 $get_class_name_by_id_an_current_term_at_pay_his = $classMembership->getClassNameByStudentIdAndCurrentTerm($_SESSION["pupil_id_ledger"], $get_academic_term);
                        

 $get_class_at_his_pay = $get_class_name_by_id_an_current_term_at_pay_his['class_name'];
 
 $get_boarding_sta_at_his_pay = $get_class_name_by_id_an_current_term_at_pay_his['boarding_status'];
 
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
    }

    // Page footer
    public function Footer() {
        $space = "<p></p>";
        // Position at 15 mm from bottom
        $this->SetY(-8);
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number
//        date_default_timezone_set("Africa/Accra");
//        $this->Cell(0, 0, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        $company = "<strong><em>Software by ANIDROL GHANA: +233 26-764-2898 / +233 24-774-5156</em></strong>";

        $this->writeHTML($company, true, 0, true, 0, 'R');
    }

}

$pdf = new MYPDF("", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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
$pdf->SetFont('times', '', 11);

// add a page
$pdf->AddPage();
$pdf->setJPEGQuality(75);

$getClassStudentById = $classMembership->getClassStudentById($_SESSION["pupil_id_ledger"]);
foreach ($getClassStudentById as $value) {
//    table
    $getSingleBillAmount = $bill->getSingleBillingAmount($value["pupil_id"]);
    $singleBillAmount = $getSingleBillAmount["billAmount"];

    $getTotalFeesPayable = $bill->getTotalFeesPayable($value["pupil_id"]);
    $getPTAAll = $bill->getPTAFeesAll($value["pupil_id"]);
    $feesPayable = "GH&cent;" . number_format(($getTotalFeesPayable["feesPayable"] + $getSingleBillAmount["billAmount"] + $getPTAAll["ptaFeesAll"]), 2, ".", ",");

    $getTotalPayments = $bill->getTotalPayments($value["pupil_id"]);
//    $payments = "GH&cent;" . number_format($getTotalPayments["totalPayments"], 2, ".", ",");
    $getAllFeesPaid = $bill->getTotalFeesPaid($value["pupil_id"]);
    $getPTAFeePaid = $bill->getPTAFeesPaid($value["pupil_id"]);
    $pay_ments_paid = $bill ->getTotalAmountPaid($value["pupil_id"]);
    
    $pay_ments_paid_format = "GH&cent;" . number_format(($pay_ments_paid["total_amount_paid"]),2,".",",");
   // $payments = "GH&cent;" . number_format(($getAllFeesPaid["allFeesPaid"] + $getPTAFeePaid["ptaFeesPaid"]), 2, ".", ",");
//    $getTermFees = $bill->getTermFees($value["pupil_id"], $get_academic_term);
//    $termFees = number_format($getTermFees["termFees"], 2, ".", ",");

    $getFeesPayable = $bill->getStudentAccount($value["pupil_id"]);
    $getTotalFeesPaid = $bill->getTotalFeesPaid($value["pupil_id"]);

//    $getBalance = number_format(($getTotalFeesPayable["feesPayable"] + $getSingleBillAmount["billAmount"]) - $getTotalPayments["totalPayments"], 2, ".", ",");   
//    $getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount) - ($getTotalFeesPaid["allFeesPaid"])), 2, ".", ",");
//    $getPTAAll = $bill->getPTAFeesAll($value["pupil_id"]);
    $getPTAFeesPaid = $bill->getPTAFeesPaid($value["pupil_id"]);
    $getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($pay_ments_paid["total_amount_paid"])), 2, ".", ",");

    if ($getBalance <= 0) {
        if ($getBalance == 0) {
            $balanceBroughtForward = "-";
        } else {
            $balanceBroughtForward = str_replace("-", "", $getBalance) . " Credit";
        }
    } else {
        $balanceBroughtForward = $getBalance . " Debit";
    }

//    if ($getBalance <= 0) {
//        $balanceBroughtForward = "GH&cent;" . str_replace("-", "", $getBalance) . " credit";
//    } else {
//        $balanceBroughtForward = "GH&cent;" . $getBalance . " Debit";
//    }

    $getStudentPhoto = $photo->getPhotoById($value["pupil_id"]);
    $studentPhoto = "../" . $getStudentPhoto["photo_url"];

    $getLogoID = $photo->getSchoolLogoID();
    $getSchoolLogo = $photo->getSchoolLogo($getLogoID["logo_id"]);
    $schoolLogo = "../" . $getSchoolLogo["photo_url"];

    $heading = "<span>GHANA EDUCATION SERVICE</span>"
            . "<h1>" . $getSchool_name . "</h1>"
            . "<span>" . $getPostalAddress . "</span><br />"
            . "<span>" . $getTelephoneNumbers . "</span>";

    $pdf->Image($studentPhoto, 155, 60, 35, 35, '', '', '', true, 150, '', false, false, 0, false, false, false);
    $pdf->Image($schoolLogo, 5, 7, 33, 34.5, '', '', '', true, 150, '', false, false, 0, false, false, false);

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

    $pageTitle = "<strong>" . "STATEMENT OF SCHOOL FEES PAYMENT AS AT " . strtoupper(date("F j, Y")) . "</strong>";
    $pdf->writeHTML($pageTitle, true, 0, true, 0, 'C');

    $pdf->writeHTML($space, true, 0, true, 0);

    $horizontalLine = "<hr />";

    $getStudentData = $pupil->getPupilById($value["pupil_id"]);
    $getStudentName = $getStudentData["other_names"] . " " . $getStudentData["family_name"];
    $getStudentID = $getStudentData["pupil_id"];
    $getGender = $getStudentData["sex"];
    $getBoardingStatus = $value["boarding_status"];
    $getClassName = $value["class_name"];

    $tbl_head_title = '<table cellspacing = "0" cellpadding = "0" border = "0">';
    $tbl_foot_title = '</table>';
    $tbl_title = '';

    $tbl_title .= '
    <tbody>
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Name:</strong></td>
         <td style="text-align: left;">' . $getStudentName . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>ID Number:</strong></td>
         <td style="text-align: left;">' . $getStudentID . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Gender:</strong></td>
         <td style="text-align: left;">' . $getGender . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Status:</strong></td>
         <td style="text-align: left;">' . $get_class_at_his_pay . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Class:</strong></td>
         <td style="text-align: left;">' . $get_boarding_sta_at_his_pay . '</td>
      </tr>
      
    <!--
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Term Fees:</strong></td>
         <td style="text-align: left;">' . $termFees . '</td>
      </tr>
    -->
    
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Total Fees Payable:</strong></td>
         <td style="text-align: left;">' . $feesPayable . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Total Fees Paid:</strong></td>
         <td style="text-align: left;">' . $pay_ments_paid_format . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Balance:</strong></td>
         <td style="text-align: left;">' . $balanceBroughtForward . '</td>
      </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

    $pdf->writeHTML($horizontalLine, true, 0, true, 0);

//    list of bill items
    $tbl_head_description = '<table cellspacing = "0" cellpadding = "0" border = "0">';
    $tbl_foot_description = '</table>';
    $tbl_description = '';

    $tbl_description .= '
        <tbody>
          <tr style="color: #000;">
             <td style="width: %5;">' . "#" . '</td>
             <td style="width: 12%;"><strong>Date</strong></td>
             <td style="width: 11%;"><strong>Time</strong></td>
             <td style="width: 18%;"><strong>Term</strong></td>
             <td style="width: 12%;">' . "<strong>Mode</strong>" . '</td>
             <td style="width: 30%;">' . "<strong>Fees received by</strong>" . '</td>
             <!--<td style="width: 23%;">' . "<strong>Fees paid by</strong>" . '</td>-->
             <td style="width: 12%; text-align: right;">' . "<strong>Amount, GH&cent;</strong>" . '</td>
          </tr>
        </tbody>';

    $pdf->writeHTML($tbl_head_description . $tbl_description . $tbl_foot_description, FALSE, false, true, false, '');

    $i = 1;
    $studentPaymentHistory = $bill->getStudentPaymentStatement($_SESSION["pupil_id_ledger"]);
    foreach ($studentPaymentHistory as $paymentHistory) {
        $getPTATransactionDetails = $payment->getPTATransactionDetails($_SESSION["pupil_id_ledger"]);
        
        $tbl_head_item = '<table cellspacing = "0" cellpadding = "0" border = "0">';
        $tbl_foot_item = '</table>';
        $tbl_item = '';

        $tbl_item .= '
        <tbody>
          <tr style="color: #000;">
             <td style="width: 5%; text-align: left;">' . $i++ . '</td>
             <td style="width: 12%; text-align: left;">' . date("d-m-Y", strtotime($paymentHistory["date"])) . '</td>
             <td style="width: 11%; text-align: left;">' . $paymentHistory["time"] . '</td>
             <td style="width: 18%; text-align: left;">' . $paymentHistory["academic_term"] . '</td>
             <td style="width: 12%; text-align: left;">' . $paymentHistory["mode_of_payment"] . '</td>
             <td style="width: 30%; text-align: left;">' . $paymentHistory["fees_received_by"] . '</td>
             <!--<td style="width: 23%; text-align: left;">' . $paymentHistory["fees_paid_by"] . '</td>-->
             <td style="text-align: right; width: 12%;">' . number_format($paymentHistory["amount"], 2, ".", ",") . '</td>
          </tr>
        </tbody>';

        $pdf->writeHTML($horizontalLine, true, 0, true, 0);

        $pdf->writeHTML($tbl_head_item . $tbl_item . $tbl_foot_item, FALSE, false, true, false, '');
    }
}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





