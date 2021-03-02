<?php
ob_start();
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$user = new User();
$bill = new Bill();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("expenses_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($entries)) {
    $entries = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_for_expenses.php';

    $bill->expensesSuccessBanner();

    $show_form = TRUE;
    if (isset($_POST["submit_entries"])) {
        $validator = new FormValidator();
        $validator->addValidation("entries", "req", "Please, fill in the <strong>Total Entries</strong> before you click the <strong>CREATE TEXTBOXES</strong> button!");

        $entries = trim(escape_value($_POST["entries"]));
        $_SESSION["entries"] = $entries;

        if ($validator->ValidateForm()) {
//            redirect_to("change_boarding_status.php");
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
                        <legend style="color: #4F1ACB;"><strong>Expenditure Entries</strong></legend>
                        <div class="wrapper">
                            <div class="control-group">
                                <label class="control-label" for="entries">Specify Total Entries</label>
                                <div class="controls">
                                    <input class="span1" type="text" name="entries" autocomplete="off" autofocus>
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" name="submit_entries" class="btn">Create textboxes</button>
                                    <a href="expenses.php" class="btn large btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<div class="container">
    <?php
    $show_form_2 = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();

        if ($validator->ValidateForm()) {
            $bill->insertExpenses();
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

    $institutionDetail_set = $institutionDetail->getInstitutionDetails();

    if (TRUE == $show_form_2) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">

                    <legend style="color: #4F1ACB;"></legend>

                    <div class="spacer"></div>

                    <div class="center-table">
                        <div class="row">
                            <div class="span8">
                                <table class="table table-condensed table-bordered margin-left">
                                    <thead class="table-head-format">
                                        <tr style="background-color: #F5F5F5;">
                                            <td style="text-align: center; width: 3%; font-weight: 600; padding: 10px; font-size: 18px;">S/N</td>
                                            <td style="text-align: center; font-weight: 600; padding: 10px; font-size: 18px;">EXPENDITURE</td>
                                            <td style="text-align: center; width: 25%; font-weight: 600; padding: 10px; font-size: 18px;">AMOUNT, GH&cent;</td>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <input type="hidden" name="school_number" value="<?php echo $institutionDetail_set["school_number"]; ?>">

                                    <?php
                                    $i = 1;

                                    for ($j = 1; $j <= $entries; $j++) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center; padding-top: 14px;"><?php echo $i++; ?></td>

                                            <td style="text-align: left; padding-left: 34px; padding-top: 14px;">
                                                <input type="text" name="description[]" class="span5" autocomplete="off" value="">
                                            </td>

                                            <td style="text-align: left; padding-left: 47px; padding-top: 14px;"><input type="text" name="amount[]" class="input-small" autocomplete="off" value=""></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>

                                <input type="submit" name="submit" value="Save" class="btn btn-danger" />
                                <a href="expenses.php" class="btn btn-danger" style="margin-left: 20px;">Cancel All</a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="spacer"></div>
                <legend style="color: #4F1ACB;"></legend>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';
