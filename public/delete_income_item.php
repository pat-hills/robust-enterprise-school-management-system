<?php

require_once '../includes/header.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$bill = new Bill();

if (getURL()) {
    $url_hash = trim(escape_value(urldecode(getURL())));
    $incomeID = $bill->getIncomeHash($url_hash);
    $income_id = $incomeID["income_id"];
}

$bill->deleteIncomeItem($income_id);


