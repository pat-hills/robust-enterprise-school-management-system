<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/House.php';
require_once '../classes/User.php';

confirm_logged_in();

$house = new House();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_house", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $house_id = trim(escape_value(urldecode(getURL())));
} else {
    redirect_to("house.php");
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("name", "req", "Please, fill in the House name.");
        $validator->addValidation("houseHead", "req", "Please, fill in the House Master/Mistress.");
        $validator->addValidation("gender", "req", "Please, fill in the gender.");

        if ($validator->ValidateForm()) {
            $house->updateHouse($house_id);
        } else {
            $get_errors = $validator->GetErrors();

            foreach ($get_errors as $input_field_name => $error_msg) {
                echo "<div class='row'>";
                echo "<div class='span12'>";
                echo "<div class='alert alert-error'>";
                echo "<ul type = 'none'>";
                echo "<li>$error_msg</li>";
                echo "</ul>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
    }

    $institution = $institutionDetail->getInstitutionDetails();
    $getUsers = $user->getUsers();
    $house_id = $house->getHouseById($house_id);
    $house_set = $house->getHouses();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter House Details</strong></legend>      

                        <div class="control-group">
                            <label class="control-label" for="name">Name of House</label>
                            <div class="controls">
                                <input class="span2" type="text" name="name" id="name" value="<?php echo $house_id["name"]; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="houseHead">House Master/Mistress</label>
                            <div class="controls">
                                <select name="houseHead">
                                    <option value="<?php echo $house_id["houseHead"]; ?>"><?php echo $house_id["houseHead"]; ?></option>
                                    <?php
                                    foreach ($getUsers as $user) {
                                        ?>
                                        <option value="<?php echo $user["other_names"] . " " . $user["family_name"]; ?>"><?php echo $user["other_names"] . " " . $user["family_name"]; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="gender">Gender</label>
                            <div class="controls">
                                <select name="gender">
                                    <option value="<?php echo $house_id["gender"]; ?>"><?php echo $house_id["gender"]; ?></option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Mixed">Mixed</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save changes</button>
                                <a href="house.php" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="spacer"></div>

                <div class="row">
                    <div class="span12">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td colspan="6" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIST OF HOUSES</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="6">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600; width: 20%;">HOUSE</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600;">HOUSE MASTER/MISTRESS</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600; width: 20%;">GENDER</td>
                                    <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">ACTION</td>
                                </tr>
                                <?php
                                $i = 1;
                                while ($houses = mysqli_fetch_assoc($house_set)) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i++; ?></td>
                                        <td><?php echo $houses["name"]; ?></td>
                                        <td><?php echo $houses["houseHead"]; ?></td>
                                        <td><?php echo $houses["gender"]; ?></td>
                                        <td><a href="edit_house.php?id=<?php echo urlencode($houses["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                        <td><a href="delete_house.php?id=<?php echo urlencode($houses["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

