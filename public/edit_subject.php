<?php
require_once '../includes/header.php';
require_once '../classes/Subject.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$subject = new Subject();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_subject", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $subject_code = $subject->getSubjectByURL(urldecode(getURL()));
} else {
    $subject_code = NULL;
}
?>

<div class = "container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    if (isset($_POST["submit"])) {
        $subject->updateSubjectByURL($subject_code["url_string"], $subject_code["school_number"]);
    }

    $institution_set = $institutionDetail->getInstitutionDetails();
    $subject_set = $subject->getSubjectsBySchoolNumber($institution_set["school_number"]);
    ?>

    <div class="row">
        <div class="span12">
            <form class="form-horizontal" method="post">
                <fieldset>
                    <legend style="color: #4F1ACB;"><strong>Enter Subject Details</strong></legend>    
                    <div class="control-group">
                        <label class="control-label" for="subject_code">Subject Code</label>
                        <div class="controls">
                            <!--<input class="span1" type="hidden" name="subject_code" autocomplete="off" value="<?php // echo $subject_code["subject_code"]; ?>">-->
                            <input class="span1" type="text" name="subject_code" autocomplete="off" value="<?php echo $subject_code["subject_code"]; ?>" disabled>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="subject_name">Subject</label>
                        <div class="controls">
                            <input class="span4" type="text" name="subject_name" autocomplete="off" value="<?php echo $subject_code["subject_name"]; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="subject_initials">Subject Initials</label>
                        <div class="controls">
                            <input class="span2" type="text" name="subject_initials" autocomplete="off" value="<?php echo $subject_code["subject_initials"]; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="subject_category">Subject Category</label>
                        <div class="controls">
                            <select name="subject_category" class="select">
                                <option value="<?php echo $subject_code["subject_category"]; ?>"><?php echo $subject_code["subject_category"]; ?></option>
                                <option value="core">Core</option>
                                <option value="elective">Elective</option>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" name="submit" class="btn">Save changes</button>
                            <a href="subject.php" class="btn btn-danger">Cancel</a>
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
                                    <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">INITIALS</td>
                                    <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">CATEGORY</td>
                                    <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; text-align: center;">ACTION</td>
                                </tr>
                                <?php
                                $i = 1;
                                while ($sub = mysqli_fetch_assoc($subject_set)) {
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
</div>

<?php
require_once '../includes/footer.php';

unset($_SESSION["class_id"]);
