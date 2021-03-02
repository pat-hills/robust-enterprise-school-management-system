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
    if (!in_array("institution_details", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($date_of_installation)) {
    $date_of_installation = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("school_number", "req", "Please, fill in the School Number.");
        $validator->addValidation("school_name", "req", "Please, fill in the School Name.");
        $validator->addValidation("sms_tag_name", "req", "Please, fill in the SMS Tag Name.");
        $validator->addValidation("students_id_initials", "req", "Please, fill in the Code For ID.");
        $validator->addValidation("school_motor", "req", "Please, fill in the School Motor.");
        $validator->addValidation("educational_cycle", "dontselect=--Select One--", "Please, fill in the Educational Cycle and Date of Installation.");
        $validator->addValidation("date_of_installation", "req", "Please, fill in the Educational Cycle and Date of Installation.");
        $validator->addValidation("telephone_1", "req", "Please, fill in the Contact Number 1.");

        $validator->addValidation("school_number", "num", "Please, fill in a valid School Number.");

        $school_number = trim(escape_value($_POST["school_number"]));
        $school_name = trim(strtoupper(escape_value($_POST["school_name"])));
        $sms_tag_name = trim(strtoupper(escape_value($_POST["sms_tag_name"])));
        $students_id_initials = trim(strtoupper(escape_value($_POST["students_id_initials"])));
        $school_motor = trim(ucfirst(escape_value($_POST["school_motor"])));
        $educational_cycle = trim(ucwords(escape_value($_POST["educational_cycle"])));
        $date_of_installation = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_installation"]))));
        $telephone_1 = trim(escape_value($_POST["telephone_1"]));
        $telephone_2 = trim(escape_value($_POST["telephone_2"]));
        $telephone_3 = trim(escape_value($_POST["telephone_3"]));
        $postal_address = trim(ucwords(escape_value($_POST["postal_address"])));
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
            $institutionDetail->insertInstitutionDetails();
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
                    echo "<strong>STEP 1 of 3:</strong>";
                    echo "<ul type='square'>";
                    echo "<li>Fill in the <strong>SCHOOL DETAILS</strong> in the form below.</li>";
                    echo "</ul>";
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Institution Details</strong></legend>
                        <div class="control-group">
                            <label class="control-label" for="school_number">School Number</label>
                            <div class="controls">
                                <input class="span2" type="text" name="school_number" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($school_number)) {
                                           echo $school_number;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="school_name">School Name</label>
                            <div class="controls">
                                <input class="span6" type="text" name="school_name" autocomplete="off"
                                       value="<?php
                                       if (isset($school_name)) {
                                           echo $school_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sms_tag_name">SMS Tag Name</label>
                            <div class="controls">
                                <input class="span2" type="text" name="sms_tag_name" maxlength="11" autocomplete="off"
                                       value="<?php
                                       if (isset($sms_tag_name)) {
                                           echo $sms_tag_name;
                                       }
                                       ?>">
                            </div>
                        </div>
                        
                        <div class="control-group">
                            <label class="control-label" for="students_id_initials">Code For ID</label>
                            <div class="controls">
                                <input class="span2" type="text" name="students_id_initials" maxlength="11" autocomplete="off"
                                       value="<?php
                                       if (isset($students_id_initials)) {
                                           echo $students_id_initials;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="school_motor">School Motto</label>
                            <div class="controls">
                                <input class="span4" type="text" name="school_motor" autocomplete="off"
                                       value="<?php
                                       if (isset($school_motor)) {
                                           echo $school_motor;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="educational_cycle">Educational Cycle</label>
                            <div class="controls">
                                <select name="educational_cycle" class="select-width">
                                    <option value="<?php
                                    if (isset($educational_cycle)) {
                                        echo $educational_cycle;
                                    } else {
                                        echo "--Select One--";
                                    }
                                    ?>">
                                                <?php
                                                if (isset($educational_cycle)) {
                                                    echo $educational_cycle;
                                                } else {
                                                    echo "--Select One--";
                                                }
                                                ?>
                                    </option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="date_of_installation">Date of Installation</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="date" name="date_of_installation" class="text-box-width" id="input-date-height" autocomplete="off" value="<?php
                                    if ($date_of_installation !== "1970-01-01" && $date_of_installation === "01-01-1970") {
                                        echo date("d-m-Y", strtotime($date_of_installation));
                                    }
                                    ?>">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="telephone_1">Contact Number 1</label>
                            <div class="controls">
                                <input class="span3" type="text" name="telephone_1" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off"
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
                                <input class="span3" type="text" name="telephone_2" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off"
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
                                <input class="span3" type="text" name="telephone_3" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off"
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
                                <input class="span6" type="text" name="postal_address" autocomplete="off"
                                       value="<?php
                                       if (isset($postal_address)) {
                                           echo $postal_address;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_1"><strong>Bank 1</strong></label>
                            <div class="controls">
                                <input class="span5" type="text" name="bank_1" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_1)) {
                                           echo $bank_1;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_1_branch">Bank 1 Branch</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_1_branch" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_1_branch)) {
                                           echo $bank_1_branch;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_1_account_number">Bank 1 Account Number</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_1_account_number" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_1_account_number)) {
                                           echo $bank_1_account_number;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_2"><strong>Bank 2</strong></label>
                            <div class="controls">
                                <input class="span5" type="text" name="bank_2" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_2)) {
                                           echo $bank_2;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_2_branch">Bank 2 Branch</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_2_branch" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_2_branch)) {
                                           echo $bank_2_branch;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_2_account_number">Bank 2 Account Number</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_2_account_number" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_2_account_number)) {
                                           echo $bank_2_account_number;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_3"><strong>Bank 3</strong></label>
                            <div class="controls">
                                <input class="span5" type="text" name="bank_3" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_3)) {
                                           echo $bank_3;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_3_branch">Bank 3 Branch</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_3_branch" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_3_branch)) {
                                           echo $bank_3_branch;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="bank_3_account_number">Bank 3 Account Number</label>
                            <div class="controls">
                                <input class="span3" type="text" name="bank_3_account_number" autocomplete="off"
                                       value="<?php
                                       if (isset($bank_3_account_number)) {
                                           echo $bank_3_account_number;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" id="submit" class="btn">Save and continue</button>
                                <a href="institution_details.php" class="btn large btn-danger">Clear</a>
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

