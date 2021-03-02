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

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$photo = new Photo();
$bill = new Bill();

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

$pageTitle = "<strong>" . htmlentities($_SESSION["debtors_creditors_list"] . " " . "DEBTORS'/CREDITORS' LIST AS AT " . strtoupper(date("F j, Y"))) . "</strong>";
$pdf->writeHTML($pageTitle, true, 0, true, 0, 'C');

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
         <td style="text-align: center; width: 10%;"><strong>Sex</strong></td>
         <td style="text-align: center; width: 20%;"><strong>Amount, GH&cent;</strong></td>
      </tr>
</tbody>';

$pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

$i = 1;

$getClassMembers = $classMembership->getClassMembersByClassName($_SESSION["debtors_creditors_list"]);
foreach ($getClassMembers as $value) {
    $getSingleBillAmount = $bill->getSingleBillingAmount($value["pupil_id"]);
    $singleBillAmount = $getSingleBillAmount["billAmount"];

    $getFeesPayable = $bill->getStudentAccount($value["pupil_id"]);
    $getTotalFeesPaid = $bill->getTotalFeesPaid($value["pupil_id"]);
    $getPTAAll = $bill->getPTAFeesAll($value["pupil_id"]);
    $getPTAFeesPaid = $bill->getPTAFeesPaid($value["pupil_id"]);
    $getTotalAmountPaid = $bill ->getTotalAmountPaid($value["pupil_id"]);

    $getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalAmountPaid["total_amount_paid"])), 2, ".", ",");
                             if ($getBalance == 0) {
                                            
                                            $balance = str_replace("-", "", $getBalance) . " ";
                                            
                                            // $balance = $getBalance. "-";
                                        }elseif ($getBalance > 0) {
                                               $balance = $getBalance . "DR";
                                            }  else {
                                                
                                                 $balance = str_replace("-", "", $getBalance) . " CR";
                                               // $balance = $getBalance . "CR";
                                               
                                            }

    $getStudentData = $pupil->getPupilById($value["pupil_id"]);
    $getBoardingStatus = $admission->getAdmissionById($value["pupil_id"]);

    $tbl_head_prduct = '<table cellspacing = "0" cellpadding = "2" border = "1">';
    $tbl_foot_prduct = '</table>';
    $tbl_body_prduct = '';
    $tbl_body_prduct .= '
    <tbody>
        <tr>
            <td style="text-align: center; width: 5%;">' . $i++ . '</td>
            <td style="width: 15%; text-align: center;">' . htmlentities($getStudentData["pupil_id"]) . '</td>
            <td style="width: 50%;">' . htmlentities($getStudentData["other_names"] . " " . $getStudentData["family_name"]) . '</td>
            <td style="width: 10%;">' . htmlentities($getStudentData["sex"]) . '</td>
            <td style="width: 20%; text-align: right;">' . htmlentities($balance) . '</td>
        </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_prduct . $tbl_body_prduct . $tbl_foot_prduct, FALSE, false, true, false, '');
}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





