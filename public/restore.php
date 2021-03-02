<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';

confirm_logged_in();

$pupil = new Pupil();

if (getURL()) {
    $pupil_id = trim(escape_value(urldecode(getURL())));
    $getHash = $pupil->getDeletedPupilByUniqueUrlString($pupil_id);
    $getPupilId = $getHash["pupil_id"];
}

$pupil->reAdmitStudent($getPupilId);


