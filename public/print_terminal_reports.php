<?php

ob_start();

require_once '../includes/header.php';
require_once '../tcpdf/tcpdf.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';
require_once '../classes/Mark.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Comment.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$user = new User();
$photo = new Photo();
$mark = new Mark();
$subjectCombination = new SubjectCombination();
$academicTerm = new AcademicTerm();
$comment = new Comment();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("print_terminal_reports", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$getInstitutionDetail = $institutionDetail->getInstitutionDetails();
$getSchool_name = $getInstitutionDetail["school_name"];
$getSchoolMotor = $getInstitutionDetail["school_motor"];
$getPostalAddress = $getInstitutionDetail["postal_address"];
$getTelephoneNumbers = $getInstitutionDetail["telephone_1"] . " " . $getInstitutionDetail["telephone_2"] . " " . $getInstitutionDetail["telephone_3"];

if (!isset($_SESSION["selected_class_name_results"], $_SESSION["selected_academic_term_results"])) {
    redirect_to("terminal_reports.php");
}

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-33);
        // Set font
        $this->SetFont('helvetica', '', 9);
        // Page number
//        date_default_timezone_set("Africa/Accra");
//        $this->Cell(0, 10, date("d/m/Y h:i:sa") . " " . 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

        $company = "<strong><em>Software by ANIDROL GHANA: +233 26-764-2898 / +233 24-774-5156</em></strong>";
        $createSpace = "<p></p>";

        $photo = new Photo();
        $getSignature = $photo->getSignatureID();
        $getSignatureName = $photo->getSignatureBySchoolNumber($getSignature["school_number"]);
        $getSignatureURL = "../" . $getSignatureName["signature_url"];

        $this->Image($getSignatureURL, 153, 232, 55, 35, '', '', '', true, 150, '', false, false, 0, false, false, false);

        $tbl_head_sign = '<table cellspacing = "0" cellpadding = "0" border = "0">';
        $tbl_foot_sign = '</table>';
        $tbl_sign = '';

        $tbl_sign .= '
        <tbody>
          <tr>
             <td style="width: 100%; text-align: right;">' . "........................................................" . '</td>
          </tr>
          
          <tr>
             <td style="width: 100%; text-align: right;">' . "<strong>Headmaster/mistress's signature</strong>" . '</td>
          </tr>
        </tbody>';

        $this->writeHTML($tbl_head_sign . $tbl_sign . $tbl_foot_sign, FALSE, false, true, false, '');

        $this->writeHTML($createSpace, true, 0, true, 0);

        $gradingSystem = "Grading System:";
        $this->writeHTML($gradingSystem, true, 0, true, 0);

        $tbl_head_exp = '<table cellspacing = "0" cellpadding = "0" border = "1">';
        $tbl_foot_exp = '</table>';
        $tbl_exp = '';

        $tbl_exp .= '
        <tbody>
          <tr>
             <td style="width: 11%; text-align: center;">' . "100 - 90" . '</td>
             <td style="width: 11%; text-align: center;">' . "89 - 80" . '</td>
             <td style="width: 11%; text-align: center;">' . "79 - 70" . '</td>
             <td style="width: 11%; text-align: center;">' . "69 - 65" . '</td>
             <td style="width: 12%; text-align: center;">' . "64 - 60" . '</td>
             <td style="width: 11%; text-align: center;">' . "59 - 55" . '</td>
             <td style="width: 11%; text-align: center;">' . "54 - 50" . '</td>
             <td style="width: 11%; text-align: center;">' . "49 - 45" . '</td>
             <td style="width: 11%; text-align: center;">' . "44 - 0" . '</td>
          </tr>
          
          <tr>
             <td style="width: 11%; text-align: center;">' . "A1" . '</td>
             <td style="width: 11%; text-align: center;">' . "B2" . '</td>
             <td style="width: 11%; text-align: center;">' . "B3" . '</td>
             <td style="width: 11%; text-align: center;">' . "C4" . '</td>
             <td style="width: 12%; text-align: center;">' . "C5" . '</td>
             <td style="width: 11%; text-align: center;">' . "C6" . '</td>
             <td style="width: 11%; text-align: center;">' . "D7" . '</td>
             <td style="width: 11%; text-align: center;">' . "E8" . '</td>
             <td style="width: 11%; text-align: center;">' . "F9" . '</td>
          </tr>
        </tbody>';

        $this->writeHTML($tbl_head_exp . $tbl_exp . $tbl_foot_exp, FALSE, false, true, false, '');

        $this->writeHTML($createSpace, true, 0, true, 0);
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

$x = 1;
$y = 0;
$prev = "";

$getClassTotal = $classMembership->getClassTotal($_SESSION["selected_class_name_results"], $_SESSION["selected_academic_term_results"]);
$getTerminalReports = $mark->getTerminalReport($_SESSION["selected_academic_term_results"], $_SESSION["selected_class_name_results"], $getClassTotal["classTotal"]);
foreach ($getTerminalReports as $studentReport) {
//add pages
    $pdf->AddPage();
    $pdf->startPageGroup();
    $pdf->setJPEGQuality(75);

    if ($prev == $studentReport["totalScores"]) {
        $position = $y;
        $classPosition = ordinal($position);
    } else {
        $position = $x;
        $classPosition = ordinal($position);
        $y = $x;
    }
    $prev = $studentReport["totalScores"];
    $x++;

    $getStudentPhoto = $photo->getPhotoById($studentReport["pupil_id"]);
    $studentPhoto = "../" . $getStudentPhoto["photo_url"];

    $getLogoID = $photo->getSchoolLogoID();
    $getSchoolLogo = $photo->getSchoolLogo($getLogoID["logo_id"]);
    $schoolLogo = "../" . $getSchoolLogo["photo_url"];

//    $getSignature = $photo->getSignatureID();
//    $getSignatureName = $photo->getSignatureBySchoolNumber($getSignature["school_number"]);
//    $getSignatureURL = "../" . $getSignatureName["signature_url"];

    $heading = "<span>GHANA EDUCATION SERVICE</span>"
            . "<h1>" . $getSchool_name . "</h1>"
            . "<span>" . $getPostalAddress . "</span><br />"
            . "<span>" . $getTelephoneNumbers . "</span>";

    $pdf->Image($studentPhoto, 170, 7, 35, 37, '', '', '', true, 150, '', false, false, 0, false, false, false);
    $pdf->Image($schoolLogo, 5, 7, 34, 37, '', '', '', true, 150, '', false, false, 0, false, false, false);
//    $pdf->Image($getSignatureURL, 147, 205, 55, 35, '', '', '', true, 150, '', false, false, 0, false, false, false);

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

    $pageTitle = "<strong>" . strtoupper(htmlentities($_SESSION["selected_academic_term_results"]) . " " . "Term Examination Report") . "</strong>";
    $pdf->writeHTML($pageTitle, true, 0, true, 0, 'C');

    $headingSpace = "<p></p>";
    $pdf->writeHTML($headingSpace, true, 0, true, 0);

    $horizontalLine = "<br />";

    $getStudentData = $pupil->getPupilById($studentReport["pupil_id"]);
    $getStudentName = $getStudentData["other_names"] . " " . $getStudentData["family_name"];
    $getStudentID = $getStudentData["pupil_id"];
    $getGender = $getStudentData["sex"];

    $getStatus = $classMembership->getClassMembershipPupilId($studentReport["pupil_id"]);
    $getBoardingStatus = $getStatus["boarding_status"];
    $getClassName = $studentReport["class"];

    $getMarksByClass = $mark->getMarksByClass($getClassName);
    $classAverage = $getMarksByClass["classAverageMark"];

    $getSubjectCombinationName = $subjectCombination->getSubjectCombination($getStatus["subject_combination_name"]);
    $countSubjects = explode("**", $getSubjectCombinationName["subject_name"]);
    $totalSubjectsPerClass = count($countSubjects);

    $getMark = $mark->getMarks($getStudentID);
    $getStudentAverageMark = $getMark["totalMarks"] / $totalSubjectsPerClass;

    $getAcademicTerm = $academicTerm->getActivatedTerm();
    $getEndDate = date("F j, Y", strtotime($getAcademicTerm["end_date"]));

    $getNextAcademicTermBeginDate = $academicTerm->getNextAcademicTerm();
    $reOpeningDate = date("F j, Y", strtotime($getNextAcademicTermBeginDate["begin_date"]));

    $getNextAcademicTerm = $getNextAcademicTermBeginDate["academic_year"] . "/" . $getNextAcademicTermBeginDate["term"];
    $getClassMembersByAcademicTerm = $classMembership->getClassMembersByAcademicTerm($getStudentID, $getNextAcademicTerm);
    $classToBePromotedTo = $getClassMembersByAcademicTerm["class_name"];

    if ($getAcademicTerm["term"] === "Third") {
        if ($classToBePromotedTo === $getClassName) {
            $classPromotedTo = "Repeated";
        } else {
            $classPromotedTo = $classToBePromotedTo;
        }
    } else {
        if ($classToBePromotedTo === $getClassName) {
            $classPromotedTo = "-";
        } else {
            $classPromotedTo = $classToBePromotedTo;
        }
    }

    $tbl_head_title = '<table cellspacing = "0" cellpadding = "2" border = "0">';
    $tbl_foot_title = '</table>';
    $tbl_title = '';

    $tbl_title .= '
    <tbody>
      <tr style="color: #000;">
         <td style="text-align: left; width: 16%;">STUDENT:</td>
         <td style="text-align: left; width: 40%; font-weight:bold;margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . strtoupper($getStudentName) . '</td>
             
        <td style="text-align: left; width: 25%;">VACATION DATE:</td>
         <td style="text-align: left; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $getEndDate . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: left; width: 16%;">ID NUMBER:</td>
         <td style="text-align: left; width: 40%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $getStudentID . '</td>
             
      <td style="text-align: left; width: 25%;">NEXT TERM BEGINS:</td>
         <td style="text-align: left; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $reOpeningDate . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: left; width: 16%;">GENDER:</td>
         <td style="text-align: left; width: 40%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $getGender . '</td>
             
         <td style="text-align: left; width: 30%;">CLASS AVERAGE MARK:</td>
         <td style="text-align: left; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . number_format($classAverage, 2, ".", ",") . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: left; width: 16%;">STATUS:</td>
         <td style="text-align: left; width: 40%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $getBoardingStatus . '</td>
             
         <td style="text-align: left; width: 30%;">STUDENT AVERAGE MARK:</td>
         <td style="text-align: left; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . number_format($getStudentAverageMark, 2, ".", ",") . '</td>
             
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: left; width: 16%;">CLASS:</td>
         <td style="text-align: left; width: 40%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $getClassName . '</td>
             
         <td style="text-align: left; width: 25%;">NUMBER ON ROLL:</td>
         <td style="text-align: left; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $getClassTotal["classTotal"] . '</td>
      </tr>
      
      <tr style="color: #000;">
         <td style="text-align: left; width: 16%;">PROMOTED TO</td>
         <td style="text-align: left; width: 40%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $classPromotedTo . '</td>
             
         <td style="text-align: left; width: 30%;">POSITION IN CLASS</td>
         <td style="text-align: left; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $classPosition . '</td>
      </tr>
    </tbody>';

    $pdf->writeHTML($tbl_head_title . $tbl_title . $tbl_foot_title, FALSE, false, true, false, '');

    $pdf->writeHTML($horizontalLine, true, 0, true, 0);

    $institution_set = $institutionDetail->getInstitutionDetails();
    $getTerminalSettings = $comment->getTerminalSettings($institution_set["school_number"]);
    $getClassScorePercentage = $getTerminalSettings["class_score_point"];
    $getExamScorePercentage = $getTerminalSettings["exam_score_point"];
    $totalScorePercentage = $getClassScorePercentage + $getExamScorePercentage;

//    list of terminal report headings
    $tbl_head_description = '<table cellspacing = "0" cellpadding = "0" border = "1">';
    $tbl_foot_description = '</table>';
    $tbl_description = '';

    $tbl_description .= '
        <tbody>
          <tr style="color: #fff; background-color: #666;">
             <td style="width: 29%; text-align: center;"><br /><br /><strong>Subjects</strong></td>
             <td style="width: 13%; text-align: center;"><strong>Class Scores(' . $getClassScorePercentage . '%)</strong></td>
             <td style="width: 13%; text-align: center;"><strong>Exam Scores(' . $getExamScorePercentage . '%)</strong></td>
             <td style="width: 14%; text-align: center;"><strong>Total Scores(' . $totalScorePercentage . '%)</strong></td>
             <td style="width: 10%; text-align: center;"><strong>Subject Positions</strong></td>
             <td style="width: 8%; text-align: center;"><strong><br />Grades</strong></td>
             <td style="width: 13%; text-align: center;"><strong><br />Remarks</strong></td>
          </tr>
        </tbody>';

    $pdf->writeHTML($tbl_head_description . $tbl_description . $tbl_foot_description, FALSE, false, true, false, '');

    $getTerminalReportDetails = $mark->getTerminalReportDetails($_SESSION["selected_academic_term_results"], $studentReport["class"], $getStudentData["pupil_id"]);
    foreach ($getTerminalReportDetails as $terminalReport) {

//        $getSubjectPositionByStudentID = $mark->getSubjectPositionByStudentID($terminalReport["pupil_id"], $terminalReport["subject"], $_SESSION["selected_academic_term_results"]);
//        $formatSubjectPosition = $getSubjectPositionByStudentID["position"];
//        if ($formatSubjectPosition === NULL) {
//            $subjectPosition = "";
//        }  else {
//            $subjectPosition = ordinal($formatSubjectPosition);
//        }

        if ($terminalReport["position"] === NULL) {
            $subjectPosition = "";
        } else {
            $subjectPosition = ordinal($terminalReport["position"]);
        }

        $tbl_head_sub = '<table cellspacing = "0" cellpadding = "3" border = "1">';
        $tbl_foot_sub = '</table>';
        $tbl_sub = '';

        $tbl_sub .= '
        <tbody>
          <tr>
             <td style="width: 29%; text-align: left;">' . ucwords(strtolower($terminalReport["subject"])) . '</td>
             <td style="width: 13%; text-align: center;">' . number_format($terminalReport["thirty_percent"], 0, ".", ",") . '</td>
             <td style="width: 13%; text-align: center;">' . number_format($terminalReport["seventy_percent"], 0, ".", ",") . '</td>
             <td style="width: 14%; text-align: center;">' . number_format($terminalReport["total"], 0, ".", ",") . '</td>
             <td style="width: 10%; text-align: center;">' . $subjectPosition . '</td>
             <td style="width: 8%; text-align: center;">' . $terminalReport["grade"] . '</td>
             <td style="width: 13%; text-align: left;">' . $terminalReport["remark"] . '</td>
          </tr>
        </tbody>';

        $pdf->writeHTML($tbl_head_sub . $tbl_sub . $tbl_foot_sub, FALSE, false, true, false, '');
    }

    $pdf->writeHTML($headingSpace, true, 0, true, 0);

    $getSchoolNumber = $getInstitutionDetail["school_number"];
    $getClassTeacherComments = $comment->getClassTeacherComments($getStudentID, $getSchoolNumber);
    $attendance = $getClassTeacherComments["attendance"];
    $outOf = $getClassTeacherComments["out_of"];
    $conduct = $getClassTeacherComments["conduct"];
    $interest = $getClassTeacherComments["interest"];
    $attitude = $getClassTeacherComments["attitude"];
    $classTeacherRemark = $getClassTeacherComments["remark"];

    $getHeadRemark = $comment->getHeadRemark($getStudentID, $getSchoolNumber);
    $headRemark = $getHeadRemark["remark"];

    $tbl_head_comment = '<table cellspacing = "0" cellpadding = "2" border = "0">';
    $tbl_foot_comment = '</table>';
    $tbl_comment = '';

    $tbl_comment .= '
        <tbody>
          <tr>
            <td style="width: 30%; text-align: left;">' . "ATTENDANCE" . '</td>
            <td style="width: 30%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $attendance . '</td>
            <td style="width: 10%;">' . "OUT OF: " .'</td>
                  <td style="width: 30%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $outOf  . '</td>
          </tr>
                 
          <tr>
             <td style="width: 30%; text-align: left;">' . "CONDUCT" . '</td>
             <td style="width: 70%;margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $conduct . '</td>
          </tr>
                 
          <tr>
             <td style="width: 30%; text-align: left;">' . "INTEREST" . '</td>
             <td style="width: 70%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $interest . '</td>
          </tr>
                 
          <tr>
             <td style="width: 30%; text-align: left;">' . "ATTITUDE" . '</td>
             <td style="width: 70%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $attitude . '</td>
          </tr>
                 
          <tr>
             <td style="width: 30%; text-align: left;">' . "CLASS TEACHER'S REMARK:" . '</td>
             <td style="width: 70%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $classTeacherRemark . '</td>
          </tr>
                 
          <!--<tr>
             <td style="width: 30%; text-align: left;">' . "HEADMASTERS REMARKS" . '</td>
             <td style="width: 70%; margin:5px; border-bottom:2px solid #ddd;padding-bottom:3px;">' . $headRemark . '</td>
          </tr>
          
          <tr>
             <td style="width: 30%; text-align: left;">' . "" . '</td>
             <td style="width: 70%;">' . "" . '</td>
          </tr>
          
          <tr>
             <td style="width: 30%; text-align: left;">' . "" . '</td>
             <td style="width: 70%;">' . "" . '</td>
          </tr>
          
          <tr>
             <td style="width: 30%; text-align: left;">' . "" . '</td>
             <td style="width: 70%;">' . "" . '</td>
          </tr>
          
          <tr>
             <td style="width: 30%; text-align: left;">' . "" . '</td>
             <td style="width: 70%;">' . "" . '</td>
          </tr>-->
          
          <!--<tr>
             <td style="width: 100%; text-align: right;">' . "........................................................" . '</td>
          </tr>
          
          <tr>
             <td style="width: 100%; text-align: right;">' . "HEAD MASTERS/MISTRESS SIGNATURE" . '</td>
          </tr>-->
        </tbody>';

    $pdf->writeHTML($tbl_head_comment . $tbl_comment . $tbl_foot_comment, FALSE, false, true, false, '');

//    $pdf->writeHTML($headingSpace, true, 0, true, 0);
}

$pdf->lastPage();

ob_end_clean();

$pdf->Output();





