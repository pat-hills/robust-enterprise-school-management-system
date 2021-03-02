<?php

require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/InstitutionDetail.php';

class Admission {

    public function insertAdmission() {
        global $connection;

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        
//        $school_number = trim(escape_value($_POST["school_number"]));
        $institutionDetail = new InstitutionDetail();
        $getSchoolNumber = $institutionDetail->getInstitutionDetails();
        $school_number = $getSchoolNumber["school_number"];
//        $admission_date = trim(escape_value(date("Y-m-d", strtotime($_POST["admission_date"]))));
        $class_admitted_to = trim(strtoupper(escape_value($_POST["class_admitted_to"])));
        $boarding_status = trim(ucwords(escape_value($_POST["boarding_status"])));
        $getHouse = trim(ucwords(escape_value($_POST["house"])));

        $user = new User();
        $getUser = $user->getUserByUsername($_SESSION["username"]);
        $name = $getUser["other_names"] . " " . $getUser["family_name"];

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $getAdmissionDetailsByPupilId = $this->getAdmissionById($pupil_id);
        if ($getAdmissionDetailsByPupilId["pupil_id"] == $pupil_id) {
            redirect_to("guardian.php");
        } else {
            $query = "INSERT INTO admissions (";
            $query .= "pupil_id, school_number, admission_date, class_admitted_to, boarding_status, house, admitted_by, academic_term";
            $query .= ") VALUES (";
            $query .= "'{$pupil_id}', '{$school_number}', NOW(), '{$class_admitted_to}', '{$boarding_status}', '{$getHouse}', '{$name}', '{$get_academic_term}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $_SESSION["class_admitted_to"] = $class_admitted_to;
                redirect_to("guardian.php");
            }
        }
    }

    public function getAdmissionById($pupil_id) {
        global $connection;
        $pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $query = "SELECT * ";
        $query .= "FROM admissions ";
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

    public function getHouseReport($school_number, $sex) {
        global $connection;
        $safe_school_number = mysqli_real_escape_string($connection, $school_number);
//        $safe_house = mysqli_real_escape_string($connection, $house);
        $safe_sex = mysqli_real_escape_string($connection, $sex);

        $query = "SELECT * ";
        $query .= "FROM pupils ";
//        $query .= "WHERE a.house = '{$safe_house}' ";
        $query .= "WHERE school_number = '{$safe_school_number}' ";
        $query .= "AND sex = '{$safe_sex}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "AND a.deleted = 'NO'";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function updatePupilAdmissionDetails() {
        global $connection;

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $admission_date = trim(escape_value(date("Y-m-d", strtotime($_POST["admission_date"]))));
        $class_admitted_to = trim(strtoupper(escape_value($_POST["class_admitted_to"])));
        $boarding_status = trim(ucwords(escape_value($_POST["boarding_status"])));
        $house = trim(ucwords(escape_value($_POST["house"])));

        $institutionDetail = new InstitutionDetail();
        $getSchoolNumber = $institutionDetail->getInstitutionDetails();
        $schoolNumber = $getSchoolNumber["school_number"];
        
        $getAdmissionDetailsByPupilId = $this->getAdmissionById($pupil_id);
        if ($getAdmissionDetailsByPupilId["pupil_id"] == $pupil_id) {
            $query = "UPDATE admissions SET ";
            $query .= "school_number = '{$schoolNumber}', ";
            $query .= "admission_date = '{$admission_date}', ";
            $query .= "class_admitted_to = '{$class_admitted_to}', ";
            $query .= "boarding_status = '{$boarding_status}', ";
            $query .= "house = '{$house}' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "AND deleted = 'NO' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result && mysqli_affected_rows($connection) >= 0) {
                $_SESSION["class_admitted_to"] = $class_admitted_to;
                redirect_to("show_update_guardian.php");
            }
        } else {
            $this->insertAdmission();
        }
    }

    public function changeBoardingStatus() {
        global $connection;

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $boarding_status = trim(ucwords(escape_value($_POST["boarding_status"])));
        $house = trim(ucwords(escape_value($_POST["house"])));

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $user = new User();
        $getUser = $user->getUserByUsername($_SESSION["username"]);
        $name = $getUser["other_names"] . " " . $getUser["family_name"];

        $institutionDetail = new InstitutionDetail();
        $getInstitutionData = $institutionDetail->getInstitutionDetails();
        $school_number = $getInstitutionData["school_number"];

        $reasons = "changed the status and house of this student to " . $boarding_status . " and " . $house . " respectively.";

        $query = "UPDATE admissions SET ";
        $query .= "boarding_status = '{$boarding_status}', ";
        $query .= "house = '{$house}' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "INSERT INTO logging (";
            $query .= "school_number, pupil_id, reasons, academic_term, time, date, entered_by";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '{$pupil_id}', '{$reasons}', '{$get_academic_term}', NOW(), NOW(), '{$name}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            redirect_to("change_boarding_status_on.php");

//            $this->changeStatusSuccessBanner();
        }
    }

    public function changeStatusSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>STUDENT'S STATUS,</strong> successfully changed!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function veriftyClassMembershipSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li>This <strong>STUDENT ID NUMBER</strong> does not exist or is wrongly typed!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
