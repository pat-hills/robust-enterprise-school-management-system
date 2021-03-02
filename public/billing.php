<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$institutionDetail = new InstitutionDetail();
$subjectCombination = new SubjectCombination();
$academicTerm = new AcademicTerm();
$bill = new Bill();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("billing", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($class_name)) {
    $class_name = "";
//    $_SESSION["billing_term"] = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_apply_to_account.php';

    $show_form = TRUE;
    if (isset($_POST["submit_billing"]) && !empty($_SESSION["billing_term"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name_billing", "dontselect=Select Class", "Please, select <strong>CLASS</strong> to be billed.");

        $class_name = trim(ucwords(escape_value($_POST["class_name_billing"])));
//        $_SESSION["previous_class_name"] = $class_name;

        if ($validator->ValidateForm()) {
//            redirect_to("promotion.php");
        } else {
            $get_errors = $validator->GetErrors();

            foreach ($get_errors as $input_field_name => $error_msg) {
                echo "<div class='row'>";
                echo "<div class='span12'>";
                echo "<div class='alert alert-error'>";
                echo "<button type='button' class='close' data-dismiss='alert'></button>";
                echo "<ul type = 'none'>";
                echo "<li>$error_msg</li>";
                echo "</ul>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
    }

    $class_set = $classes->getClasses();
//    $class_membership_set = $classMembership->getClassMembersByClassName($class_name);
    $class_membership_set = $classMembership->getClassMembersByClassName($class_name, $_SESSION["billing_term"]);
    $institution_set = $institutionDetail->getInstitutionDetails();

    if (TRUE == $show_form) {
        ?>

        <div class="spacer"></div>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="center-table">
                        <div class="span8">
                            <div class="span2" style="margin-left: 180px;">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>
                                            <select name="class_name_billing" class="select">
                                                <option value="Select Class">--Select CLASS--</option>
                                                <?php
                                                while ($classes = mysqli_fetch_assoc($class_set)) {
                                                    ?>
                                                    <option value="<?php echo $classes["class_name"]; ?>"><?php echo $classes["class_name"]; ?></option>
                                                    <?php
                                                }
                                                ?> 
                                            </select>
                                        </td>

                                        <td>
                                            <div class="span2">
                                                <button type="submit" name="submit_billing" class="btn btn-block btn-danger">Show Class Members</button>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="span12">
                        <?php
                        if (isset($_POST["pupil_id"], $_POST["academic_term"], $_POST["fee_amount"]) && !empty($_SESSION["billing_term"])) {
                            $bill->applyBillToStudentAccount();
                        }
                        ?>
                        <form class="form-horizontal" method="post">
                            <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                            <button type="submit" class="btn">Apply Bill to <?php echo $class_name; ?> Students' Accounts</button>

                            <a href="billing.php" class="btn btn-danger" style="margin-left: 20px;">Cancel</a>

                            <table class="table table-condensed table-bordered margin-left table-striped">
                                <thead class="table-head-format">
                                    <tr>
                                        <td colspan="6" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($class_name)) {
                                                echo $class_name;
                                            } else {
                                                echo "";
                                            }
                                            ?> STUDENTS
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="6">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 3%; text-align: center; font-weight: 600;">S/N</td>
                                        <td style="text-align: center; width: 10%; font-weight: 600;">ID NUMBERS</td>
                                        <td style="text-align: center; font-weight: 600;">STUDENTS</td>
                                        <td style="text-align: center; font-weight: 600; width: 5%;">SEX</td>
                                        <td style="text-align: center; font-weight: 600; width: 15%;">BOARDING STATUS</td>
                                        <td style="text-align: center; font-weight: 600; width: 15%;">TOTAL FEES, GH&cent;</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;

                                    while ($members = mysqli_fetch_assoc($class_membership_set)) {
                                        $getBillByClassNameAll = $bill->getSummarizedBillByClassNameAll($class_name, $_SESSION["billing_term"]);
                                        $getBillByClassNameFemale = $bill->getSummarizedBillByClassNameFemale($class_name, $_SESSION["billing_term"]);
                                        $getBillByClassNameMale = $bill->getSummarizedBillByClassNameMale($class_name, $_SESSION["billing_term"]);
                                        
                                        $getBillByClassNameAllPTA = $bill->getSummarizedBillByClassNameAllPTA($class_name, $_SESSION["billing_term"]);
                                        $getBillByClassNameFemalePTA = $bill->getSummarizedBillByClassNameFemalePTA($class_name, $_SESSION["billing_term"]);
                                        $getBillByClassNameMalePTA = $bill->getSummarizedBillByClassNameMalePTA($class_name, $_SESSION["billing_term"]);
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            
                                            <td style="text-align: center;">
                                                <?php echo htmlentities($members["pupil_id"]); ?>
                                                <input type="text" name="pupil_id[]" value="<?php echo htmlentities($members["pupil_id"]); ?>">
                                                <input type="text" name="academic_term" value="<?php echo $_SESSION["billing_term"]; ?>">
                                                <input type="text" name="fee_amount[]" value="<?php
                                                if ($members["boarding_status"] === "Day Student" && $members["sex"] === "Male") {
                                                    $feesToBeBilled = $getBillByClassNameAll["dayFeeAll"] + $getBillByClassNameMale["dayFeeMale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Day Student" && $members["sex"] === "Female") {
                                                    $feesToBeBilled = $getBillByClassNameAll["dayFeeAll"] + $getBillByClassNameFemale["dayFeeFemale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Boarding Student" && $members["sex"] === "Male") {
                                                    $feesToBeBilled = $getBillByClassNameAll["boardingFeeAll"] + $getBillByClassNameMale["boardingFeeMale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Boarding Student" && $members["sex"] === "Female") {
                                                    $feesToBeBilled = $getBillByClassNameAll["boardingFeeAll"] + $getBillByClassNameFemale["boardingFeeFemale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                }
                                                ?>">
                                                
                                                <input type="text" name="pta_fee[]" value="<?php
                                                if ($members["boarding_status"] === "Day Student" && $members["sex"] === "Male") {
                                                    $feesToBeBilled = $getBillByClassNameAllPTA["dayFeeAll"] + $getBillByClassNameMalePTA["dayFeeMale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Day Student" && $members["sex"] === "Female") {
                                                    $feesToBeBilled = $getBillByClassNameAllPTA["dayFeeAll"] + $getBillByClassNameFemalePTA["dayFeeFemale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Boarding Student" && $members["sex"] === "Male") {
                                                    $feesToBeBilled = $getBillByClassNameAllPTA["boardingFeeAll"] + $getBillByClassNameMalePTA["boardingFeeMale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Boarding Student" && $members["sex"] === "Female") {
                                                    $feesToBeBilled = $getBillByClassNameAllPTA["boardingFeeAll"] + $getBillByClassNameFemalePTA["boardingFeeFemale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                }
                                                ?>">
                                            </td>
                                            
                                            <td>
                                                <?php
                                                $getNames = $pupil->getPupilById($members["pupil_id"]);
                                                echo $getNames["other_names"] . " " . $getNames["family_name"];
                                                ?>
                                            </td>
                                            
                                            <td style="text-align: left;">
                                                <?php
                                                $getSex = $pupil->getPupilById($members["pupil_id"]);
                                                echo $getSex["sex"];
                                                ?>
                                            </td>
                                            
                                            <td>
                                                <?php
                                                $admissions = $admission->getAdmissionById($members["pupil_id"]);
                                                echo $admissions["boarding_status"];
                                                ?>
                                            </td>

                                            <td style="text-align: right;">
                                                <?php
                                                if ($members["boarding_status"] === "Day Student" && $members["sex"] === "Male") {
                                                    $feesToBeBilled = $getBillByClassNameAll["dayFeeAll"] + $getBillByClassNameMale["dayFeeMale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Day Student" && $members["sex"] === "Female") {
                                                    $feesToBeBilled = $getBillByClassNameAll["dayFeeAll"] + $getBillByClassNameFemale["dayFeeFemale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Boarding Student" && $members["sex"] === "Male") {
                                                    $feesToBeBilled = $getBillByClassNameAll["boardingFeeAll"] + $getBillByClassNameMale["boardingFeeMale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                } elseif ($members["boarding_status"] === "Boarding Student" && $members["sex"] === "Female") {
                                                    $feesToBeBilled = $getBillByClassNameAll["boardingFeeAll"] + $getBillByClassNameFemale["boardingFeeFemale"];
                                                    echo number_format($feesToBeBilled, 2, ".", ",");
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<?php
require_once '../includes/footer.php';
