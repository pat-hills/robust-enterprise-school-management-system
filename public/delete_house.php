<?php

require_once '../includes/header.php';
require_once '../classes/House.php';

confirm_logged_in();

$house = new House();

if (getURL()) {
    $house_id = trim(escape_value(urldecode(getURL())));
}

$house->deleteHouse($house_id);

