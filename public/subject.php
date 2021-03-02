<?php
require_once '../includes/header.php';
require_once '../classes/Subject.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$subject = new Subject();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("subject", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("subject_code", "req", "Please, fill in the subject code.");
        $validator->addValidation("subject_name", "req", "Please, fill in the subject name.");
        $validator->addValidation("subject_initials", "req", "Please, fill in the subject subject initials.");
        $validator->addValidation("subject_category", "dontselect=select one", "Please, fill in the subject category.");

//        $validator->addValidation("subject_code", "num", "Please, fill in a valid subject code.");

        $subject_code = trim(escape_value($_POST["subject_code"]));
        $subject_name = trim(ucwords(escape_value($_POST["subject_name"])));
        $subject_initials = trim(strtoupper(escape_value($_POST["subject_initials"])));

        if ($validator->ValidateForm()) {
            $subject->insertSubject();
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

    $institution_set = $institutionDetail->getInstitutionDetails();
    $subjects = $subject->getSubjectsBySchoolNumber($institution_set["school_number"]);

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Subject Details</strong></legend>    
                        <div class="control-group">
                            <label class="control-label" for="subject_code">Subject Code</label>
                            <div class="controls">
                                <input class="span1" type="text" name="subject_code" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($subject_code)) {
                                           echo $subject_code;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>" />

                        <div class="control-group">
                            <label class="control-label" for="subject_name">Subject</label>
                            <div class="controls">
                                <input class="span4" type="text" name="subject_name" autocomplete="off" 
                                       value="<?php
                                       if (isset($subject_name)) {
                                           echo $subject_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="subject_initials">Subject Initials</label>
                            <div class="controls">
                                <input class="span1" type="text" name="subject_initials" autocomplete="off" 
                                       value="<?php
                                       if (isset($subject_initials)) {
                                           echo $subject_initials;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="subject_category">Subject Category</label>
                            <div class="controls">
                                <select name="subject_category" class="select">
                                    <option value="select one">--Select One--</option>
                                    <option value="core">Core</option>
                                    <option value="elective">Elective</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="subject.php" class="btn large btn-danger">Clear</a>
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
                                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">CODE</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">NAME</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">INITIALS</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">CATEGORY</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    while ($sub = mysqli_fetch_assoc($subjects)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo $i++; ?></td>
                                            <td style="text-align: center;"><?php echo $sub["subject_code"]; ?></td>
                                            <td><?php echo $sub["subject_name"]; ?></td>
                                            <td><?php echo $sub["subject_initials"]; ?></td>
                                            <td><?php echo $sub["subject_category"]; ?></td>
                                            <td><a href="edit_subject.php?id=<?php echo urlencode(htmlentities($sub["url_string"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                            <td><a href="delete_subject.php?id=<?php echo urlencode(htmlentities($sub["url_string"])); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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

