<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/Classes.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/FormMaster.php';

confirm_logged_in();

$classes = new Classes();
$user = new User();
$formMaster = new FormMaster();
$institutionDetail = new InstitutionDetail();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("editFormMaster", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';
    $institutionSet = $institutionDetail->getInstitutionDetails();
    $getClasses = $classes->getClasses();
    $getStaff = $user->getTeachers();
    $getFormMastersById = $formMaster->getFormMasterById(urldecode(getURL()));

    $show_form = TRUE;
    if (isset($_POST["school_number"], $_POST["class_name"], $_POST["house_head"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "req", "Please, select a class name.");
        $validator->addValidation("class_name", "alnum_s", "Please, fill in a valid class name.");

        $validator->addValidation("house_head", "req", "Please, select a form Master/Mistress.");

        if ($validator->ValidateForm()) {
            $formMaster->updateFormMaster(urldecode(getURL()));
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

    $formMasters = $formMaster->getFormMasters();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Edit Class Teacher Assignment</strong></legend>

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institutionSet["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="class_name">Classes</label>
                            <div class="controls">
                                <select name="class_name">
                                    <option value="<?php echo $getFormMastersById["class_name"]; ?>"><?php echo $getFormMastersById["class_name"]; ?></option>
                                    <?php
                                    while ($class = mysqli_fetch_assoc($getClasses)) {
                                        ?>
                                        <option value="<?php echo htmlentities($class["class_name"]); ?>"><?php echo htmlentities($class["class_name"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="house_head">Class Teacher</label>
                            <div class="controls">
                                <select name="house_head">
                                    <option value="<?php echo $getFormMastersById["house_head"]; ?>"><?php echo $getFormMastersById["house_head"]; ?></option>
                                    <?php
                                    while ($staff = mysqli_fetch_assoc($getStaff)) {
                                        ?>
                                        <option value="<?php echo ucwords($staff["other_names"] . " " . $staff["family_name"]); ?>"><?php echo ucwords($staff["other_names"] . " " . $staff["family_name"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="spacer"></div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save changes</button>
                                <a href="form_master.php" class="btn btn-danger">Cancel</a>
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
                                        <td colspan="5" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>CLASS TEACHERS AND RESPECTIVE CLASSES</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 10%; text-align: center;">CLASS</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">CLASS TEACHERS</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 25%; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    while ($form = mysqli_fetch_assoc($formMasters)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo $i++; ?></td>
                                            <td style="text-align: center;"><?php echo $form["class_name"]; ?></td>
                                            <td><?php echo $form["house_head"]; ?></td>
                                            <td><a href="editFormMaster.php?id=<?php echo urlencode($form["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="deleteFormMaster.php?id=<?php echo urlencode($form["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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




