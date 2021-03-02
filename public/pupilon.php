<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/Pupil.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$region = new Region();
$pupil = new Pupil();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("pupilon", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($date_of_birth)) {
    $date_of_birth = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_pupil_success.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("family_name", "req", "Please, fill in the family name.");
        $validator->addValidation("other_names", "req", "Please, fill in the other names.");
        $validator->addValidation("date_of_birth", "req", "Please, fill in the Date of Birth.");
        $validator->addValidation("sex", "req", "Please, fill in the sex.");
        $validator->addValidation("hometown", "req", "Please, fill in the hometown.");
        $validator->addValidation("region", "dontselect=--Select Region--", "Please, fill in the region.");

        $school_number = trim(escape_value($_POST["school_number"]));
        $pupil_id = trim(escape_value($_POST["pupil_id"]));
        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $getRegion = trim(ucwords(escape_value($_POST["region"])));

        $_SESSION["pupil_id"] = $pupil_id;

        if ($validator->ValidateForm()) {
            $pupil->insertPupil();
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

   $id_code = $pupil->generateid();
    $year_term_part = $pupil->term_year_part();
    $number_part = $pupil->getNumber_part();

    $term_part = "";
    $year_part = "";
    $increment_number_id = "";
    while ($value = mysqli_fetch_assoc($id_code)) {
        $id_tag = $value['students_id_initials'];


        while ($value1 = mysqli_fetch_assoc($year_term_part)) {

            $term_part = $value1['term'];
            $year_part = $value1['academic_year'];

            if ($term_part == "First") {
                $term_part ="01";
                $ac_year = explode("/", $year_part);
                $year = $ac_year[0];
                $first_year_number = substr($year, 2); 
                $year_part = $first_year_number ;
            }
            if ($term_part == "Second") {
                $term_part = "02";
                $ac_year = explode("/", $year_part);
                $year = $ac_year[1];
                $first_year_number = substr($year, 2); 
                $year_part = $first_year_number;
            }
            if ($term_part == "Third") {
                $term_part = "03";
                $ac_year = explode("/", $year_part);
               $year = $ac_year[1];
                $first_year_number = substr($year, 2); 
                $year_part = $first_year_number;
            }
        }
if(mysqli_num_rows($number_part) > 0){
        while ($value2 = mysqli_fetch_assoc($number_part)) {
 
            $to_be_exploded = $value2['pupil_id'];
            $exploded_number = explode('/', $to_be_exploded);
            $increment_number_id = $exploded_number[3] + 1;
            if (strlen($increment_number_id) == 1){
                $increment_number_id = "00".$increment_number_id;
            }
            if (strlen($increment_number_id) == 2){
                $increment_number_id = "0".$increment_number_id;
            }
        }
}
                else {
               $increment_number_id = '001'; 
            }
        $new_student_id = $id_tag . "/" . $year_part . "/" . $term_part . "/" . $increment_number_id;
    }
    $institution_set = $institutionDetail->getInstitutionDetails();
    $region_set = $region->getRegions();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <div class="alert alert-success">
                    <?php
                    echo "<strong>STEP 1 of 4:</strong>";
                    echo "<ul type='square'>";
                    echo "<li><strong>STUDENT ADMISSION</strong> process, successfully completed!.</li>";
                    echo "<li>You may <strong>register</strong> another student by filling the form below.</li>";
                    echo "Note that the system generates a <strong>unique ID NUMBER</strong> for each student.";
                    echo "</ul>";
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Pupil Details</strong></legend>

                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2"  readonly="true" type="text" name="pupil_id" value="<?php echo $new_student_id; ?>" autocomplete="off">
                            </div>
                        </div>

                        
                        
                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="family_name">Family Name</label>
                            <div class="controls">
                                <input class="span3" type="text" name="family_name" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($family_name)) {
                                           echo $family_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="other_names">Other Names</label>
                            <div class="controls">
                                <input class="span3" type="text" name="other_names" autocomplete="off"
                                       value="<?php
                                       if (isset($other_names)) {
                                           echo $other_names;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="date_of_birth">Date of Birth</label>
                            <div class="controls">
                                <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                    <input type="text" name="date_of_birth" id="input-date-height" autocomplete="off" value="<?php
                                    if ($date_of_birth !== "1970-01-01" && $date_of_birth === "01-01-1970") {
                                        echo date("d-m-Y", strtotime($date_of_birth));
                                    }
                                    ?>">
                                    <span class="add-on"><i class="icon-calendar-5"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="sex">Sex</label>
                            <div class="controls">
                                <label class="radio inline">
                                    <input type="radio" name="sex" id="male" value="Male">
                                    <span class="metro-radio">Male</span>
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="sex" id="female" value="Female">
                                    <span class="metro-radio">Female</span>
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="hometown">Hometown</label>
                            <div class="controls">
                                <input class="span3" type="text" name="hometown" autocomplete="off"
                                       value="<?php
                                       if (isset($hometown)) {
                                           echo $hometown;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="region">Region</label>
                            <div class="controls">
                                <select name="region">
                                    <option value="<?php
                                    if (isset($getRegion)) {
                                        echo $getRegion;
                                    } else {
                                        echo "--Select Region--";
                                    }
                                    ?>">
                                                <?php
                                                if (isset($getRegion)) {
                                                    echo $getRegion;
                                                } else {
                                                    echo "--Select Region--";
                                                }
                                                ?>
                                    </option>
                                    <?php
                                    while ($regions = mysqli_fetch_assoc($region_set)) {
                                        ?>
                                        <option value="<?php echo ucwords($regions["name"]); ?>"><?php echo ucwords($regions["name"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save and continue</button>
                                <a href="pupilon.php" class="btn btn-danger">Clear</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

