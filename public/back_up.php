<?php
ob_start();

require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/DBBackup.php';

confirm_logged_in();

$user = new User();
$dbBackup = new DBBackup();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("back_up", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$host = "localhost";
$db_user = "inak";
$pass = "";
$name = "iskool";

$dbBackup->backup_tables($host, $user, $pass, $name);

//backup_tables($host, $db_user, $pass, $name, $tables = '*');

require_once '../includes/footer.php';


    