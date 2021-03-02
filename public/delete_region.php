<?php

require_once '../includes/header.php';
require_once '../classes/Region.php';

confirm_logged_in();

$region = new Region();

if (getURL()) {
    $region_id = trim(escape_value(urldecode(getURL())));
}

$region->deleteRegionByURL($region_id);


