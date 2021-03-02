<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/House.php';
require_once '../classes/RelationToPupil.php';
require_once '../classes/Admission.php';
require_once '../classes/Guardian.php';
require_once '../classes/Pupil.php';
require_once '../classes/Photo.php';
require_once '../classes/User.php';

confirm_logged_in();

$region = new Region();
$house = new House();
$relationToPupil = new RelationToPupil();
$admission = new Admission();
$guardian = new Guardian();
$pupil = new Pupil();
$photo = new Photo();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("display_data", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$pupil_set = $pupil->getPupilById($_SESSION["pupil_id"]);
if ($_SESSION["pupil_id"] != $pupil_set["pupil_id"]) {
    redirect_to("show_data_off.php");
}

$admission_set = $admission->getAdmissionById($_SESSION["pupil_id"]);
$guardian_set = $guardian->getGuardianByPupilId($_SESSION["pupil_id"]);
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_print_result.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "req", "Please, fill in the <strong>ID Number</strong> before you click the <strong>FIND</strong> button!");

        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $_SESSION["pupil_id"] = $pupil_id;

        if ($validator->ValidateForm()) {
            redirect_to("display_data.php");
        } else {
            $get_errors = $validator->GetErrors();

            foreach ($get_errors as $input_field_name => $error_msg) {
                echo "<div class='row'>";
                echo "<div class='span12'>";
                echo "<div class='alert alert-error'>";
                echo "<button type='button' class='close' data-dismiss='alert'></button>";
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
                        <legend style="color: #4F1ACB;"><strong>Find Student Details</strong></legend>
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
                                    <a href="display_data.php" class="btn large btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <?php $getStudentPhoto = $photo->getPhotoById($_SESSION["pupil_id"]); ?>

                <div id="alignPhoto">
                    <img src="<?php echo "../" . $getStudentPhoto["photo_url"]; ?>" width="100">
                </div> 

                <div class="center-table">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td colspan="2" style="text-align: center; background-color: #F5F5F5; font-weight: 600;"><strong>STUDENT DETAILS</strong></td>
                            </tr>
                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">ID Number</td><td><?php echo $pupil_set["pupil_id"]; ?></td>
                            </tr>

                            <tr>
                                <td class="td-width" style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Full Name</td><td><?php echo $pupil_set["other_names"] . " " . $pupil_set["family_name"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Date of Birth</td><td>
                                    <?php
                                    if (empty($pupil_set["date_of_birth"])) {
                                        echo "";
                                    } else {
                                        echo date("F j, Y", strtotime($pupil_set["date_of_birth"]));
                                    }
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Sex</td><td><?php echo $pupil_set["sex"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Hometown</td><td><?php echo $pupil_set["hometown"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Region</td><td><?php echo $pupil_set["region"]; ?></td>
                            </tr>

                            <tr>
                                <td colspan="2" style="text-align: right; background-color: #fff;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center; background-color: #F5F5F5; font-weight: 600;"><strong>ADMISSION DETAILS</strong></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Admission Date</td><td>
                                    <?php
                                    if (empty($admission_set["admission_date"])) {
                                        echo "";
                                    } else {
                                        echo date("F j, Y", strtotime($admission_set["admission_date"]));
                                    }
                                    ?>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Class</td><td><?php echo $admission_set["class_admitted_to"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Boarding Status</td><td><?php echo $admission_set["boarding_status"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">House</td><td><?php echo $admission_set["house"]; ?></td>
                            </tr>

                            <tr>
                                <td colspan="2" style="text-align: right; background-color: #fff;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center; background-color: #F5F5F5;"><strong>GUARDIAN DETAILS</strong></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Family Name</td><td><?php echo $guardian_set["guardian_family_name"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Other Names</td><td><?php echo $guardian_set["guardian_other_names"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Sex</td><td><?php echo $guardian_set["guardian_sex"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Occupation</td><td><?php echo $guardian_set["occupation"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Relation To Pupil</td><td><?php echo $guardian_set["relation_to_pupil"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Contact Numbers</td><td><?php echo $guardian_set["telephone_1"] . " " . " " . " " . $guardian_set["telephone_2"] . " " . " " . " " . $guardian_set["telephone_3"]; ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Postal Address</td><td><?php echo ucwords($guardian_set["postal_address"]); ?></td>
                            </tr>

                            <tr>
                                <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">House Number</td><td><?php echo ucwords($guardian_set["house_number"]); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="show_data.php" class="btn btn-primary pull-right">Back</a>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

