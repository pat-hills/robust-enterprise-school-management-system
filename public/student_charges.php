<?php
require_once '../includes/header.php';
require_once '../classes/Classes.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Admission.php';
require_once '../classes/Pupil.php';
require_once '../classes/User.php';
require_once '../classes/Bill.php';
require_once '../classes/AcademicTerm.php';

confirm_logged_in();

$classes = new Classes();
$classMembership = new ClassMembership();
$pupil = new Pupil();
$admission = new Admission();
$user = new User();
$bill = new Bill();
$academicTerm = new AcademicTerm();

$getAcademicTerm = $academicTerm->getActivatedTerm();
$get_academic_term = $getAcademicTerm["academic_year"] . "/" . $getAcademicTerm["term"];

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("student_charges", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["selected_class_name_charges"], $_SESSION["selected_academic_term_charges"], $_SESSION["status"], $_SESSION["names"], $_SESSION["termFees"])) {
    $_SESSION["selected_class_name_charges"] = "";
    $_SESSION["selected_academic_term_charges"] = "";
    $_SESSION["status"] = "";
    $_SESSION["names"] = "";
    $_SESSION["termFees"] = "";
}
?>

<div class="container">

    <?php
    require_once '../includes/breadcrumb_for_student_charges.php';

    $show_form = TRUE;
    if (isset($_POST["submit_charges"])) {
        $validator = new FormValidator();
//        $validator->addValidation("selected_academic_term", "dontselect=--Select Term--", "Please, select both <strong>ACADEMIC TERM</strong> and <strong>CLASS</strong>.");
        $validator->addValidation("selected_class_name", "dontselect=--Select Class--", "Please, select <strong>CLASS</strong>.");

//        $selected_academic_term = trim(ucwords(escape_value($_POST["selected_academic_term"])));
        $class_name = trim(ucwords(escape_value($_POST["selected_class_name"])));

//        $_SESSION["selected_academic_term_charges"] = $selected_academic_term;
        $_SESSION["selected_class_name_charges"] = $class_name;

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
    } else {
        $selected_academic_term = "";
        $class_name = "";
    }

    $getClassMembership = $classMembership->getClassMembersByClassName($class_name, $get_academic_term);
    $getBillItems = $bill->getStudentChargesAll($class_name, $get_academic_term);

//    $getStudentChargesBoardingStudents = $bill->getStudentChargesBoardingStudents($class_name, $selected_academic_term);
//    foreach ($getStudentChargesBoardingStudents as $amount) {
//        echo $amount["boarding_amount"];
//    }

    $getStudentChargesTotal = $bill->getTotalStudentCharges($class_name, $get_academic_term);
    $getClasses = $classes->getClasses();
//    $academic_terms = $academicTerm->getLastThreeAcademicTerms();
    $academic_terms = $academicTerm->getLastAcademicTerm();

    $getStudentChargesMale = $bill->getStudentChargesMale($class_name, $get_academic_term);
    $getStudentChargesFemale = $bill->getStudentChargesFemale($class_name, $get_academic_term);

    $countStudentsInClass = mysqli_num_rows($getClassMembership);

    $getTermFees = $bill->getAllTermFees($get_academic_term);
    $getTermPTA = $bill->getTermPTA($get_academic_term);
//    $termFees = $getTermFees["termFees"];

    if ($countStudentsInClass > 0) {
        $expectedTermFees = number_format(($getTermFees["termFees"] + $getTermPTA["termPTAFees"]), 2, ".", ",");
    } else {
        $expectedTermFees = "";
    }

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form method="post">
                    <div class="spacer"></div>
                    <div>
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; padding: 10px;" colspan="10">
    <!--                                        <select name="selected_academic_term">
                                            <option value="--Select Term--">--Select Academic Term--</option>
                                        <?php
                                        foreach ($academic_terms as $value) {
                                            ?>
                                                        <option value="<?php echo htmlentities($value["academic_year"] . "/" . $value["term"]); ?>"><?php echo htmlentities($value["academic_year"] . "/" . $value["term"]); ?></option>
                                            <?php
                                        }
                                        ?>
                                        </select>-->

                                        <select name="selected_class_name" class="select">
                                            <option value="--Select Class--">--Select Class--</option>
                                            <?php
                                            foreach ($getClasses as $value) {
                                                ?>
                                                <option value="<?php echo htmlentities($value["class_name"]); ?>"><?php echo htmlentities($value["class_name"]); ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>

                                        <button type="submit" name="submit_charges" class="btn">Show Charges for Selected Class</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>

                <!--printing buttons-->
                <div class="row">
                    <div class="span12">
<!--                        <a href="#" target="_blank" class="btn btn-primary" style="margin-top: 10px;">Print 
                            <?php
                            if (isset($class_name)) {
                                echo $class_name;
                            }
                            ?>
                            Student Charges
                        </a>-->

                        <form class="form-horizontal" method="post">
                            <table class="table table-condensed table-bordered margin-left table-striped">
                                <thead class="table-head-format">
                                    <tr>
                                        <?php
                                        $rowTotal = mysqli_num_rows($getBillItems);
                                        $maleRow = mysqli_num_rows($getStudentChargesMale);
                                        $femaleRow = mysqli_num_rows($getStudentChargesFemale);
                                        $totalRow = 6 + $rowTotal + $maleRow + $femaleRow + $rowTotal;
                                        $totalColumn = 4 + $rowTotal + $maleRow + $femaleRow;
                                        ?>
                                        <td colspan="<?php echo $totalRow; ?>" style="text-align: center; font-weight: 600; font-size: 20px; background-color: #F9F9F9; padding: 10px;">
                                            <?php
                                            if (isset($class_name)) {
                                                echo $class_name;
                                            } else {
                                                echo "";
                                            }
                                            ?> STUDENT CHARGES FOR <?php echo strtoupper(htmlentities($get_academic_term)); ?> TERM
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="<?php echo htmlentities($totalRow); ?>">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 3%; text-align: center; font-weight: 600;"><br />S/N</td>
                                        <td style="text-align: center; font-weight: 600;">ID NUMBERS</td>
                                        <td style="text-align: center; font-weight: 600;"><br />STUDENTS</td>
                                        <td style="text-align: center; font-weight: 600;"><br />STATUS</td>
                                        <?php
                                        foreach ($getBillItems as $item) {
                                            ?>
                                            <td style="text-align: center; font-weight: 600;"><?php echo htmlentities($item["bill_item"]); ?></td>
                                            <?php
                                        }
                                        ?>
                                        <!--
                                        <?php
//                                        $getStudentChargesForMale = $bill->getStudentChargesForMale($class_name, $selected_academic_term);
                                        $getStudentChargesForMaleLoop = $bill->getStudentChargesForMaleLoop($class_name, $get_academic_term);
                                        $getClassMembers = $classMembership->getClassMembersByName($class_name);

                                        foreach ($getStudentChargesForMaleLoop as $maleLoop) {
                                            if ($getClassMembers["sex"] === "Male") {
                                                ?>
                                                                                                                                <td style="text-align: center; font-weight: 600;"><?php echo htmlentities($maleLoop["bill_item"]); ?></td> 
                                                <?php
                                            }
                                        }

//                                        $getStudentChargesForFemale = $bill->getStudentChargesForFemale($class_name, $selected_academic_term);
                                        $getStudentChargesForFemaleLoop = $bill->getStudentChargesForFemaleLoop($class_name, $get_academic_term);
                                        $getClassStudents = $classMembership->getClassMembersByName($class_name);

                                        foreach ($getStudentChargesForFemaleLoop as $femaleLoop) {
                                            if ($getClassStudents["sex"] !== "Female") {
                                                ?>
                                                                                                                                <td style="text-align: center; font-weight: 600;"><?php echo htmlentities($femaleLoop["bill_item"]); ?></td> 
                                                <?php
                                            }
                                        }
                                        ?>
                                        -->
                                        <td style="text-align: center; font-weight: 600;">TOTAL, GH&cent;</td>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    $i = 1;
//totals of bill items
                                    while ($members = mysqli_fetch_assoc($getClassMembership)) {
                                        $getTermFees = $bill->getTermFees($members["pupil_id"], $get_academic_term);
                                        $_SESSION["termFees"] = $getTermFees;
                                        $termFees = number_format($_SESSION["termFees"]["termFees"] + $getTermPTA["amount"], 2, ".", ",");
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
                                                $_SESSION["names"] = $getNames;
                                                echo $getNames["other_names"] . " " . $getNames["family_name"];
                                                ?>
                                            </td>

                                            <td>
                                                <?php
                                                $_SESSION["status"] = $members["boarding_status"];
                                                echo $members["boarding_status"];
                                                ?>
                                            </td>

                                            <?php
                                            foreach ($getBillItems as $charge) {
                                                ?>
                                                <td style="text-align: right;">
                                                    <?php
                                                    if ($_SESSION["status"] === "Day Student") {
                                                        echo number_format(htmlentities($charge["day_amount"]), 2, ".", ",");
                                                    } else {
                                                        echo number_format(htmlentities($charge["boarding_amount"]), 2, ".", ",");
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                            }
                                            ?>
                                            <!--
                                            <?php
                                            $getStudentChargesMaleExtraLoop = $bill->getStudentChargesForMaleLoop($class_name, $get_academic_term);
                                            foreach ($getStudentChargesMaleExtraLoop as $extraLoopMale) {
                                                if ($getClassMembers["sex"] === "Male") {
                                                    ?>
                                                                                                                                        <td style="text-align: right; ">
                                                    <?php
                                                    if ($_SESSION["status"] === "Day Student" && $_SESSION["names"]["sex"] === "Male") {
//                                                            echo number_format(htmlentities((double) $extraLoopMale["day_amount"]), 2, ".", ",");
                                                        echo $termFees;
                                                    } elseif ($_SESSION["status"] === "Boarding Student" && $_SESSION["names"]["sex"] === "Male") {
//                                                            echo number_format(htmlentities((double) $extraLoopMale["boarding_amount"]), 2, ".", ",");
                                                        echo $termFees;
                                                    } else {
                                                        echo "0.00";
                                                    }
                                                    ?>
                                                                                                                                        </td> 
                                                    <?php
                                                }
                                            }

                                            $getStudentChargesFemaleExtraLoop = $bill->getStudentChargesForFemaleLoop($class_name, $get_academic_term);
                                            foreach ($getStudentChargesFemaleExtraLoop as $extraLoopFemale) {
                                                if ($getClassStudents["sex"] !== "Female") {
                                                    ?>
                                                                                                                                        <td style="text-align: right;">
                                                    <?php
                                                    if ($_SESSION["status"] === "Day Student" && $_SESSION["names"]["sex"] === "Female") {
//                                                            echo number_format(htmlentities((double) $extraLoopFemale["day_amount"]), 2, ".", ",");
                                                        echo $termFees;
                                                    } elseif ($_SESSION["status"] === "Boarding Student" && $_SESSION["names"]["sex"] === "Female") {
//                                                            echo number_format(htmlentities((double) $extraLoopFemale["boarding_amount"]), 2, ".", ",");
                                                        echo $termFees;
                                                    } else {
                                                        echo "0.00";
                                                    }
                                                    ?>
                                                                                                                                        </td> 
                                                    <?php
                                                }
                                            }
                                            ?>
                                            -->
                                            <?php
                                            foreach ($getStudentChargesTotal as $chargesTotal) {
                                                $getStudentChargesForMaleTotal = $bill->getStudentChargesForMaleTotal($class_name, $get_academic_term);
                                                $getStudentChargesForFemaleTotal = $bill->getStudentChargesForFemaleTotal($class_name, $get_academic_term);
                                                ?>
                                                <td style="text-align: right; font-weight: 600;">
                                                    <?php
                                                    if ($_SESSION["status"] === "Day Student" && $_SESSION["names"]["sex"] === "Male") {
//                                                        echo number_format(htmlentities((double) ($chargesTotal["dayFees"] + $getStudentChargesForMaleTotal["maleDayTotal"])), 2, ".", ",");
                                                        echo $termFees;
                                                    } elseif ($_SESSION["status"] === "Boarding Student" && $_SESSION["names"]["sex"] === "Male") {
//                                                        echo number_format(htmlentities((double) ($chargesTotal["boardingFees"] + $getStudentChargesForMaleTotal["maleBoardingTotal"])), 2, ".", ",");
                                                        echo $termFees;
                                                    } elseif ($_SESSION["status"] === "Day Student" && $_SESSION["names"]["sex"] === "Female") {
//                                                        echo number_format(htmlentities((double) ($chargesTotal["dayFees"] + $getStudentChargesForFemaleTotal["femaleDayTotal"])), 2, ".", ",");
                                                        echo $termFees;
                                                    } elseif ($_SESSION["status"] === "Boarding Student" && $_SESSION["names"]["sex"] === "Female") {
//                                                        echo number_format(htmlentities((double) ($chargesTotal["boardingFees"] + $getStudentChargesForFemaleTotal["femaleBoardingTotal"])), 2, ".", ",");
                                                        echo $termFees;
                                                    } else {
                                                        echo "0.00";
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>

                                    <tr>
                                        <td colspan="<?php echo $totalColumn; ?>" style="text-align: right;"><strong>TOTAL, GH&cent;</strong></td>
                                        <!--
                                        <?php
                                        foreach ($getBillItems as $multiplyChargesByClassPopulation) {
                                            ?>
                                                                <td style="text-align: right; font-weight: 600;">
                                            <?php
                                            if ($_SESSION["status"] === "Day Student") {
                                                echo number_format(htmlentities($multiplyChargesByClassPopulation["day_amount"] * $countStudentsInClass), 2, ".", ",");
                                            }

//                                                if ($getClassStudents["boarding_status"] === "Boarding Student") {
//                                                    foreach ($getStudentChargesBoardingStudents as $boarding) {
//                                                        echo number_format(htmlentities($getStudentChargesBoardingStudents["boarding_amount"] * $countStudentsInClass), 2, ".", ",");
//                                                    }
//                                                }
                                            ?>
                                                                </td>
                                            <?php
                                        }
                                        ?>
                                        -->
                                        <!--
                                        <?php
                                        $getStudentChargesMaleExtraLoop = $bill->getStudentChargesForMaleLoop($class_name, $get_academic_term);
                                        foreach ($getStudentChargesMaleExtraLoop as $extraLoopMale) {
                                            if ($getClassMembers["sex"] === "Male") {
                                                ?>
                                                                                                                                <td style="text-align: right; font-weight: 600;">
                                                <?php
                                                if ($_SESSION["status"] === "Day Student" && $_SESSION["names"]["sex"] === "Male") {
                                                    echo number_format(htmlentities((double) ($extraLoopMale["dayTotal"] + $extraLoopMale["boardingTotal"])), 2, ".", ",");
                                                } elseif ($_SESSION["status"] === "Boarding Student" && $_SESSION["names"]["sex"] === "Male") {
                                                    echo number_format(htmlentities((double) ($extraLoopMale["boardingTotal"] + $extraLoopMale["dayTotal"])), 2, ".", ",");
                                                } else {
                                                    echo "0.00";
                                                }
                                                ?>
                                                                                                                                </td> 
                                                <?php
                                            }
                                        }

                                        $getStudentChargesFemaleExtraLoop = $bill->getStudentChargesForFemaleLoop($class_name, $get_academic_term);
                                        foreach ($getStudentChargesFemaleExtraLoop as $extraLoopFemale) {
                                            if ($getClassStudents["sex"] !== "Female") {
                                                ?>
                                                                                                                                <td style="text-align: right; font-weight: 600;">
                                                <?php
                                                if ($_SESSION["status"] === "Day Student" && $_SESSION["names"]["sex"] === "Female") {
                                                    echo number_format(htmlentities((double) ($extraLoopFemale["dayTotal"] + $extraLoopFemale["boardingTotal"])), 2, ".", ",");
                                                } elseif ($_SESSION["status"] === "Boarding Student" && $_SESSION["names"]["sex"] === "Female") {
                                                    echo number_format(htmlentities((double) ($extraLoopFemale["boardingTotal"] + $extraLoopFemale["dayTotal"])), 2, ".", ",");
                                                } else {
                                                    echo "0.00";
                                                }
                                                ?>
                                                                                                                                </td> 
                                                <?php
                                            }
                                        }
                                        ?>
                                        -->
                                        <td style="text-align: right; font-weight: 600;"><?php echo htmlentities($expectedTermFees); ?></td>
                                    </tr>
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
