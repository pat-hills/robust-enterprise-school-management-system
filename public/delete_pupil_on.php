<?php
ob_start();

require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';

confirm_logged_in();

$pupil = new Pupil();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("delete_pupil_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_search.php';

    $pupil->deleteBanner();
    
    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "req", "Please, fill in the Student ID.");
        $validator->addValidation("reason", "req", "Please, give reasons.");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));

        if ($validator->ValidateForm()) {
            $pupil->deletePupil($pupil_id);
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type = 'none'>";
            echo "<li>All <strong>FIELDS</strong> are required!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Student's ID Number</strong></legend>
                        <div class="wrapper">
                            <div class="control-group">
                                <label class="control-label" for="pupil_id">ID Number</label>
                                <div class="controls">
                                    <input class="span2" type="text" name="pupil_id" autocomplete="off" autofocus value="<?php
                                    if (isset($pupil_id)) {
                                        echo $pupil_id;
                                    }
                                    ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="reason">Reason(s)</label>
                                <div class="controls">
                                    <textarea class="span3" rows="3" name="reason"></textarea>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" name="submit" class="btn" onclick="return confirm('Are you sure you want to delete this student\'s data ? If YES, click on OK, otherwise click on CANCEL')">Delete</button>
                                    <a href="delete_pupil.php" class="btn btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

