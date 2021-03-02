<?php

require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("sms_class_bills_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_sms_class_bills.php';

//    getSMSResponse($_SESSION["response"]);
    
    echo "<div class='row'>";
    echo "<div class='span12'>";
    echo "<div class='alert alert-success'>";
    echo "<button type='button' class='close' data-dismiss='alert'></button>";
    echo "<ul type='square'>";
    echo "<li>Student bills, successfully sent to all parents/guardians!</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    ?>

    <div class="row">
        <div class="span12">
            <legend class="legend"></legend>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

