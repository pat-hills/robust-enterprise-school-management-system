<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/House.php';
require_once '../classes/User.php';

confirm_logged_in();

$house = new House();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("house", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$institutionDetail = new InstitutionDetail();
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("name", "req", "Please, fill in the House name.");
        $validator->addValidation("houseHead", "dontselect=Select Master/Mistress", "Please, fill in the House Master/Mistress.");
        $validator->addValidation("gender", "dontselect=Select Gender", "Please, fill in the gender.");

        $name = trim(ucwords(escape_value($_POST["name"])));
        $houseHead = trim(ucwords(escape_value($_POST["houseHead"])));
        $gender = trim(ucwords(escape_value($_POST["gender"])));

        if ($validator->ValidateForm()) {
            $house->insertHouse();
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
    $getUsers = $user->getTeachers();
    $house_set = $house->getHouses();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter House Details</strong></legend>      

                        <input type="hidden" name="school_number" value="<?php echo $institution["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="name">Name of House</label>
                            <div class="controls">
                                <input class="span2" type="text" name="name" autocomplete="off" autofocus>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="houseHead">House Master/Mistress</label>
                            <div class="controls">
                                <select name="houseHead">
                                    <option value="Select Master/Mistress">--Select Master/Mistress--</option>
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
                                    <option value="Select Gender">--Select Gender--</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Mixed">Mixed</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" id="submit" class="btn">Save</button>
                                <a href="house.php" class="btn btn-danger">Clear</a>
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
                                    <td style="background-color: #F5F5F5; font-weight: 600; width: 10%;">GENDER</td>
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

