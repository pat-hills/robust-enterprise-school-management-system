<?php

require_once '../classes/AcademicYear.php';
require_once '../includes/header.php';

confirm_logged_in();

$academicYear = new AcademicYear();

if (getURL()) {
    $academicYearId = trim(escape_value(urldecode(getURL())));
}

$academicYear->deleteAcademicYearByURL($academicYearId);


