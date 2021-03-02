<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';

confirm_logged_in();

$pupil = new Pupil();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_pupil_off", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_search.php';

    $pupil->noRecordFoundBanner();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "req", "Please, fill in the ID number.");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
     $_SESSION["pupil_id"] = strtoupper($pupil_id);

        if ($validator->ValidateForm()) {
            redirect_to("show_update_pupil.php");
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, fill in the <strong>ID Number</strong> before you click the <strong>FIND</strong> button!</li>";
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
                        <legend style="color: #4F1ACB;"><strong>Find Student's Details</strong></legend>
                        <div class="wrapper">
                            <div class="control-group">
                                <label class="control-label" for="pupil_id">ID Number</label>
                                <div class="controls">
                                    <input class="span2" type="text" name="pupil_id" autocomplete="off" autofocus>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" name="submit" class="btn">Find</button>
                                    <a href="edit_pupil.php" class="btn large btn-danger">Clear</a>
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

