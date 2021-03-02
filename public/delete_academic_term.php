<?php

require_once '../includes/header.php';
require_once '../classes/AcademicTerm.php';

confirm_logged_in();

$academicTerm = new AcademicTerm();

if (getURL()) {
    $academicTermId = trim(escape_value(urldecode(getURL())));
}

$academicTerm->deleteAcademicTerm($academicTermId);


