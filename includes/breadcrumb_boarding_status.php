<?php
require_once '../includes/header.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/AcademicYear.php';
require_once '../classes/Classes.php';

$academicTerm = new AcademicTerm();
$academicYear = new AcademicYear();
$classes = new Classes();

$getAcademicYear = $academicYear->getAcademicYear();
$activatedAcademicYear = $getAcademicYear["academic_year"];
//$getAcademicTerm = $academicTerm->getAcademicTerm($activatedAcademicYear);
$getAcademicTerm = $academicTerm->getLastAcademicTerm();
$getClasses = $classes->getClasses();

if (!isset($_SESSION["boarding_status_term"])) {
    $_SESSION["boarding_status_term"] = "";
}
?>

<div class="row">
    <form class="form-horizontal" method="post">
        <div class="span12">
            <?php
            if (isset($_POST["boarding_status_term"]) && !empty($_POST["boarding_status_term"])) {
                $boarding_status_term = trim(escape_value($_POST["boarding_status_term"]));

                $_SESSION["boarding_status_term"] = $boarding_status_term;
            } else {
                echo "";
//                echo "<div class='row'>";
//                echo "<div class='span12'>";
//                echo "<div class='alert alert-info'>";
//                echo "<p><strong><u>REQUIRED</u></strong></p>";
//                echo "<ul type = 'square'>";
//                echo "<li>Select both <strong>PROMOTED TO</strong> class and <strong>ACADEMIC TERM</strong></li>";
//                echo "</ul>";
//                echo "</div>";
//                echo "</div>";
//                echo "</div>";
            }
            ?>
            <ul class="breadcrumb">
                <li><a href="dashboard.php">Home</a><span class="divider">/</span></li>
                <li class="active">Boarding Status<span class="divider">/</span></li>
                <li><a href="logout.php">Logout</a><span class="divider">/</span></li>
<!--                <li style="padding-left: 214px;"><strong>STEP 1:</strong><small>
                        <select name="boarding_status_class" class="select-medium-2">
                            <option value="<?php
                            if (isset($boarding_status_class)) {
                                echo $boarding_status_class;
                            }
                            ?>">
                                        <?php
                                        if (isset($boarding_status_class)) {
                                            echo $boarding_status_class;
                                        } else {
                                            echo "--Select PROMOTED TO class--";
                                        }
                                        ?>
                            </option>

                            <?php
                            foreach ($getClasses as $value) {
                                ?>
                                <option value="<?php echo htmlentities($value["class_name"]); ?>"><?php echo htmlentities($value["class_name"]); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </small><span class="divider">/</span>
                </li>-->

                <li style="padding-left: 610px;"><small>
                        <select name="boarding_status_term" class="select-medium">
                            <option value="<?php
                            if (isset($boarding_status_term)) {
                                echo $boarding_status_term;
                            }
                            ?>">
                                        <?php
                                        if (isset($boarding_status_term)) {
                                            echo $boarding_status_term;
                                        } else {
                                            echo "--Select Academic Term--";
                                        }
                                        ?>
                            </option>

                            <?php
                            foreach ($getAcademicTerm as $value) {
                                ?>
                                <option value="<?php echo htmlentities($value["academic_year"] . "/" . $value["term"]); ?>"><?php echo htmlentities($value["academic_year"] . "/" . $value["term"]); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn-mini">Activate</button>
                    </small><span class="divider">/</span>
                </li>
        </div>
    </form>
</div>

