<?php

require_once '../includes/header.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$bill = new Bill();

if (getURL()) {
    $url_hash = trim(escape_value(urldecode(getURL())));
    $expenditureID = $bill->getExpenditureHash($url_hash);
    $expenditure_id = $expenditureID["expense_id"];
}

$bill->deleteExpenditureItem($expenditure_id);


