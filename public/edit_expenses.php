<?php
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
    if (!in_array("edit_expenses", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_for_edit_expenses.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();

        if ($validator->ValidateForm()) {
//            $bill->replaceExpenditure();
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
    $getExpenditure = $bill->getExpenditure();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">

                    <legend style="color: #4F1ACB; font-weight: 600;">Edit Expenditure</legend>

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
                                            <td colspan="2" style="text-align: center; width: 30%; font-weight: 600; padding: 10px; font-size: 18px;">ACTION</td>
                                        </tr>
                                    </thead>

                                    <tbody>

                                    <input type="hidden" name="school_number" value="<?php echo $institutionDetail_set["school_number"]; ?>">

                                    <?php
                                    $i = 1;

                                    foreach ($getExpenditure as $expenditure) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center; padding-top: 7px;"><?php echo $i++; ?></td>

                                            <td style="text-align: left; padding-left: 5px; padding-top: 7px;">
                                                <input type="hidden" name="description" value="<?php echo htmlentities(ucwords($expenditure["description"])); ?>">
                                                <?php echo htmlentities(ucwords($expenditure["description"])); ?>
                                            </td>

                                            <td style="text-align: right; padding-top: 7px;"><?php echo number_format(htmlentities($expenditure["expenditureTotal"]), 2, ".", ","); ?></td>

                                            <td style="text-align: left; padding-left: 5px; padding-bottom: 5px;"><a href="edit_expenditure_item.php?id=<?php echo urlencode(htmlentities($expenditure["expense_hash"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td style="text-align: left; padding-left: 5px; padding-bottom: 5px;"><a href="delete_expenditure_item.php?id=<?php echo urlencode(htmlentities($expenditure["expense_hash"])); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click OK, otherwise click CANCEL')">Delete</a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>

                                <!--<input type="submit" name="submit" value="Save changes" class="btn btn-danger" />-->
                                <!--<a href="edit_expenses.php" class="btn btn-danger" style="margin-left: 20px;">Cancel</a>-->
                            </div>
                        </div>
                    </div>
                </form>
                <!--<div class="spacer"></div>-->
                <legend style="color: #4F1ACB;"></legend>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';
