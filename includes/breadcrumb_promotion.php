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

if (!isset($_SESSION["selected_promoted_class"], $_SESSION["promoted_term"])) {
    $_SESSION["selected_promoted_class"] = "";
    $_SESSION["promoted_term"] = "";
}
?>

<div class="row">
    <form class="form-horizontal" method="post">
        <div class="span12">
            
            <?php
            if (isset($_POST["promoted_class"]) && !empty($_POST["promoted_class"]) && isset($_POST["promoted_term"]) && !empty($_POST["promoted_term"])) {
                $promoted_class = trim(escape_value($_POST["promoted_class"]));
                $promoted_term = trim(escape_value($_POST["promoted_term"]));

                $_SESSION["selected_promoted_class"] = $promoted_class;
                $_SESSION["promoted_term"] = $promoted_term;
            } 
//            else {
//                echo "<div class='row'>";
//                echo "<div class='span12'>";
//                echo "<div class='alert alert-info'>";
//                echo "<p><strong><u>REQUIRED</u></strong></p>";
//                echo "<ul type = 'square'>";
//                echo "<li>Select both <strong>PROMOTED TO class</strong> and <strong>ACADEMIC TERM</strong></li>";
//                echo "</ul>";
//                echo "</div>";
//                echo "</div>";
//                echo "</div>";
//            }
            ?>
            
            <ul class="breadcrumb">
                <li><a href="dashboard.php">Home</a><span class="divider">/</span></li>
                <li class="active">Promotion<span class="divider">/</span></li>
                <li><a href="logout.php">Logout</a><span class="divider">/</span></li>
                <li style="padding-left: 230px;"><strong>STEP 1:</strong><small>
                        <select name="promoted_class" class="select-medium-4">
                            <option value="<?php
                            if (isset($promoted_class)) {
                                echo $promoted_class;
                            }
                            ?>">
                                        <?php
                                        if (isset($promoted_class)) {
                                            echo $promoted_class;
                                        } else {
                                            echo "--Select CLASS to be promoted to--";
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
                </li>

                <li style="padding-left: 20px;"><strong>STEP 2:</strong><small>
                        <select name="promoted_term" class="select-medium">
                            <option value="<?php
                            if (isset($promoted_term)) {
                                echo $promoted_term;
                            }
                            ?>">
                                        <?php
                                        if (isset($promoted_term)) {
                                            echo $promoted_term;
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

