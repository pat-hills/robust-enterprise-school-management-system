<?php
require_once '../includes/header.php';
require_once '../classes/AcademicTerm.php';
require_once '../classes/AcademicYear.php';

$academicTerm = new AcademicTerm();
$academicYear = new AcademicYear();

$getAcademicYear = $academicYear->getAcademicYear();
$activatedAcademicYear = $getAcademicYear["academic_year"];
//$getAcademicTerm = $academicTerm->getAcademicTerm($activatedAcademicYear);
$getAcademicTerm = $academicTerm->getAcademicTerms();
?>

<div class="row">
    <?php
    if (isset($_POST["term_setting_change"])) {
        $selected_term_setting = trim(escape_value($_POST["selected_term_setting"]));
        $_SESSION["selected_term_setting"] = $selected_term_setting;
    }
    ?>
    <form class="form-horizontal" method="post">
        <div class="span12">
            <ul class="breadcrumb">
                <li><a href="dashboard.php">Home</a><span class="divider">/</span></li>
                <li class="active">Term Settings<span class="divider">/</span></li>
                <li><a href="logout.php">Logout</a></li>
<!--                <li><small style="padding-left: 600px;">
                        <select name="selected_term_setting">
                            <option value="">
                                <?php 
                                    if (isset($_SESSION["selected_term_setting"])) {
                                        echo $_SESSION["selected_term_setting"];
                                    }  else {
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
                    </small><span class="divider">/</span>
                    <button type="submit" name="term_setting_change" class="btn-mini">Activate</button>
                </li>-->
        </div>
    </form>
</div>

