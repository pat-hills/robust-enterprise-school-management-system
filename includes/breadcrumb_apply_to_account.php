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
$getAcademicTerm = $academicTerm->getLastAcademicTerm();
$getClasses = $classes->getClasses();
?>

<div class="row">
    <form class="form-horizontal" method="post">
        <div class="span12">
            <?php
            if (isset($_POST["billing_term"]) && !empty($_POST["billing_term"])) {
                $billing_term = trim(escape_value($_POST["billing_term"]));
                $_SESSION["billing_term"] = $billing_term;
            }
            ?>
            <ul class="breadcrumb">
                <li><a href="dashboard.php">Home</a><span class="divider">/</span></li>
                <li class="active">Apply Bill to Ledgers<span class="divider">/</span></li>
                <li><a href="logout.php">Logout</a><span class="divider">/</span></li>
                <li style="padding-left: 529px;"><small>
                        <select name="billing_term" class="select-medium">
                            <option value="<?php
                            if (isset($_SESSION["billing_term"])) {
                                echo $_SESSION["billing_term"];
                            }
                            ?>">
                                        <?php
                                        if (isset($_SESSION["billing_term"])) {
                                            echo $_SESSION["billing_term"];
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

