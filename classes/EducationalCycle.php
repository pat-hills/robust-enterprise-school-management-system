<?php

require_once '../includes/header.php';

class EducationalCycle {

    public function insertEducationalCycle() {
        global $connection;
        $school_number = trim(escape_value($_POST["school_number"]));
        $name = trim(ucwords(escape_value($_POST["name"])));

        $query = "INSERT INTO educational_cycle (";
        $query .= "school_number, name ";
        $query .= ") VALUES (";
        $query .= "'{$school_number}', '{$name}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("institution_details.php");
        }
    }

    public function getEducationalCycle() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM educational_cycle ";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($cycle = mysqli_fetch_assoc($query_results)) {
            return $cycle;
        } else {
            return NULL;
        }
    }

    public function updateEducationalCycle($name) {
        global $connection;

        $query = "UPDATE educational_cycle SET ";
        $query .= "name = '{$name}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);

        if ($query_result) {
            redirect_to("institution_details_update.php");
        }
    }

}
