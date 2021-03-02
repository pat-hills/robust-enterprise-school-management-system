<?php

require_once '../includes/header.php';

class AcademicYear {

    public function insertAcademicYear() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $begin_date = trim(escape_value(date("Y-m-d", strtotime($_POST["begin_date"]))));
        $end_date = trim(escape_value(date("Y-m-d", strtotime($_POST["end_date"]))));
        $academic_year = trim(escape_value(date("Y", strtotime($begin_date)))) . "/" . trim(escape_value(date("Y", strtotime($end_date))));

        $random_numbers = generate_id_numbers() . microtime();
        $url_string = md5($random_numbers);

        $query = "INSERT INTO academic_year (";
        $query .= "school_number, url_string, academic_year, begin_date, end_date";
        $query .= ") VALUES (";
        $query .= "'{$school_number}', '{$url_string}', '{$academic_year}', '{$begin_date}', '{$end_date}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("academic_term.php");
        }
    }

    public function updateAcademicYear($academicYearId) {
        global $connection;
        $academicYearId = mysqli_real_escape_string($connection, $academicYearId);

        $begin_date = trim(escape_value(date("Y-m-d", strtotime($_POST["begin_date"]))));
        $end_date = trim(escape_value(date("Y-m-d", strtotime($_POST["end_date"]))));
        $active = trim(escape_value($_POST["active"]));

        $sql = "UPDATE academic_year SET ";
        $sql .= "active = 'NO' ";
        $sql .= "WHERE active = 'YES'";

        $result = mysqli_query($connection, $sql);

        if ($result) {
            $query = "UPDATE academic_year SET ";
            $query .= "begin_date = '$begin_date', ";
            $query .= "end_date = '$end_date', ";
            $query .= "active = '{$active}' ";
            $query .= "WHERE academic_year_id = '$academicYearId' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("academic_year.php");
            }
        }
    }

    public function updateAcademicYearByURL($url) {
        global $connection;
        $safe_url = mysqli_real_escape_string($connection, $url);

        $begin_date = trim(escape_value(date("Y-m-d", strtotime($_POST["begin_date"]))));
        $end_date = trim(escape_value(date("Y-m-d", strtotime($_POST["end_date"]))));
        $active = trim(escape_value($_POST["active"]));

        $sql = "UPDATE academic_year SET ";
        $sql .= "active = 'NO' ";
        $sql .= "WHERE active = 'YES'";

        $result = mysqli_query($connection, $sql);

        if ($result) {
            $query = "UPDATE academic_year SET ";
            $query .= "begin_date = '$begin_date', ";
            $query .= "end_date = '$end_date', ";
            $query .= "active = 'YES' ";
            $query .= "WHERE url_string = '$safe_url' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("academic_year.php");
            }
        }
    }
    
    public function deleteAcademicYear($academicYearId) {
        global $connection;
        $academicYearId = mysqli_real_escape_string($connection, $academicYearId);

        $query = "UPDATE academic_year SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE academic_year_id = '$academicYearId' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("academic_year.php");
        }
    }

     public function deleteAcademicYearByURL($url) {
        global $connection;
        $safe_url = mysqli_real_escape_string($connection, $url);

        $query = "UPDATE academic_year SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE url_string = '$safe_url' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("academic_year.php");
        }
    }
    
    public function getAcademicYearDetails() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM academic_year ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY academic_year_id DESC ";
        $query .= "LIMIT 2";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getLastAcademicYearDetails() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM academic_year ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY academic_year_id DESC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getActivatedAcademicYear($academic_year) {
        global $connection;

        $academic_year = mysqli_real_escape_string($connection, $academic_year);

        $query = "SELECT * ";
        $query .= "FROM academic_year ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND active = 'YES' ";
        $query .= "AND academic_year = '{$academic_year}' ";
        $query .= "ORDER BY begin_date DESC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getAcademicYear() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM academic_year ";
        $query .= "WHERE active = 'YES' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY begin_date DESC";

        $query_results = mysqli_query($connection, $query) or die(mysql_error());
//        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getAcademicYearDetailsById($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM academic_year ";
        $query .= "WHERE academic_year_id = '$id' ";
        $query .= "ORDER BY begin_date DESC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($academicYearId = mysqli_fetch_assoc($query_results)) {
            return $academicYearId;
        } else {
            return NULL;
        }
    }

    public function getAcademicYearDetailsByURL($url) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM academic_year ";
        $query .= "WHERE url_string = '$url' ";
        $query .= "ORDER BY begin_date DESC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($academicYearId = mysqli_fetch_assoc($query_results)) {
            return $academicYearId;
        } else {
            return NULL;
        }
    }
    
    public function deleteAcademicYearSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>ACADEMIC YEAR, </strong> successfully <strong>DELETED!</strong></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function editAcademicYearSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>ACADEMIC YEAR, </strong> successfully <strong>EDITED!</strong></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

//    public function updateAcademicYear($academic_year_id) {
//        global $connection;
//
//        if (isset($_POST["submit"])) {
//            $begin_date = trim(escape_value(date("Y-m-d", strtotime($_POST["begin_date"]))));
//            $end_date = trim(escape_value(date("Y-m-d", strtotime($_POST["end_date"]))));
//
//            $query = "UPDATE academic_year SET ";
//            $query .= "begin_date = '{$begin_date}', ";
//            $query .= "end_date = '{$end_date}' ";
//            $query .= "WHERE academic_year_id = '$academic_year_id' ";
//            $query .= "AND deleted = 'NO' ";
//            $query .= "LIMIT 1";
//
//            $query_result = mysqli_query($connection, $query);
//            confirm_query($query_result);
//
//            $_SESSION["academic_year_id"] = NULL;
//
//            if ($query_result) {
//                redirect_to("edit_academic_year_success.php");
//            }
//        }
//    }
}
