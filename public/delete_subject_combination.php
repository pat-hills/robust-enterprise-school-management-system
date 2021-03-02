<?php

require_once '../includes/header.php';
require_once '../classes/SubjectCombination.php';

confirm_logged_in();

$subjectCombination = new SubjectCombination();

if (getURL()) {
    $subject_combination_id = trim(escape_value(urldecode(getURL())));
}

$subjectCombination->deleteSubjectCombination($subject_combination_id);


