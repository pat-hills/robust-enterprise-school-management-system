<?php

require_once '../includes/header.php';
require_once '../classes/FormMaster.php';

class User {

    public function insertUser() {
        global $connection;

        $user_id = trim(escape_value($_POST["user_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));
        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $username = trim(strtolower(escape_value($_POST["username"])));
        $password = trim(password_encrypt(escape_value($_POST["password"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $sex = trim(ucwords(escape_value($_POST["sex"])));
        $contact_number = trim(escape_value($_POST["contact_number"]));
        $date_of_appointment = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_appointment"]))));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $regionName = trim(ucwords(escape_value($_POST["region"])));
        $user_type = trim(ucwords(escape_value($_POST["user_type"])));
        $email = trim(strtolower(escape_value($_POST["email"])));
        $qualification = trim(ucwords(escape_value($_POST["qualification"])));
        $post = trim(strtoupper(escape_value($_POST["post"])));

        $random_numbers = generate_id_numbers() . microtime();
        $user_hash = md5($random_numbers);

        if ($user_type === "Administrator") {
            $access_pages = "class//house//form_master//class_assignment//user//subject_combination//upload_picture//comment//terminal_report_setting//change_class//change_status//change_boarding_status//change_boarding_status_on//show_data//find_pupil_by_name//pupil//edit_pupil//academic_year//academic_term//delete_pupil//show_wsd//region//class_menu//level//institution_details//edit_institution_details//subject//display_data//admission//guardian//class_membership//update_pupilon//show_update_pupil//show_update_admission//show_update_guardian//edit_region//edit_level//edit_subject//edit_class//edit_house//editFormMaster//edit_class_assignment//assign_classes//edit_academic_year//edit_academic_term//edit_pupil_off//edit_user//show_data_off//view_details//verify_status//verify_class_membership//pupilon//edit_subject_combination//promotion//print_class_list//print_continuous_assessment//boarding_status//third_term_promotion//general_reports//print_house_report//print_staff_list//print_statistical_report//upload_logo//terminal_report_setting_on//delete_pupil_on//level_on//subject_combination_on//comment_on//change_class_membership//change_class_membership_on//change_boarding_status_on//print_terminal_reports//terminal_reports//back_up//print_broadsheet//student_sms//send_sms//bulk_student_sms//send_bulk_sms//send_bulk_sms_on//send_sms_on//sms_bulk_exam_results_on//sms_response_bal//upload_signature";
        } elseif ($user_type === "Head-Teacher") {
            $access_pages = "head_teacher//head_teacher_comment//list_students//show_terminal_reports//edit_head_teacher_comment//edit_remark";
        } elseif ($user_type === "Teacher") {
            $access_pages = "teacher//teachers_continuous_assessment//load_teaching_classes//continuous_assessment_entries//teacher_comment//class_teacher_students//teacher_terminal_report//edit_teacher_comment//class_teacher_members//edit_teacher_terminal_report//attendance";
        } elseif ($user_type === "Accountant") {
            $access_pages = "accountant//ledger//bill_item//student_bill//single_student_billing//edit_bill_item//edit_student_bill//edit_student_bill_on//student_bills//print_class_bills//billing//print_student_bill//print_receipt//print_payment_history//single_student_billing_on//ledger_on//debtors_creditors//print_debtors_creditors//fees_collected//print_fees_collected_today//print_fees_report//print_fees_report_accountant//print_fees_statement//student_charges//student_bill_on//print_income_and_expenditure//canteen//other_incomes//other_incomes_on//edit_other_incomes//edit_other_incomes_on//study_fees//expenses//expenses_on//edit_expenses//edit_expenditure_item//edit_income_item//sms_class_bills//sms_class_bills_on//sms_response_bal//print_pta_history";
        }
         elseif ($user_type === "Librarian") {
            $access_pages = "library//books//library_records//library_tray//edit_book//delete_book//search_student_to_library_tray";
        }else {
            $access_pages = "clerk//ledger//print_receipt//print_student_bill//print_payment_history//ledger_on//print_fees_collected_today";
        }

        $getUserById = $this->getUserByUsername($username);
        if ($getUserById["username"] === $username) {
            $this->userExistBanner();
        } else {
            $query = "INSERT INTO users (";
            $query .= "user_id, user_hash, username, password, school_number, family_name, other_names, date_of_birth, sex, contact_number, date_of_appointment, hometown, region, user_type, email, qualification, post, access_pages";
            $query .= ") VALUES (";
            $query .= "'{$user_id}', '{$user_hash}', '{$username}', '{$password}', '{$school_number}', '{$family_name}', '{$other_names}', '$date_of_birth', '{$sex}', '{$contact_number}', '$date_of_appointment', '{$hometown}', '{$regionName}', '{$user_type}', '{$email}', '{$qualification}', '{$post}', '{$access_pages}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result) {
                redirect_to("user.php");
//                $this->insertUserSuccessBanner();
            }
        }
    }

    public function getUserByUsername($username) {
        global $connection;

        $escape_username = mysqli_real_escape_string($connection, $username);

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE username = '{$escape_username}' ";
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

    public function getUsers() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY user_type DESC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getStaffCategory() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY user_type ASC ";
        $query .= "GROUP BY user_type";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getStaffByCategory($user_type) {
        global $connection;
        $safe_user_type = mysqli_real_escape_string($connection, $user_type);

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE user_type = '{$safe_user_type}' ";
        $query .= "AND post = 'YES' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getTeachers() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND user_type = 'Teacher' ";
        $query .= "AND post = 'YES' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getAccountClerks() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND user_type = 'Account Clerk' ";
        $query .= "AND post = 'YES' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getAccountants() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND user_type = 'Accountant' ";
        $query .= "AND post = 'YES' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getTeacherById($user_hash) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND user_type = 'Teacher' ";
        $query .= "AND post = 'YES' ";
        $query .= "AND user_hash = '{$user_hash}' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getTeacherInfo($user_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND user_type = 'Teacher' ";
        $query .= "AND post = 'YES' ";
        $query .= "AND user_id = '$user_id' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($row = mysqli_fetch_assoc($query_results)) {
            return $row;
        } else {
            return NULL;
        }
    }

    public function getDeletedTeachersById() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'YES' ";
        $query .= "AND user_type = 'Teacher' ";
        $query .= "AND post = 'YES' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getStaff() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND post = 'YES' ";
        $query .= "ORDER BY other_names ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getUserById($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE user_id = '{$id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($user = mysqli_fetch_assoc($query_results)) {
            return $user;
        } else {
            return NULL;
        }
    }

    public function getUserHash($url_hash) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE user_hash = '{$url_hash}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($user = mysqli_fetch_assoc($query_results)) {
            return $user;
        } else {
            return NULL;
        }
    }

    public function getUsersByCategory($category) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM users ";
        $query .= "WHERE user_id = '{$category}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($user = mysqli_fetch_assoc($query_results)) {
            return $user;
        } else {
            return NULL;
        }
    }

    public function updateUser($user_id) {
        global $connection;

        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $username = trim(strtolower(escape_value($_POST["username"])));
        $password = trim(password_encrypt(escape_value($_POST["password"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $sex = trim(ucwords(escape_value($_POST["sex"])));
        $contact_number = trim(escape_value($_POST["contact_number"]));
        $date_of_appointment = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_appointment"]))));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $region = trim(ucwords(escape_value($_POST["region"])));
        $user_type = trim(ucwords(escape_value($_POST["user_type"])));
        $email = trim(strtolower(escape_value($_POST["email"])));
        $qualification = trim(ucwords(escape_value($_POST["qualification"])));
        $post = trim(strtoupper(escape_value($_POST["post"])));

        $formMaster = new FormMaster();
        $name = $other_names . " " . $family_name;
        $getClassTeacher = $formMaster->getFormMasterByFullName($name);

        if ($user_type === "Administrator") {
            $access_pages = "class//house//form_master//class_assignment//user//subject_combination//upload_picture//comment//terminal_report_setting//change_class//change_status//change_boarding_status//change_boarding_status_on//show_data//find_pupil_by_name//pupil//edit_pupil//academic_year//academic_term//delete_pupil//show_wsd//region//class_menu//level//institution_details//edit_institution_details//subject//display_data//admission//guardian//class_membership//update_pupilon//show_update_pupil//show_update_admission//show_update_guardian//edit_region//edit_level//edit_subject//edit_class//edit_house//editFormMaster//edit_class_assignment//assign_classes//edit_academic_year//edit_academic_term//edit_pupil_off//edit_user//show_data_off//view_details//verify_status//verify_class_membership//pupilon//edit_subject_combination//promotion//print_class_list//print_continuous_assessment//boarding_status//third_term_promotion//general_reports//print_house_report//print_staff_list//print_statistical_report//upload_logo//terminal_report_setting_on//delete_pupil_on//level_on//subject_combination_on//comment_on//change_class_membership//change_class_membership_on//change_boarding_status_on//print_terminal_reports//terminal_reports//back_up//print_broadsheet//student_sms//send_sms//bulk_student_sms//send_bulk_sms//send_bulk_sms_on//send_sms_on//sms_bulk_exam_results_on//sms_response_bal//upload_signature//class_continous_assessment";
        } elseif ($user_type === "Head-Teacher") {
            $access_pages = "head_teacher//head_teacher_comment//list_students//show_terminal_reports//edit_head_teacher_comment//edit_remark";
        } elseif ($getClassTeacher["house_head"]) {
            $access_pages = "class_teacher//teachers_continuous_assessment//load_teaching_classes//continuous_assessment_entries//teacher_comment//class_teacher_students//teacher_terminal_report//edit_teacher_comment//class_teacher_members//edit_teacher_terminal_report";
        } elseif ($user_type === "Teacher") {
            $access_pages = "teacher//teachers_continuous_assessment//load_teaching_classes//continuous_assessment_entries//teacher_comment//class_teacher_students//teacher_terminal_report//edit_teacher_comment//class_teacher_members//edit_teacher_terminal_report//attendance";
        } elseif ($user_type === "Accountant") {
            $access_pages = "accountant//ledger//bill_item//student_bill//single_student_billing//edit_bill_item//edit_student_bill//edit_student_bill_on//student_bills//print_class_bills//print_class_bill//billing//print_student_bill//print_receipt//print_payment_history//single_student_billing_on//ledger_on//debtors_creditors//print_debtors_creditors//fees_collected//print_fees_collected_today//print_fees_report//print_fees_report_accountant//print_fees_statement//student_charges//student_bill_on//print_income_and_expenditure//canteen//other_incomes//other_incomes_on//edit_other_incomes//edit_other_incomes_on//study_fees//expenses//expenses_on//edit_expenses//edit_expenditure_item//edit_income_item//sms_class_bills//sms_class_bills_on//sms_response_bal//print_pta_history";
        } else {
            $access_pages = "clerk//ledger//print_receipt//print_student_bill//print_payment_history//ledger_on//print_fees_collected_today";
        }

        $query = "UPDATE users SET ";
        $query .= "family_name = '{$family_name}', ";
        $query .= "other_names = '{$other_names}', ";
        $query .= "username = '{$username}', ";
        $query .= "password = '{$password}', ";
        $query .= "date_of_birth = '$date_of_birth', ";
        $query .= "sex = '{$sex}', ";
        $query .= "contact_number = '{$contact_number}', ";
        $query .= "date_of_appointment = '{$date_of_appointment}', ";
        $query .= "hometown = '{$hometown}', ";
        $query .= "region = '{$region}', ";
        $query .= "user_type = '{$user_type}', ";
        $query .= "email = '{$email}', ";
        $query .= "qualification = '{$qualification}', ";
        $query .= "post = '{$post}', ";
        $query .= "access_pages = '{$access_pages}' ";
        $query .= "WHERE user_id = '{$user_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("user.php");
        }
    }

    public function deleteUser($user_hash) {
        global $connection;

        $query = "UPDATE users SET ";
        $query .= "deleted = 'YES', ";
        $query .= "post = 'NO' ";
        $query .= "WHERE user_hash = '{$user_hash}' ";
        $query .= "LIMIT 1";

        $userID = $this->getUserHash($user_hash);
        $user_id = $userID["user_id"];

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            $query = "UPDATE teacher_classes SET ";
            $query .= "deleted = 'YES' ";
            $query .= "WHERE user_id = '{$user_id}'";

            $query_result2 = mysqli_query($connection, $query);
            confirm_query($query_result2);

            if ($query_result2 && mysqli_affected_rows($connection) >= 0) {
                redirect_to("user.php");
            }
        }
    }

    public function userExistBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-error'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>USERNAME</strong> exists!</li>";
        echo "<li>Kindly, use a different username.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function editUserSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>STAFF INFO,</strong> successfully edited!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function insertUserSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>STAFF ACCOUNT,</strong> successfully <strong>created!</strong> Click <strong>Clear button</strong> to refresh the page.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function deleteUserSuccessBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>STAFF ACCOUNT,</strong> successfully deleted!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
