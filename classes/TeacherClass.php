<?php

require_once '../includes/header.php';

class TeacherClass {

    public function insertTeacherClass() {
        global $connection;

        if (isset($_POST["submit"])) {
            $school_number = trim(escape_value($_POST["school_number"]));
            $academic_year = trim(escape_value($_POST["academic_year"]));
            $name = trim(ucwords(escape_value($_POST["name"])));
            $user_id = trim(escape_value($_POST["user_id"]));
            $url_string = trim(escape_value($_POST["url_string"]));
            $subject_name = trim(ucfirst(escape_value($_POST["subject_name"])));
            $class_names = trim(strtoupper(escape_value(implode("-", $_POST["class_names"]))));

            $query = "INSERT INTO teacher_classes (";
            $query .= "school_number, name, user_id, url_string, subject_name, class_names, academic_year";
            $query .= ") VALUES (";
            $query .= "'{$school_number}', '{$name}', '{$user_id}', '{$url_string}', '{$subject_name}', '{$class_names}', '{$academic_year}'";
            $query .= ")";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if (!$query_result) {
//                redirect_to("classAssignmentError.php");
            } else {
                redirect_to("class_assignment.php");
            }
        }
    }

    public function getTeachingClasses($school_number) {
        global $connection;

        $escape_school_number = escape_value($school_number);
        
        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND school_number = '{$escape_school_number}' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getClassNamesAndSubjects() {
        global $connection;

        $query = "SELECT subject_name, class_names ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "ORDER BY subject_name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getTeachingClassesById($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE id = '{$id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function getTeachingClassesByAutoId($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE id = '{$id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($user = mysqli_fetch_assoc($query_results)) {
            return $user;
        } else {
            return NULL;
        }
    }
    
    public function getSubjectsAndClassNamesByUserId($user_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE user_id = '{$user_id}' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;

//        if ($subjectsClassNames = mysqli_fetch_assoc($query_results)) {
//            return $subjectsClassNames;
//        } else {
//            return NULL;
//        }
    }

    public function getClassesByAutoId($id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE id = '{$id}' ";
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
    
    public function getClassAutoIdByUserID($user_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE user_id = '{$user_id}' ";
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
    
    public function getClassesByURL($url) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE url_string = '{$url}' ";
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

    public function getSubjectByTeacherId($user_id) {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE deleted = 'NO' ";
        $query .= "AND user_id = '{$user_id}' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        if ($teacher = mysqli_fetch_assoc($query_results)) {
            return $teacher;
        }
        return NULL;
    }

    public function updateTeacherClass($id) {
        global $connection;

        $subject_name = trim(ucfirst(escape_value($_POST["subject_name"])));
        $class_names = trim(strtoupper(escape_value(implode("-", $_POST["class_names"]))));

        $query = "UPDATE teacher_classes SET ";
        $query .= "subject_name = '{$subject_name}', ";
        $query .= "class_names = '{$class_names}' ";
        $query .= "WHERE id = '$id' ";
        $query .= "AND deleted = 'NO' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("class_assignment.php");
        }
    }

    public function deleteTeacherClass($id) {
        global $connection;

        $query = "UPDATE teacher_classes SET ";
        $query .= "deleted = 'YES' ";
        $query .= "WHERE id = '{$id}' ";
        $query .= "LIMIT 1";

        $query_result = mysqli_query($connection, $query);
        confirm_query($query_result);

        if ($query_result && mysqli_affected_rows($connection) >= 0) {
            redirect_to("class_assignment.php");
        }
    }

    public function activateTeacherClass() {
        global $connection;

        if (isset($_GET["id"])) {
            $user_id = trim(escape_value($_GET["id"]));

            $query = "UPDATE teacher_classes SET ";
            $query .= "deleted = 'NO' ";
            $query .= "WHERE user_id = '{$user_id}' ";
            $query .= "LIMIT 1";

            $query_result = mysqli_query($connection, $query);
            confirm_query($query_result);

            if ($query_result && mysqli_affected_rows($connection) >= 0) {
                redirect_to("activateTeacherAccountSuccess.php");
            }
        }
    }

    public function getDeletedTeachers() {
        global $connection;

        $query = "SELECT * ";
        $query .= "FROM teacher_classes ";
        $query .= "WHERE deleted = 'YES' ";
        $query .= "ORDER BY name ASC";

        $query_results = mysqli_query($connection, $query);
        confirm_query($query_results);

        return $query_results;
    }

    public function classAssignmentErrorBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-danger'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>ACTIVATE</strong> this teacher account before class and subject assignment.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public function activateTeacherBanner() {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-success'>";
        echo "<button type='button' class='close' data-dismiss='alert'></button>";
        echo "<ul type='square'>";
        echo "<li><strong>TEACHER ACCOUNT,</strong> successfully activated!</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

}
