<?php

require_once '../includes/header.php';

class Region {

    public function getRegions() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM regions ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getRegionById($region_id) {
        global $connection;

        $region_id = mysqli_real_escape_string($connection, $region_id);

        $query = "SELECT * ";
        $query .= "FROM regions ";
        $query .= "WHERE region_id = '$region_id' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY name ASC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($region = mysqli_fetch_assoc($query_results)) {
            return $region;
        } else {
            return NULL;
        }
    }

      public function getRegionByURL($url) {
        global $connection;

        $safe_url = mysqli_real_escape_string($connection, $url);

        $query = "SELECT * ";
        $query .= "FROM regions ";
        $query .= "WHERE url_string = '{$safe_url}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY name ASC ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($region = mysqli_fetch_assoc($query_results)) {
            return $region;
        } else {
            return NULL;
        }
    }
    
    public function insertRegion() {
        global $connection;
        $name = trim(ucwords(escape_value($_POST["name"])));

        $query = "INSERT INTO regions (";
        $query .= "name";
        $query .= ") VALUES (";
        $query .= "'{$name}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("region.php");
        }
    }

    public function updateRegion($region_id) {
        global $connection;
        $name = trim(ucwords(escape_value($_POST["name"])));

        $query = "UPDATE regions SET ";
        $query .= "name = '{$name}' ";
        $query .= "WHERE region_id = '$region_id' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("region.php");
        }
    }
    
    public function updateRegionByURL($url) {
        global $connection;
        $name = trim(ucwords(escape_value($_POST["name"])));

        $query = "UPDATE regions SET ";
        $query .= "name = '{$name}' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("region.php");
        }
    }

    public function deleteRegion($region_id) {
        global $connection;

        $query = "UPDATE regions SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE region_id = '$region_id' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("region.php");
        }
    }

    public function deleteRegionByURL($url) {
        global $connection;

        $query = "UPDATE regions SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE url_string = '{$url}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("region.php");
        }
    }
    
    public function saveRegionSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>NEW REGION</strong>, successfully created!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
