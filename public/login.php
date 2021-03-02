<?php

require_once '../includes/header.php';
require_once '../classes/FormMaster.php';

if (empty($_POST["username"]) || empty($_POST["password"])) {
    redirect_to("index.php");
}

if (isset($_POST["submit"]) && !empty($_POST["username"]) && !empty($_POST["password"])) {
    $username = trim(escape_value($_POST["username"]));
    $password = trim(escape_value($_POST["password"]));

    $found_user = attempt_login($username, $password);

    if (!$found_user) {
        redirect_to("login_failure.php");
    } else {
        $_SESSION["user_id"] = $found_user["user_id"];
        $_SESSION["username"] = $found_user["username"];

        $user_set = find_user_by_username($_SESSION["username"]);
        $user_type = $user_set["user_type"];

        $name = $user_set["other_names"] . " " . $user_set["family_name"];

        $formMaster = new FormMaster();
        $getClassTeacher = $formMaster->getFormMasterByFullName($name);

        if ($user_type === "Administrator") {
            redirect_to("dashboard.php");
        } elseif ($user_type === "Head-Teacher") {
            redirect_to("head_teacher.php");
        } elseif ($getClassTeacher["house_head"]) {
            redirect_to("class_teacher.php");
        } elseif ($user_type === "Teacher") {
            redirect_to("teacher.php");
        } elseif ($user_type === "Accountant") {
            redirect_to("accountant.php");
        } elseif ($user_type === "Librarian") {
            redirect_to("library.php");
        }
        
        else {
            redirect_to("clerk.php");
        }
    }
}

if (isset($connection)) {
    mysqli_close($connection);
}