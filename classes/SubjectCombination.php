<?php

require_once '../includes/header.php';

class SubjectCombination {

    public function getSubjectCombinationBySchoolNumber($school_number) {
        global $connection;

        $escape_school_number = mysqli_real_escape_string($connection, $school_number);

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND school_number = '{$escape_school_number}' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getSubjectCombinationByName($name) {
        global $connection;

        $name = mysqli_real_escape_string($connection, $name);

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE subject_combination_name = '{$name}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getSubjectCombination($name) {
        global $connection;

        $name = mysqli_real_escape_string($connection, $name);

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE subject_combination_name = '{$name}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    

    public function getClassSubjectCombination($class) {
        global $connection;

        $name = mysqli_real_escape_string($connection, $class);

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE class_name = '{$class}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getSubjectCombinationById($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE subject_combination_id = '$id' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getSubjectCombinationByCombinationId($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE subject_combination_id = '$id' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getSubjectCombinationByURL($url) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM subject_combination ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY subject_combination_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function insertSubjectCombination() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $subject_combination_name = trim(ucwords(escape_value($_POST["subject_combination_name"])));
        $class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $subject_name = implode("**", $_POST["subject_name"]);

        $random_numbers = generate_id_numbers() . microtime();
        $url_string = md5($random_numbers);

        $getSubjectCombination = $this->getSubjectCombination($subject_combination_name);

        if ($getSubjectCombination["subject_combination_name"] === $subject_combination_name) {
            $this->subjectCombinationExistBanner();
        } else {
            $query = "INSERT INTO subject_combination (";
            $query .= "url_string, school_number, subject_combination_name, class_name, subject_name";
            $query .= ") VALUES (";
            $query .= "'{$url_string}', '{$school_number}', '{$subject_combination_name}', '{$class_name}', '{$subject_name}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if (!$query_result) {
//            die("Failed to persist subject combination!");
                $this->subjectCombinationErrorBanner();
            } else {
                redirect_to("subject_combination.php");
            }
        }
    }

    public function updateSubjectCombination($url) {
        global $connection;

        $subject_combination_name = trim(ucwords(escape_value($_POST["subject_combination_name"])));
        $class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $subject_name = implode("**", $_POST["subject_name"]);

        $query = "UPDATE subject_combination SET ";
        $query .= "subject_combination_name = '{$subject_combination_name}', ";
        $query .= "class_name = '{$class_name}', ";
        $query .= "subject_name = '{$subject_name}' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("subject_combination.php");
        }
    }

    public function deleteSubjectCombination($url) {
        global $connection;

        $query = "UPDATE subject_combination SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("subject_combination.php");
        }
    }

    public function subjectCombinationErrorBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>SUBJECT COMBINATION</strong> failed to saved! Contact the <strong>System Administrator</strong></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function subjectCombinationExistBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li>Sorry, <strong>SUBJECT COMBINATION</strong> exists!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
