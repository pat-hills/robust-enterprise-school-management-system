<?php

require_once '../includes/header.php';

class RelationToPupil {

    public function getRelationToPupil() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM relation_to_pupil ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);
        
        return $query_results;
    }

}
