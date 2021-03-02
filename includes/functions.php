<?php

//--------------------------------COMMON----------------------------------------
function formatPhoneNumbers($phone_number) {
    $phone_number_substr = substr($phone_number, 0, 3);
    $mobile_number = substr($phone_number, 3);

    $phone_number_int = substr($phone_number, 0, 2);
    $mobile_number_int = substr($phone_number, 2);

    if ($phone_number_substr === "020") {
        return $formatedPhoneNumber = "23320" . $mobile_number;
    } elseif ($phone_number_substr === "050") {
        return $formatedPhoneNumber = "23350" . $mobile_number;
    } elseif ($phone_number_substr === "024") {
        return $formatedPhoneNumber = "23324" . $mobile_number;
    } elseif ($phone_number_substr === "054") {
        return $formatedPhoneNumber = "23354" . $mobile_number;
    } elseif ($phone_number_substr === "027") {
        return $formatedPhoneNumber = "23327" . $mobile_number;
    } elseif ($phone_number_substr === "057") {
        return $formatedPhoneNumber = "23357" . $mobile_number;
    } elseif ($phone_number_substr === "026") {
        return $formatedPhoneNumber = "23326" . $mobile_number;
    } elseif ($phone_number_substr === "028") {
        return $formatedPhoneNumber = "23328" . $mobile_number;
    } elseif ($phone_number_substr === "023") {
        return $formatedPhoneNumber = "23323" . $mobile_number;
    } elseif ($phone_number_int === "00") {
        return $formatedPhoneNumber = $mobile_number_int;
    }
}

function getSMSDeliveryReport($report) {
    switch ($report) {
        case 1701:
            echo "Message, successfully sent!";
            break;
        case 1701:
            echo "Invalid URL";
            break;
        case 1703:
            echo "Invalid Username and/or Password field";
            break;
        case 1704:
            echo "Invalid Message type";
            break;
        case 1705:
            echo "Invalid Message";
            break;
        case 1706:
            echo "Invalid Phone number";
            break;
        case 1707:
            echo "Invalid Sender";
        case 1708:
            echo "Invalid value for Delivery Report";
            break;
        case 1709:
            echo "User Validation Failed";
            break;
        case 1710:
            echo "Internal Error";
            break;
        case 1025:
            echo "Insufficient credit";
            break;
        case 1715:
            echo "Response timeout";
            break;
    }
}

function ordinal($num) {
    if (!in_array(($num % 100), array(11, 12, 13))) {
        switch ($num % 10) {
            // Handle 1st, 2nd, 3rd
            case 1: return $num . 'st';
            case 2: return $num . 'nd';
            case 3: return $num . 'rd';
        }
    }
    return $num . 'th';
}

function sendSMS($phone_number, $message) {
    $institutionDetail = new InstitutionDetail();
    $institution_set = $institutionDetail->getInstitutionDetails();
    $api_key = "NjWe8jKSDXJiTJvE8Um3FvVqe";
    $sender = $institution_set["sms_tag_name"];
    $url = "http://bulk.mnotification.com/smsapi?key=$api_key&to=$phone_number&msg=$message&sender_id=" . $sender;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return split(',', $response);
}

//$marks = [79, 5, 18, 5, 32, 1, 16, 1, 82, 13];
//function getRank($marks) {
//    $rank = 1;
//    $count = 0;
//    $ranks = [];
//    //sort the marks in the descending order
//    arsort($marks, 1);
//    foreach ($marks as $mark) {
//        //check if this mark is already ranked
//        if (array_key_exists($mark, $ranks)) {
//            //increase the count to keep how many times each value is repeated
//            $count++;
//            //no need to give rank - as it is already given
//        } else {
//            $ranks[$mark] = $i + $j;
//            $i++;
//        }
//        return $ranks;
//    }
//}

function getURL() {
    if (isset($_GET["id"])) {
        return $id = trim(escape_value($_GET["id"]));
    }
}

function getRandomURL() {
    if (isset($_GET["id"])) {
        $id = trim(escape_value($_GET["id"]));

        $random = md5(uniqid());
        $_SESSION['url'][$random] = $id;
        return "http://127.0.0.1/iskool/$random";
    }
}

function getHousePage() {
    if (!isset($_GET["id"])) {
        redirect_to("house.php");
    }
}

function getFormMasterPage() {
    if (!isset($_GET["id"])) {
        redirect_to("form_master.php");
    }
}

function getClassAssignmentPage() {
    if (!isset($_GET["id"])) {
        redirect_to("classAssignment.php");
    }
}

function getSticky($case, $par, $value = "", $initial = "") {
    switch ($case) {
        case 1://text field
            if (isset($_GET[$par]) && $_GET[$par] != "") {
                echo stripslashes($_GET[$par]);
            }
            break;

        case 2://select
            if (isset($_GET[$par]) && $_GET[$par] == $value) {
                echo " selected = 'selected'";
            }
            break;

        case 3://checkbox group
            if (isset($_GET[$par]) && $_GET[$par] != "") {
                echo " checked = 'checked'";
            }
            break;

        case 4://radio buttons
            if (isset($_GET[$par]) && $_GET[$par] == $value) {
                echo " checked = 'checked'";
            } else {
                if ($initial != "") {
                    echo " checked = 'checked'";
                }
            }
            break;
    }
}

function escape_value($input_string) {
    global $connection;

    $escape_string = mysqli_real_escape_string($connection, $input_string);
    return $escape_string;
}

function redirect_to($location = NULL) {
    if ($location != NULL) {
        header("Location:{$location}");
        exit();
    }
}

function __autoload($class_name) {
    $class_name = strtolower($class_name);
    $path = "../includes/{$class_name}.php";

    if (file_exists($path)) {
        require_once($path);
    } else {
        die("The file {$class_name}.php could not be found.");
    }
}

function generate_id_numbers() {
    
    
    $number = mt_rand(1000, 9999);
    $year = strftime("%Y", time());
    $number_format = number_format($number, 0, "", "");
//    $separator = "-";
    $id_number = $year;
//$id_number .= $separator;
    $id_number .= $number_format;
    return $id_number;
}

function generate_receipt_numbers() {
    $number = mt_rand(100000, 999999);
    $year = strftime("%m", time());
    $number_format = number_format($number, 0, "", "");
//    $separator = "-";
    $id_number = $year;
//$id_number .= $separator;
    $id_number .= $number_format;
    return $id_number;
}

function show_full_name($first_name = "", $last_name = "") {
    if (isset($first_name) && isset($last_name)) {
        return $last_name . " " . $first_name;
    }
}

function confirm_query($query_result) {
    if (!$query_result) {
        die("Database Query Failed!");
    }
}

//---------------------------------------FIND ALL------------------------------
//function find_all_houses() {
//    global $connection;
//
//    $query = "SELECT * ";
//    $query .= "FROM houses ";
//    $query .= "WHERE deleted = 'NO' ";
//    $query .= "ORDER BY name ASC";
//
//    $query_results = mysqli_query($connection, $query);
//    confirm_query($query_results);
//    return $query_results;
//}

function find_all_terms() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM academic_term ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "ORDER BY term ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_all_levels() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM levels ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "ORDER BY level_name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_all_subjects() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "ORDER BY class_id ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

//function find_all_regions() {
//    global $connection;
//
//    $query = "SELECT * ";
//    $query .= "FROM regions ";
//    $query .= "WHERE deleted = 'NO' ";
//    $query .= "ORDER BY name ASC";
//
//    $query_results = mysqli_query($connection, $query);
//    confirm_query($query_results);
//    return $query_results;
//}

function find_all_pupils() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM pupils ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "ORDER BY other_names ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

//function find_all_de_activations() {
//    global $connection;
//
//    $query = "SELECT * ";
//    $query .= "FROM pupils ";
//    $query .= "WHERE withdrawn = 'YES' ";
//    $query .= "OR stopped = 'YES' ";
//    $query .= "OR deleted = 'YES'";
//
//    $query_results = mysqli_query($connection, $query);
//    confirm_query($query_results);
//    return $query_results;
//}
//function find_all_educational_cycle() {
//    global $connection;
//
//    $query = "SELECT * ";
//    $query .= "FROM educational_cycle ";
//
//    $query_results = mysqli_query($connection, $query);
//    confirm_query($query_results);
//    return $query_results;
//}

function find_all_classes() {
    global $connection;
//    $escape_level_id = mysqli_real_escape_string($connection, $level_id);

    $query = "SELECT * ";
    $query .= "FROM classes ";
    $query .= "WHERE deleted = 'NO' ";
//    $query .= "AND level_id = '$escape_level_id' ";
    $query .= "ORDER BY class_name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

//------------------------------FIND BY-----------------------------------------
function find_subjects_by_school_number($school_number) {
    global $connection;
    $escape_school_number = mysqli_real_escape_string($connection, $school_number);

    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "AND school_number = '{$escape_school_number}' ";
    $query .= "ORDER BY subject_name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_subject_combination_by_school_number($school_number) {
    global $connection;
    $escape_school_number = mysqli_real_escape_string($connection, $school_number);

    $query = "SELECT * ";
    $query .= "FROM subject_combination ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "AND school_number = '{$escape_school_number}' ";
    $query .= "ORDER BY subject_combination_name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_subject_combination_by_id($subject_combination_id) {
    global $connection;
    $escape_subject_combination_id = mysqli_real_escape_string($connection, $subject_combination_id);

    $query = "SELECT * ";
    $query .= "FROM subject_combination ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "AND subject_combination_id = '{$escape_subject_combination_id}' ";
    $query .= "ORDER BY subject_combination_name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);

    if ($subject_combination = mysqli_fetch_assoc($query_results)) {
        return $subject_combination;
    } else {
        return NULL;
    }
}

//function find_subjects_by_code($subject_code) {
//    global $connection;
//    $escape_subject_code = mysqli_real_escape_string($connection, $subject_code);
//
//    $query = "SELECT * ";
//    $query .= "FROM subjects ";
//    $query .= "WHERE deleted = 'NO' ";
//    $query .= "AND subject_code = '{$escape_subject_code}' ";
//    $query .= "ORDER BY subject_name ASC";
//
//    $query_results = mysqli_query($connection, $query);
//    confirm_query($query_results);
//
//    if ($codes = mysqli_fetch_assoc($query_results)) {
//        return $codes;
//    } else {
//        return NULL;
//    }
//}

function find_all_classes_by_level_id($level_id) {
    global $connection;
    $escape_level_id = mysqli_real_escape_string($connection, $level_id);

    $query = "SELECT * ";
    $query .= "FROM classes ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "AND level_id = '$escape_level_id' ";
    $query .= "ORDER BY class_name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_subjects_by_class($class_id) {
    global $connection;
    $escape_class_id = mysqli_real_escape_string($connection, $class_id);

    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE class_id = '$escape_class_id' ";
    $query .= "AND deleted = 'NO'";
    $query .= "ORDER BY subject_name ASC";
//    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_withdrawn_pupil_by_id($pupil_id) {
    global $connection;
    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

    $query = "SELECT * ";
    $query .= "FROM pupils ";
    $query .= "WHERE pupil_id = '$escape_pupil_id' ";
    $query .= "AND deleted = 'YES' ";
    $query .= "LIMIT 1";

    $result_set = mysqli_query($connection, $query);
    confirm_query($result_set);
    if ($pupil = mysqli_fetch_assoc($result_set)) {
        return $pupil;
    } else {
        return NULL;
    }
}

function find_all_admissions_by_id($pupil_id) {
    global $connection;
    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

    $query = "SELECT * ";
    $query .= "FROM admissions ";
    $query .= "WHERE pupil_id = '$escape_pupil_id' ";
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

function find_admitted_class_list($class_id) {
    global $connection;
    $escape_class_id = mysqli_real_escape_string($connection, $class_id);

    $query = "SELECT * ";
    $query .= "FROM admissions ";
    $query .= "WHERE class_id = '$escape_class_id' ";
    $query .= "AND deleted = 'NO' ";
//    $query .= "LIMIT 1";

    $result_set = mysqli_query($connection, $query);
    confirm_query($result_set);
    if ($pupil = mysqli_fetch_assoc($result_set)) {
        return $pupil;
    } else {
        return NULL;
    }
}

function find_all_admissions_by_name_and_id($pupil_id, $family_name, $other_names) {
    global $connection;
    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);
    $escape_family_name = mysqli_real_escape_string($connection, $family_name);
    $escape_other_names = mysqli_real_escape_string($connection, $other_names);

    $query = "SELECT * ";
    $query .= "FROM pupils, admissions ";
    $query .= "WHERE pupils.pupil_id = '$escape_pupil_id' ";
    $query .= "AND admissions.pupil_id = '$escape_pupil_id' ";
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

//function find_pupil_by_id($pupil_id) {
//    global $connection;
//    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);
//
//    $query = "SELECT * ";
//    $query .= "FROM pupils ";
//    $query .= "WHERE pupil_id = '$escape_pupil_id' ";
//    $query .= "AND withdrawn = 'NO' ";
//    $query .= "AND stopped = 'NO' ";
//    $query .= "AND deleted = 'NO' ";
//    $query .= "LIMIT 1";
//
//    $result_set = mysqli_query($connection, $query);
//    confirm_query($result_set);
//    if ($pupil = mysqli_fetch_assoc($result_set)) {
//        return $pupil;
//    } else {
//        return NULL;
//    }
//}

function find_region_by_name($name) {
    global $connection;
    $escape_region_id = mysqli_real_escape_string($connection, $name);

    $query = "SELECT * ";
    $query .= "FROM regions ";
    $query .= "WHERE name = '$name' ";
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

function find_class_by_id($class_id) {
    global $connection;
    $escape_class_id = mysqli_real_escape_string($connection, $class_id);

    $query = "SELECT * ";
    $query .= "FROM classes ";
    $query .= "WHERE class_id = '$escape_class_id' ";
    $query .= "AND deleted = 'NO' ";
    $query .= "ORDER BY class_name ASC ";
    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    if ($class = mysqli_fetch_assoc($query_results)) {
        return $class;
    } else {
        return NULL;
    }
}

function find_class_by_name($class_name) {
    global $connection;
    $escape_class_name = mysqli_real_escape_string($connection, $class_name);

    $query = "SELECT * ";
    $query .= "FROM classes ";
    $query .= "WHERE class_name = '{$escape_class_name}' ";
    $query .= "AND deleted = 'NO' ";
    $query .= "ORDER BY class_name ASC ";
    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    if ($class = mysqli_fetch_assoc($query_results)) {
        return $class;
    } else {
        return NULL;
    }
}

function find_subject_by_name($subject_name) {
    global $connection;
    $escape_subject_name = mysqli_real_escape_string($connection, $subject_name);

    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE subject_name = '{$escape_subject_name}' ";
    $query .= "AND deleted = 'NO' ";
    $query .= "ORDER BY subject_name ASC ";
    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
//    if ($class = mysqli_fetch_assoc($query_results)) {
//        return $class;
//    } else {
//        return NULL;
//    }
    return $query_results;
}

function find_pupil_by_id_re_admission($pupil_id) {
    global $connection;
    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

    $query = "SELECT * ";
    $query .= "FROM pupils ";
    $query .= "WHERE pupil_id = '$escape_pupil_id' ";
//    $query .= "AND withdrawn = 'YES' ";
//    $query .= "AND stopped = 'NO' ";
//    $query .= "AND deleted = 'NO' ";
    $query .= "LIMIT 1";

    $result_set = mysqli_query($connection, $query);
    confirm_query($result_set);
    if ($pupil = mysqli_fetch_assoc($result_set)) {
        return $pupil;
    } else {
        return NULL;
    }
}

//function find_guardian_by_id($pupil_id) {
//    global $connection;
//    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);
//
//    $query = "SELECT * ";
//    $query .= "FROM guardians ";
//    $query .= "WHERE pupil_id = '$escape_pupil_id' ";
//    $query .= "AND deleted = 'NO' ";
//    $query .= "LIMIT 1";
//
//    $result_set = mysqli_query($connection, $query);
//    confirm_query($result_set);
//    if ($pupil = mysqli_fetch_assoc($result_set)) {
//        return $pupil;
//    } else {
//        return NULL;
//    }
//}
//function find_pupil_by_full_name($family_name, $other_names) {
//    global $connection;
//    $escape_family_name = mysqli_real_escape_string($connection, $family_name);
//    $escape_other_names = mysqli_real_escape_string($connection, $other_names);
//
//    $query = "SELECT * ";
//    $query .= "FROM pupils ";
//    $query .= "WHERE family_name = '$escape_family_name' ";
//    $query .= "AND other_names = '$escape_other_names' ";
//    $query .= "AND deleted = 'NO' ";
//    $query .= "LIMIT 1";
//
//    $result_set = mysqli_query($connection, $query);
//    confirm_query($result_set);
//    if ($pupil = mysqli_fetch_assoc($result_set)) {
//        return $pupil;
//    } else {
//        return NULL;
//    }
//}

function find_level_by_id($level_id) {
    global $connection;
    $escape_level_id = mysqli_real_escape_string($connection, $level_id);

    $query = "SELECT * ";
    $query .= "FROM levels ";
    $query .= "WHERE level_id = '$escape_level_id' ";
    $query .= "AND deleted = 'NO'";
//    $query .= "LIMIT 1";

    $result_set = mysqli_query($connection, $query);
    confirm_query($result_set);
    if ($level = mysqli_fetch_assoc($result_set)) {
        return $level;
    } else {
        return NULL;
    }
}

function find_classes_by_level_id($level_id) {
    global $connection;
    $escape_level_id = mysqli_real_escape_string($connection, $level_id);

    $query = "SELECT * ";
    $query .= "FROM classes ";
    $query .= "WHERE level_id = '$escape_level_id' ";
    $query .= "AND deleted = 'NO'";
    $query .= "ORDER BY class_name ASC";
//    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_subjects_by_class_id($class_id) {
    global $connection;
    $escape_class_id = mysqli_real_escape_string($connection, $class_id);

    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE class_id = '$escape_class_id' ";
    $query .= "AND deleted = 'NO'";
    $query .= "ORDER BY subject_name ASC";
//    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_pupils_by_class_admitted_to($class_id) {
    global $connection;
    $escape_class_id = mysqli_real_escape_string($connection, $class_id);

    $query = "SELECT * ";
    $query .= "FROM admissions ";
    $query .= "WHERE class_id = '{$escape_class_id}' ";
    $query .= "AND deleted = 'NO'";
//    $query .= "ORDER BY subject_name ASC";
//    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_class_id_by_subject_name($subject_name) {
    global $connection;
    $escape_subject_name = mysqli_real_escape_string($connection, $subject_name);

    $query = "SELECT class_id ";
    $query .= "FROM subjects ";
    $query .= "WHERE subject_name = '{$escape_subject_name}' ";
    $query .= "AND deleted = 'NO' ";
//    $query .= "ORDER BY subject_name ASC";
    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_subject_by_subject_name_and_class_id($subject_name, $class_id) {
    global $connection;
    $escape_subject_name = mysqli_real_escape_string($connection, $subject_name);
    $escape_class_id = mysqli_real_escape_string($connection, $class_id);

    $query = "SELECT * ";
    $query .= "FROM subjects ";
    $query .= "WHERE subject_name = '{$escape_subject_name}' ";
    $query .= "AND class_id = '$escape_class_id' ";
    $query .= "AND deleted = 'NO' ";
    $query .= "ORDER BY subject_name ASC ";
    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function find_stopped_withdrawn_deleted_pupil_by_id($pupil_id) {
    global $connection;
    $escape_pupil_id = mysqli_real_escape_string($connection, $pupil_id);

    $query = "SELECT * ";
    $query .= "FROM pupils ";
    $query .= "WHERE pupil_id = '$escape_pupil_id' ";
//    $query .= "AND withdrawn = 'YES' ";
//    $query .= "AND stopped = 'YES' ";
//    $query .= "AND deleted = 'YES' ";
    $query .= "LIMIT 1";

    $result_set = mysqli_query($connection, $query);
    confirm_query($result_set);
    if ($pupil = mysqli_fetch_assoc($result_set)) {
        return $pupil;
    } else {
        return NULL;
    }
}

function find_academic_year_details_by_school_number($school_number) {
    global $connection;
    $escape_school_number = escape_value($school_number);

    $query = "SELECT * ";
    $query .= "FROM academic_year ";
    $query .= "WHERE school_number = '$escape_school_number'";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);

    if ($academic_year = mysqli_fetch_assoc($query_results)) {
        return $academic_year;
    } else {
        return NULL;
    }
}

function find_class_members_by_class_name($class_name) {
    global $connection;
    $escape_class_name = mysqli_real_escape_string($connection, $class_name);

    $query = "SELECT * ";
    $query .= "FROM class_membership ";
    $query .= "WHERE class_name = '{$escape_class_name}' ";
    $query .= "AND deleted = 'NO'";
//    $query .= "ORDER BY subject_name ASC";
//    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

//--------------------------------OTHERS---------------------------------------
function find_institution_details() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM institution_details";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);

    if ($institution = mysqli_fetch_assoc($query_results)) {
        return $institution;
    } else {
        return NULL;
    }
}

function find_academic_year_details() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM academic_year";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);

    if ($academic_year = mysqli_fetch_assoc($query_results)) {
        return $academic_year;
    } else {
        return NULL;
    }
}

function find_all_classes_for_level($level_id) {
    global $connection;
    $escape_level_id = mysqli_real_escape_string($connection, $level_id);

    $query = "SELECT * ";
    $query .= "FROM classes ";
    $query .= "WHERE level_id = '$escape_level_id' ";
    $query .= "AND deleted = 'NO'";
//    $query .= "LIMIT 1";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

//function find_relation_to_pupil() {
//    global $connection;
//
//    $query = "SELECT * ";
//    $query .= "FROM relation_to_pupil ";
//    $query .= "WHERE deleted = 'NO' ";
//    $query .= "ORDER BY name ASC";
//
//    $query_results = mysqli_query($connection, $query);
//    confirm_query($query_results);
//    return $query_results;
//}

function find_boarding_status() {
    global $connection;

    $query = "SELECT * ";
    $query .= "FROM boarding_status ";
    $query .= "WHERE deleted = 'NO' ";
    $query .= "ORDER BY name ASC";

    $query_results = mysqli_query($connection, $query);
    confirm_query($query_results);
    return $query_results;
}

function encrypt($sData) {
    $id = (double) $sData * 525325.24;
    return base64_encode($id);
}

function decrypt($sData) {
    $url_id = base64_decode($sData);
    $id = (double) $url_id / 525325.24;
    return $id;
}

function decryptInt($sData) {
    $url_id = base64_decode($sData);
    $id = (int) $url_id / 525325.24;
    return $id;
}

//login details
function find_user_by_username($username) {
    global $connection;

    $escape_username = mysqli_real_escape_string($connection, $username);

    $query = "SELECT * ";
    $query .= "FROM users ";
    $query .= "WHERE username = '{$escape_username}' ";
    $query .= "AND deleted = 'NO' ";
    $query .= "LIMIT 1";

    $user_result = mysqli_query($connection, $query);

    if (!$user_result) {
        die("Database query failed!");
    }

    if ($user = mysqli_fetch_assoc($user_result)) {
        return $user;
    } else {
        return NULL;
    }
}

function attempt_login($username, $password) {
    $user = find_user_by_username($username);
  
    if ($user) {
        if (password_check($password, $user["password"])) {
            return $user;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

function password_encrypt($password) {
    $hash_format = "$2y$10$";
    $salt_length = 22;
    $salt = generate_salt($salt_length);
    $format_and_salt = $hash_format . $salt;
    $hash = crypt($password, $format_and_salt);
    return $hash;
}

function generate_salt($length) {
    $unique_random_string = md5(uniqid(mt_rand(), true));
    $base64_string = base64_encode($unique_random_string);
    $modified_base64_string = str_replace('+', '.', $base64_string);
    $salt = substr($modified_base64_string, 0, $length);
    return $salt;
}

function password_check($password, $existing_hash) {
    $hash = crypt($password, $existing_hash);
    if ($hash === $existing_hash) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function logged_in() {
    return isset($_SESSION["user_id"]);
}

function confirm_logged_in() {
    if (!logged_in()) {
        redirect_to("index.php");
    }
}


  function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
                        $str = trim($str);
                        $str = htmlentities($str);
                        $str = stripcslashes($str);
                     
		}
		return mysql_real_escape_string($str);
	}
