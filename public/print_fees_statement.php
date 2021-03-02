<?php

ob_start();

require_once '../includes/header.php';
require_once '../tcpdf/tcpdf.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';
require_once '../classes/Payments.php';
require_once '../classes/Bill.php';
require_once '../classes/AcademicTerm.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$user = new User();
$photo = new Photo();
$payment = new Payments();
$bill = new Bill();
$academicTerm = new AcademicTerm();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

$get_name = $getUserDetails["other_names"] . " " . $getUserDetails["family_name"];

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("print_fees_statement", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$getInstitutionDetail = $institutionDetail->getInstitutionDetails();
$getSchool_name = $getInstitutionDetail["school_name"];
$getSchoolMotor = $getInstitutionDetail["school_motor"];
$getPostalAddress = $getInstitutionDetail["postal_address"];
$getTelephoneNumbers = $getInstitutionDetail["telephone_1"] . " " . $getInstitutionDetail["telephone_2"] . " " . $getInstitutionDetail["telephone_3"];

$getAcademicTerm = $academicTerm->getActivatedTerm();
$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

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

$pageTitle = "<strong>STATEMENT OF SCHOOL FEES AS AT " . strtoupper(date("F j, Y")) . "</strong>";
$pdf->writeHTML($pageTitle, true, 0, true, 0, 'C');

$headingSpace = "<div></div>";
$pdf->writeHTML($headingSpace, true, 0, true, 0);
$pdf->writeHTML($headingSpace, true, 0, true, 0);

$tbl_head_title = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_title = '</table>';
$tbl_title = '';

$tbl_title .= '
<tbody>
      <tr style="background-color: #666; color: #fff;">
         <td style="text-align: left; width: 75%;"><strong>DESCRIPTION</strong></td>
         <td style="text-align: right; width: 25%;"><strong>GH&cent;</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$getTermFees = $bill->getAllTermFees($get_academic_term);
$termFees = $getTermFees["termFees"];
$expectedTermFees = $getTermFees["termFees"];

$getSingleBillAmount = $bill->getAllSingleBillingAmountsBySchoolNumber();
$singleBillAmount = $getSingleBillAmount["billAmount"];

$getFeesPayable = $bill->getAllFeesPayableBySchoolNumber();
$getTotalFeesPaid = $bill->getAllFeesPaidBySchoolNumber();
$getBalance = number_format((($getFeesPayable["allFeesPayable"] - $getTotalFeesPaid["allFeesPaid"]) + $singleBillAmount), 2, ".", ",");

$getAllTermFeesPaid = $bill->getAllTermFeesPaid($get_academic_term);
$termFeesPaid = $getAllTermFeesPaid["allTermFeesPaid"];
$formatTermFeesPaid = $termFeesPaid;

$getPTATransactionDetails = $payment->getTermPTAPayments($get_academic_term);
$ptaAmountPaid = $getPTATransactionDetails["ptaTermPayment"];

$totalTermFeesPaid = number_format($formatTermFeesPaid + $ptaAmountPaid, 2, ".", ",");

$balance = (($getFeesPayable["allFeesPayable"] - $getTotalFeesPaid["allFeesPaid"]) + $singleBillAmount);
$allFeesPaid = $getTotalFeesPaid["allFeesPaid"];
//$arrears = $balance - $allFeesPaid;
$arrears = $balance;
if ($arrears < 0) {
    $formatArrears = str_replace("-", "", $arrears);
} else {
    $formatArrears = $arrears;
}

$differenceBtnExpectedTermFeesAndArrears = $termFees + $arrears;

$balanceCD = $differenceBtnExpectedTermFeesAndArrears - $totalTermFeesPaid;
if ($balanceCD < 0) {
    $formatBalanceCD = str_replace("-", "", $balanceCD);
} else {
    $formatBalanceCD = $balanceCD;
}

$tbl_head_arrears = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_arrears = '</table>';
$tbl_body_arrears = '';
$tbl_body_arrears .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;">' . "Balance b/f" . '</td>
            <td style="width: 25%; text-align: right;">' . number_format($formatArrears, 2, ".", ",") . '</td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_arrears . $tbl_body_arrears . $tbl_foot_arrears, FALSE, false, true, false, '');

$tbl_head_expected = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_expected = '</table>';
$tbl_body_expected = '';
$tbl_body_expected .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;">' . "This term's school fees payable" . '</td>
            <td style="width: 25%; text-align: right;"><u>' . number_format($expectedTermFees, 2, ".", ",") . '</u></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_expected . $tbl_body_expected . $tbl_foot_expected, FALSE, false, true, false, '');

$tbl_head_diff = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_diff = '</table>';
$tbl_body_diff = '';
$tbl_body_diff .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;">' . "" . '</td>
            <td style="width: 25%; text-align: right;">' . number_format($differenceBtnExpectedTermFeesAndArrears, 2, ".", ",") . '</td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_diff . $tbl_body_diff . $tbl_foot_diff, FALSE, false, true, false, '');

$tbl_head_term_fees = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_term_fees = '</table>';
$tbl_body_term_fees = '';
$tbl_body_term_fees .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;">' . "Total fees collected during this term" . '</td>
            <td style="width: 25%; text-align: right;"><u>' . $totalTermFeesPaid . '</u></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_term_fees . $tbl_body_term_fees . $tbl_foot_term_fees, FALSE, false, true, false, '');

$tbl_head_balance = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_balance = '</table>';
$tbl_body_balance = '';
$tbl_body_balance .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;">' . "Balance c/d" . '</td>
            <td style="width: 25%; text-align: right;"><u>' . number_format($formatBalanceCD, 2, ".", ",") . '</u></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_balance . $tbl_body_balance . $tbl_foot_balance, FALSE, false, true, false, '');
//}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





