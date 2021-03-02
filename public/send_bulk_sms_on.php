<?php

require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("send_bulk_sms_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_send_bulk_sms.php';
    
    echo "<div class='row'>";
    echo "<div class='span12'>";
    echo "<div class='alert alert-success'>";
    echo "<button type='button' class='close' data-dismiss='alert'></button>";
    echo "<ul type='square'>";
    echo "<li>Message, successfully sent!</li>";
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

