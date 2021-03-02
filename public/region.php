<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/User.php';

confirm_logged_in();

$region = new Region();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("region", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("name", "req", "Please, fill in the name.");

        if ($validator->ValidateForm()) {
            $region->insertRegion();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, fill in the <strong>REGION NAME</strong> before you click the <strong>SAVE</strong> button!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $region_set = $region->getRegions();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Region</strong></legend>                        
                        <div class="control-group">
                            <label class="control-label" for="name">Name of Region</label>
                            <div class="controls">
                                <input class="span2" type="text" name="name" autocomplete="off" autofocus>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" id="submit" class="btn">Save</button>
                                <a href="region.php" class="btn large btn-danger">Clear</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="spacer"></div>

                <div class="center-table">
                    <div class="row">
                        <div class="span8">
                            <table class="table table-bordered table-condensed">
                                <tbody>
                                    <tr>
                                        <td colspan="4" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIST OF REGIONS</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 60%;">REGIONS</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    while ($regions = mysqli_fetch_assoc($region_set)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo $i++; ?></td>
                                            <td><?php echo $regions["name"]; ?></td>
                                            <td><a href="edit_region.php?id=<?php echo urlencode($regions["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="delete_region.php?id=<?php echo urlencode($regions["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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

