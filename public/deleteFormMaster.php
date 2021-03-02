<?php

require_once '../classes/FormMaster.php';
require_once '../includes/header.php';

confirm_logged_in();

$formMaster = new FormMaster();

if (getURL()) {
    $id = trim(escape_value(urldecode(getURL())));
}

$formMaster->deleteFormMaster($id);
