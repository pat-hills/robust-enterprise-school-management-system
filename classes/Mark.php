<?php

require_once '../includes/header.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Comment.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Subject.php';

class Mark {

    public function insertMarks() {
        global $connection;

        $pupil_id = $_POST["pupil_id"];
        $class = trim(escape_value($_POST["class"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $subject = trim(escape_value($_POST["subject"]));
        $exam_scores = $_POST["exam_scores"];

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $studentTotal = count($pupil_id);
        for ($i = 0; $i < $studentTotal; $i++) {
            $query = "INSERT INTO marks (";
            $query .= "pupil_id, class, school_number, subject, exam_scores, academic_term";
            $query .= ") VALUES (";
            $query .= "'{$pupil_id[$i]}', '{$class}', '{$school_number}', '{$subject}', '$exam_scores[$i]', '{$get_academic_term}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);
        }
        if ($query_result) {
            $this->enterMarksSuccessBanner();
        }
    }

    public function updateMarks() {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $pupil_id = $_POST["pupil_id"];
        $exam_scores = $_POST["exam_scores"];
        $subject = trim(escape_value($_POST["subject"]));
        $academic_term = trim(escape_value($_POST["academic_term"]));

        if ($get_academic_term === $academic_term) {
            $studentTotal = count($pupil_id);
            for ($i = 0; $i < $studentTotal; $i++) {
                $query = "UPDATE marks SET ";
                $query .= "exam_scores = '$exam_scores[$i]' ";
                $query .= "WHERE academic_term = '{$academic_term}' ";
                $query .= "AND subject = '{$subject}' ";
                $query .= "AND pupil_id = '{$pupil_id[$i]}' ";
                $query .= "AND deleted = 'NO' ";
                $query .= "LIMIT 1";

                $query_result = mysqli_query($connection, $query);
                confirm_query($query_result);
            }
        }

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $this->updateMarksSuccessBanner();
        }
    }
    
    
    public function gradingSystem(){
        
        $institution = new InstitutionDetail();
        $get_school_number = $institution->getInstitutionDetails();
        
        $school_number = $get_school_number["school_number"];
        $query = "SELECT * FROM exam_grade WHERE deleted = 'NO' AND updated = 'NO' AND school_number='$school_number'";
        $result_query = mysql_query($query) or die(mysql_error());
        
        if($result_query){
            if(mysql_num_rows($result_query)>0){
                while ($row = mysql_fetch_array($result_query)) {
                    return $row;
                }
            }
        }  else {
            
        
            return NULL;
        }
        
    }
    
    
    

    public function replaceMarks() {
        global $connection;

        $pupil_id = $_POST["pupil_id"];
        $subject = trim(escape_value($_POST["subject"]));
        $academic_term = trim(escape_value($_POST["academic_term"]));

        $studentTotal = count($pupil_id);
        for ($i = 0; $i < $studentTotal; $i++) {
            $query = "DELETE FROM marks ";
            $query .= "WHERE academic_term = '{$academic_term}' ";
            $query .= "AND subject = '{$subject}' ";
            $query .= "AND pupil_id = '{$pupil_id[$i]}' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);
        }

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $this->insertMarks();
        }
    }

    public function  replaceContinuousAssessmentMarks() {
        global $connection;

        $pupil_id = $_POST["pupil_id"];
        $subject = trim(escape_value($_POST["subject"]));
        $academic_term = trim(escape_value($_POST["academic_term"]));

        $studentTotal = count($pupil_id);
        for ($i = 0; $i < $studentTotal; $i++) {
            $query = "DELETE FROM continuous_assessments ";
            $query .= "WHERE academic_term = '{$academic_term}' ";
            $query .= "AND subject = '{$subject}' ";
            $query .= "AND pupil_id = '{$pupil_id[$i]}' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);
        }

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $this->insertContinuousAssessmentMarks();
        }
    }

    public function insertContinuousAssessmentMarks() {
        
        
        global $connection;
        
       $grading = $this->gradingSystem();
         

        $pupil_id = $_POST["pupil_id"];
        $class = trim(escape_value($_POST["class"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $subject = trim(escape_value($_POST["subject"]));
       // $indiv_test = $_POST["indiv_test"];
       // $group_work = $_POST["group_work"];
        $class_test = $_POST["class_test"];
        //$project_work = $_POST["project_work"];
        $exam_work = $_POST["exam_work"];

        $academicTerm = new AcademicTerm();
        $subjectName = new Subject();

        $getSubjectByName = $subjectName->getSubjectByName($subject);
        if ($getSubjectByName["subject_category"] === "Core") {
            $category = "Core";
        } else {
            $category = "Elective";
        }

        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $institutionDetail = new InstitutionDetail();
        $comment = new Comment();

        $institution_set = $institutionDetail->getInstitutionDetails();
        $getTerminalSettings = $comment->getTerminalSettings($institution_set["school_number"]);

        $studentTotal = count($pupil_id);
        for ($i = 0; $i < $studentTotal; $i++) {
            $totalClassWork = 0;

            $totalClass = $totalClassWork += $class_test[$i];

            $thirty_percent = number_format((($totalClass / $getTerminalSettings["class_total_mark"]) * $getTerminalSettings["class_score_point"]), 0, ".", ",");
            $seventy_percent = number_format((($exam_work[$i] / $getTerminalSettings["exam_total_mark"]) * $getTerminalSettings["exam_score_point"]), 0, ".", ",");

            $total = number_format(($thirty_percent + $seventy_percent), 0, ".", ",");
          
                
//            if ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//               $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } elseif ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//                $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } elseif ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//                  $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } elseif ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//               $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } elseif ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//               $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } elseif ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//                 $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } elseif ($total >= $grading["grade_lower_bound"] && $total <= $grading["grade_upper_bound"]) {
//               $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            } else {
//            $grade = $grading["exam_grade_id"];
//                $remark =  $grading["grade_description"];
//            }
            
            
             if ($total >= 90 && $total <= 100) {
               $grade = "A1";
                $remark =  "Excellent";
            } elseif ($total >= 80 && $total <= 89) {
                $grade = "B2";
                $remark = "Very Good";
            } elseif ($total >= 70 && $total <= 79) {
                  $grade = "B3";
                $remark =  "Good";
            } elseif ($total >= 65 && $total <= 69) {
               $grade = "C4";
                $remark = "Credit";
            } elseif ($total >= 60 && $total <= 64) {
               $grade = "C5";
                $remark = "Credit";
            } elseif ($total >= 55 && $total <= 59) {
                 $grade = "C6";
                $remark = "Credit";
            } elseif ($total >= 50 && $total <= 54) {
               $grade = "D7";
                $remark = "Pass";
            }
             elseif ($total >= 45 && $total <= 49) {
               $grade = "E8";
                $remark = "Pass";
            }
            
            else {
            $grade = "F9";
                $remark =  "Fail";
            }
            

            $query = "INSERT INTO continuous_assessments (";
            $query .= "pupil_id, class, school_number, subject, category, class_test, class_assessment, exam_work, thirty_percent, seventy_percent, total, grade, remark, academic_term";
            $query .= ") VALUES (";
            $query .= "'{$pupil_id[$i]}', '{$class}', '{$school_number}', '{$subject}', '{$category}', '$class_test[$i]', '$totalClass', '$exam_work[$i]', '$thirty_percent', '$seventy_percent', '$total', '{$grade}', '{$remark}', '{$get_academic_term}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);
            
        }

//        if ($query_result) {
//            for ($i = 0; $i < $studentTotal; $i++) {
//                $query2 = "DELETE FROM subject_positions ";
//                $query2 .= "WHERE academic_term = '{$get_academic_term}' ";
//                $query2 .= "AND subject = '{$subject}' ";
//                $query2 .= "AND class_name = '{$class}' ";
//                $query2 .= "AND deleted = 'NO'";
//
//                $query_result2 = mysqli_query($connection, $query2);
//                confirm_query($query_result2);
//            }

            if ($query_result) {
                
                $x = 1;
                $y = 0;
                $prev = "";
                $getSubjectTotalScores = $this->getContinuousAssessmentTotals($class, $subject, $get_academic_term);
                foreach ($getSubjectTotalScores as $subjectTotal) {
                    if ($prev == $subjectTotal["total"]) {
                        $position = $y;
                        $subjectPosition = $position;
                    } else {
                        $position = $x;
                        $subjectPosition = $position;
                        $y = $x;
                    }
                    $prev = $subjectTotal["total"];
                    $x++;

                   $query3 = "INSERT INTO subject_positions (";
                    $query3 .= "school_number, pupil_id, class_name, subject, total, position, academic_term";
                    $query3 .= ") VALUES (";
                    $query3 .= "'{$school_number}', '{$subjectTotal["pupil_id"]}', '{$class}', '{$subject}', '{$subjectTotal["total"]}', '{$subjectPosition}', '{$get_academic_term}'";
                    $query3 .= ")";

                    $query3 = "UPDATE continuous_assessments SET ";
                    $query3 .= "position = '{$subjectPosition}' "; //This column in table "continous_assessments" was missing and always giving query failed
                    $query3 .= "WHERE academic_term = '{$get_academic_term}' ";
                    $query3 .= "AND subject = '{$subject}' ";
                    $query3 .= "AND pupil_id = '{$subjectTotal["pupil_id"]}' ";
                    $query3 .= "LIMIT 1";

                    $query_result3 = mysqli_query($connection, $query3);
                    confirm_query($query_result3);
                }
            }

            if ($query_result3) {
                $this->enterAssessmentMarksSuccessBanner();
            }
//        }
    }

    public function getSubjectPositionByStudentID($studentID, $subject, $academic_term) {
        global $connection;

        $safe_studentID = escape_value($studentID);
        $safe_subject = escape_value($subject);
        $safe_academic_term = escape_value($academic_term);

        $query = "SELECT position ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_studentID}' ";
        $query .= "AND subject = '{$safe_subject}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getSubjectTotalScores($class, $subject, $academic_term) {
        global $connection;

        $safe_class = escape_value($class);
        $safe_subject = escape_value($subject);
        $safe_academic_term = escape_value($academic_term);

        $query = "SELECT * ";
        $query .= "FROM subject_positions ";
        $query .= "WHERE class = '{$safe_class}' ";
        $query .= "AND subject = '{$safe_subject}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY total DESC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getContinuousAssessmentTotals($class, $subject, $academic_term) {
        global $connection;

        $safe_class = escape_value($class);
        $safe_subject = escape_value($subject);
        $safe_academic_term = escape_value($academic_term);

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$safe_class}' ";
        $query .= "AND subject = '{$safe_subject}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY total DESC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getMarksById($pupil_id, $subject) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM marks ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND subject = '{$subject}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function getContinuousAssessmentMarksById($pupil_id, $subject,$academic_term) {
        global $connection;
        
        
         $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND subject = '{$subject}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function getMarksByClass($class) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(total) AS classAverageMark ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$class}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getTotalScore($class, $pupil_id, $academicTerm) {
        global $connection;

        $safe_pupil_id = mysqli_real_escape_string($connection, $pupil_id);
        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_academic_term = mysqli_real_escape_string($connection, $academicTerm);

        $query = "SELECT *, SUM(total) AS totalScore ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_pupil_id}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY totalScore DESC ";
//        $query .= "GROUP BY pupil_id";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getClassTotalScores($class, $academic_term) {
        global $connection;

        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);

        $query = "SELECT *, SUM(total) AS totalScores ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id ";
        $query .= "ORDER BY totalScores DESC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getClassTotalScoresByID($class, $studentID, $academic_term) {
        global $connection;

        $safe_class = escape_value($class);
        $safe_studentID = escape_value($studentID);
        $safe_academic_term = escape_value($academic_term);

        $query = "SELECT *, SUM(total) AS totalScores ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_studentID}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id ";
        $query .= "ORDER BY totalScores DESC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getSubjectScores($class, $pupil_id, $subject, $academicTerm) {
      //  global $connection;

        $safe_pupil_id = escape_value($pupil_id);
        $safe_class = escape_value($class);
        $safe_subject = escape_value($subject);
        $safe_academicTerm = escape_value($academicTerm);

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_pupil_id}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$safe_academicTerm}' ";
        $query .= "AND subject = '{$safe_subject}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY total DESC";
//        $query .= "GROUP BY pupil_id";

        $result_set = mysql_query($query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getTotalScoresByStudentID($class, $academic_term, $pupil_id) {
        global $connection;

        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);
        $safe_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT *, SUM(total) AS totalScores ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_pupil_id}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id ";
//        $query .= "ORDER BY totalScores DESC";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getTerminalReports($academic_term, $class) {
        global $connection;

        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);
        $safe_class = mysqli_real_escape_string($connection, $class);

        $query = "SELECT *, SUM(total) AS overallScore ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE academic_term = '{$safe_academic_term}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND deleted = 'NO'";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getTerminalReport($academic_term, $class, $classTotal) {
        global $connection;

        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);
        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_class_total = mysqli_real_escape_string($connection, $classTotal);
        $studentTotal = $safe_class_total;

//        $query = "SELECT * ";
        $query = "SELECT *, SUM(total) AS totalScores ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE academic_term = '{$safe_academic_term}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id ";
        $query .= "ORDER BY totalScores DESC ";
        $query .= "LIMIT $studentTotal";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getTerminalReportData($academic_term, $class, $classTotal) {
        global $connection;

        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);
        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_class_total = mysqli_real_escape_string($connection, $classTotal);
        $studentTotal = $safe_class_total;

        $query = "SELECT *, SUM(total) AS totalScores ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE academic_term = '{$safe_academic_term}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY totalScores DESC ";
        $query .= "LIMIT $studentTotal";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getTerminalReportDetails($academic_term, $class, $pupil_id) {
        global $connection;

        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);
        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_pupil_id}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject ASC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getTerminalReportDetail($academic_term, $class, $pupil_id) {
        global $connection;

        $safe_academic_term = mysqli_real_escape_string($connection, $academic_term);
        $safe_class = mysqli_real_escape_string($connection, $class);
        $safe_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$safe_pupil_id}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND class = '{$safe_class}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY total DESC ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getMarks($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT SUM(total) AS totalMarks ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getClassSubjects($class, $academic_term) {
        global $connection;

        $safe_class = escape_value($class);
        $safe_academic_term = escape_value($academic_term);

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$safe_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getContinuousAssessmentDetails($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject, total DESC";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;

//        if ($row = mysqli_fetch_assoc($result_set)) {
//            return $row;
//        } else {
//            return NULL;
//        }
    }

    public function getSubjectTotals($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT SUM(total) AS overall ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY total DESC";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;

//        if ($row = mysqli_fetch_assoc($result_set)) {
//            return $row;
//        } else {
//            return NULL;
//        }
    }

    public function getTotals($class) {
        global $connection;

        $safe_class = mysqli_real_escape_string($connection, $class);

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT pupil_id, SUM(total) AS overAll ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$safe_class}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id ";
        $query .= "ORDER BY total DESC";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getRankingsForSubjects($class, $subject, $pupil_id, $academic_term) {
        global $connection;
        $escape_class = escape_value($class);
        $escape_subject = escape_value($subject);
        $escape_pupil_id = escape_value($pupil_id);
        $escape_academic_term = escape_value($academic_term);

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$escape_pupil_id}' ";
        $query .= "AND class = '{$escape_class}' ";
        $query .= "AND subject = '{$escape_subject}' ";
        $query .= "AND academic_term = '{$escape_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "GROUP BY pupil_id ";
        $query .= "ORDER BY total DESC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getMarksDetails($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

//        return $result_set;

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getClassMarks($pupil_id, $subject) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND subject = '{$subject}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection,$query);
       confirm_query($result_set);

//        if ($row = mysqli_fetch_assoc($result_set)) {
//            return $row;
//        } else {
//            return NULL;
//        }
        
        if($result_set){
            if(mysqli_num_rows($result_set)>0){
                while ($row = mysqli_fetch_assoc($result_set)) {
                    return $row;
                }
            }
        }
    }

    public function getExamWorkAverage($class) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(exam_work) AS totalExamMarkAverage ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$class}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "GROUP BY class";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getStudentExamAverage($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(exam_work) AS studentExamAverage ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "GROUP BY class";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getClassAverageMark($class) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(class_work + group_work + class_test) AS classAverageMark ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE class = '{$class}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getStudentClassAverageMark($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(class_work + group_work + class_test) AS studentClassAverageMark ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getStudentClassWorkAverage($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(class_work) AS studentClassWorkAverage ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getStudentGroupWorkAverage($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(group_work) AS studentGroupWorkAverage ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getStudentClassTestAverage($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT AVG(class_test) AS studentClassTestAverage ";
        $query .= "FROM continuous_assessments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getMarksByPupilIdAndSubjectAndAcademicTermId($pupil_id, $subject, $academic_term) {
        global $connection;

        $query = "  SELECT * ";
        $query .= "FROM marks ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND subject = '{$subject}' ";
        $query .= "AND academic_term = '{$academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function enterMarksSuccessBanner() {
        echo "

        <div class = 'row'>";
        echo "<div class = 'span12'>";
        echo "<div class = 'alert alert-success'>";
        echo "<button type = 'button' class = 'close' data-dismiss = 'alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>STUDENT MARKS</strong>, successfully saved!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>

        ";
    }

    public function enterAssessmentMarksSuccessBanner() {
        echo "<div class = 'row'>";
        echo "<div class = 'span12'>";
        echo "<div class = 'alert alert-success'>";
        echo "<button type = 'button' class = 'close' data-dismiss = 'alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>CONTINUOUS ASSESSMENT MARKS</strong>, successfully saved!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>

        ";
    }

    public function updateMarksSuccessBanner() {
        echo "<div class = 'row'>";
        echo "<div class = 'span12'>";
        echo "<div class = 'alert alert-info'>";
        echo "<button type = 'button' class = 'close' data-dismiss = 'alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>STUDENT MARKS</strong>, successfully edited!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>

        ";
    }

    public function marksEnteredSuccessBanner() {
        echo "<div class = 'row'>";
        echo "<div class = 'span12'>";
        echo "<div class = 'alert alert-error'>";
        echo "<button type = 'button' class = 'close' data-dismiss = 'alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>STUDENT MARKS</strong>, already entered!</li>";
        echo "<li>To edit <strong>STUDENTS' MARKS</strong>, click the <strong>Edit marks</strong> button</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
