<?php

require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("sms_response_bal", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_bulk_sms_response.php';
    
    echo "<div class='row'>";
    echo "<div class='span12'>";
    echo "<div class='alert alert-warning'>";
    echo "<button type='button' class='close' data-dismiss='alert'></button>";
    echo "<ul type='square'>";
    echo "<li>Insufficient credit in your account. Kindly, recharge!</li>";
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

