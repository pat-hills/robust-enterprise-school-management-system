<?php

require_once '../includes/header.php';

class Subject {

    public function getSubjectsBySchoolNumber($school_number) {
        global $connection;

        $escape_school_number = mysqli_real_escape_string($connection, $school_number);

        $query = "SELECT * ";
        $query .= "FROM subjects ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND school_number = '{$escape_school_number}' ";
        $query .= "ORDER BY subject_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getSubjects() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM subjects ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY subject_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getSubjectByName($subject_name) {
        global $connection;
        $escape_subject_name = mysqli_real_escape_string($connection, $subject_name);

        $query = "SELECT * ";
        $query .= "FROM subjects ";
        $query .= "WHERE subject_name = '{$escape_subject_name}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($codes = mysqli_fetch_assoc($query_results)) {
            return $codes;
        } else {
            return NULL;
        }
    }

    public function getSubjectByCode($subject_code) {
        global $connection;
        $escape_subject_code = mysqli_real_escape_string($connection, $subject_code);

        $query = "SELECT * ";
        $query .= "FROM subjects ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND subject_code = '{$escape_subject_code}' ";
        $query .= "ORDER BY subject_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($codes = mysqli_fetch_assoc($query_results)) {
            return $codes;
        } else {
            return NULL;
        }
    }

      public function getSubjectByURL($url) {
        global $connection;
        $escape_subject_url = mysqli_real_escape_string($connection, $url);

        $query = "SELECT * ";
        $query .= "FROM subjects ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND url_string = '{$escape_subject_url}' ";
        $query .= "ORDER BY subject_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($codes = mysqli_fetch_assoc($query_results)) {
            return $codes;
        } else {
            return NULL;
        }
    }
    
    public function deleteSubject($subjectCode) {
        global $connection;

        $query = "UPDATE subjects SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE subject_code = '{$subjectCode}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("subject.php");
        }
    }

    public function deleteSubjectByURL($url) {
        global $connection;

        $query = "UPDATE subjects SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("subject.php");
        }
    }

    
    public function insertSubject() {
        global $connection;

        $subject_code = trim(escape_value($_POST["subject_code"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $subject_name = trim(ucwords(escape_value($_POST["subject_name"])));
        $subject_initials = trim(strtoupper(escape_value($_POST["subject_initials"])));
        $subject_category = trim(ucwords(escape_value($_POST["subject_category"])));

        $random_numbers = generate_id_numbers() . microtime();
        $url_string = md5($random_numbers);

        $getSubjectCode = $this->getSubjectByCode($subject_code);
        if ($subject_code == $getSubjectCode["subject_code"]) {
            $subjectCodeExist = $this->subjectCodeExistBanner();
            echo $subjectCodeExist;
        } else {
            $query = "INSERT INTO subjects (";
            $query .= "url_string, subject_code, school_number, subject_name, subject_initials, subject_category";
            $query .= ") VALUES (";
            $query .= "'{$url_string}', '{$subject_code}', '{$school_number}', '{$subject_name}', '{$subject_initials}', '{$subject_category}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("subject.php");
            }
        }
    }

    public function updateSubject($subjectCode, $schoolNumber) {
        global $connection;
        $subjectCode = mysqli_real_escape_string($connection, $subjectCode);
        $schoolNumber = mysqli_real_escape_string($connection, $schoolNumber);

        $subject_name = trim(ucwords(escape_value($_POST["subject_name"])));
        $subject_initials = trim(strtoupper(escape_value($_POST["subject_initials"])));
        $subject_category = trim(ucwords(escape_value($_POST["subject_category"])));

        $query = "UPDATE subjects SET ";
        $query .= "subject_name = '{$subject_name}', ";
        $query .= "subject_initials = '{$subject_initials}', ";
        $query .= "subject_category = '{$subject_category}' ";
        $query .= "WHERE subject_code = '{$subjectCode}' ";
        $query .= "AND school_number = '{$schoolNumber}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("subject.php");
//            $this->updateSubjectSuccessBanner();
        }
    }

    public function updateSubjectByURL($url, $schoolNumber) {
        global $connection;
        $safe_url = mysqli_real_escape_string($connection, $url);
        $schoolNumber = mysqli_real_escape_string($connection, $schoolNumber);

        $subject_name = trim(ucwords(escape_value($_POST["subject_name"])));
        $subject_initials = trim(strtoupper(escape_value($_POST["subject_initials"])));
        $subject_category = trim(ucwords(escape_value($_POST["subject_category"])));

        $query = "UPDATE subjects SET ";
        $query .= "subject_name = '{$subject_name}', ";
        $query .= "subject_initials = '{$subject_initials}', ";
        $query .= "subject_category = '{$subject_category}' ";
        $query .= "WHERE url_string = '{$safe_url}' ";
        $query .= "AND school_number = '{$schoolNumber}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("subject.php");
//            $this->updateSubjectSuccessBanner();
        }
    }
    
    public function saveSubjectSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>SUBJECT,</strong> successfully saved!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function updateSubjectSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>SUBJECT,</strong> successfully updated!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function subjectCodeExistBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li>Sorry, <strong>SUBJECT CODE</strong> exists. Please, enter a <strong>unique subject code</strong> and select a <strong>SUBJECT CATEGORY</strong> before you click the <strong>SAVE</strong> button.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
