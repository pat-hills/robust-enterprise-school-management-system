<?php
require_once '../includes/header.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Subject.php';
require_once '../classes/SubjectCombination.php';
require_once '../classes/Classes.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$classMembership = new ClassMembership();
$subject = new Subject();
$subjectCombination = new SubjectCombination();
$classes = new Classes();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("change_class_membership", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    if (isset($_POST["submit"])) {
        $classMembership->updateClassMembership();
    }

    $institution_set = $institutionDetail->getInstitutionDetails();
    $subject_combination_set = $subjectCombination->getSubjectCombinationBySchoolNumber($institution_set["school_number"]);
    $getStudentId = $classMembership->getClassMembershipById($_SESSION["pupil_id_change_class"]);
    $getClasses = $classes->getClasses();
    ?>

    <div class="alert alert-info">
        <?php
        echo "<strong>STEPS</strong>";
        echo "<ul type='1'>";
        echo "<li>Select <strong>NEW CLASS</strong>.</li>";
        echo "<li>Select <strong>SUBJECT COMBINATION</strong>.</li>";
        echo "</ul>";
        ?>
    </div>

    <div class="row">
        <div class="span12">
            <form class="form-horizontal" method="post">
                <fieldset>
                    <legend style="color: #4F1ACB;"><strong>Change Class Membership</strong></legend>    

                    <div class="control-group">
                        <label class="control-label" for="pupil_id">ID Number</label>
                        <div class="controls">
                            <input class="span2" type="text" value="<?php echo $_SESSION["pupil_id_change_class"]; ?>" disabled>
                            <input class="span2" type="hidden" name="pupil_id" value="<?php echo $_SESSION["pupil_id_change_class"]; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="class_name">Class</label>
                        <div class="controls">
                            <select name="class_name" class="select">
                                <option><?php echo htmlentities($getStudentId["class_name"]); ?></option>

                                <?php
                                foreach ($getClasses as $value) {
                                    ?>
                                    <option value="<?php echo $value["class_name"]; ?>"><?php echo $value["class_name"]; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="subject_combination_name">Subject Combination</label>
                        <div class="controls">
                            <select name="subject_combination_name" class="select">
                                <option value="<?php echo htmlentities($getStudentId["subject_combination_name"]); ?>
                                        "><?php
                                            if (isset($getStudentId["subject_combination_name"])) {
                                                echo htmlentities($getStudentId["subject_combination_name"]);
                                            } else {
                                                echo "--Select One--";
                                            }
                                            ?></option>
                                <?php
                                while ($subject_combinations = mysqli_fetch_assoc($subject_combination_set)) {
                                    ?>
                                    <option value="<?php echo $subject_combinations["subject_combination_name"]; ?>"><?php echo $subject_combinations["subject_combination_name"]; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <button type="submit" name="submit" id="submit" class="btn">Save change</button>
                            <a href="change_class.php" class="btn btn-warning">Back</a>
                        </div>
                    </div>
                </fieldset>
                <legend class="legend"></legend>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

