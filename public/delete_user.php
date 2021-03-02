<?php

require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

if (getURL()) {
    $user_hash = trim(escape_value(urldecode(getURL())));
}

$user->deleteUser($user_hash);


