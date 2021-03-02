<?php
require_once '../includes/header.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/Subject.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';
require_once '../classes/Classes.php';

confirm_logged_in();

$subject = new Subject();
$institutionDetail = new InstitutionDetail();
$subjectCombination = new SubjectCombination();
$user = new User();
$classes = new Classes();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("subject_combination", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($subject_name)) {
    $subject_name = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_subject_combination.php';

    $show_form = TRUE;
    if (isset($_POST["submit"]) && !empty($_POST["subject_name"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=Select Class", "");
        $validator->addValidation("subject_combination_name", "req", "");
//        $validator->addValidation("subject_name", "req", "");

        $subject_combination_name = trim(ucwords(escape_value($_POST["subject_combination_name"])));
        $class_name = trim(escape_value($_POST["class_name"]));

        if ($validator->ValidateForm()) {
            $subjectCombination->insertSubjectCombination();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type = 'none'>";
            echo "<li>All <strong>FIELDS</strong> are required!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $institution_set = $institutionDetail->getInstitutionDetails();
    $subject_set = $subject->getSubjectsBySchoolNumber($institution_set["school_number"]);
    $subject_combination_set = $subjectCombination->getSubjectCombinationBySchoolNumber($institution_set["school_number"]);
    $getClasses = $classes->getClasses();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <div class="alert alert-info">
                    <?php
                    echo "<ul type='square'>";
                    echo "<li>Fill in all the <strong>SUBJECT COMBINATIONS</strong> of the school.</li>";
                    echo "</ul>"
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Subject Combination Details</strong></legend>

                        <input class="span6" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="class_name">Class</label>
                            <div class="controls">
                                <select name="class_name" class="select">
                                    <option value="Select Class">--Select Class--</option>
                                    <?php
                                    foreach ($getClasses as $value) {
                                        ?>
                                        <option value="<?php echo htmlentities($value["class_name"]); ?>"><?php echo htmlentities($value["class_name"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="subject_combination_name">Combination Name</label>
                            <div class="controls">
                                <input class="span6" type="text" name="subject_combination_name" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($subject_combination_name)) {
                                           echo $subject_combination_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="spacer"></div>

                        <div class="center-table">
                            <div class="row">
                                <div class="span8">
                                    <table class="table table-bordered table-condensed">
                                        <tbody>
                                            <tr>
                                                <td style="background-color: #F5F5F5; font-weight: 600; width: 3%; text-align: center;">S/N</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; width: 5%; text-align: center;">SELECT</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">SUBJECT INITIALS</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">SUBJECT NAME</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; width: 22%; text-align: center;">SUBJECT CATEGORY</td>
                                            </tr>

                                            <?php
                                            $i = 1;
                                            while ($subjects = mysqli_fetch_assoc($subject_set)) {
                                                ?>
                                                <tr>
                                                    <td style="text-align: center;"><?php echo $i++; ?></td>
                                                    <td style="text-align: center; padding-bottom: 5px;"><input type="checkbox" name="subject_name[]" value="<?php echo htmlentities($subjects["subject_name"]); ?>"></td>
                                                    <td style="text-align: left;"><?php echo $subjects["subject_initials"]; ?></td>
                                                    <td><?php echo htmlentities($subjects["subject_name"]); ?></td>
                                                    <td style="text-align: left;"><?php echo htmlentities($subjects["subject_category"]); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="spacer"></div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="subject_combination.php" class="btn btn-danger">Clear</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="spacer"></div>

                <!--<div class="center-table">-->
                <div class="row">
                    <div class="span12">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td style="background-color: #F5F5F5; font-weight: 600; width: 3%; text-align: center;">S/N</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 22%;">SUBJECT COMBINATION NAME</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">CLASS</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">SUBJECTS</td>
                                    <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 15%;">ACTION</td>
                                </tr>

                                <?php
                                $i = 1;
                                while ($subject_combinations = mysqli_fetch_assoc($subject_combination_set)) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i++; ?></td>
                                        <td><?php echo $subject_combinations["subject_combination_name"]; ?></td>
                                        <td><?php echo $subject_combinations["class_name"]; ?></td>
                                        <td><?php echo $subject_combinations["subject_name"]; ?></td>
                                        <td><a href="edit_subject_combination.php?id=<?php echo urlencode($subject_combinations["url_string"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                        <td><a href="delete_subject_combination.php?id=<?php echo urlencode($subject_combinations["url_string"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--</div>-->
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

