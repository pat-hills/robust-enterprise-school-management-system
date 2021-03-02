<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Bill.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$bill = new Bill();
$pay_ment = new Payments();


$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("debtors_creditors", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["debtors_creditors_list"])) {
    $_SESSION["debtors_creditors_list"] = "";
}
?>

<div class="container">

    <?php
    require_once '../includes/breadcrumb_creditors_debtors.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=--Select Class--", "Please, select <strong>CLASS</strong> to view its members.");

        $class_name = trim(ucwords(escape_value($_POST["class_name"])));
        $_SESSION["debtors_creditors_list"] = $class_name;

        if ($validator->ValidateForm()) {
//            redirect_to("class_menu.php");
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

    $class_membership_set = $classMembership->getClassMembersByClassName($_SESSION["debtors_creditors_list"]);
    $class_set = $classes->getClasses();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="center-table">
                        <div class="span8">
                            <div class="span2" style="margin-left: 145px;">
                                <table class="table table-condensed">
                                    <tr>
                                        <td>
                                            <select name="class_name" class="select">
                                                <option value="<?php
                                                if (isset($class_name)) {
                                                    echo $class_name;
                                                } else {
                                                    echo "--Select Class--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($class_name)) {
                                                                echo $class_name;
                                                            } else {
                                                                echo "--Select Class--";
                                                            }
                                                            ?>
                                                </option>
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
                                            <div class="span3">
                                                <button type="submit" name="submit" class="btn btn-block btn-danger">Show Debtors/Creditors</button>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>

                <!--printing buttons-->
                <div class="row">
                    <div class="span12">
                        <a href="print_debtors_creditors.php" target="_blank" class="btn btn-primary" style="margin-top: 10px;">Print
                            <?php
                            if (isset($_SESSION["debtors_creditors_list"])) {
                                echo $_SESSION["debtors_creditors_list"];
                            }
                            ?>
                            List of Debtors/Creditors
                        </a>

                        <form class="form-horizontal" method="post">
                            <table class="table table-condensed table-bordered margin-left table-striped">
                                <thead class="table-head-format">
                                    <tr>
                                        <td colspan="6" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($_SESSION["debtors_creditors_list"])) {
                                                echo $_SESSION["debtors_creditors_list"];
                                            } else {
                                                echo "";
                                            }
                                            ?> DEBTORS LIST
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
                                        <td style="text-align: center; font-weight: 600; width: 15%;">AMOUNT, GH&cent;</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;

                                    while ($members = mysqli_fetch_assoc($class_membership_set)) {
                                            // $pupil_id = $members["pupil_id"];
                                        $getSingleBillAmount = $bill->getSingleBillingAmount($members["pupil_id"]);
                                        $singleBillAmount = $getSingleBillAmount["billAmount"];

                                        $getFeesPayable = $bill->getStudentAccount($members["pupil_id"]);
                                        $getTotalFeesPaid = $bill->getTotalFeesPaid($members["pupil_id"]);
                                        $getTotalAmountPaid = $bill ->getTotalAmountPaid($members["pupil_id"]);
                                        $getPTAAll = $bill->getPTAFeesAll($members["pupil_id"]);
                                        $getPTAFeesPaid = $bill->getPTAFeesPaid($members["pupil_id"]);
                                        
                                        $getBalance = number_format((($getFeesPayable["allFeesPayable"] + $singleBillAmount + $getPTAAll["ptaFeesAll"]) - ($getTotalAmountPaid["total_amount_paid"])), 2, ".", ",");
                                        if ($getBalance == 0) {
                                            
                                            $balance = str_replace("-", "", $getBalance) . " ";
                                            
                                            // $balance = $getBalance. "-";
                                        }elseif ($getBalance > 0) {
                                               $balance = $getBalance . "DR";
                                            }  else {
                                                
                                                 $balance = str_replace("-", "", $getBalance) . " CR";
                                               // $balance = $getBalance . "CR";
                                               
                                            } 
                                        ?>
                                        <tr>
                                            <td style="text-align: center;">
                                                <?php
                                                echo $i++;
                                                ?>
                                            </td>
                                            <td style="text-align: center;"><?php echo $members["pupil_id"]; ?></td>
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
                                            <td style="text-align: right;"><?php echo $balance; ?></td>
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
?>


 