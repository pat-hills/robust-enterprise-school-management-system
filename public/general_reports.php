<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/House.php';
require_once '../classes/Admission.php';

confirm_logged_in();

$user = new User();
$house = new House();
$admission = new Admission();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("general_reports", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php require_once '../includes/breadcrumb_general_reports.php'; ?>

    <div class="row">
        <div class="span12">
            <legend style="color: #4F1ACB;"><strong>General Reports</strong></legend> 

            <table class="table table-condensed">
                <tr>
                    <td style="width: 40%;">
                        <?php
                        $show_form_house = TRUE;

                        if (isset($_POST["submit_house_report"])) {
                            $validator = new FormValidator();
                            $validator->addValidation("house_name", "dontselect=--Select House--", "Please, select <strong>HOUSE</strong>.");
                            $validator->addValidation("gender", "dontselect=--Select Gender--", "Please, select <strong>GENDER</strong>.");

                            $house_name = trim(ucwords(escape_value($_POST["house_name"])));
                            $gender = trim(ucwords(escape_value($_POST["gender"])));

                            $_SESSION["selected_house_report"] = $house_name;
                            $_SESSION["selected_gender_report"] = $gender;

                            if ($validator->ValidateForm()) {
//                                redirect_to("print_house_report.php");
                            } else {
                                echo "<div class='row'>";
                                echo "<div class='span5'>";
                                echo "<div class='alert alert-error'>";
                                echo "<ul type='square'>";
                                echo "<li>Please, select both <strong>HOUSE</strong> and <strong>GENDER</strong>.</li>";
                                echo "</ul>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }

                        $getHouses = $house->getHouses();

                        if (TRUE == $show_form_house) {
                            ?>
                            <form class="form-horizontal" method="post">
                                <fieldset>
                                    <div class="control-group">
                                        <label class="control-label" for="house_name">House</label>
                                        <div class="controls">
                                            <select name="house_name" class="select">
                                                <option value="<?php
                                                if (isset($house_name)) {
                                                    echo $house_name;
                                                } else {
                                                    echo "--Select House--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($house_name)) {
                                                                echo $house_name;
                                                            } else {
                                                                echo "--Select House--";
                                                            }
                                                            ?>
                                                </option>
                                                <?php
                                                while ($house = mysqli_fetch_assoc($getHouses)) {
                                                    ?>
                                                    <option value="<?php echo htmlentities($house["name"]); ?>"><?php echo htmlentities($house["name"]); ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="gender">Gender</label>
                                        <div class="controls">
                                            <select name="gender" class="select">
                                                <option value="<?php
                                                if (isset($gender)) {
                                                    echo $gender;
                                                } else {
                                                    echo "--Select Gender--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($gender)) {
                                                                echo $gender;
                                                            } else {
                                                                echo "--Select Gender--";
                                                            }
                                                            ?>
                                                </option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit_house_report" class="btn">Find</button>
                                            <a href="print_house_report.php" class="btn btn-info" target="_blank" style="margin-left: 30px;">Print</a>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                        }
                        ?>
                    </td>

                    <td style="width: 40%;">
                        <?php
                        $show_form_staff = TRUE;
                        if (isset($_POST["submit_staff_report"])) {
                            $validator = new FormValidator();
                            $validator->addValidation("staff_category", "dontselect=--Select Staff Category--", "Please, select <strong>STAFF CATEGORY</strong>.");

                            $staff_category = trim(ucwords(escape_value($_POST["staff_category"])));
                            $_SESSION["staff_category"] = $staff_category;

                            if ($validator->ValidateForm()) {
//            $academicTerm->insertAcademicTerm();
                            } else {
                                $get_errors = $validator->GetErrors();

                                foreach ($get_errors as $input_field_name => $error_msg) {
                                    echo "<div class='row'>";
                                    echo "<div class='span5'>";
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

                        if (TRUE == $show_form_staff) {
                            ?>
                            <form class="form-horizontal" method="post">
                                <fieldset>                      
                                    <div class="control-group">
                                        <label class="control-label" for="staff_category">Staff Category</label>
                                        <div class="controls">
                                            <select name="staff_category">
                                                <option value="<?php
                                                if (isset($staff_category)) {
                                                    echo $staff_category;
                                                } else {
                                                    echo "--Select Staff Category--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($staff_category)) {
                                                                echo $staff_category . "s";
                                                            } else {
                                                                echo "--Select Staff Category--";
                                                            }
                                                            ?>       
                                                </option>
                                                <option value="Administrator">Administrators</option>
                                                <option value="Head-Teacher">Head-teachers</option>
                                                <option value="Teacher">Teachers</option>
                                                <option value="Accountant">Accountants</option>
                                                <option value="Account Clerk">Account Clerks</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit_staff_report" class="btn">Find</button>
                                            <a href="print_staff_list.php" class="btn btn-info" target="_blank" style="margin-left: 30px;">Print</a>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                            <?php
                        }
                        ?>
                    </td>

                    <td style="width: 20%;"><a href="print_statistical_report.php" class="btn btn-primary" style="margin-left: 30px;" target="_blank">Print School Population</a></td>
                </tr>
            </table>

            <a href="general_reports.php" class="btn btn-danger" style="margin-left: 550px;">Cancel</a>

            <legend class="legend"></legend>
        </div>
        </table>
    </div>
</div>

<?php
require_once '../includes/footer.php';

