<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Level.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("class", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

$institutionDetail = new InstitutionDetail();
$classes = new Classes();
$level = new Level();
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "req", "Please, fill in the Class name.");
        $validator->addValidation("level_name", "dontselect=Select Level", "Please, fill in the Level.");

        $class_name = trim(strip_tags(strtoupper(escape_value($_POST["class_name"]))));

        if ($validator->ValidateForm()) {
            $classes->insertClass();
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

    $institutionDetail_set = $institutionDetail->getInstitutionDetails();
    $level_set = $level->getLevels();
    $class_set = $classes->getClasses();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Class</strong></legend>                        
                        <div class="control-group">
                            <label class="control-label" for="class_name">Name of Class</label>
                            <div class="controls">
                                <input class="span2" type="text" name="class_name" autocomplete="off" autofocus>
                                <input class="span2" type="hidden" name="school_number" value="<?php echo htmlentities($institutionDetail_set["school_number"]); ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="level_name">Level</label>
                            <div class="controls">
                                <select name="level_name" class="select">
                                    <option value="Select Level">--Select Level--</option>
                                    <?php
                                    while ($level = mysqli_fetch_assoc($level_set)) {
                                        ?>
                                        <option value="<?php echo ucwords($level["level_name"]); ?>"><?php echo htmlentities(ucwords($level["level_name"])); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="class.php" class="btn large btn-danger">Clear</a>
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
                                        <td colspan="5" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIST OF ACTIVE CLASSES</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; width: 60%;">CLASSES</td>
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
                                            <td><?php echo htmlentities($classRow["class_name"]); ?></td>
                                            <td><a href="edit_class.php?id=<?php echo urlencode($classRow["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="delete_class.php?id=<?php echo urlencode($classRow["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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

