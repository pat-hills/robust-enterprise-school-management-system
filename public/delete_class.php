<?php

require_once '../includes/header.php';
require_once '../classes/Classes.php';

confirm_logged_in();

$classes = new Classes();

if (getURL()) {
    $classId = trim(escape_value(urldecode(getURL())));
}

$classes->deleteClassByURL($classId);

