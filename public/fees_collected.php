<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/Admission.php';

confirm_logged_in();

$user = new User();
$admission = new Admission();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("fees_collected", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($begin_date, $end_date, $_SESSION["account_clerk"], $_SESSION["clerk"], $_SESSION["accountant"])) {
    $begin_date = "";
    $end_date = "";
    $_SESSION["account_clerk"] = "";
    $_SESSION["clerk"] = "";
    $_SESSION["accountant"] = "";
}
?>

<div class="container">
    <?php require_once '../includes/breadcrumb_fees.php'; ?>

    <div class="row">
        <div class="span12">
            <legend style="color: #4F1ACB;"><strong>Fees Reports by Account Clerks</strong></legend> 

            <table class="table table-condensed">
                <tr>
                    <td style="width: 50%;">
                        <?php
                        $show_form_fee = TRUE;
                        if (isset($_POST["submit_fee_report"])) {
                            $validator = new FormValidator();
                            $validator->addValidation("begin_date", "req", "Please, select <strong>Begin Date</strong>.");
                            $validator->addValidation("end_date", "req", "Please, select <strong>End Date</strong>.");
                            $validator->addValidation("clerk", "dontselect=--Select Account Clerk--", "Please, select <strong>Account Clerk</strong>.");

                            $begin_date = trim(escape_value(date("Y-m-d", strtotime($_POST["begin_date"]))));
                            $end_date = trim(escape_value(date("Y-m-d", strtotime($_POST["end_date"]))));
                            $clerk = trim(ucwords(escape_value($_POST["clerk"])));

                            $_SESSION["beginning_date"] = $begin_date;
                            $_SESSION["ending_date"] = $end_date;
                            $_SESSION["clerk"] = $clerk;

                            if ($validator->ValidateForm()) {
//                                redirect_to("print_house_report.php");
                            } else {
                                echo "<div class='row'>";
                                echo "<div class='span5'>";
                                echo "<div class='alert alert-error'>";
                                echo "<ul type='square'>";
                                echo "<li>All <strong>fields</strong> are required.</li>";
                                echo "</ul>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }

                        $getAccountClerk = $user->getAccountClerks();

                        if (TRUE == $show_form_fee) {
                            ?>
                            <form class="form-horizontal" method="post">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="clerk">Account Officers</label>
                                        <div class="controls">
                                            <select name="clerk">
                                                <?php
                                                foreach ($getAccountClerk as $getClerk) {
                                                    ?>
                                                    <option value="<?php
                                                    if (isset($clerk)) {
                                                        echo $clerk;
                                                    } else {
                                                        echo "--Select Account Clerk--";
                                                    }
                                                    ?>">
                                                                <?php
                                                                if (isset($clerk)) {
                                                                    echo $clerk;
                                                                } else {
                                                                    echo "--Select Account Clerk--";
                                                                }
                                                                ?>
                                                    </option>

                                                    <option value="<?php echo htmlentities($getClerk["other_names"] . " " . $getClerk["family_name"]); ?>"><?php echo htmlentities($getClerk["other_names"] . " " . $getClerk["family_name"]); ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="begin_date">Begin Date</label>
                                        <div class="controls">
                                            <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                                <input type="text" name="begin_date" id="input-date-height" autocomplete="off" value="<?php
                                                if ($begin_date !== "1970-01-01" && $begin_date === "01-01-1970") {
                                                    echo date("d-m-Y", strtotime($begin_date));
                                                }
                                                ?>">
                                                <span class="add-on"><i class="icon-calendar-5"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="end_date">End Date</label>
                                        <div class="controls">
                                            <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                                <input type="text" name="end_date" id="input-date-height" autocomplete="off" value="<?php
                                                if ($end_date !== "1970-01-01" && $end_date === "01-01-1970") {
                                                    echo date("d-m-Y", strtotime($end_date));
                                                }
                                                ?>">
                                                <span class="add-on"><i class="icon-calendar-5"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit_fee_report" class="btn">Find</button><br /><br />
                                            <a href="print_fees_report.php" class="btn btn-info" target="_blank">Print fees collected by <?php echo $_SESSION["clerk"]; ?></a>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                        }
                        ?>
                    </td>

                    <td style="width: 50%;">
                        <?php
                        $show_form_account = TRUE;
                        if (isset($_POST["submit_account_officer_report"])) {
                            $validator = new FormValidator();
                            $validator->addValidation("account_clerk", "dontselect=--Select Account Clerk--", "Please, select <strong>Account Clerk</strong>.");

                            $account_clerk = trim(ucwords(escape_value($_POST["account_clerk"])));
                            $_SESSION["account_clerk"] = $account_clerk;

                            if ($validator->ValidateForm()) {
//            $academicTerm->insertAcademicTerm();
                            } else {
                                $get_errors = $validator->GetErrors();

                                foreach ($get_errors as $input_field_name => $error_msg) {
                                    echo "<div class='row'>";
                                    echo "<div class='span5'>";
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

                        $getAccountClerks = $user->getAccountClerks();

                        if (TRUE == $show_form_account) {
                            ?>
                            <form class="form-horizontal" method="post">
                                <fieldset>                      
                                    <div class="control-group">
                                        <label class="control-label" for="account_clerk">Account Officers</label>
                                        <div class="controls">
                                            <select name="account_clerk">
                                                <?php
                                                foreach ($getAccountClerks as $accountClerk) {
                                                    ?>
                                                    <option value="<?php
                                                    if (isset($account_clerk)) {
                                                        echo $account_clerk;
                                                    } else {
                                                        echo "--Select Account Clerk--";
                                                    }
                                                    ?>">
                                                                <?php
                                                                if (isset($account_clerk)) {
                                                                    echo $account_clerk;
                                                                } else {
                                                                    echo "--Select Account Clerk--";
                                                                }
                                                                ?>
                                                    </option>

                                                    <option value="<?php echo htmlentities($accountClerk["other_names"] . " " . $accountClerk["family_name"]); ?>"><?php echo htmlentities($accountClerk["other_names"] . " " . $accountClerk["family_name"]); ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit_account_officer_report" class="btn">Find</button><br /><br />
                                            <a href="print_fees_collected.php" class="btn btn-info" target="_blank" style="margin-left: 0px;">Print today's fees collected by <?php echo $_SESSION["account_clerk"]; ?></a>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <td style="width: 50%;">

                <legend style="color: #4F1ACB;"><strong>Fees Reports by Accountants</strong></legend> 

                <?php
                $show_form_accountant = TRUE;
                if (isset($_POST["submit_fee_report_accountant"])) {
                    $validator = new FormValidator();
                    $validator->addValidation("begin_date", "req", "Please, select <strong>Begin Date</strong>.");
                    $validator->addValidation("end_date", "req", "Please, select <strong>End Date</strong>.");
                    $validator->addValidation("accountant", "dontselect=--Select Accountant--", "Please, select <strong>Accountant</strong>.");

                    $begin_date = trim(escape_value(date("Y-m-d", strtotime($_POST["begin_date"]))));
                    $end_date = trim(escape_value(date("Y-m-d", strtotime($_POST["end_date"]))));
                    $accountant = trim(ucwords(escape_value($_POST["accountant"])));

                    $_SESSION["beginning_date_accountant"] = $begin_date;
                    $_SESSION["ending_date_accountant"] = $end_date;
                    $_SESSION["accountant"] = $accountant;

                    if ($validator->ValidateForm()) {
//                                redirect_to("print_house_report.php");
                    } else {
                        echo "<div class='row'>";
                        echo "<div class='span5'>";
                        echo "<div class='alert alert-error'>";
                        echo "<ul type='square'>";
                        echo "<li>All <strong>fields</strong> are required.</li>";
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                }

                $getAccountants = $user->getAccountants();

                if (TRUE == $show_form_accountant) {
                    ?>
                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <div class="control-group">
                                <label class="control-label" for="accountant">Accountant</label>
                                <div class="controls">
                                    <select name="accountant">
                                        <?php
                                        foreach ($getAccountants as $getAccountant) {
                                            ?>
                                            <option value="<?php
                                            if (isset($accountant)) {
                                                echo $accountant;
                                            } else {
                                                echo "--Select Accountant--";
                                            }
                                            ?>">
                                                        <?php
                                                        if (isset($accountant)) {
                                                            echo $accountant;
                                                        } else {
                                                            echo "--Select Accountant--";
                                                        }
                                                        ?>
                                            </option>

                                            <option value="<?php echo htmlentities($getAccountant["other_names"] . " " . $getAccountant["family_name"]); ?>"><?php echo htmlentities($getAccountant["other_names"] . " " . $getAccountant["family_name"]); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="begin_date">Begin Date</label>
                                <div class="controls">
                                    <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                        <input type="text" name="begin_date" id="input-date-height" autocomplete="off" value="<?php
                                        if ($begin_date !== "1970-01-01" && $begin_date === "01-01-1970") {
                                            echo date("d-m-Y", strtotime($begin_date));
                                        }
                                        ?>">
                                        <span class="add-on"><i class="icon-calendar-5"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="end_date">End Date</label>
                                <div class="controls">
                                    <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                        <input type="text" name="end_date" id="input-date-height" autocomplete="off" value="<?php
                                        if ($end_date !== "1970-01-01" && $end_date === "01-01-1970") {
                                            echo date("d-m-Y", strtotime($end_date));
                                        }
                                        ?>">
                                        <span class="add-on"><i class="icon-calendar-5"></i></span>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" name="submit_fee_report_accountant" class="btn">Find</button><br /><br />
                                    <a href="print_fees_report_accountant.php" class="btn btn-info" target="_blank">Print fees collected by <?php echo $_SESSION["accountant"]; ?></a>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    <?php
                }
                ?>
                </td>

                <td>
                    <br />
                    <a href="print_fees_statement.php" class="btn btn-primary" style="margin-left: 50px;" target="_blank">Print Statement of School Fees</a>
                    <br /><br /><br />
                    <a href="print_income_and_expenditure.php" class="btn btn-primary" style="margin-left: 50px;" target="_blank">Print Income and Expenditure Account</a>

                    <br /><br /><br />
                    <a href="fees_collected.php" class="btn btn-danger" style="margin-left: 50px;">Cancel</a>
                </td>
                </tr>
            </table>
            <legend class="legend"></legend>
        </div>
        </table>
    </div>
</div>

<?php
require_once '../includes/footer.php';

