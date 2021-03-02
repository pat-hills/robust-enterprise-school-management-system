<?php
require_once '../includes/header.php';
require_once '../classes/RelationToPupil.php';
require_once '../classes/Pupil.php';
require_once '../classes/Guardian.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$relationToPupil = new RelationToPupil();
$pupil = new Pupil();
$guardian = new Guardian();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("show_update_guardian", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["pupil_id"])) {
    $_SESSION["pupil_id"] = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_update.php';

    $getPupilById = $pupil->getPupilById($_SESSION["pupil_id"]);
    $getGuardianByPupilId = $guardian->getGuardianByPupilId($_SESSION["pupil_id"]);
    $institution_set = $institutionDetail->getInstitutionDetails();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("guardian_family_name", "req", "Please, fill in the guardian's family name.");
        $validator->addValidation("guardian_other_names", "req", "Please, fill in the guardian's other names.");
        $validator->addValidation("occupation", "req", "Please, fill in the guardian's occupation.");
        $validator->addValidation("telephone_1", "req", "Please, fill in the guardian' telephone.");
        $validator->addValidation("relation_to_pupil", "req", "Please, fill in the guardian's relation to student.");

        if ($validator->ValidateForm()) {
            $guardian->updateGuardianByPupilId();
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

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Edit Guardian Details</strong></legend>
                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2" type="text" value="<?php echo $getPupilById["pupil_id"]; ?>" disabled>
                                <input class="span2" type="hidden" name="pupil_id" value="<?php echo $getPupilById["pupil_id"]; ?>">
                            </div>
                        </div>

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="guardian_family_name">Family Name</label>
                            <div class="controls">
                                <input class="span3" type="text" name="guardian_family_name" value="<?php echo $getGuardianByPupilId["guardian_family_name"]; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="guardian_other_names">Other Names</label>
                            <div class="controls">
                                <input class="span3" type="text" name="guardian_other_names" value="<?php echo $getGuardianByPupilId["guardian_other_names"]; ?>" autocomplete="off">
                            </div>
                        </div>
                        <br />
                        <div class="control-group">
                            <label class="control-label" for="guardian_sex">Sex</label>
                            <div class="controls">
                                <label class="radio inline">
                                    <input type="radio" name="guardian_sex" id="male" value="Male" value="<?php echo $getGuardianByPupilId["guardian_sex"]; ?>"
                                    <?php
                                    if ($getGuardianByPupilId["guardian_sex"] == "Male") {
                                        echo "checked";
                                    }
                                    ?>>
                                    <span class="metro-radio">Male</span>
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="guardian_sex" id="female" value="Female"
                                    <?php
                                    if ($getGuardianByPupilId["guardian_sex"] == "Female") {
                                        echo "checked";
                                    }
                                    ?>>
                                    <span class="metro-radio">Female</span>
                                </label>
                            </div>
                        </div>
                        <br />
                        <div class="control-group">
                            <label class="control-label" for="occupation">Occupation</label>
                            <div class="controls">
                                <input class="span3" type="text" name="occupation" value="<?php echo $getGuardianByPupilId["occupation"]; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="relation_to_pupil">Relation to Student</label>
                            <div class="controls">
                                <select name="relation_to_pupil" class="select-width">
                                    <option value="<?php echo $getGuardianByPupilId["relation_to_pupil"]; ?>"><?php echo $getGuardianByPupilId["relation_to_pupil"]; ?></option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Uncle">Uncle</option>
                                    <option value="Aunt">Aunt</option>
                                    <option value="Groundfather">Groundfather</option>
                                    <option value="Groundmother">Groundmother</option>
                                    <option value="Brother">Brother</option>
                                    <option value="Sister">Sister</option>
                                    <option value="Others">Guardian</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_1">Contact Number 1</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_1" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" value="<?php echo $getGuardianByPupilId["telephone_1"]; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_2">Contact Number 2</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_2" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" value="<?php echo $getGuardianByPupilId["telephone_2"]; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_3">Contact Number 3</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_3" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" value="<?php echo $getGuardianByPupilId["telephone_3"]; ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="postal_address">Postal Address</label>
                            <div class="controls">
                                <input class="span6" type="text" name="house_number" autocomplete="off" value="<?php echo $getGuardianByPupilId["postal_address"]; ?>" />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="house_number">House Number</label>
                            <div class="controls">
                                <input class="span6" type="text" name="house_number" autocomplete="off" value="<?php echo $getGuardianByPupilId["house_number"]; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save changes</button>
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

