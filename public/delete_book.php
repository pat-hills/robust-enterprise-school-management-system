 <?php

require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();
$user = new User();

require_once '../classes/Library.php';




$lira = new Library();


$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_book", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}


if (getURL()) {
    $user_hash = trim(escape_value(urldecode(getURL())));
    
    $_SESSION['deleted_id'] = $user_hash;
    
    
    $lira ->deleteBook();
    
    
    
}

 


