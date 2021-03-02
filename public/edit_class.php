<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/User.php';

confirm_logged_in();

$classes = new Classes();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_class", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $class_id = trim(ucwords(escape_value(urldecode(getURL()))));
//    $_SESSION["class_id"] = $class_id;
} else {
    redirect_to("class.php");
}

$class_by_id = $classes->getClassByURL($class_id);
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    $class_set = $classes->getClasses();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "req", "Please, fill in the Class name.");

//        $class_id = trim(escape_value($_POST["class_id"]));

        if ($validator->ValidateForm()) {
            $classes->updateClassByURL($class_id);
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
                        <legend style="color: #4F1ACB;"><strong>Edit Class</strong></legend>                        
                        <div class="control-group">
                            <label class="control-label" for="class_name">Name of Class</label>
                            <div class="controls">
                                <!--<input class="span2" type="hidden" name="class_id" value="<?php // echo htmlentities($class_by_id["class_id"]); ?>">-->
                                <input class="span2" type="text" name="class_name" value="<?php echo htmlentities($class_by_id["class_name"]); ?>" autocomplete="off">
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save changes</button>
                                <a href="class.php" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="center-table">
                    <div class="row">
                        <div class="span8">
                            <table class="table table-bordered table-condensed">
                                <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIST OF ACTIVE CLASSES</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 10%;">CLASSES</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;

                                    while ($classRow = mysqli_fetch_assoc($class_set)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="width: 60%;"><?php echo htmlentities($classRow["class_name"]); ?></td>
                                            <td><a href="edit_class.php?id=<?php echo urlencode($classRow["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="delete_class.php?id=<?php echo urlencode($classRow["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!--<a href="class.php" class="btn btn-primary">Create new class</a>-->
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

