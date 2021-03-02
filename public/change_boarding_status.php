<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/House.php';
require_once '../classes/BoardingStatus.php';
require_once '../classes/Classes.php';
require_once '../classes/Admission.php';
require_once '../classes/User.php';

confirm_logged_in();

$pupil = new Pupil();
$house = new House();
$boardingStatus = new BoardingStatus();
$classes = new Classes();
$admission = new Admission();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("change_boarding_status", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($_SESSION["pupil_id_change_status"])) {
    redirect_to("change_status.php");
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_update.php';

    $getPupilById = $pupil->getPupilById($_SESSION["pupil_id_change_status"]);
    $getBoardingStatus = $boardingStatus->getBoardingStatus();
    $getHouses = $house->getHouses();
    $getClasses = $classes->getClasses();

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("boarding_status", "req", "Please, fill in the date of birth.");
        $validator->addValidation("house", "req", "Please, fill in the house.");

        if ($validator->ValidateForm()) {
            $admission->changeBoardingStatus();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type='square'>";
            echo "<li>Please, all <strong>FIELDS</strong> are required!</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    }
    
    $getAdmission = $admission->getAdmissionById($_SESSION["pupil_id_change_status"]);

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Change Status</strong></legend>
                        <div class="control-group">
                            <label class="control-label" for="pupil_id">ID Number</label>
                            <div class="controls">
                                <input class="span2" type="text" value="<?php echo $getPupilById["pupil_id"]; ?>" disabled>
                                <input class="span2" type="hidden" name="pupil_id" value="<?php echo $getPupilById["pupil_id"]; ?>">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="boarding_status">Boarding Status</label>
                            <div class="controls">
                                <select name="boarding_status">
                                    <option value="<?php echo htmlentities($getAdmission["boarding_status"]); ?>"><?php echo htmlentities($getAdmission["boarding_status"]); ?></option>
                                    <?php
                                    while ($status = mysqli_fetch_assoc($getBoardingStatus)) {
                                        ?>
                                        <option value="<?php echo htmlentities(ucwords($status["name"])); ?>"><?php echo htmlentities(ucwords($status["name"])); ?></option>
                                        <?php
                                    }
                                    ?>  
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="house">House</label>
                            <div class="controls">
                                <select name="house">
                                    <option value="<?php echo htmlentities($getAdmission["house"]); ?>"><?php echo htmlentities($getAdmission["house"]); ?></option>
                                    <?php
                                    while ($house = mysqli_fetch_assoc($getHouses)) {
                                        ?>
                                        <option value="<?php echo htmlentities(ucwords($house["name"])); ?>"><?php echo htmlentities(ucwords($house["name"])); ?></option>
                                        <?php
                                    }
                                    ?>  
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save changes</button>
                                <a href="change_status.php" class="btn btn-warning">Back</a>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

