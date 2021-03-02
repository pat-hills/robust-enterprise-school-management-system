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
    if (!in_array("print_student_bill", $splitAccessPages, TRUE)) {
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

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-30);
        // Set font
        $this->SetFont('helvetica', '', 8);
        // Page number
//        date_default_timezone_set("Africa/Accra");
//        $this->Cell(0, 10, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        $takeNote = "<strong><u>FOOTNOTES:</u></strong>";
        $horizontalLine = "<hr />";
        $space = "<p></p>";
        $msg = "1. <strong>Credit balance</strong> means that, the school owes the student the said amount.";
        $info = "2. All fees must be paid on or before the Re-opening date";
        $company = "<strong><em>Software by ANIDROL GHANA: +233 26-764-2898 / +233 24-774-5156</em></strong>";

        $this->writeHTML($horizontalLine, true, 0, true, 0);
        $this->writeHTML($takeNote, true, 0, true, 0);
        $this->writeHTML($space, true, 0, true, 0);
        $this->writeHTML($msg, true, 0, true, 0);
        $this->writeHTML($info, true, 0, true, 0);
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
$pdf->SetFont('times', '', 12);

// add a page
$pdf->AddPage();
$pdf->setJPEGQuality(75);

$getClassStudentById = $classMembership->getClassStudentById($_SESSION["pupil_id_ledger"]);
foreach ($getClassStudentById as $value) {
    $getSingleBillAmount = $bill->getSingleBillingAmount($value["pupil_id"]);
    $singleBillAmount = $getSingleBillAmount["billAmount"];

    $getTermFees = $bill->getTermFees($value["pupil_id"], $get_academic_term);
    $getPTA = $bill->getPTAFees($value["pupil_id"], $_SESSION["chosen_term"]);
    $getPTAAll = $bill->getPTAFeesAll($value["pupil_id"]);
//    $termFeesOnly = number_format($getTermFees["termFees"], 2, ".", ",");
    $termFees = number_format(($getTermFees["termFees"] + $getPTA["ptaFees"]), 2, ".", ",");
    if ($termFees <= 0) {
        if ($termFees == 0) {
            $termFeesFormated = "";
        } else {
            $termFeesFormated = "GH&cent;" . str_replace("-", "", $termFees) . " Credit";
        }
    } else {
        $termFeesFormated = "GH&cent;" . $termFees;
    }

    $getFeesPayable = $bill->getStudentAccount($value["pupil_id"]);
    $getTotalFeesPaid = $bill->getTotalFeesPaid($value["pupil_id"]);
//    $getBalance = number_format((($getFeesPayable["allFeesPayable"] - $getTotalFeesPaid["allFeesPaid"]) + $singleBillAmount), 2, ".", ",");
    $getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalFeesPaid["allFeesPaid"])), 2, ".", ",");

    if ($getBalance <= 0) {
        if ($getBalance == 0) {
            $balanceBroughtForward = "";
        } else {
            $balanceBroughtForward = "GH&cent;" . str_replace("-", "", $getBalance) . " Credit";
        }
    } else {
        $balanceBroughtForward = "GH&cent;" . $getBalance . " Debit";
    }

//    $totalFees = number_format(((($getFeesPayable["allFeesPayable"] - $getTotalFeesPaid["allFeesPaid"]) + $singleBillAmount) + $termFees), 2, ".", ",");
    $totalFees = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"] + $termFees) - $getTotalFeesPaid["allFeesPaid"]), 2, ".", ",");

    if ($totalFees <= 0) {
        if ($totalFees == 0) {
            $balanceCarriedForward = "";
        } else {
            $balanceCarriedForward = "GH&cent;" . str_replace("-", "", $totalFees) . " Credit";
        }
    } else {
        $balanceCarriedForward = "GH&cent;" . $totalFees;
    }

//    table
    $getStudentPhoto = $photo->getPhotoById($value["pupil_id"]);
    $studentPhoto = "../" . $getStudentPhoto["photo_url"];

    $getLogoID = $photo->getSchoolLogoID();
    $getSchoolLogo = $photo->getSchoolLogo($getLogoID["logo_id"]);
    $schoolLogo = "../" . $getSchoolLogo["photo_url"];

    $heading = "<span>GHANA EDUCATION SERVICE</span>"
            . "<h1>" . $getSchool_name . "</h1>"
            . "<span>" . $getPostalAddress . "</span><br />"
            . "<span>" . $getTelephoneNumbers . "</span>";

    $pdf->Image($studentPhoto, 155, 75, 35, 35, '', '', '', true, 150, '', false, false, 0, false, false, false);
    $pdf->Image($schoolLogo, 15, 15, 34, 37, '', '', '', true, 150, '', false, false, 0, false, false, false);

    $tbl_head_heading = '<table cellspacing = "2" cellpadding = "0">';
    $tbl_foot_heading = '</table>';
    $tbl_heading = '';

    $tbl_heading .= '
    <tbody>
      <tr>
         <td style="text-align: center; background-color: #666; color: #fff;" colspan="4">' . $heading . '</td>
      </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_heading . $tbl_heading . $tbl_foot_heading, true, false, true, false, '');

    $className = "<strong>" . htmlentities(strtoupper($get_academic_term . " " . "Term Bill")) . "</strong>";
    $pdf->writeHTML($className, true, 0, true, 0, 'C');

    $headingSpace = "<div></div>";
    $pdf->writeHTML($headingSpace, true, 0, true, 0);

    $horizontalLine = "<hr />";

    $getStudentData = $pupil->getPupilById($value["pupil_id"]);
    $getStudentName = $getStudentData["other_names"] . " " . $getStudentData["family_name"];
    $getStudentID = $getStudentData["pupil_id"];
    $getGender = $getStudentData["sex"];
    $getBoardingStatus = $value["boarding_status"];
    $getClassName = $value["class_name"];

    $tbl_head_title = '<table cellspacing = "2" cellpadding = "0" border = "0">';
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
         <td style="text-align: left;">' . $getBoardingStatus . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Class:</strong></td>
         <td style="text-align: left;">' . $getClassName . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Balance b/f:</strong></td>
         <td style="text-align: left;">' . $balanceBroughtForward . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Term Fees:</strong></td>
         <td style="text-align: left;">' . $termFeesFormated . '</td>
      </tr>
    
      <tr style="color: #000;">
         <td style="text-align: right; width: 20%;"><strong>Total Fees:</strong></td>
         <td style="text-align: left;">' . $balanceCarriedForward . '</td>
      </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

    $pdf->writeHTML($horizontalLine, true, 0, true, 0);

//    list of bill items
    $tbl_head_description = '<table cellspacing = "2" cellpadding = "5" border = "0">';
    $tbl_foot_description = '</table>';
    $tbl_description = '';

    $tbl_description .= '
        <tbody>
          <tr style="color: #000;">
             <td style="width: 8.5%;">&nbsp;</td>
             <td style="width: 67%; text-align: left;">' . "<strong>BILL ITEMS</strong>" . '</td>
             <td style="text-align: right; width: 16%;">' . "<strong>GH&cent;</strong>" . '</td>
          </tr>
        </tbody>';

    $pdf->writeHTML($tbl_head_description . $tbl_description . $tbl_foot_description, FALSE, false, true, false, '');

    $classBillItemsForStudent = $bill->getClassBillForIndividualStudent($getClassName);
    foreach ($classBillItemsForStudent as $classBillItemStudent) {
        if ($getBoardingStatus === "Day Student") {
            $schoolFees = $classBillItemStudent["day_amount"];
        } else {
            $schoolFees = $classBillItemStudent["boarding_amount"];
        }

        $tbl_head_item = '<table cellspacing = "1" cellpadding = "0" border = "0">';
        $tbl_foot_item = '</table>';
        $tbl_item = '';

        $tbl_item .= '
        <tbody>
          <tr style="color: #000;">
             <td style="width: 10%;">&nbsp;</td>
             <td style="width: 70%;">' . $classBillItemStudent["bill_item"] . '</td>
             <td style="text-align: right; width: 10%;">' . number_format($schoolFees, 2, ".", ",") . '</td>
          </tr>
        </tbody>';

        $pdf->writeHTML($tbl_head_item . $tbl_item . $tbl_foot_item, FALSE, false, true, false, '');
    }

    if ($getGender === "Male") {
        $classBillItemsForMale = $bill->getClassBillForMaleStudent($getClassName);
        foreach ($classBillItemsForMale as $classBillItemMale) {
            if ($getBoardingStatus === "Day Student") {
                $schoolFeesMale = $classBillItemMale["day_amount"];
            } else {
                $schoolFeesMale = $classBillItemMale["boarding_amount"];
            }

            $tbl_head_item = '<table cellspacing = "1" cellpadding = "0" border = "0">';
            $tbl_foot_item = '</table>';
            $tbl_item = '';

            $tbl_item .= '
        <tbody>
          <tr style="color: #000;">
             <td style="width: 10%;">&nbsp;</td>
             <td style="width: 70%;">' . $classBillItemMale["bill_item"] . '</td>
             <td style="text-align: right; width: 10%;">' . number_format($schoolFeesMale, 2, ".", ",") . '</td>
          </tr>
        </tbody>';

            $pdf->writeHTML($tbl_head_item . $tbl_item . $tbl_foot_item, FALSE, false, true, false, '');
        }
    } else {
        $classBillItemsForFemale = $bill->getClassBillForFemaleStudent($getClassName);
        foreach ($classBillItemsForFemale as $classBillItemFemale) {
            if ($getBoardingStatus === "Day Student") {
                $schoolFeesFemale = $classBillItemFemale["day_amount"];
            } else {
                $schoolFeesFemale = $classBillItemFemale["boarding_amount"];
            }

            $tbl_head_item = '<table cellspacing = "1" cellpadding = "0" border = "0">';
            $tbl_foot_item = '</table>';
            $tbl_item = '';

            $tbl_item .= '
        <tbody>
          <tr style="color: #000;">
             <td style="width: 10%;">&nbsp;</td>
             <td style="width: 70%;">' . $classBillItemFemale["bill_item"] . '</td>
             <td style="text-align: right; width: 10%;">' . number_format($schoolFeesFemale, 2, ".", ",") . '</td>
          </tr>
        </tbody>';

            $pdf->writeHTML($tbl_head_item . $tbl_item . $tbl_foot_item, FALSE, false, true, false, '');
        }
    }
}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





