<?php

require_once '../includes/header.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$bill = new Bill();

if (getURL()) {
    $getBillItemId = trim(escape_value(urldecode(getURL())));
}

$bill->deleteBillItemByURL($getBillItemId);


