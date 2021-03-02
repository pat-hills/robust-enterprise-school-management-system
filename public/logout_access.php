<?php

require_once '../includes/header.php';

$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), "", time()-42000, "/");
}
session_destroy();

redirect_to("access_denied.php");

