<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/AcademicYear.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$academicTerm = new AcademicTerm();
$academicYear = new AcademicYear();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("academic_term", $splitAccessPages, TRUE)) {
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
        $validator->addValidation("academic_year", "dontselect=--Select One--", "Please, select academic year.");
        $validator->addValidation("term", "req", "Please, fill in the term.");
        $validator->addValidation("begin_date", "req", "Please, fill in the academic term's dates.");
        $validator->addValidation("end_date", "req", "Please, fill in the academic term's dates.");

        if ($validator->ValidateForm()) {
            $academicTerm->insertAcademicTerm();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, fill in all the <strong>FIELDS</strong> before you click the <strong>SAVE</strong> button!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $institution_set = $institutionDetail->getInstitutionDetails();

    $getAcademicYear = $academicYear->getAcademicYear();
//    echo $getAcademicYear["academic_year"];
//    $getActivatedAcademicYear = $academicYear->getActivatedAcademicYear($getAcademicYear["academic_year"]);
    $getActivatedAcademicYear = $academicYear->getLastAcademicYearDetails();
//    $academic_term_set = $academicTerm->getAcademicTerm($getAcademicYear["academic_year"]);
    $academic_term_set = $academicTerm->getAcademicTerms();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <div class="alert alert-info">
                    <?php
                    echo "<strong>STEP 3 of 3:</strong>";
                    echo "<ul type='square'>";
                    echo "<li>Fill in the <strong>ACADEMIC TERM DETAILS</strong> in the form below.</li>";
                    echo "</ul>";
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Academic Term Details</strong></legend>                        

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="academic_year">Academic Year</label>
                            <div class="controls">
                                <select name="academic_year">
                                    <option value="--Select One--">--Select Academic Year--</option>
                                    <?php
                                    while ($academic_years = mysqli_fetch_assoc($getActivatedAcademicYear)) {
                                        ?>
                                        <option value="<?php echo htmlentities($academic_years["academic_year"]); ?>"><?php echo htmlentities($academic_years["academic_year"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="term">Term</label>
                            <div class="controls">
                                <select name="term">
                                    <option value="">--Select Academic Term--</option>
                                    <option value="First">First</option>
                                    <option value="Second">Second</option>
                                    <option value="Third">Third</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="begin_date">Begin Date</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="begin_date" size="16" class="text-box-width" id="input-date-height" autocomplete="off">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="end_date">End Date</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="end_date" size="16" class="text-box-width" id="input-date-height" autocomplete="off">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="academic_term.php" class="btn btn-danger">Clear</a>
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
                                        <td colspan="8" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIST OF ACADEMIC TERMS</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 5%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">ACADEMIC YEAR</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">TERM</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">BEGIN DATE</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">END DATE</td>
                                        <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">ACTIVE</td>
                                        <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 20%; text-align: center;">ACTION</td>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    while ($academic_terms = mysqli_fetch_assoc($academic_term_set)) {
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php
                                                echo htmlentities($academic_terms["academic_year"]);
                                                ?>
                                            </td>
                                            <td style="text-align: left;"><?php echo $academic_terms["term"]; ?></td>
                                            <td style="text-align: center;"><?php echo htmlentities(date("d-m-Y", strtotime($academic_terms["begin_date"]))); ?></td>
                                            <td style="text-align: center;"><?php echo htmlentities(date("d-m-Y", strtotime($academic_terms["end_date"]))); ?></td>
                                            <td style="text-align: center;"><?php echo $academic_terms["active"]; ?></td>
                                            <td><a href="edit_academic_term.php?id=<?php echo urlencode(htmlentities($academic_terms["url_string"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit/Activate</a></td>
                                            <td><a href="delete_academic_term.php?id=<?php echo urlencode(htmlentities($academic_terms["url_string"])); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click OK, otherwise click CANCEL')">Delete</a></td>
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

