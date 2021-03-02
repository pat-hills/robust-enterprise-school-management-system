<?php
require_once '../includes/header.php';
require_once '../classes/Bill.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$bill= new Bill();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("bill_item", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("name", "req", "Please, fill in the Bill Item name.");

        $name = trim(ucwords(escape_value($_POST["name"])));

        if ($validator->ValidateForm()) {
            $bill->insertBillItem();
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

    $institution_set = $institutionDetail->getInstitutionDetails();
    $billItems = $bill->getBillItemBySchoolNumber($institution_set["school_number"]);
    
    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Bill Item</strong></legend>    

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>" />

                        <div class="control-group">
                            <label class="control-label" for="name">Bill Item</label>
                            <div class="controls">
                                <input class="span5" type="text" name="name" autocomplete="off" autofocus>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="bill_item.php" class="btn large btn-danger">Clear</a>
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
                                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 5%;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">SCHOOL FEES ITEMS</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 20%;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    while ($items = mysqli_fetch_assoc($billItems)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo $i++; ?></td>
                                            <td><?php echo $items["name"]; ?></td>
                                            <td><a href="edit_bill_item.php?id=<?php echo urlencode(htmlentities($items["url_string"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="delete_bill_item.php?id=<?php echo urlencode(htmlentities($items["url_string"])); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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

