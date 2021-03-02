<?php

require_once '../includes/header.php';

class Level {

    public function getLevels() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM levels ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY level_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getLevelById($level_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM levels ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND level_id = '$level_id' ";
        $query .= "ORDER BY level_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($levels = mysqli_fetch_assoc($query_results)) {
            return $levels;
        } else {
            return NULL;
        }
    }

    public function getLevelByURL($url) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM levels ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND url_string = '{$url}' ";
        $query .= "ORDER BY level_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($levels = mysqli_fetch_assoc($query_results)) {
            return $levels;
        } else {
            return NULL;
        }
    }

    public function insertLevel() {
        global $connection;
        $school_number = trim(strip_tags(escape_value($_POST["school_number"])));
        $level_name = trim(strip_tags(ucwords(escape_value($_POST["level_name"]))));

        $random_numbers = generate_id_numbers() . microtime();
        $url_string = md5($random_numbers);

        $query = "INSERT INTO levels (";
        $query .= "url_string, school_number, level_name";
        $query .= ") VALUES (";
        $query .= "'{$url_string}', '{$school_number}', '{$level_name}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("level_on.php");
        }
    }

    public function updateRegion($level_id) {
        global $connection;
        $level_name = trim(ucwords(escape_value($_POST["level_name"])));

        $query = "UPDATE levels SET ";
        $query .= "level_name = '{$level_name}' ";
        $query .= "WHERE level_id = '$level_id' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("level.php");
        }
    }

    public function updateRegionByURL($url) {
        global $connection;

        $level_name = trim(ucwords(escape_value($_POST["level_name"])));

        $query = "UPDATE levels SET ";
        $query .= "level_name = '{$level_name}' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("level.php");
        }
    }

    public function deleteLevel($level_id) {
        global $connection;

        $query = "UPDATE levels SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE level_id = '$level_id' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("level.php");
        }
    }

    public function deleteLevelByURL($url) {
        global $connection;

        $query = "UPDATE levels SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("level.php");
        }
    }

    public function insertLevelSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>NEW LEVEL,</strong> successfully created!.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function editLevelSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>LEVEL,</strong> successfully edited!.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function deleteLevelSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>LEVEL,</strong> successfully deleted!.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
