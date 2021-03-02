<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/Region.php';
require_once '../classes/User.php';
require_once '../classes/Photo.php';


confirm_logged_in();

$pupil = new Pupil();
$region = new Region();
$user = new User();
$photo = new Photo();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);
$imgLink = $photo->getPhotoById($_SESSION["pupil_id"]);
$directory = $imgLink['photo_url'];

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("show_update_pupil", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_update.php';

    if (!isset($_FILES["photo"]["name"], $_FILES["photo"]["size"])) {
        $_FILES["photo"]["name"] = "";
        $_FILES["photo"]["size"] = "";
    }

    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["photo"]["name"]);
    $extension = end($temp);


    $getPupilById = $pupil->getPupilById($_SESSION["pupil_id"]);
    if ($_SESSION["pupil_id"] != $getPupilById["pupil_id"]) {
        redirect_to("edit_pupil_off.php");
    }

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("family_name", "req", "Please, fill in the family name.");
        $validator->addValidation("other_names", "req", "Please, fill in the other names.");
        $validator->addValidation("date_of_birth", "req", "Please, fill in the date of birth.");
        $validator->addValidation("sex", "req", "Please, fill in the sex.");
        $validator->addValidation("hometown", "req", "Please, fill in the hometown.");
        $validator->addValidation("region", "req", "Please, fill in the region.");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $school_number = trim(escape_value($_POST["school_number"]));

        if ($validator->ValidateForm()) {
            if (isset($_POST["submit"]) && (($_FILES["photo"]["type"] == "image/gif") || ($_FILES["photo"]["type"] == "image/jpeg") || ($_FILES["photo"]["type"] == "image/jpg") || ($_FILES["photo"]["type"] == "image/pjpeg") || ($_FILES["photo"]["type"] == "image/x-png") || ($_FILES["photo"]["type"] == "image/png")) && ($_FILES["photo"]["size"] < 1024000) && in_array($extension, $allowedExts)) {
                $photo->updatePhoto($pupil_id);
                 move_uploaded_file($_FILES["photo"]["tmp_name"], "../photos/" . $_FILES["photo"]["name"]);
            }
            $pupil->updatePupil($pupil_id);
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

    $getRegions = $region->getRegions();
    $checkPhotoUpload = $pupil->checkPhotoUpload();
    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Edit Pupil Details</strong></legend>
                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2" type="text" value="<?php echo htmlentities($getPupilById["pupil_id"]); ?>" disabled>
                                <input class="span2" type="hidden" name="pupil_id" value="<?php echo $_SESSION["pupil_id"]; ?>">
                            </div>
                        </div>

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">


                        <div class="control-group">
                            <label class="control-label" for="family_name">Family Name</label>
                            <div class="controls">
                                <input class="span3" type="text" name="family_name" value="<?php echo htmlentities($getPupilById["family_name"]); ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="other_names">Other Names</label>
                            <div class="controls">
                                <input class="span3" type="text" name="other_names" value="<?php echo htmlentities($getPupilById["other_names"]); ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="date_of_birth">Date of Birth</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="date_of_birth" class="text-box-width" value="<?php echo htmlentities(date("d-m-Y", strtotime($getPupilById["date_of_birth"]))); ?>" id="input-date-height" autocomplete="off">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sex">Sex</label>
                            <div class="controls">
                                <label class="radio inline">
                                    <input type="radio" name="sex" id="male" value="Male"
                                    <?php
                                    if ($getPupilById["sex"] == "Male") {
                                        echo "checked";
                                    }
                                    ?>>
                                    <span class="metro-radio">Male</span>
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="sex" id="female" value="Female"
                                    <?php
                                    if ($getPupilById["sex"] == "Female") {
                                        echo "checked";
                                    }
                                    ?>>
                                    <span class="metro-radio">Female</span>
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="hometown">Hometown</label>
                            <div class="controls">
                                <input class="span3" type="text" name="hometown" value="<?php echo htmlentities($getPupilById["hometown"]); ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="region">Region</label>
                            <div class="controls">
                                <select name="region" class="select-width">
                                    <option value="<?php echo htmlentities($getPupilById["region"]); ?>"><?php echo htmlentities($getPupilById["region"]); ?></option>

                                    <?php
                                    while ($region = mysqli_fetch_assoc($getRegions)) {
                                        ?>
                                        <option value="<?php echo htmlentities(ucwords($region["name"])); ?>"><?php echo htmlentities(ucwords($region["name"])); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="photo">Select Photograph</label>
                            <div class="controls">
                                <div class="imagges">
                                    <img src="../<?php echo $directory; ?>" style="width: 150px; height: 150px"/>
                                </div>
                                <input type="file" name="photo" />
                                <div class="controls">
                                </div>    
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save changes and continue</button>
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

