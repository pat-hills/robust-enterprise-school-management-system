 <?php

require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/AcademicTerm.php';

class Pupil {

    public function getPupilById($pupil_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY other_names ASC ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function getDeletedPupilById($pupil_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'YES' ";
        $query .= "ORDER BY other_names ASC ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function getPupilByUniqeUrlString($url_string) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE unique_url_string = '{$url_string}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY other_names ASC ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function getDeletedPupilByUniqueUrlString($url_string) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE unique_url_string = '{$url_string}' ";
        $query .= "AND deleted = 'YES' ";
        $query .= "ORDER BY other_names ASC ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function verifyPupilById($pupil_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "AND deleted = 'YES' ";
        $query .= "ORDER BY other_names ASC ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function getPupilBySchoolNumber($school_number) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE school_number = '{$school_number}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY other_names ASC ";
        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        if ($pupil = mysqli_fetch_assoc($result_set)) {
            return $pupil;
        } else {
            return NULL;
        }
    }

    public function checkPhotoUpload() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE pupil_id NOT IN ";
        $query .= "(";
        $query .= "SELECT pupil_id ";
        $query .= "FROM photos ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= ")";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY other_names ASC ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function deletePupil($pupil_id) {
        global $connection;
        $pupil_id = trim(escape_value($pupil_id));
        $reason = trim(escape_value($_POST["reason"]));

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "UPDATE pupils SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "INSERT INTO reasons (";
            $query .= "pupil_id, reason, time, date";
            $query .= ") VALUES (";
            $query .= "'{$pupil_id}', '{$reason}', NOW(), NOW()";
            $query .= ")";

            $result = mysqli_query($connection, $query);
            confirm_query($result);
        }

        if ($result) {
            $query = "UPDATE photos SET ";
            $query .= "deleted = 'YES' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "LIMIT 1";

            $result_photo = mysqli_query($connection, $query);
            confirm_query($result_photo);
        }

        if ($result_photo && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE class_membership SET ";
            $query .= "deleted = 'YES' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "AND academic_term = '{$get_academic_term}' ";
            $query .= "LIMIT 1";

            $result2 = mysqli_query($connection, $query);
            confirm_query($result2);
        }

        if ($result2 && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE admissions SET ";
            $query .= "deleted = 'YES' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "LIMIT 1";

            $result3 = mysqli_query($connection, $query);
            confirm_query($result);
        }

        if ($result3 && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE guardians SET ";
            $query .= "deleted = 'YES' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "LIMIT 1";

            $result4 = mysqli_query($connection, $query);
            confirm_query($result4);

            if ($result4) {
                redirect_to("delete_pupil_on.php");
            }
        }
    }

    public function reAdmitStudent($pupil_id) {
        global $connection;

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $query = "UPDATE pupils SET ";
        $query .= "deleted = 'NO' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE class_membership SET ";
            $query .= "deleted = 'NO' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "AND academic_term = '{$get_academic_term}' ";
            $query .= "LIMIT 1";

            $query_result2 = mysqli_query($connection, $query);
            confirm_query($query_result2);
        }

        if ($query_result2 && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE admissions SET ";
            $query .= "deleted = 'NO' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "LIMIT 1";

            $query_result3 = mysqli_query($connection, $query);
            confirm_query($query_result3);
        }

        if ($query_result3 && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE photos SET ";
            $query .= "deleted = 'NO' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "LIMIT 1";

            $result = mysqli_query($connection, $query);
            confirm_query($result);
        }

        if ($result && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE guardians SET ";
            $query .= "deleted = 'NO' ";
            $query .= "WHERE pupil_id = '{$pupil_id}' ";
            $query .= "LIMIT 1";

            $query_result3 = mysqli_query($connection, $query);
            confirm_query($query_result3);
        }

        if ($query_result3) {
            redirect_to("show_wsd.php");
        }
    }

    public function getDeletedStudents() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE deleted = 'YES' ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function generateid() {
        global $connection;


        $query = "SELECT *";
        $query .= "FROM institution_details ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);
        return $result_set;
    }
public function gettinggenerateid() {
        global $connection;


        $query = "SELECT *";
        $query .= "FROM institution_details ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);
        while ($values = mysqli_fetch_assoc($result_set)){
            $code = $values["students_id_initials"];
        }
        return $code;
    }

    public function term_year_part() {
        global $connection;
        $term = "";
        $year = "";
        $query = "SELECT academic_year, term from academic_term where active = 'YES' ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);
        return$result_set;
//        if ($result_set) {
//            if (mysql_affected_rows($result_set) > 0) {
//                $res = mysql_fetch_array($result_set);
//                $term = $res[0];
//                $year = $res[1];
//
//
//                confirm_query($result_set);
//                if (term == First) {
//                    $term = 01;
//                    $ac_year = explode("/", $year);
//                    $year = $ac_year[0];
//                }
//                if (term == Second) {
//                    $term = 02;
//                    $ac_year = explode("/", $year);
//                    $year = $ac_year[1];
//                }
//                if (term == Third) {
//                    $term = 03;
//                    $ac_year = explode("/", $year);
//                    $year = $ac_year[1];
//                }
//            }
    }

    public function term_part() {
        //$term = "01";
        $term="";
        global $connection;
        $query = "SELECT  term, academic_year from academic_term where active = 'YES' ";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);
//        return$result_set;
        while ($res = mysqli_fetch_assoc($result_set)) {


            $term = $res["term"];
            $year = $res["academic_year"];
            $year_to_search = "";

            if ($term == "First") {
                $term = "01";
                $ac_year = explode("/", $year);
                $year = $ac_year[0];
                $year_to_search = substr($year, -2);
            }
            if ($term == "Second") {
                $term = "02";
                $ac_year = explode("/", $year);
                $year = $ac_year[1];
                $year_to_search = substr($year, -2);
            }
            if ($term == "Third") {
                $term = "03";
                $ac_year = explode("/", $year);
                $year = $ac_year[1];
                $year_to_search = substr($year, -2);
            }
            return $year_to_search."/".$term;
        }
    }

    public function getNumber_part() {
        global $connection;
        $code = $this->gettinggenerateid();
        $term_year = $this->term_part();
 $exploded = explode("/", $term_year);
 $year = $exploded[0];
 $term = $exploded[1];
        $student_id_initials = "";
        $query = "SELECT pupil_id from pupils where  pupil_id LIKE '%$code/$year/$term/%' order by pupil_id DESC LIMIT 1 ";
        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);     
        return $result_set;
    }

    public function insertPupil() {
        global $connection;

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $sex = trim(ucwords(escape_value($_POST["sex"])));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $residence_address = trim(ucwords(escape_value($_POST["residence_address"])));
        $getRegion = trim(ucwords(escape_value($_POST["region"])));

        $random_numbers = generate_id_numbers() . microtime();
        $md5Hashing = md5($random_numbers);

        $getStudentId = $this->getPupilById($pupil_id);

        $user = new User();
        $getUser = $user->getUserByUsername($_SESSION["username"]);
        $name = $getUser["other_names"] . " " . $getUser["family_name"];

        if ($getStudentId["pupil_id"] === $pupil_id) {
            $this->iDExistBanner();
        } else {
            $query = "INSERT INTO pupils (";
            $query .= "pupil_id, unique_url_string, school_number, family_name, other_names, date_of_birth, sex, hometown,res_address,region, entered_by";
            $query .= ") VALUES (";
            $query .= "'{$pupil_id}', '{$md5Hashing}', '{$school_number}', '{$family_name}', '{$other_names}', '$date_of_birth', '{$sex}', '{$hometown}','{$residence_address}','{$getRegion}', '{$name}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("admission.php");
            }
        }
    }

    public function updatePupil($pupil_id) {
        global $connection;

        $safe_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $sex = trim(ucwords(escape_value($_POST["sex"])));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $region = trim(ucwords(escape_value($_POST["region"])));
        $school_number = trim(ucwords(escape_value($_POST["school_number"])));

        $academicTerm = new AcademicTerm();
        $getAcademicTerm = $academicTerm->getActivatedTerm();
        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

        $user = new User();
        $getUser = $user->getUserByUsername($_SESSION["username"]);
        $name = $getUser["other_names"] . " " . $getUser["family_name"];

        $institutionDetail = new InstitutionDetail();
        $getInstitutionData = $institutionDetail->getInstitutionDetails();
        $school_number = $getInstitutionData["school_number"];

        $reasons = "Edited the bio-data of this student.";

        $query = "UPDATE pupils SET ";
        $query .= "family_name = '{$family_name}', ";
        $query .= "other_names = '{$other_names}', ";
        $query .= "date_of_birth = '$date_of_birth', ";
        $query .= "sex = '{$sex}', ";
        $query .= "hometown = '{$hometown}', ";
        $query .= "region = '{$region}' ";
        $query .= "WHERE pupil_id = '{$safe_pupil_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "INSERT INTO logging (";
            $query .= "school_number, pupil_id, reasons, academic_term, time, date, entered_by";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '{$safe_pupil_id}', '{$reasons}', '{$get_academic_term}', NOW(), NOW(), '{$name}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            redirect_to("show_update_admission.php");
        }
    }

    public function getStudentByFullName($family_name, $other_names) {
        global $connection;

        $family_name = mysqli_real_escape_string($connection, $family_name);
        $other_names = mysqli_real_escape_string($connection, $other_names);

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE family_name = '{$family_name}' ";
        $query .= "AND other_names = '{$other_names}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
//        
//        if ($pupil = mysqli_fetch_assoc($result_set)) {
//            return $pupil;
//        } else {
//            return NULL;
//        }
    }

    public function getManyStudentsByFullName($family_name, $other_names) {
        global $connection;

        $family_name = mysqli_real_escape_string($connection, $family_name);
        $other_names = mysqli_real_escape_string($connection, $other_names);

        $query = "SELECT * ";
        $query .= "FROM pupils ";
        $query .= "WHERE family_name = '{$family_name}' ";
        $query .= "AND other_names = '{$other_names}' ";
        $query .= "AND deleted = 'NO'";
//        $query .= "LIMIT 1";

        $result_set = mysqli_query($connection, $query);
        confirm_query($result_set);

        return $result_set;
    }

    public function continueRegistration() {
        global $connection;

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $sex = trim(ucwords(escape_value($_POST["sex"])));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $region = trim(ucwords(escape_value($_POST["region"])));

        $query = "UPDATE pupils SET ";
        $query .= "family_name = '{$family_name}', ";
        $query .= "other_names = '{$other_names}', ";
        $query .= "date_of_birth = '$date_of_birth', ";
        $query .= "sex = '{$sex}', ";
        $query .= "hometown = '{$hometown}', ";
        $query .= "region = '{$region}' ";
        $query .= "WHERE pupil_id = '{$pupil_id}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("admission.php");
        }
    }

    public function noRecordFoundBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li>Sorry, no <strong>RECORD</strong> found!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function deleteBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>STUDENT DATA</strong>, successfully deleted!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function reAdmitBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>STUDENT INFORMATATION</strong>, successfully restored!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function iDExistBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>ID NUMBER</strong> already taken!</li>";
        echo "<li>Kindly use the suggested <strong>ID Number</strong> shown below or enter a different<strong> ID NUMBER</strong>.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
