<?php

require_once '../includes/header.php';
require_once '../classes/TeacherClass.php';

confirm_logged_in();

$teacherClass = new TeacherClass();

if (getURL()) {
    $id = trim(escape_value(urldecode(getURL())));
}

$teacherClass->deleteTeacherClass($id);
