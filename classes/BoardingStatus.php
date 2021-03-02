<?php

require_once '../includes/header.php';

class BoardingStatus {

    public function getBoardingStatus() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM boarding_status ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);
        
        return $query_results;
    }

}
