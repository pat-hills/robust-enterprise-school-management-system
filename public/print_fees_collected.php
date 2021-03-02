<?php

ob_start();

require_once '../includes/header.php';
require_once '../tcpdf/tcpdf.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';
require_once '../classes/Payments.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$user = new User();
$photo = new Photo();
$payment = new Payments();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("print_debtors_creditors", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$getInstitutionDetail = $institutionDetail->getInstitutionDetails();
$getSchool_name = $getInstitutionDetail["school_name"];
$getSchoolMotor = $getInstitutionDetail["school_motor"];
$getPostalAddress = $getInstitutionDetail["postal_address"];
$getTelephoneNumbers = $getInstitutionDetail["telephone_1"] . " " . $getInstitutionDetail["telephone_2"] . " " . $getInstitutionDetail["telephone_3"];

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-8);
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number
//        date_default_timezone_set("Africa/Accra");
//        $this->Cell(0, 10, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        $horizontalLine = "<hr />";
        $company = "<strong><em>Software by ANIDROL GHANA: +233 26-764-2898 / +233 24-774-5156</em></strong>";
        
        $this->writeHTML($horizontalLine, true, 0, true, 0);
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

$getLogoID = $photo->getSchoolLogoID();
$getSchoolLogo = $photo->getSchoolLogo($getLogoID["logo_id"]);
$schoolLogo = "../" . $getSchoolLogo["photo_url"];

$heading = "<span>GHANA EDUCATION SERVICE</span>"
        . "<h1>" . $getSchool_name . "</h1>"
        . "<span>" . $getPostalAddress . "</span><br />"
        . "<span>" . $getTelephoneNumbers . "</span>";

$pdf->Image($schoolLogo, 5, 7, 34, 37, '', '', '', true, 150, '', false, false, 0, false, false, false);

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

$headingSpace = "<div></div>";

$dailyFees = "<strong>DAILY FEES COLLECTION STATEMENT</strong>";
$pdf->writeHTML($dailyFees, true, 0, true, 0, 'C');

$pdf->writeHTML($headingSpace, true, 0, true, 0);

$pageTitle = "<strong>Total Fees Collected by " . strtoupper(htmlentities($_SESSION["account_clerk"])) . " on " . date("F j, Y") . ".</strong>";
$pdf->writeHTML($pageTitle, true, 0, true, 0, 'C');

$pdf->writeHTML($headingSpace, true, 0, true, 0);

$tbl_head_title = '<table cellspacing = "0" cellpadding = "2" border = "1">';
$tbl_foot_title = '</table>';
$tbl_title = '';

$tbl_title .= '
<tbody>
      <tr style="background-color: #666; color: #fff;">
         <td style="text-align: center; width: 5%;"><strong>#</strong></td>
         <td style="text-align: center; width: 12%;"><strong>Student IDs</strong></td>
         <td style="text-align: center; width: 12%;"><strong>Mode</strong></td>
         <td style="text-align: center; width: 16%;"><strong>Cheque/Draft No.</strong></td>
         <td style="text-align: center; width: 12%;"><strong>Time</strong></td>
         <td style="text-align: center; width: 30%;"><strong>Fees paid by</strong></td>
         <td style="text-align: center; width: 13%;"><strong>Amount, GH&cent;</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$i = 1;

$todayDate = date("Y-m-d");
$getTotalDailyFeesCollected = $payment->getTotalDailyFeesCollected($_SESSION["account_clerk"], $todayDate);
foreach ($getTotalDailyFeesCollected as $feesCollectedToday) {
    if (empty($feesCollectedToday["mode_of_payment_number"])) {
        $payment_number = "none";
    } else {
        $payment_number = $feesCollectedToday["mode_of_payment_number"];
    }

    $tbl_head_prduct = '<table cellspacing = "0" cellpadding = "2" border = "1">';
    $tbl_foot_prduct = '</table>';
    $tbl_body_prduct = '';
    $tbl_body_prduct .= '
    <tbody>
        <tr>
            <td style="text-align: center; width: 5%;">' . $i++ . '</td>
            <td style="width: 12%; text-align: center;">' . htmlentities($feesCollectedToday["pupil_id"]) . '</td>
            <td style="width: 12%; text-align: left;">' . htmlentities($feesCollectedToday["mode_of_payment"]) . '</td>
            <td style="width: 16%; text-align: left;">' . htmlentities($payment_number) . '</td>
            <td style="width: 12%; text-align: center;">' . htmlentities($feesCollectedToday["time"]) . '</td>
            <td style="width: 30%; text-align: left;">' . htmlentities($feesCollectedToday["fees_paid_by"]) . '</td>
            <td style="width: 13%; text-align: right;">' . number_format(htmlentities($feesCollectedToday["amount"]), 2, ".", ",") . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_prduct . $tbl_body_prduct . $tbl_foot_prduct, FALSE, false, true, false, '');
}

$getPTADailyPaid = $payment->getPTADailyPaid($_SESSION["account_clerk"], $todayDate);
foreach ($getPTADailyPaid as $ptaPaid) {
    if (empty($ptaPaid["mode_of_payment_number"])) {
        $payment_number = "PTA Deduction";
    } else {
        $payment_number = $ptaPaid["mode_of_payment_number"];
    }

    $tbl_head_pta = '<table cellspacing = "0" cellpadding = "2" border = "1">';
    $tbl_foot_pta = '</table>';
    $tbl_body_pta = '';
    $tbl_body_pta .= '
    <tbody>
        <tr>
            <td style="text-align: center; width: 5%;">' . $i++ . '</td>
            <td style="width: 12%; text-align: center;">' . htmlentities($ptaPaid["pupil_id"]) . '</td>
            <td style="width: 12%; text-align: left;">' . htmlentities($feesCollectedToday["mode_of_payment"]) . '</td>
            <td style="width: 16%; text-align: left;">' . htmlentities($payment_number) . '</td>
            <td style="width: 12%; text-align: center;">' . htmlentities($ptaPaid["time"]) . '</td>
            <td style="width: 30%; text-align: left;">' . htmlentities($feesCollectedToday["fees_paid_by"]) . '</td>
            <td style="width: 13%; text-align: right;">' . number_format(htmlentities($ptaPaid["amount"]), 2, ".", ",") . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_pta . $tbl_body_pta . $tbl_foot_pta, FALSE, false, true, false, '');
}

$getPTATransactionDetails = $payment->getPTADailyPayments($_SESSION["account_clerk"], $todayDate);
$ptaAmountPaid = $getPTATransactionDetails["ptaDailyPayment"];
$getSumOfDailyFeesCollected = $payment->getSumOfDailyFeesCollected($_SESSION["account_clerk"], $todayDate);

$tbl_head_total = '<table cellspacing = "0" cellpadding = "2" border = "0">';
$tbl_foot_total = '</table>';
$tbl_body_total = '';
$tbl_body_total .= '
    <tbody>
        <tr>
            <td cols="7" style="width: 87%; text-align: right;">' . "<strong>TOTAL FEES COLLECTED</strong>" . '</td>
            <td style="width: 13%; text-align: right;"><strong>' . number_format(htmlentities($getSumOfDailyFeesCollected["totalAmountCollected"] + $ptaAmountPaid), 2, ".", ",") . '</strong></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_total . $tbl_body_total . $tbl_foot_total, FALSE, false, true, false, '');

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





