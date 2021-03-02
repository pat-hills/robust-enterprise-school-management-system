<?php

require_once '../includes/header.php';

class InstitutionDetail {

    public function getInstitutionDetails() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM institution_details ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY school_name ASC";
        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($institution = mysqli_fetch_assoc($query_results)) {
            return $institution;
        } else {
            return NULL;
        }
    }
    
       public function getInstitutionName() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM institution_details ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY school_name ASC";
        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($institution = mysqli_fetch_assoc($query_results)) {
            $name = $institution["school_name"];
            
              return $name;
            
            $_SESSION['school_name']  = $name;
            
          
        } else {
            return NULL;
        }
    }

    public function insertInstitutionDetails() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $school_name = trim(strtoupper(escape_value($_POST["school_name"])));
              $sms_tag_name = trim(strtoupper(escape_value($_POST["sms_tag_name"])));
  $students_id_initials = trim(strtoupper(escape_value($_POST["students_id_initials"])));        
        $school_motor = trim(ucfirst(escape_value($_POST["school_motor"])));
        $educational_cycle = trim(ucwords(escape_value($_POST["educational_cycle"])));
        $date_of_installation = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_installation"]))));
        $telephone_1 = trim(escape_value($_POST["telephone_1"]));
        $telephone_2 = trim(escape_value($_POST["telephone_2"]));
        $telephone_3 = trim(escape_value($_POST["telephone_3"]));
        $postal_address = trim(ucwords(escape_value($_POST["postal_address"])));
        $bank_1 = trim(ucwords(escape_value($_POST["bank_1"])));
        $bank_1_branch = trim(ucwords(escape_value($_POST["bank_1_branch"])));
        $bank_1_account_number = trim(escape_value($_POST["bank_1_account_number"]));
        $bank_2 = trim(ucwords(escape_value($_POST["bank_2"])));
        $bank_2_branch = trim(ucwords(escape_value($_POST["bank_2_branch"])));
        $bank_2_account_number = trim(escape_value($_POST["bank_2_account_number"]));
        $bank_3 = trim(ucwords(escape_value($_POST["bank_3"])));
        $bank_3_branch = trim(ucwords(escape_value($_POST["bank_3_branch"])));
        $bank_3_account_number = trim(escape_value($_POST["bank_3_account_number"]));

        $getInstitutionDetails = $this->getInstitutionDetails();
        if ($getInstitutionDetails["school_number"] === $school_number) {
            $this->updateInstitutionDetails();
//            $this->schoolNumberExistBanner();
        } else {
            $query = "INSERT INTO institution_details (";
            $query .= "school_number, school_name, sms_tag_name, students_id_initials, school_motor, educational_cycle, date_of_installation, telephone_1, telephone_2, telephone_3, postal_address, bank_1, bank_1_branch, bank_1_account_number, bank_2, bank_2_branch, bank_2_account_number, bank_3, bank_3_branch, bank_3_account_number";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '{$school_name}', '{$sms_tag_name}', '{$students_id_initials}' '{$school_motor}', '{$educational_cycle}', '$date_of_installation', '{$telephone_1}', '{$telephone_2}', '{$telephone_3}', '{$postal_address}', '{$bank_1}', '{$bank_1_branch}', '{$bank_1_account_number}', '{$bank_2}', '{$bank_2_branch}', '{$bank_2_account_number}', '{$bank_3}', '{$bank_3_branch}', '{$bank_3_account_number}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("academic_year.php");
            }
        }
    }

    public function updateInstitutionDetails() {
        global $connection;

        $school_number = trim(escape_value($_POST["school_number"]));
        $school_name = trim(strtoupper(escape_value($_POST["school_name"])));
        $sms_tag_name = trim(strtoupper(escape_value($_POST["sms_tag_name"])));
        $school_motor = trim(ucfirst(escape_value($_POST["school_motor"])));
        $educational_cycle = trim(ucwords(escape_value($_POST["educational_cycle"])));
        $date_of_installation = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_installation"]))));
        $telephone_1 = trim(escape_value($_POST["telephone_1"]));
        $telephone_2 = trim(escape_value($_POST["telephone_2"]));
        $telephone_3 = trim(escape_value($_POST["telephone_3"]));
        $postal_address = trim(ucwords(escape_value($_POST["postal_address"])));
        $bank_1 = trim(ucwords(escape_value($_POST["bank_1"])));
        $bank_1_branch = trim(ucwords(escape_value($_POST["bank_1_branch"])));
        $bank_1_account_number = trim(escape_value($_POST["bank_1_account_number"]));
        $bank_2 = trim(ucwords(escape_value($_POST["bank_2"])));
        $bank_2_branch = trim(ucwords(escape_value($_POST["bank_2_branch"])));
        $bank_2_account_number = trim(escape_value($_POST["bank_2_account_number"]));
        $bank_3 = trim(ucwords(escape_value($_POST["bank_3"])));
        $bank_3_branch = trim(ucwords(escape_value($_POST["bank_3_branch"])));
        $bank_3_account_number = trim(escape_value($_POST["bank_3_account_number"]));

        $getInstitutionDetails = $this->getInstitutionDetails();
        $getSchoolNumber = $getInstitutionDetails["school_number"];
        if ($getSchoolNumber === $school_number) {
            $query = "UPDATE institution_details SET ";
            $query .= "school_name = '{$school_name}', ";
            $query .= "sms_tag_name = '{$sms_tag_name}', ";
            $query .= "school_motor = '{$school_motor}', ";
            $query .= "educational_cycle = '{$educational_cycle}', ";
            $query .= "date_of_installation = '$date_of_installation', ";
            $query .= "telephone_1 = '{$telephone_1}', ";
            $query .= "telephone_2 = '{$telephone_2}', ";
            $query .= "telephone_3 = '{$telephone_3}', ";
            $query .= "postal_address = '{$postal_address}', ";
            $query .= "bank_1 = '{$bank_1}', ";
            $query .= "bank_1_branch = '{$bank_1_branch}', ";
            $query .= "bank_1_account_number = '{$bank_1_account_number}', ";
            $query .= "bank_2 = '{$bank_2}', ";
            $query .= "bank_2_branch = '{$bank_2_branch}', ";
            $query .= "bank_2_account_number = '{$bank_2_account_number}', ";
            $query .= "bank_3 = '{$bank_3}', ";
            $query .= "bank_3_branch = '{$bank_3_branch}', ";
            $query .= "bank_3_account_number = '{$bank_3_account_number}' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result && mysqli_affected_rows($connection) >= 0) {
                $this->updateSuccessBanner();
            }
        }
    }

    public function updateSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>SCHOOL DETAILS</strong>, successfully edited!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function schoolNumberExistBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>SCHOOL NUMBER</strong> already taken!</li>";
        echo "<li>Enter a different <strong>SCHOOL NUMBER</strong>.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
