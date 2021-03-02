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
    if (!in_array("print_income_and_expenditure", $splitAccessPages, TRUE)) {
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
        $spacer = "<p></p>";
        $horizontalLine = "<hr />";
        $company = "<strong><em>Software by ANIDROL GHANA: +233 26-764-2898 / +233 24-774-5156</em></strong>";

        // Position at 15 mm from bottom
        $this->SetY(-25);
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number
        $this->writeHTML($spacer, true, 0, true, 0, 0);
        date_default_timezone_set("Africa/Accra");
        $this->Cell(0, 10, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        $this->writeHTML($horizontalLine, true, 0, true, 0);
        $this->writeHTML($spacer, true, 0, true, 0, 0);
        $this->writeHTML($spacer, true, 0, true, 0, 0);
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

$pageTitle = "<strong>INCOME AND EXPENDITURE ACCOUNT FOR " . strtoupper($get_academic_term) . " TERM" . "</strong>";
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
         <td style="text-align: left; width: 50%;"><strong>DESCRIPTION</strong></td>
         <td style="text-align: right; width: 25%;"><strong>GH&cent;</strong></td>
         <td style="text-align: right; width: 25%;"><strong>GH&cent;</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$getAllTermFeesPaid = $bill->getAllTermFeesPaid($get_academic_term);
$termFeesPaid = $getAllTermFeesPaid["allTermFeesPaid"];
$formatTermFeesPaid = number_format($termFeesPaid, 2, ".", ",");

$getAllTermPTAFeesPaid = $bill->getAllTermPTAFeesPaid($get_academic_term);
$ptaTermFeesPaid = $getAllTermPTAFeesPaid["allTermPTAFeesPaid"];
$formatPTATermFeesPaid = number_format($ptaTermFeesPaid, 2, ".", ",");

$tbl_head_income_title = '<table cellspacing = "0" cellpadding = "5" border = "0">';
$tbl_foot_income_title = '</table>';
$tbl_body_income_title = '';
$tbl_body_income_title .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;"><strong>' . "Income" . '</strong></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_income_title . $tbl_body_income_title . $tbl_foot_income_title, FALSE, false, true, false, '');

$tbl_head_term_fees = '<table cellspacing = "0" cellpadding = "1" border = "0">';
$tbl_foot_term_fees = '</table>';
$tbl_body_term_fees = '';
$tbl_body_term_fees .= '
    <tbody>
        <tr>
            <td style="width: 5%; text-align: right;">' . "&nbsp;" . '</td>
            <td style="width: 45%; text-align: left;">' . "School Fees" . '</td>
            <td style="width: 50%; text-align: right;">' . $formatTermFeesPaid . '</td>
            <!--<td style="width: 25%; text-align: right;">' . "&nbsp;" . '</td>-->
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_term_fees . $tbl_body_term_fees . $tbl_foot_term_fees, FALSE, false, true, false, '');

$tbl_head_term_pta_fees = '<table cellspacing = "0" cellpadding = "1" border = "0">';
$tbl_foot_term_pta_fees = '</table>';
$tbl_body_term_pta_fees = '';
$tbl_body_term_pta_fees .= '
    <tbody>
        <tr>
            <td style="width: 5%; text-align: right;">' . "&nbsp;" . '</td>
            <td style="width: 45%; text-align: left;">' . "PTA Fees" . '</td>
            <td style="width: 50%; text-align: right;">' . $formatPTATermFeesPaid . '</td>
            <!--<td style="width: 25%; text-align: right;">' . "&nbsp;" . '</td>-->
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_term_pta_fees . $tbl_body_term_pta_fees . $tbl_foot_term_pta_fees, FALSE, false, true, false, '');

$getIncomes = $bill->getIncomes();
foreach ($getIncomes as $income) {
    $description = ucwords($income["description"]);
    $incomeAmount = $income["incomeTotal"];

    $tbl_head_incomes = '<table cellspacing = "0" cellpadding = "1" border = "0">';
    $tbl_foot_incomes = '</table>';
    $tbl_body_incomes = '';
    $tbl_body_incomes .= '
    <tbody>
        <tr>
            <td style="width: 5%; text-align: right;">' . "&nbsp;" . '</td>
            <td style="width: 45%; text-align: left;">' . $description . '</td>
            <td style="width: 50%; text-align: right;">' . number_format($incomeAmount, 2, ".", ",") . '</td>
            <!--<td style="width: 25%; text-align: right;">' . "&nbsp;" . '</td>-->
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_incomes . $tbl_body_incomes . $tbl_foot_incomes, FALSE, false, true, false, '');
}

$getTotalIncome = $bill->getTotalIncome();
$totalIncome = $getTotalIncome["totalIncome"] + $termFeesPaid + $ptaTermFeesPaid;

$tbl_head_total_income = '<table cellspacing = "0" cellpadding = "1" border = "0">';
$tbl_foot_total_income = '</table>';
$tbl_body_total_income = '';
$tbl_body_total_income .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;"><strong>' . "<em>Total Income</em>" . '</strong></td>
            <td style="width: 25%; text-align: right; text-decoration: overline;">' . number_format($totalIncome, 2, ".", ",") . '</td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_total_income . $tbl_body_total_income . $tbl_foot_total_income, FALSE, false, true, false, '');

$pdf->writeHTML($headingSpace, true, 0, true, 0);

$tbl_head_expense = '<table cellspacing = "0" cellpadding = "1" border = "0">';
$tbl_foot_expense = '</table>';
$tbl_body_expense = '';
$tbl_body_expense .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;"><strong>' . "Expenditure" . '</strong></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_expense . $tbl_body_expense . $tbl_foot_expense, FALSE, false, true, false, '');

$getExpenditure = $bill->getExpenditure();
foreach ($getExpenditure as $expense) {
    $description = ucwords($expense["description"]);
    $expenditure = $expense["expenditureTotal"];

    $tbl_head_expenditure = '<table cellspacing = "0" cellpadding = "1" border = "0">';
    $tbl_foot_expenditure = '</table>';
    $tbl_body_expenditure = '';
    $tbl_body_expenditure .= '
    <tbody>
        <tr>
            <td style="width: 5%; text-align: right;">' . "&nbsp;" . '</td>
            <td style="width: 45%; text-align: left;">' . $description . '</td>
            <td style="width: 25%; text-align: right;">' . number_format($expenditure, 2, ".", ",") . '</td>
            <td style="width: 25%; text-align: right;">' . "&nbsp;" . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_expenditure . $tbl_body_expenditure . $tbl_foot_expenditure, FALSE, false, true, false, '');
}

$getTotalExpenditure = $bill->getTotalExpenditure();
$totalExpenditure = $getTotalExpenditure["totalExpenditure"];

$tbl_head_total_expense = '<table cellspacing = "0" cellpadding = "1" border = "0">';
$tbl_foot_total_expense = '</table>';
$tbl_body_total_expense = '';
$tbl_body_total_expense .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;"><strong>' . "<em>Total Expenditure</em>" . '</strong></td>
            <td style="width: 25%; text-align: right;"><u>' . number_format($totalExpenditure, 2, ".", ",") . '</u></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_total_expense . $tbl_body_total_expense . $tbl_foot_total_expense, FALSE, false, true, false, '');

$differenceBtnIncomeAndExpenditure = $totalIncome - $totalExpenditure;
if ($differenceBtnIncomeAndExpenditure < 0) {
    $formatDifference = str_replace("-", "", $differenceBtnIncomeAndExpenditure);
    $word = "<strong>Deficit</strong>";
} else {
    $formatDifference = $differenceBtnIncomeAndExpenditure;
    $word = "<strong>Surplus</strong>";
}

$tbl_head_balance = '<table cellspacing = "0" cellpadding = "1" border = "0">';
$tbl_foot_balance = '</table>';
$tbl_body_balance = '';
$tbl_body_balance .= '
    <tbody>
        <tr>
            <td style="width: 75%; text-align: left;">' . $word . '</td>
            <td style="width: 25%; text-align: right; text-decoration: underline;"><strong>' . number_format($formatDifference, 2, ".", ",") . '</strong></td>
        </tr>
    </tbody>';

$pdf->writeHTML($tbl_head_balance . $tbl_body_balance . $tbl_foot_balance, FALSE, false, true, false, '');
//}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





