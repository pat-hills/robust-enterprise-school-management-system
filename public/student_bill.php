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
    if (!in_array("student_bill", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["selected_class_name"])) {
    $_SESSION["selected_class_name"] = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_student_bill.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("class_name", "dontselect=select one", "Please, select Class.");
        $validator->addValidation("bill_type", "dontselect=select one", "Please, select Bill Type.");
        $validator->addValidation("academic_term", "dontselect=select one", "Please, select Academic Term.");
        $validator->addValidation("bill_item", "dontselect=select one", "Please, select Bill Item.");
        $validator->addValidation("day_amount", "req", "Please, fill in the Day Amount.");
        $validator->addValidation("boarding_amount", "req", "Please, fill in the Boarding Amount.");

        if ($validator->ValidateForm()) {
            $bill->insertClassBill();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<button type='button' class='close' data-dismiss='alert'></button>";
            echo "<ul type = 'none'>";
            echo "<li>All <strong>FIELDS</strong> are required!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }

    $getClasses = $classes->getClasses();
     $getClasses_sec = $classes->getClasses();
    $institution_set = $institutionDetail->getInstitutionDetails();
    $billItems = $bill->getBillItemBySchoolNumber($institution_set["school_number"]);

    $getAcademicYear = $academicYear->getAcademicYear();
    $academic_terms = $academicTerm->getLastFourAcademicTerms();
    ?>
    <div class="row">
        <div class="span12">
            <?php
            if (TRUE == $show_form) {
                ?>
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Bill Details</strong></legend> 

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
                                                                <option value="<?php
                                                                if (isset($_SESSION["selected_academic_term"])) {
                                                                    echo $_SESSION["selected_academic_term"];
                                                                } else {
                                                                    echo "select one";
                                                                }
                                                                ?>">
                                                                            <?php
                                                                            if (isset($_SESSION["selected_academic_term"])) {
                                                                                echo $_SESSION["selected_academic_term"];
                                                                            } else {
                                                                                echo "";
                                                                            }
                                                                            ?>
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="control-group">
                                                        <label class="control-label" for="bill_item">Bill Item</label>
                                                        <div class="controls">
                                                            <select name="bill_item" class="select">
                                                                <option value="select one">--Select One--</option>
                                                                <?php
                                                                while ($value = mysqli_fetch_assoc($billItems) ) {
                                                                    ?>
                                                                    <option value="<?php echo htmlentities($value["name"]) ?>"><?php echo htmlentities($value["name"]) ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

        <!--                                                <td>
            <div class="control-group">
                <label class="control-label" for="gender">Sex</label>
                <div class="controls">
                    <select name="gender" class="select">
                        <option value="select one">--Select One--</option>
                        <option value="All">All</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
                </div>
            </div>
        </td> -->
                                            </tr>

                                            <tr>
                                                <td>
                                                    <div class="control-group">
                                                        <label class="control-label" for="class_name">Class</label>
                                                        <div class="controls">
                                                            <select name="class_name" class="select">
                                                                <option value="select one">--Select One--</option>
                                                                <?php
                                                               while ($value = mysqli_fetch_assoc($getClasses) ) {
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
                                                                <option value="select one">--Select One--</option>
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
                                                            <input class="span2" type="text" name="day_amount" autocomplete="off" value="<?php echo 0; ?>">
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="control-group">
                                                        <label class="control-label" for="boarding_amount">Boarding Amount</label>
                                                        <div class="controls">
                                                            <input class="span2" type="text" name="boarding_amount" autocomplete="off" value="<?php echo 0; ?>">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <button type="submit" name="submit" class="btn" style="margin-left: 320px;">Save</button>
                                    <a href="student_bill.php" class="btn large btn-danger" style="margin-left: 10px;">Clear</a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="spacer"></div>

                <form class="form-horizontal" method="post">
                    <fieldset>
                        <?php
                        $show_form_2 = TRUE;
                        if (isset($_POST["show_class_bill_item"])) {
                            $validator = new FormValidator();
                            $validator->addValidation("selected_academic_term", "dontselect=select term", "Please, select Academic Term.");
                            $validator->addValidation("selected_class_name", "dontselect=select class", "Please, select Class.");

                            $selected_term = trim(escape_value($_POST["selected_academic_term"]));
                            $_SESSION["chosen_term"] = $selected_term;

                            $selected_class = trim(escape_value($_POST["selected_class_name"]));
                            $_SESSION["selected_class_name"] = $selected_class;

                            $getClassID = $classes->getClassByName($selected_class);

                            if ($validator->ValidateForm()) {
//                                $class = trim(escape_value($_POST["class_name"]));
                            } else {
                                echo "<div class='row'>";
                                echo "<div class='span12'>";
                                echo "<div class='alert alert-error'>";
                                echo "<button type='button' class='close' data-dismiss='alert'></button>";
                                echo "<ul type = 'none'>";
                                echo "<li>Please, select both Academic Term and Class.</li>";
                                echo "</ul>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            $selected_term = "";
                            $selected_class = "";
                        }

                        if (TRUE == $show_form_2) {
                            ?>
                            <div class="row">
                                <div class="span12">
                                    <table class="table table-bordered table-condensed">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; padding: 10px;" colspan="10">
                                                    <select name="selected_academic_term">
                                                        <option value="select term">--Select Academic Term--</option>
                                                        <?php
                                                        while ($value = mysqli_fetch_assoc($academic_terms) ) {
                                                            ?>
                                                            <option value="<?php echo htmlentities($value["academic_year"] . "/" . $value["term"]); ?>"><?php echo htmlentities($value["academic_year"] . "/" . $value["term"]); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>

                                                    <select name="selected_class_name" class="select">
                                                        <option value="select class">--Select Class--</option>
                                                        <?php
                                                               while ($value = mysqli_fetch_assoc($getClasses_sec) ) {
                                                                    ?>
                                                                    <option value="<?php echo htmlentities($value["class_name"]) ?>"><?php echo htmlentities($value["class_name"]) ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                    </select>

                                                    <button type="submit" name="show_class_bill_item" class="btn">Show Bill Items for Selected Class</button>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center;">BILL ITEMS</td>
                                                <!--<td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 12%;">ACADEMIC TERM</td>-->
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 5%;">CLASS</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">BILL TYPE</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 10%;">DAY AMOUNT</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 15%;">BOARDING AMOUNT</td>
                                                <td style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 12%;">ACADEMIC TERM</td>
                                                <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; text-align: center; width: 12%;">ACTION</td>
                                            </tr>
                                            <?php
                                            $i = 1;
//                                        $getAcademicTerm = $academicTerm->getActivatedTerm();
//                                        $get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

                                            $classBillItems = $bill->getClassBillItemByName($selected_class, $selected_term);
//                                            $getClassPTA = $bill->getPTAByClassName($selected_class, $selected_term);
                                            while ($classBillItem = mysqli_fetch_assoc($classBillItems)) {
                                                ?>
                                                <tr>
                                                    <td style="text-align: center;"><?php echo $i++; ?></td>
                                                    <td><?php echo $classBillItem["bill_item"]; ?></td>
                                                    <!--<td style="text-align: center;"><?php // echo $classBillItem["academic_term"]; ?></td>-->
                                                    <td style="text-align: center;"><?php echo $classBillItem["class_name"]; ?></td>
                                                    <td style="text-align: left;"><?php echo $classBillItem["bill_type"]; ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($classBillItem["day_amount"], 2, ".", ","); ?></td>
                                                    <td style="text-align: right;"><?php echo number_format($classBillItem["boarding_amount"], 2, ".", ","); ?></td>
                                                    <td style="text-align: center;"><?php echo $classBillItem["academic_term"]; ?></td>
                                                    <td><a href="edit_student_bill.php?id=<?php echo urlencode(htmlentities($classBillItem["url_string"])); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                                    <td><a href="delete_student_bill.php?id=<?php echo urlencode(htmlentities($classBillItem["url_string"])); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </fieldset>
                    
                    <a href="print_class_bill.php" onclick="" class="btn btn-primary" target="_blank">Generate Student Bills</a>
                  
                    
                    <legend class="legend"></legend>
                </form>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
//unset($_SESSION["selected_academic_term"]);
require_once '../includes/footer.php';

