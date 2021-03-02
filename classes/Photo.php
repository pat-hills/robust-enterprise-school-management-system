<?php

require_once '../includes/header.php';

class Photo {

    public function insertPhoto() {
        global $connection;

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
//        $logo_id = trim(escape_value($_POST["logo_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $photo_url = trim(escape_value("photos/" . $_FILES["photo"]["name"]));

        $query = "INSERT INTO photos (";
        $query .= "pupil_id, school_number, photo_url";
        $query .= ") VALUES (";
        $query .= "'{$pupil_id}', '{$school_number}', '{$photo_url}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result) {
            $this->photoUploadSuccessBanner();
        }
    }
    
    public function insertPic($pupil_id) {
        global $connection;

//        $pupil_id = trim(escape_value($_POST["pupil_id"]));
//        $logo_id = trim(escape_value($_POST["logo_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $photo_url = trim(escape_value("photos/" . $_FILES["photo"]["name"]));

        $query = "INSERT INTO photos (";
        $query .= "pupil_id, school_number, photo_url";
        $query .= ") VALUES (";
        $query .= "'{$pupil_id}', '{$school_number}', '{$photo_url}'";
        $query .= ")";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

//        if ($query_result) {
//            $this->photoUploadSuccessBanner();
//        }
    }

    public function insertLogo() {
        global $connection; 

        $logo_id = trim(escape_value($_POST["logo_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $photo_url = trim(escape_value("photos/" . $_FILES["logo"]["name"]));

        $getLogoDetails = $this->getSchoolLogoID();
        if ($getLogoDetails["pupil_id"] === "NO") {
            $this->updateLogo();
        } else {
            $query = "INSERT INTO photos (pupil_id, logo_id,school_number, photo_url) VALUES ('NO', '{$logo_id}', '{$school_number}', '{$photo_url}')";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $this->photoUploadSuccessBanner();
            }
        }
    }

    public function updateLogo() {
        global $connection;

        $photo_url = trim(escape_value("photos/" . $_FILES["logo"]["name"]));

        $query = "UPDATE photos SET ";
        $query .= "photo_url = '{$photo_url}' ";
        $query .= "WHERE pupil_id = 'NO' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $this->updatedPhotoSuccessBanner();
        }
    }

    public function updateSignature() {
        global $connection;

        $signature_url = trim(escape_value("photo_signature/" . $_FILES["sign"]["name"]));

        $query = "UPDATE photo_signature SET ";
        $query .= "signature_url = '{$signature_url}' ";
//        $query .= "WHERE signature = 'NO' ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $this->updatedSignatureSuccessBanner();
        }
    }
    
    public function insertSignature() {
        global $connection;

        $signature_name = trim(escape_value($_POST["signature_name"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $signature_url = trim(escape_value("photo_signature/" . $_FILES["sign"]["name"]));

        $getSignatureDetails = $this->getSignatureID();
        if ($getSignatureDetails["deleted"] === "NO") {
            $this->updateSignature();
        } else {
            $query = "INSERT INTO photo_signature (";
            $query .= "signature_name, school_number, signature_url";
            $query .= ") VALUES (";
            $query .= "'{$signature_name}', '{$school_number}', '{$signature_url}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                $this->signatureUploadSuccessBanner();
            }
        }
    }
    
    public function getSchoolLogo($logo_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM photos ";
        $query .= "WHERE logo_id = '{$logo_id}' ";
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

    public function getSchoolLogoID() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM photos ";
        $query .= "WHERE pupil_id = 'NO' ";
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

    public function getSignatureID() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM photo_signature ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function getPhotoById($pupil_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils a, photos b ";
        $query .= "WHERE a.pupil_id = '{$pupil_id}' ";
        $query .= "AND b.pupil_id = '{$pupil_id}' ";
        $query .= "AND a.deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }
    
    public function getLogoId() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM photos ";
        $query .= "WHERE pupil_id = 'NO' ";
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
    
    public function getSignatureBySchoolNumber($school_number) {
        global $connection;
        
        $safe_school_number = escape_value($school_number);

        $query = "SELECT * ";
        $query .= "FROM photo_signature ";
        $query .= "WHERE school_number = '{$safe_school_number}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($row = mysqli_fetch_assoc($result_set)) {
            return $row;
        } else {
            return NULL;
        }
    }
    
    public function updatePhoto($pupil_id) {
        global $connection;

//        $pupil_id = trim(escape_value($_POST["pupil_id"]));
//        $school_number = trim(escape_value($_POST["school_number"]));
        $photo_url = trim(escape_value("photos/" . $_FILES["photo"]["name"]));

        $query = "UPDATE photos SET ";
        $query .= "photo_url = '{$photo_url}' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query) or die(mysql_error());
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $this->updatedPhotoSuccessBanner();
        }
    }

    public function photoUploadSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>PHOTOGRAPH</strong>, successfully uploaded!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    public function signatureUploadSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>HEAD SIGNATURE</strong>, successfully uploaded!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function updatedPhotoSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>PHOTOGRAPH</strong>, successfully replaced!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    public function updatedSignatureSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type = 'square'>";
        echo "<li><strong>HEAD SIGNATURE</strong>, successfully replaced!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
