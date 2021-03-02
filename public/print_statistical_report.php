<?php

ob_start();

require_once '../includes/header.php';
require_once '../tcpdf/tcpdf.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';
require_once '../classes/Classes.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$user = new User();
$photo = new Photo();
$classes = new Classes();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("print_statistical_report", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$getInstitutionDetail = $institutionDetail->getInstitutionDetails();
$getSchool_name = $getInstitutionDetail["school_name"];
$getSchoolMotor = $getInstitutionDetail["school_motor"];
$getPostalAddress = $getInstitutionDetail["postal_address"];
$getTelephoneNumbers = $getInstitutionDetail["telephone_1"] . " " . $getInstitutionDetail["telephone_2"] . " " . $getInstitutionDetail["telephone_3"];

//$getLogo = $getInstitutionDetail["logo"];

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
    }

    // Page footer
    public function Footer() {

        // Position at 15 mm from bottom
        $this->SetY(-8);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
//        date_default_timezone_set("Africa/Accra");
//        $this->Cell(0, 12, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

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
         <td style="text-align: center; background-color: #666; color: #fff;">' . $heading . '</td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_heading . $tbl_heading . $tbl_foot_heading, true, false, true, false, '');

$pageTitle = "<strong>" . "SCHOOL POPULATION AS AT " . strtoupper(date("F j, Y")) . "</strong>";
$pdf->writeHTML($pageTitle, true, 0, true, 0, 'C');

$headingSpace = "<p></p>";
$pdf->writeHTML($headingSpace, true, 0, true, 0);

$i = 1;

$tbl_head_title = '<table cellspacing = "0" cellpadding = "2" border = "1">';
$tbl_foot_title = '</table>';
$tbl_title = '';

$tbl_title .= '
<tbody>
      <tr style="background-color: #666; color: #fff;">
         <td style="text-align: center; width: 5%;"><strong>#</strong></td>
         <td style="text-align: center; width: 14%;"><strong>Classes</strong></td>
         <td style="text-align: right; width: 28%;"><strong>Males</strong></td>
         <td style="text-align: right; width: 28%;"><strong>Females</strong></td>
         <td style="text-align: right; width: 25%;"><strong>Total</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$getStatistics = $classMembership->getStatistics();
foreach ($getStatistics as $value) {
    $getMalePopulation = $classMembership->getMalePopulationByClassName($value["class_name"]);
    $malePopulation = number_format($getMalePopulation["malePopulation"], 0, ".", ",");

    $getFemalePopulation = $classMembership->getFemalePopulationByClassName($value["class_name"]);
    $femalePopulation = number_format($getFemalePopulation["femalePopulation"], 0, ".", ",");

    $totalClassPopulation = number_format(($malePopulation + $femalePopulation), 0, ".", ",");

    $getClasses = $classes->getClasses();
    $tbl_head_population = '<table cellspacing = "0" cellpadding = "2" border = "1">';
    $tbl_foot_population = '</table>';
    $tbl_body_population = '';
    $tbl_body_population .= '
    <tbody>
        <tr>
            <td style="text-align: center; width: 5%;">' . $i++ . '</td>
            <td style="width: 14%; text-align: center;">' . htmlentities($value["class_name"]) . '</td>
            <td style="width: 28%; text-align: right;">' . htmlentities($malePopulation) . '</td>
            <td style="width: 28%; text-align: right;">' . htmlentities($femalePopulation) . '</td>
            <td style="width: 25%; text-align: right;">' . htmlentities($totalClassPopulation) . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_population . $tbl_body_population . $tbl_foot_population, FALSE, false, true, false, '');
}

$tbl_head_pop = '<table cellspacing = "0" cellpadding = "2" border = "0">';
$tbl_foot_pop = '</table>';
$tbl_pop = '';

$getTotalPopulation = $classMembership->getTotalPopulation();
$population = number_format($getTotalPopulation["totalPopulation"], 0, ".", ",");

$tbl_pop .= '
<tbody>
      <tr>
         <td colspan="5" style="width: 75%; text-align: right;">' . "<strong>Total Population</strong>" . '</td>
         <td style="width: 25%; text-align: right;"><strong>' . htmlentities($population) . ' students</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_pop . $tbl_pop . $tbl_foot_pop, FALSE, false, true, false, '');

$pdf->writeHTML($headingSpace, true, 0, true, 0);
$pdf->writeHTML($headingSpace, true, 0, true, 0);

$tbl_head_only = '<table cellspacing = "0" cellpadding = "2" border = "1">';
$tbl_foot_only = '</table>';
$tbl_only = '';

$tbl_only .= '
<tbody>
      <tr style="background-color: #666; color: #fff;">
         <td style="text-align: center; width: 5%;"><strong>#</strong></td>
         <td style="text-align: center; width: 14%;"><strong>Classes</strong></td>
         <td style="text-align: right; width: 26%;"><strong>Day Students</strong></td>
         <td style="text-align: right; width: 30%;"><strong>Boarding Students</strong></td>
         <td style="text-align: right; width: 25%;"><strong>Total</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_only . $tbl_only . $tbl_foot_only, FALSE, false, true, false, '');

$j = 1;
$getPopulationData = $classMembership->getStatistics();
foreach ($getPopulationData as $populationData) {
    $getDayStudents = $classMembership->getDayStudents($populationData["class_name"]);
    $dayStudents = number_format($getDayStudents["dayStudents"], 0, ".", ",");

    $getBoardingStudents = $classMembership->getBoardingStudents($populationData["class_name"]);
    $boardingStudents = number_format($getBoardingStudents["boardingStudents"], 0, ".", ",");

    $totalPopulation = number_format(($dayStudents + $boardingStudents), 0, ".", ",");

    $getClasses = $classes->getClasses();
    $tbl_head_total = '<table cellspacing = "0" cellpadding = "2" border = "1">';
    $tbl_foot_total = '</table>';
    $tbl_body_total = '';
    $tbl_body_total .= '
    <tbody>
        <tr>
            <td style="text-align: center; width: 5%;">' . $j++ . '</td>
            <td style="width: 14%; text-align: center;">' . htmlentities($populationData["class_name"]) . '</td>
            <td style="width: 26%; text-align: right;">' . htmlentities($dayStudents) . '</td>
            <td style="width: 30%; text-align: right;">' . htmlentities($boardingStudents) . '</td>
            <td style="width: 25%; text-align: right;">' . htmlentities($totalPopulation) . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_total . $tbl_body_total . $tbl_foot_total, FALSE, false, true, false, '');
}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





