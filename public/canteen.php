<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
//require_once '../classes/AcademicTerm.php';
require_once '../classes/User.php';
require_once '../classes/Classes.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
//$academicTerm = new AcademicTerm();
$user = new User();
$classes = new Classes();
$bill = new Bill();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("canteen", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_for_canteen.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
//        $validator->addValidation("amount", "All <strong>FIELDS</strong> are required.");

        if ($validator->ValidateForm()) {
            $bill->replaceCanteenFees();
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
    $getClasses = $classes->getClasses();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form method="post">

                    <legend style="color: #4F1ACB;"><strong>Enter Canteen Fees</strong></legend>

                    <div class="spacer"></div>

                    <div class="center-table">
                        <div class="row">
                            <div class="span8">
                                <table class="table table-condensed table-bordered margin-left">
                                    <thead class="table-head-format">
                                        <tr style="background-color: #F5F5F5;">
                                            <td style="text-align: center; width: 3%; font-weight: 600; padding: 10px; font-size: 16px;">S/N</td>
                                            <td style="text-align: center; font-weight: 600; padding: 10px; font-size: 15px;">CLASSES</td>
                                            <td style="text-align: center; width: 22%; font-weight: 600; padding: 10px; font-size: 15px;">TOTAL STUDENTS</td>
                                            <td style="text-align: center; width: 20%; font-weight: 600; padding: 10px; font-size: 15px;">CANTEEN FEES</td>
                                            <td style="text-align: center; width: 25%; font-weight: 600; padding: 10px; font-size: 15px;">TOTAL AMOUNT, GH&cent;</td>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($getClasses as $class) {
                                            $getCanteenFees = $bill->getCanteenFees($class["class_name"]);
                                            ?>
                                            <tr>
                                                <td style="text-align: center; padding-top: 14px;"><?php echo $i++; ?></td>

                                                <td style="text-align: left; padding-left: 15px; padding-top: 14px;">
                                                    <input type="hidden" name="school_number" value="<?php echo $institutionDetail_set["school_number"]; ?>">
                                                    <input type="hidden" name="description[]" value="<?php echo "Canteen Fees"; ?>">
                                                    <input type="hidden" name="class[]" value="<?php echo $class["class_name"]; ?>">
                                                    <?php echo $class["class_name"]; ?>
                                                </td>

                                                <td style="padding-left: 52px; padding-top: 10px;"><input type="text" name="total_students[]" class="span1" autofocus autocomplete="off" value="<?php
                                                    if (isset($getCanteenFees["total_students"])) {
                                                        echo number_format($getCanteenFees["total_students"], 0, ".", ",");
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>">
                                                </td>

                                                <td style="padding-left: 45px; padding-top: 10px;"><input type="text" name="canteen_fees[]" class="span1" autocomplete="off" value="<?php
                                                    if (isset($getCanteenFees["canteen_fees"])) {
                                                        echo number_format($getCanteenFees["canteen_fees"], 2, ".", ",");
                                                    } else {
                                                        echo "";
                                                    }
                                                    ?>">
                                                </td>

                                                <td style="text-align: right; padding-top: 14px;">
                                                    <?php
                                                    if (isset($getCanteenFees["amount"])) {
                                                        echo number_format($getCanteenFees["amount"], 2, ".", ",");
                                                    }  else {
                                                        echo "";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <input type="submit" name="submit" value="Save" class="btn btn-danger" />
                                <a href="canteen.php" class="btn btn-danger" style="margin-left: 20px;">Cancel All</a>
                            </div>
                        </div>
                    </div>
                </form>
                <legend style="color: #4F1ACB;"></legend>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';
