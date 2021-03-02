<?php

require_once '../includes/header.php';
require_once '../classes/AcademicTerm.php';

class Comment {

    public function checkCommment() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE pupil_id NOT IN (";
        $query .= "SELECT pupil_id ";
        $query .= "FROM class_teacher_comments ";
        $query .= "WHERE deleted = 'NO'";
        $query .= ")";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY other_names ASC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getConducts($school_number) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM report_comment ";
        $query .= "WHERE type = 'Conduct' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY comment ASC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getInterests($school_number) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM report_comment ";
        $query .= "WHERE type = 'Interest' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY comment ASC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getAttitudes($school_number) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM report_comment ";
        $query .= "WHERE type = 'Attitude' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY comment ASC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function getRemarks($school_number) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM report_comment ";
        $query .= "WHERE type = 'Remarks' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY comment ASC";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function insertComment() {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $school_number = trim(escape_value($_POST["school_number"]));
        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $attendance = (int) trim(escape_value($_POST["attendance"]));
        $total_attendance = (int) trim(escape_value($_POST["out_of"]));
        $conduct = trim(ucfirst(escape_value($_POST["conduct"])));
        $interest = trim(ucfirst(escape_value($_POST["interest"])));
        $attitude = trim(ucfirst(escape_value($_POST["attitude"])));
        $remark = trim(ucfirst(escape_value($_POST["remark"])));

        $check = $this->getClassTeacherComments($pupil_id, $school_number);
        if ($check["pupil_id"] === $pupil_id) {
            $this->updateClassTeacherComment($attendance, $conduct, $interest, $attitude, $remark, $total_attendance, $pupil_id, $school_number, $get_academic_term);
        } else {
            $query = "INSERT INTO class_teacher_comments (";
            $query .= "school_number, pupil_id, attendance, out_of, conduct, interest, attitude, remark, academic_term";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '{$pupil_id}', '$attendance', '$total_attendance', '{$conduct}', '{$interest}', '{$attitude}', '{$remark}', '{$get_academic_term}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $query = "UPDATE class_membership SET ";
                $query .= "comment = 'YES' ";
                $query .= "WHERE pupil_id = '{$pupil_id}' ";
                $query .= "AND academic_term = '{$get_academic_term}' ";
                $query .= "AND deleted = 'NO' ";
                $query .= "LIMIT 1";

                $query_result2 = mysqli_query($connection, $query);
                confirm_query($query_result);

                if ($query_result2 && mysqli_affected_rows($connection) >= 0) {
                    redirect_to("class_teacher_students.php");
                }
            }
        }
    }

    public function insertHeadComment() {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $school_number = trim(escape_value($_POST["school_number"]));
        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $remark = trim(ucfirst(escape_value($_POST["remark"])));

        $check = $this->getHeadRemark($pupil_id, $school_number);
        if ($check["pupil_id"] === $pupil_id) {
            $this->updateHeadRemark($remark, $pupil_id, $school_number);
        } else {
            $query = "INSERT INTO head_teacher_comments (";
            $query .= "school_number, pupil_id, remark, academic_term";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '{$pupil_id}', '{$remark}', '{$get_academic_term}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $query = "UPDATE class_membership SET ";
                $query .= "head_comment = 'YES' ";
                $query .= "WHERE pupil_id = '{$pupil_id}' ";
                $query .= "AND academic_term = '{$get_academic_term}' ";
                $query .= "AND deleted = 'NO' ";
                $query .= "LIMIT 1";

                $query_result2 = mysqli_query($connection, $query);
                confirm_query($query_result);

                if ($query_result2 && mysqli_affected_rows($connection) >= 0) {
                    redirect_to("list_students.php");
                }
            }
        }
    }

    public function updateHeadRemark($remark, $pupil_id, $school_number) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "UPDATE head_teacher_comments SET ";
        $query .= "remark = '{$remark}' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE class_membership SET ";
            $query .= "head_comment = 'YES' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "AND academic_term = '{$get_academic_term}' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $this->updateHeadRemarkSuccessBanner();
            }
        }
    }

    public function updateClassTeacherComment($attendance, $conduct, $interest, $attitude, $remark, $total_attendance, $pupil_id, $school_number, $academic_term) {
        global $connection;

        $query = "UPDATE class_teacher_comments SET ";
        $query .= "attendance = '{$attendance}', ";
        $query .= "conduct = '{$conduct}', ";
        $query .= "interest = '{$interest}', ";
        $query .= "attitude = '{$attitude}', ";
        $query .= "remark = '{$remark}', ";
        $query .= "out_of = '{$total_attendance}' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND academic_term = '{$academic_term}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE class_membership SET ";
            $query .= "comment = 'YES' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "AND academic_term = '{$academic_term}' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $this->updateClassTeacherCommentSuccessBanner();
            }
        }
    }

    public function getHeadRemark($pupil_id, $school_number) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM head_teacher_comments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getClassTeacherComments($pupil_id, $school_number) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM class_teacher_comments ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND school_number = '{$school_number}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function insertCommentType() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $comment = trim(ucwords(escape_value($_POST["comment"])));
        $type = trim(ucwords(escape_value($_POST["type"])));

        $query = "INSERT INTO report_comment (";
        $query .= "school_number, comment, type";
        $query .= ") VALUES (";
        $query .= "'{$school_number}', '{$comment}', '{$type}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("comment_on.php");
//            $this->saveCommentTypeSuccessBanner();
        }
    }

    public function insertTerminalReportSettings() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $academic_term = trim(escape_value($_POST["academic_term"]));
        $total_attendance = (int) trim(escape_value($_POST["total_attendance"]));
        $class_score_point = (double) trim(escape_value($_POST["class_score_point"]));
        $exam_score_point = (double) trim(escape_value($_POST["exam_score_point"]));
        $class_total_mark = (double) trim(escape_value($_POST["class_total_mark"]));
        $exam_total_mark = (double) trim(escape_value($_POST["exam_total_mark"]));

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        if ($academic_term === $get_academic_term) {
            $query = "UPDATE terminal_report_settings SET ";
            $query .= "total_attendance = '$total_attendance', ";
            $query .= "class_score_point = '$class_score_point', ";
            $query .= "exam_score_point = '$exam_score_point', ";
            $query .= "class_total_mark = '$class_total_mark', ";
            $query .= "exam_total_mark = '$exam_total_mark' ";
            $query .= "WHERE academic_term = '{$get_academic_term}' ";
            $query .= "AND school_number = '{$school_number}' ";
            $query .= "AND deleted = 'NO'";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result && mysqli_affected_rows($connection) >= 0) {
                $this->updateTerminalSettingsSuccessBanner();
            }
        } else {
            $query = "INSERT INTO terminal_report_settings (";
            $query .= "school_number, total_attendance, class_score_point, exam_score_point, class_total_mark, exam_total_mark, academic_term";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '$total_attendance', '$class_score_point', '$exam_score_point', '$class_total_mark', '$exam_total_mark', '{$get_academic_term}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("terminal_report_setting_on.php");
//                $this->saveTerminalSettingsSuccessBanner();
            }
        }
    }

    public function getTerminalSettings($school_number) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "SELECT * ";
        $query .= "FROM terminal_report_settings ";
        $query .= "WHERE school_number = '{$school_number}' ";
        $query .= "AND academic_term = '{$get_academic_term}' ";
        $query .= "AND deleted = 'NO'";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function saveCommentTypeSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>COMMENT,</strong> successfully saved!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function updateHeadRemarkSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>HEAD TEACHER REMARK,</strong> successfully updated!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function updateClassTeacherCommentSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>TERMINAL REPORT COMMENTS,</strong> successfully updated!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function saveTerminalSettingsSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>TERMINAL REPORT SETTINGS,</strong> successfully saved!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function updateTerminalSettingsSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>TERMINAL REPORT SETTINGS,</strong> successfully updated!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
