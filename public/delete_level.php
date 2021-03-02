<?php

require_once '../includes/header.php';
require_once '../classes/Level.php';

confirm_logged_in();

$level = new Level();

if (getURL()) {
    $level_id = trim(escape_value(urldecode(getURL())));
}

$level->deleteLevelByURL($level_id);


