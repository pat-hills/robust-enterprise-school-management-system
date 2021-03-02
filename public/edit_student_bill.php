<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Bill.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/AcademicYear.php';
require_once '../classes/User.php';

confirm_logged_in();

$classes = new Classes();
$institutionDetail = new InstitutionDetail();
$bill = new Bill();
$academicTerm = new AcademicTerm();
$academicYear = new AcademicYear();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_student_bill", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $student_bill_id = $bill->getStudentBillByURL(escape_value(urldecode(getURL())));
} else {
    $student_bill_id = NULL;
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_student_bill.php';

    if (isset($_POST["submit"])) {
        $bill->updateStudentBillByURL($student_bill_id["url_string"]);
    }

    $institution_set = $institutionDetail->getInstitutionDetails();
    $getClasses = $classes->getClasses();
    $getBillTypes = $bill->getBillType($institution_set["school_number"]);
    $billItems = $bill->getBillItemBySchoolNumber($institution_set["school_number"]);

    $getAcademicYear = $academicYear->getAcademicYear();
    $academic_terms = $academicTerm->getAcademicTerm($getAcademicYear["academic_year"]);
    ?>
    <div class="row">
        <div class="span12">
            <form class="form-horizontal" method="post">
                <fieldset>
                    <legend style="color: #4F1ACB;"><strong>Edit Bill Details</strong></legend> 

                    <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>" />

                    <div class="center-table">
                        <div class="row">
                            <div class="span8">
                                <table class="table table-condensed">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="control-group">
                                                    <label class="control-label" for="academic_term">Academic Term</label>
                                                    <div class="controls">
                                                        <select name="academic_term" class="select">
                                                            <option value="<?php echo $student_bill_id["academic_term"]; ?>"><?php echo $student_bill_id["academic_term"]; ?></option>
                                                            <?php
                                                            foreach ($academic_terms as $value) {
                                                                ?>
                                                                <option value="<?php echo htmlentities($value["academic_year"] . "/" . $value["term"]) ?>"><?php echo htmlentities($value["academic_year"] . "/" . $value["term"]) ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="control-group">
                                                    <label class="control-label" for="bill_item">Bill Item</label>
                                                    <div class="controls">
                                                        <select name="bill_item" class="select-medium">
                                                            <option value="<?php echo $student_bill_id["bill_item"]; ?>"><?php echo $student_bill_id["bill_item"]; ?></option>
                                                            <?php
                                                            foreach ($billItems as $value) {
                                                                ?>
                                                                <option value="<?php echo htmlentities($value["name"]) ?>"><?php echo htmlentities($value["name"]) ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>

<!--                                            <td>
    <div class="control-group">
        <label class="control-label" for="gender">Sex</label>
        <div class="controls">
            <select name="gender" class="select">
                <option value="<?php echo $student_bill_id["gender"]; ?>"><?php echo $student_bill_id["gender"]; ?></option>
                <option value="All">All</option>
                <option value="Female">Female</option>
                <option value="Male">Male</option>
            </select>
        </div>
    </div>
</td>-->
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="control-group">
                                                    <label class="control-label" for="class_name">Class</label>
                                                    <div class="controls">
                                                        <select name="class_name" class="select">
                                                            <option value="<?php echo $student_bill_id["class_name"]; ?>"><?php echo $student_bill_id["class_name"]; ?></option>
                                                            <?php
                                                            foreach ($getClasses as $value) {
                                                                ?>
                                                                <option value="<?php echo htmlentities($value["class_name"]) ?>"><?php echo htmlentities($value["class_name"]) ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="control-group">
                                                    <label class="control-label" for="bill_type">Bill Type</label>
                                                    <div class="controls">
                                                        <select name="bill_type" class="select">
                                                            <option value="<?php echo $student_bill_id["bill_type"]; ?>"><?php echo $student_bill_id["bill_type"]; ?></option>
                                                            <option value="School Fee">School Fees</option>
                                                            <option value="PTA Fee">PTA Fees</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="control-group">
                                                    <label class="control-label" for="day_amount">Day Amount</label>
                                                    <div class="controls">
                                                        <input class="span2" type="text" name="day_amount" autocomplete="off" 
                                                               value="<?php
                                                               if (isset($student_bill_id["day_amount"])) {
                                                                   echo $student_bill_id["day_amount"];
                                                               } else {
                                                                   echo 0;
                                                               }
                                                               ?>">
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="control-group">
                                                    <label class="control-label" for="boarding_amount">Boarding Amount</label>
                                                    <div class="controls">
                                                        <input class="span2" type="text" name="boarding_amount" autocomplete="off"
                                                               value="<?php
                                                               if (isset($student_bill_id["boarding_amount"])) {
                                                                   echo $student_bill_id["boarding_amount"];
                                                               } else {
                                                                   echo 0;
                                                               }
                                                               ?>">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" name="submit" class="btn" style="margin-left: 300px;">Save changes</button>
                                <a href="student_bill.php" class="btn large btn-danger">Cancel</a>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <legend class="legend"></legend>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

