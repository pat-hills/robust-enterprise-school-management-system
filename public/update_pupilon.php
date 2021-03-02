<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/House.php';
require_once '../classes/Pupil.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$region = new Region();
$house = new House();
$institutionDetail = new InstitutionDetail();
$pupil = new Pupil();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("update_pupilon", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_pupil_success.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("family_name", "req", "Please, fill in the family name.");
        $validator->addValidation("other_names", "req", "Please, fill in the other names.");
        $validator->addValidation("date_of_birth", "req", "Please, fill in the date_of_birth.");
        $validator->addValidation("sex", "req", "Please, fill in the sex.");
        $validator->addValidation("hometown", "req", "Please, fill in the hometown.");
        $validator->addValidation("region", "req", "Please, fill in the region.");

        $school_number = trim(escape_value($_POST["school_number"]));
        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
//            $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
//            $sex = trim(ucwords(escape_value($_POST["sex"])));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
//            $region = trim(ucwords(escape_value($_POST["region"])));

        $_SESSION["pupil_id"] = $pupil_id;

        if ($validator->ValidateForm()) {
            $pupil->insertPupil();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, fill in all the <strong>FIELDS</strong> before you click the <strong>SAVE AND CONTINUE</strong> button!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $id_number = generate_id_numbers();
    $institution_set = $institutionDetail->getInstitutionDetails();
    $region_set = $region->getRegions();
    $house_set = $house->getHouses();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <div class="alert alert-success">
                    <?php
//                    echo "<strong>STEP 1 of 4:</strong>";
                    echo "<ul type='square'>";
                    echo "<li><strong>STUDENT INFORMATION</strong>, successfully edited!.</li>";
                    echo "<li>You may <strong>register</strong> another student by filling the form below.</li>";
                    echo "Note that the system generates a <strong>unique ID NUMBER</strong> each time this page is refreshed.";
                    echo "</ul>";
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" action="" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Pupil Details</strong></legend>

                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2" type="text" name="pupil_id" value="<?php echo $id_number; ?>" autocomplete="off">
                            </div>
                        </div>

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="family_name">Family Name</label>
                            <div class="controls">
                                <input class="span3" type="text" name="family_name" autocomplete="off"
                                       value="<?php
                                       if (isset($family_name)) {
                                           echo $family_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="other_names">Other Names</label>
                            <div class="controls">
                                <input class="span3" type="text" name="other_names" autocomplete="off"
                                       value="<?php
                                       if (isset($other_names)) {
                                           echo $other_names;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="date_of_birth">Date of Birth</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="date_of_birth" id="input-date-height" autocomplete="off">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sex">Sex</label>
                            <div class="controls">
                                <label class="radio inline">
                                    <input type="radio" name="sex" id="male" value="Male">
                                    <span class="metro-radio">Male</span>
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="sex" id="female" value="Female">
                                    <span class="metro-radio">Female</span>
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="hometown">Hometown</label>
                            <div class="controls">
                                <input class="span3" type="text" name="hometown" autocomplete="off"
                                       value="<?php
                                       if (isset($hometown)) {
                                           echo $hometown;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="region">Region</label>
                            <div class="controls">
                                <select name="region">
                                    <option value="">--Select Region--</option>
                                    <?php
                                    while ($region = mysqli_fetch_assoc($region_set)) {
                                        ?>
                                        <option value="<?php echo ucwords($region["name"]); ?>"><?php echo ucwords($region["name"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save and continue</button>
                                <a href="update_pupilon.php" class="btn btn-danger">Clear</a>
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

