<?php
require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("third_term_promotion", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php require_once '../includes/breadcrumb_third_term_promotion.php'; ?>

    <div class="spacer"></div>

    <div class="row">
        <div class="span12">
            <div class="alert alert-info">
                <?php
                echo "<strong>TAKE NOTE</strong>";
                echo "<ul type='square'>";
                echo "<li>You can <strong>PROMOTE</strong> or <strong>REPEAT</strong> students <strong>only</strong> in <strong>THIRD TERM</strong>!</li>";
                echo "</ul>";
                ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
