 <?php

require_once '../includes/header.php';
//require_once '../classes/User.php';
//
//confirm_logged_in();
//
//$user = new User();

require_once '../classes/Library.php';




$lira = new Library();


if (getURL()) {
    $user_hash = trim(escape_value(urldecode(getURL())));
    
    $_SESSION['clear_id'] = $user_hash;
    
    
    $lira ->clearStudent();
    
    
    
}

 


