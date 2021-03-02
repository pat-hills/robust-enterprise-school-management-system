<?php

ob_start();
require_once '../includes/header.php';
require_once '../tcpdf/tcpdf.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';
require_once '../classes/AcademicTerm.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$user = new User();
$photo = new Photo();
$academicTerm = new AcademicTerm();

$getAcademicTerm = $academicTerm->getActivatedTerm();
$current_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("print_house_report", $splitAccessPages, TRUE)) {
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

$pdf->Image($schoolLogo, 5, 7, 33, 37, '', '', '', true, 150, '', false, false, 0, false, false, false);

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

$houseTitle = "<strong>" . strtoupper(htmlentities($_SESSION["selected_house_report"] . " " . $_SESSION["selected_gender_report"]) . "S AS AT " . date("F j, Y")) . "</strong>";
$pdf->writeHTML($houseTitle, true, 0, true, 0, 'C');

$headingSpace = "<div></div>";
$pdf->writeHTML($headingSpace, true, 0, true, 0);

$tbl_head_title = '<table cellspacing = "0" cellpadding = "2" border = "1">';
$tbl_foot_title = '</table>';
$tbl_title = '';

$tbl_title .= '
<tbody>
      <tr style="background-color: #666; color: #fff;">
         <td style="text-align: center; width: 5%;"><strong>#</strong></td>
         <td style="text-align: center; width: 15%;"><strong>Student IDs</strong></td>
         <td style="text-align: center; width: 50%;"><strong>Students</strong></td>
         <td style="text-align: center; width: 10%;"><strong>Classes</strong></td>
         <td style="text-align: center; width: 20%;"><strong>Status</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$i = 1;

$getClassMembers = $classMembership->getHouseMembers($_SESSION["selected_house_report"], $_SESSION["selected_gender_report"], $current_academic_term);
foreach ($getClassMembers as $value) {
    $getStudentData = $pupil->getPupilById($value["pupil_id"]);

    $tbl_head_prduct = '<table cellspacing = "0" cellpadding = "2" border = "1">';
    $tbl_foot_prduct = '</table>';
    $tbl_body_prduct = '';
    $tbl_body_prduct .= '
    <tbody>
        <tr>
            <td style="text-align: center; width: 5%;">' . $i++ . '</td>
            <td style="width: 15%; text-align: center;">' . htmlentities($value["pupil_id"]) . '</td>
            <td style="width: 50%;">' . htmlentities($getStudentData["other_names"] . " " . $getStudentData["family_name"]) . '</td>
            <td style="width: 10%;">' . htmlentities($value["class_name"]) . '</td>
            <td style="width: 20%;">' . htmlentities($value["boarding_status"]) . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_prduct . $tbl_body_prduct . $tbl_foot_prduct, FALSE, false, true, false, '');
}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





