<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_institution_details", $splitAccessPages, TRUE)) {
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
        $validator->addValidation("school_name", "req", "Please, fill in the school name.");
        $validator->addValidation("sms_tag_name", "req", "Please, fill in the SMS Tag Name.");
        $validator->addValidation("students_id_initials", "req", "Please, fill in the Code For ID.");
        $validator->addValidation("school_motor", "req", "Please, fill in the school motor.");
        $validator->addValidation("educational_cycle", "req", "Please, fill in the educational cycle.");
        $validator->addValidation("date_of_installation", "req", "Please, fill in the date of installation.");
        $validator->addValidation("telephone_1", "req", "Please, fill in the telephone 1.");

        $school_number = trim(ucwords(escape_value($_POST["school_number"])));
        $school_name = trim(ucwords(escape_value($_POST["school_name"])));
        $sms_tag_name = trim(strtoupper(escape_value($_POST["sms_tag_name"])));
        $students_id_initials = trim(strtoupper(escape_value($_POST["students_id_initials"])));
        $school_motor = trim(ucfirst(escape_value($_POST["school_motor"])));
        $educational_cycle = trim(ucwords(escape_value($_POST["educational_cycle"])));
        $date_of_installation = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_installation"]))));
        $telephone_1 = trim(escape_value($_POST["telephone_1"]));
        $bank_1 = trim(ucwords(escape_value($_POST["bank_1"])));
        $bank_1_branch = trim(ucwords(escape_value($_POST["bank_1_branch"])));
        $bank_1_account_number = trim(escape_value($_POST["bank_1_account_number"]));
        $bank_2 = trim(ucwords(escape_value($_POST["bank_2"])));
        $bank_2_branch = trim(ucwords(escape_value($_POST["bank_2_branch"])));
        $bank_2_account_number = trim(escape_value($_POST["bank_2_account_number"]));
        $bank_3 = trim(ucwords(escape_value($_POST["bank_3"])));
        $bank_3_branch = trim(ucwords(escape_value($_POST["bank_3_branch"])));
        $bank_3_account_number = trim(escape_value($_POST["bank_3_account_number"]));

        if ($validator->ValidateForm()) {
            $institutionDetail->updateInstitutionDetails();
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

    $getInstitutionDetails = $institutionDetail->getInstitutionDetails();

    if (TRUE == $show_form) {
        ?>

        <!--            <div class="row">
                        <div class="span12">
                            <div class="alert alert-info">
        <?php
        echo "<strong>STEP 2 of 4:</strong>";
        echo "<ul type='square'>";
        echo "<li>Fill in the <strong>SCHOOL DETAILS</strong> in the form below.</li>";
        echo "</ul>";
        ?>
                            </div>
                        </div>
                    </div>-->

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Edit School Details</strong></legend>
                        <div class="control-group">
                            <label class="control-label" for="school_number">School Number</label>
                            <div class="controls">
                                <input class="span2" type="text" name="school_number" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["school_number"]); ?>" disabled>
                                <input class="span2" type="hidden" name="school_number" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["school_number"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="school_name">School Name</label>
                            <div class="controls">
                                <input class="span6" type="text" name="school_name" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["school_name"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sms_tag_name">SMS Tag Name</label>
                            <div class="controls">
                                <input class="span2" type="text" name="sms_tag_name" maxlength="11" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["sms_tag_name"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="students_id_initials">Code For ID</label>
                            <div class="controls">
                                <input class="span2" type="text" name="students_id_initials" maxlength="11" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["students_id_initials"]); ?>">
                                       
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="school_motor">School Motto</label>
                            <div class="controls">
                                <input class="span4" type="text" name="school_motor" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["school_motor"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="educational_cycle">Educational Cycle</label>
                            <div class="controls">
                                <select name="educational_cycle" class="select-width">
                                    <option value="<?php echo htmlentities($getInstitutionDetails["educational_cycle"]); ?>"><?php echo htmlentities($getInstitutionDetails["educational_cycle"]); ?></option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="date_of_installation">Date of Installation</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="date" name="date_of_installation" class="text-box-width" id="input-date-height" autocomplete="off" value="<?php echo htmlentities(date("d-m-Y", strtotime($getInstitutionDetails["date_of_installation"]))); ?>">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_1">Contact Number 1</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_1" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["telephone_1"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_2">Contact Number 2</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_2" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["telephone_2"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_3">Contact Number 3</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_3" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["telephone_3"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="postal_address">Postal Address</label>
                            <div class="controls">
                                <input class="span6" type="text" name="postal_address" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["postal_address"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_1"><strong>Bank 1</strong></label>
                            <div class="controls">
                                <input class="span5" type="text" name="bank_1" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_1"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_1_branch">Bank 1 Branch</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_1_branch" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_1_branch"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_1_account_number">Bank 1 Account Number</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_1_account_number" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_1_account_number"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_2"><strong>Bank 2</strong></label>
                            <div class="controls">
                                <input class="span5" type="text" name="bank_2" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_2"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_2_branch">Bank 2 Branch</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_2_branch" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_2_branch"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_2_account_number">Bank 2 Account Number</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_2_account_number" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_2_account_number"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_3"><strong>Bank 3</strong></label>
                            <div class="controls">
                                <input class="span5" type="text" name="bank_3" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_3"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_3_branch">Bank 3 Branch</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_3_branch" autocomplete="off" 
                                       value="<?php echo htmlentities($getInstitutionDetails["bank_3_branch"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_3_account_number">Bank 3 Account Number</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_3_account_number" autocomplete="off" value="<?php echo htmlentities($getInstitutionDetails["bank_3_account_number"]); ?>">
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

