<?php

require_once '../includes/header.php';

class House {

    public function getHouses() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM houses ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getHouseById($url) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM houses ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND url_string = '{$url}' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($house = mysqli_fetch_assoc($query_results)) {
            return $house;
        } else {
            return NULL;
        }
    }

    public function insertHouse() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $name = trim(ucwords(escape_value($_POST["name"])));
        $houseHead = trim(ucwords(escape_value($_POST["houseHead"])));
        $gender = trim(ucwords(escape_value($_POST["gender"])));

        $query = "INSERT INTO houses (";
        $query .= "school_number, name, houseHead, gender";
        $query .= ") VALUES (";
        $query .= "'{$school_number}', '{$name}', '{$houseHead}', '{$gender}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("house.php");
        }
    }

    public function updateHouse($url) {
        global $connection;

        $name = trim(ucwords(escape_value($_POST["name"])));
        $houseHead = trim(ucwords(escape_value($_POST["houseHead"])));
        $gender = trim(ucwords(escape_value($_POST["gender"])));

        $query = "UPDATE houses SET ";
        $query .= "name = '{$name}', ";
        $query .= "houseHead = '{$houseHead}', ";
        $query .= "gender = '{$gender}' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("house.php");
        }
    }

    public function deleteHouse($url) {
        global $connection;

        $query = "UPDATE houses SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("house.php");
        }
    }

    public function houseSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>HOUSE DETAILS,</strong> successfully saved!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function editHouseSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>HOUSE DETAILS,</strong> successfully edited!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function deleteHouseSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>HOUSE DETAILS,</strong> successfully deleted!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
