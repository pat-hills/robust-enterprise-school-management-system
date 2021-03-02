<?php

require_once '../includes/header.php';

class Guardian {

    public function insertGuardian() {
        global $connection;

        $pupil_id = trim(escape_value($_SESSION["pupil_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $guardian_family_name = trim(ucwords(escape_value($_POST["guardian_family_name"])));
        $guardian_other_names = trim(ucwords(escape_value($_POST["guardian_other_names"])));
        $guardian_sex = trim(ucwords(escape_value($_POST["guardian_sex"])));
        $occupation = trim(ucwords(escape_value($_POST["occupation"])));
        $relation_to_pupil = trim(ucwords(escape_value($_POST["relation_to_pupil"])));
        $telephone_1 = trim(escape_value($_POST["telephone_1"]));
        $telephone_2 = trim(escape_value($_POST["telephone_2"]));
        $telephone_3 = trim(escape_value($_POST["telephone_3"]));

        $postal_address = trim(ucwords(escape_value($_POST["postal_address"])));
        $house_number = trim(ucwords(escape_value($_POST["house_number"])));

        $query = "INSERT INTO guardians (";
        $query .= "pupil_id, school_number, guardian_family_name, guardian_other_names, guardian_sex, occupation, relation_to_pupil, telephone_1, telephone_2, telephone_3, postal_address, house_number";
        $query .= ") VALUES (";
        $query .= "'{$pupil_id}', '{$school_number}', '{$guardian_family_name}', '{$guardian_other_names}', '{$guardian_sex}', '{$occupation}', '{$relation_to_pupil}', '{$telephone_1}', '{$telephone_2}', '{$telephone_3}', '{$postal_address}', '{$house_number}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            redirect_to("class_membership.php");
        }
    }

    public function getGuardianByPupilId($pupil_id) {
        global $connection;
        $pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT * ";
        $query .= "FROM guardians ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);
        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function updateGuardianByPupilId() {
        global $connection;
        $pupil_id = trim(escape_value($_POST["pupil_id"]));

        $getStudentById = $this->getGuardianByPupilId($pupil_id);
        if ($getStudentById["pupil_id"] === $pupil_id) {
            $guardian_family_name = trim(ucwords(escape_value($_POST["guardian_family_name"])));
            $guardian_other_names = trim(ucwords(escape_value($_POST["guardian_other_names"])));
            $guardian_sex = trim(ucwords(escape_value($_POST["guardian_sex"])));
            $occupation = trim(ucwords(escape_value($_POST["occupation"])));
            $relation_to_pupil = trim(ucwords(escape_value($_POST["relation_to_pupil"])));
            $telephone_1 = trim(escape_value($_POST["telephone_1"]));
            $telephone_2 = trim(escape_value($_POST["telephone_2"]));
            $telephone_3 = trim(escape_value($_POST["telephone_3"]));
            $postal_address = trim(ucwords(escape_value($_POST["postal_address"])));
            $house_number = trim(ucwords(escape_value($_POST["house_number"])));

            $query = "UPDATE guardians SET ";
            $query .= "guardian_family_name = '{$guardian_family_name}', ";
            $query .= "guardian_other_names = '{$guardian_other_names}', ";
            $query .= "guardian_sex = '{$guardian_sex}', ";
            $query .= "occupation = '{$occupation}', ";
            $query .= "relation_to_pupil = '{$relation_to_pupil}', ";
            $query .= "telephone_1 = '{$telephone_1}', ";
            $query .= "telephone_2 = '{$telephone_2}', ";
            $query .= "telephone_3 = '{$telephone_3}', ";
            $query .= "postal_address = '{$postal_address}', ";
            $query .= "house_number = '{$house_number}' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result && mysqli_affected_rows($connection) >= 0) {
//                unset($_SESSION["pupil_id"]);
                redirect_to("class_membership.php");
            }
        } else {
            $this->insertGuardian();
        }
    }

}
