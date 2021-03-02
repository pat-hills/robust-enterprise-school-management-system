<?php
require_once '../classes/InstitutionDetail.php';
require_once '../classes/AcademicYear.php';
require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$academicYear = new AcademicYear();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("academic_year", $splitAccessPages, TRUE)) {
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
        $validator->addValidation("begin_date", "req", "Please, fill in the academic term's dates.");
        $validator->addValidation("end_date", "req", "Please, fill in the academic term's dates.");

        if ($validator->ValidateForm()) {
            $academicYear->insertAcademicYear();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, fill in the <strong>DATES</strong> before you click the <strong>SAVE</strong> button!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $institution_set = $institutionDetail->getInstitutionDetails();
    
    $getAcademicYear = $academicYear->getAcademicYear();
    $academic_year_set = $academicYear->getAcademicYearDetails();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <div class="alert alert-info">
                    <?php
                    echo "<strong>STEP 2 of 3:</strong>";
                    echo "<ul type='square'>";
                    echo "<li>Fill in the <strong>ACADEMIC YEAR DETAILS</strong> in the form below.</li>";
                    echo "</ul>";
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Academic Year Details</strong></legend>                        

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="begin_date">Begin Date</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="begin_date" class="text-box-width" id="input-date-height" autocomplete="off">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="end_date">End Date</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="end_date" class="text-box-width" id="input-date-height" autocomplete="off">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="academic_year.php" class="btn large btn-danger">Clear</a>
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
                                        <td colspan="7" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;">ACADEMIC YEARS</td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">ACADEMIC YEAR</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">BEGIN DATE</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">END DATE</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">ACTIVE</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    while ($academic_years = mysqli_fetch_assoc($academic_year_set)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $begin_date = htmlentities(date("Y", strtotime($academic_years["begin_date"])));
                                                echo "/";
                                                echo $end_date = htmlentities(date("Y", strtotime($academic_years["end_date"])));
                                                ?>
                                            </td>
                                            <td style="text-align: center;"><?php echo htmlentities(date("d-m-Y", strtotime($academic_years["begin_date"]))); ?></td>
                                            <td style="text-align: center;"><?php echo htmlentities(date("d-m-Y", strtotime($academic_years["end_date"]))); ?></td>
                                            <td style="text-align: center;"><?php echo htmlentities($academic_years["active"]); ?></td>
                                            <td><a href="edit_academic_year.php?id=<?php echo urlencode(htmlentities($academic_years["url_string"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit/Activate</a></td>
                                            <td><a href="delete_academic_year.php?id=<?php echo urlencode(htmlentities($academic_years["url_string"])); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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

