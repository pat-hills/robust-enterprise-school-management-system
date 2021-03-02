<?php
require_once '../includes/header.php';
require_once '../classes/Guardian.php';
require_once '../classes/Pupil.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/RelationToPupil.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$guardian = new Guardian();
$relationToPupil = new RelationToPupil();
$pupil = new Pupil();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("guardian", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_guardian.php';

    $institutionDetail_set = $institutionDetail->getInstitutionDetails();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("guardian_family_name", "req", "Please, fill in the guardian's Family name.");
        $validator->addValidation("guardian_other_names", "req", "Please, fill in the guardian's Other names.");
        $validator->addValidation("occupation", "req", "Please, fill in the guardian's Occupation.");
        $validator->addValidation("telephone_1", "req", "Please, fill in the guardian' Contact Number 1.");
        $validator->addValidation("relation_to_pupil", "dontselect=--Select One--", "Please, fill in the guardian's Relation to Student.");
        $validator->addValidation("guardian_sex", "req", "Please, select the guardian's gender.");
//        $validator->addValidation("postal_address", "req", "Please, fill in the guardian's Postal Address.");
//        $validator->addValidation("house_number", "req", "Please, fill in the guardian's House Number.");

        $school_number = trim(escape_value($_POST["school_number"]));
        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $guardian_family_name = trim(ucwords(escape_value($_POST["guardian_family_name"])));
        $guardian_other_names = trim(ucwords(escape_value($_POST["guardian_other_names"])));
//            $guardian_sex = trim(ucwords(escape_value($_POST["guardian_sex"])));
        $occupation = trim(ucwords(escape_value($_POST["occupation"])));
        $telephone_1 = trim(escape_value($_POST["telephone_1"]));
        $telephone_2 = trim(escape_value($_POST["telephone_2"]));
        $telephone_3 = trim(escape_value($_POST["telephone_3"]));
        $relation_to_pupil = trim(ucwords(escape_value($_POST["relation_to_pupil"])));
        $postal_address = trim(ucwords(escape_value($_POST["postal_address"])));
        $house_number = trim(ucwords(escape_value($_POST["house_number"])));

        $_SESSION["pupil_id"] = $pupil_id;
//            $_SESSION["other_names"] = $other_names;

        if ($validator->ValidateForm()) {
            $guardian->insertGuardian();
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

                <div class="alert alert-info">
                    <?php
                    echo "<strong>STEP 3 of 4:</strong>";
                    echo "<ul type='square'>";
                    echo "<li>Fill in the <strong>guardian details</strong> of the pupil in the form below.</li>";
                    echo "</ul>";
                    ?>
                </div>

                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Guardian Details</strong></legend>

                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2" type="text" value="<?php echo $_SESSION["pupil_id"]; ?>" disabled>
                                <input class="span2" type="hidden" name="pupil_id" value="<?php echo $_SESSION["pupil_id"]; ?>">
                            </div>
                        </div>

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institutionDetail_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="guardian_family_name">Family Name</label>
                            <div class="controls">
                                <input class="span4" type="text" name="guardian_family_name" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($guardian_family_name)) {
                                           echo $guardian_family_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="guardian_other_names">Other Names</label>
                            <div class="controls">
                                <input class="span4" type="text" name="guardian_other_names" autocomplete="off"
                                       value="<?php
                                       if (isset($guardian_other_names)) {
                                           echo $guardian_other_names;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="guardian_sex">Sex</label>
                            <div class="controls">
                                <label class="radio inline">
                                    <input type="radio" name="guardian_sex" value="Male">
                                    <span class="metro-radio">Male</span>
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="guardian_sex" value="Female">
                                    <span class="metro-radio">Female</span>
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="occupation">Occupation</label>
                            <div class="controls">
                                <input class="span3" type="text" name="occupation" autocomplete="off"
                                       value="<?php
                                       if (isset($occupation)) {
                                           echo $occupation;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="relation_to_pupil">Relation To Student</label>
                            <div class="controls">
                                <select name="relation_to_pupil" class="select-width">
                                    <option value="<?php
                                    if (isset($relation_to_pupil)) {
                                        echo $relation_to_pupil;
                                    } else {
                                        echo "--Select One--";
                                    }
                                    ?>">
                                                <?php
                                                if (isset($relation_to_pupil)) {
                                                    echo $relation_to_pupil;
                                                } else {
                                                    echo "--Select One--";
                                                }
                                                ?>
                                    </option>
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
                                <input class="span2" type="text" name="telephone_1" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off"
                                       value="<?php
                                       if (isset($telephone_1)) {
                                           echo $telephone_1;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_2">Contact Number 2</label>
                            <div class="controls">
                                <input class="span2" type="text" name="telephone_2" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$"
                                       value="<?php
                                       if (isset($telephone_2)) {
                                           echo $telephone_2;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_3">Contact Number 3</label>
                            <div class="controls">
                                <input class="span2" type="text" name="telephone_3" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$"
                                       value="<?php
                                       if (isset($telephone_3)) {
                                           echo $telephone_3;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="postal_address">Postal Address</label>
                            <div class="controls">
                                <input class="span6" type="text" name="postal_address" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($postal_address)) {
                                           echo $postal_address;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="house_number">House Number</label>
                            <div class="controls">
                                <input class="span6" type="text" name="house_number" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($house_number)) {
                                           echo $house_number;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save and continue</button>
                                <a href="guardian.php" class="btn btn-danger large">Clear</a>
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

