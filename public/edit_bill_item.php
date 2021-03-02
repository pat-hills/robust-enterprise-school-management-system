<?php
require_once '../includes/header.php';
require_once '../classes/Bill.php';
require_once '../classes/InstitutionDetail.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$bill = new Bill();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_bill_item", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $bill_item_id = $bill->getBillItemByURL(urldecode(getURL()));
} else {
//    $bill_item_id = "";
    redirect_to("bill_item.php");
}
?>

<div class = "container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';
    
    $institution_set = $institutionDetail->getInstitutionDetails();

    if (isset($_POST["submit"])) {
        $bill->updateBillItemByURL($bill_item_id["url_string"], $institution_set["school_number"]);
    }

    $billItems = $bill->getBillItemBySchoolNumber($institution_set["school_number"]);
    ?>

    <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Edit Bill Item</strong></legend>    

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>" />

                        <div class="control-group">
                            <label class="control-label" for="name">Bill Item</label>
                            <div class="controls">
                                <input class="span5" type="text" name="name" autocomplete="off" value="<?php echo htmlentities($bill_item_id["name"]); ?>" autofocus>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="bill_item.php" class="btn large btn-danger">Cancel</a>
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
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">NAME</td>
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
</div>

<?php
require_once '../includes/footer.php';

unset($_SESSION["class_id"]);
