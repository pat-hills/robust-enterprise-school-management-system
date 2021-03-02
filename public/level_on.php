<?php
ob_start();

require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Level.php';
require_once '../classes/User.php';

confirm_logged_in();

$level = new Level();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("level_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $level->insertLevelSuccessBanner();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("level_name", "req", "Please, fill in the level name.");

        if ($validator->ValidateForm()) {
            $level->insertLevel();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, fill in the <strong>LEVEL</strong> before you click the <strong>SAVE</strong> button!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $institutionDetail_set = $institutionDetail->getInstitutionDetails();
    $level_set = $level->getLevels();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" action="" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter level name</strong></legend>                        
                        <div class="control-group">
                            <label class="control-label" for="level_name">Level</label>
                            <div class="controls">
                                <input class="span2" type="text" name="level_name" autocomplete="off" autofocus>
                                <input type="hidden" name="school_number" value="<?php echo htmlentities($institutionDetail_set["school_number"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="level.php" class="btn large btn-danger">Clear</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="center-table">
                    <div class="row">
                        <div class="span8">
                            <table class="table table-bordered table-condensed">
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIST OF ACTIVE LEVELS</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%; text-align: center; font-weight: 600; background-color: #F5F5F5; width: 3%;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600;">LEVELS</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;

                                    while ($level_for_url = mysqli_fetch_assoc($level_set)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td><?php echo htmlentities($level_for_url["level_name"]); ?></td>
                                            <td><a href="edit_level.php?id=<?php echo urlencode($level_for_url["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="delete_level.php?id=<?php echo urlencode($level_for_url["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

