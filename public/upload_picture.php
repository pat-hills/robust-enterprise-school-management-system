<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Pupil.php';
require_once '../classes/Photo.php';
require_once '../classes/User.php';
require_once '../classes/ClassMembership.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$pupil = new Pupil();
$photo = new Photo();
$user = new User();
$classMembership = new ClassMembership();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("upload_picture", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_photograph.php';

    if (!isset($_FILES["photo"]["name"], $_FILES["photo"]["size"])) {
        $_FILES["photo"]["name"] = "";
        $_FILES["photo"]["size"] = "";
    }

    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["photo"]["name"]);
    $extension = end($temp);

    $show_form = TRUE;
    if (isset($_POST["submit"]) && (($_FILES["photo"]["type"] == "image/gif") || ($_FILES["photo"]["type"] == "image/jpeg") || ($_FILES["photo"]["type"] == "image/jpg") || ($_FILES["photo"]["type"] == "image/pjpeg") || ($_FILES["photo"]["type"] == "image/x-png") || ($_FILES["photo"]["type"] == "image/png")) && ($_FILES["photo"]["size"] < 1024000) && in_array($extension, $allowedExts)) {
        $validator = new FormValidator();
        $validator->addValidation("pupil_id", "dontselect=select student", "Please, select both <strong>STUDENT</strong> and <strong>PHOTOGRAPH</strong>.");

        if ($validator->ValidateForm()) {
             
                move_uploaded_file($_FILES["photo"]["tmp_name"], "../photos/" . $_FILES["photo"]["name"]);
                $photo->insertPhoto();
          
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
    } else {
        echo "<div class='row'>";
        echo "<div class='span12'>";
        echo "<div class='alert alert-info'>";
        echo "<strong>TAKE NOTE:</strong>";
        echo "<ul type ='1'>";
        echo "<li><strong>Photograph size</strong> must be less than <strong>1MB</strong>.</li>";
        echo "<li>Only upload Photograph with one of these extensions: gif, jpeg, jpg, png.</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    $institution_set = $institutionDetail->getInstitutionDetails();
    $checkPhotoUpload = $pupil->checkPhotoUpload();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Student Photograph Upload</strong></legend>                        

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="pupil_id">List of Students</label>
                            <div class="controls">
                                <select name="pupil_id">
                                    <option value="select student">--Select Student--</option>
                                    <?php
                                    while ($getUploadedPhotos = mysqli_fetch_assoc($checkPhotoUpload)) {
                                        $getClass = $classMembership->getClassMembershipById($getUploadedPhotos["pupil_id"]);
                                        ?>
                                        <option value="<?php echo htmlentities($getUploadedPhotos["pupil_id"]); ?>"><?php echo htmlentities($getClass["class_name"] . " -- " . $getUploadedPhotos["pupil_id"] . " -- " . $getUploadedPhotos["other_names"] . " " . $getUploadedPhotos["family_name"]); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <label class="control-label" for="photo">Select Photograph</label>
                        <div class="controls">
                            <input type="file" name="photo" />
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save photo</button>
                                <a href="upload_picture.php" class="btn btn-danger">Cancel</a>
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

