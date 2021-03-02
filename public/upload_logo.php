<?php
require_once '../includes/header.php';
require_once '../classes/InstitutionDetail.php';
//require_once '../classes/Pupil.php';
require_once '../classes/Photo.php';
require_once '../classes/User.php';

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
//$pupil = new Pupil();
$photo = new Photo();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);
$imgLink = $photo->getLogoId();
$directory = $imgLink['photo_url'];

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("upload_logo", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_photograph.php';

    if (!isset($_FILES["logo"]["name"], $_FILES["logo"]["size"])) {
        $_FILES["logo"]["name"] = "";
        $_FILES["logo"]["size"] = "";
    }

    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["logo"]["name"]);
    $extension = end($temp);

    $show_form = TRUE;
    if (isset($_POST["submit"]) && (($_FILES["logo"]["type"] == "image/gif") || ($_FILES["logo"]["type"] == "image/jpeg") || ($_FILES["logo"]["type"] == "image/jpg") || ($_FILES["logo"]["type"] == "image/pjpeg") || ($_FILES["logo"]["type"] == "image/x-png") || ($_FILES["logo"]["type"] == "image/png")) && ($_FILES["logo"]["size"] < 1024000) && in_array($extension, $allowedExts)) {
        $validator = new FormValidator();
        $validator->addValidation("logo_id", "req", "Please, fill in the <strong>LOGO NUMBER</strong>.");


        $logo_id = trim(escape_value($_POST["logo_id"]));


        if ($validator->ValidateForm()) {

            move_uploaded_file($_FILES["logo"]["tmp_name"], "../photos/" . $_FILES["logo"]["name"]);
            $photo->insertLogo();
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
//    $checkPhotoUpload = $pupil->checkPhotoUpload();

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>School Logo Upload</strong></legend> 


                        <div class="control-group">

                            <div class="controls">
                                <div class="imagges">
                                                                        <img src="../<?php echo $directory; ?>" style="width: 150px; height: 150px"/>

                                </div>

                                <div class="controls">
                                </div>    
                            </div>
                        </div>


                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="logo_id">Logo Name</label>
                            <div class="controls">
                                <input class="span3" type="text" name="logo_id" autocomplete="off" autofocus
                                       value="<?php
    if (isset($logo_id)) {
        echo $logo_id;
    }
        ?>">
                            </div>
                        </div>

                        <label class="control-label" for="logo">Select School Logo</label>
                        <div class="controls">
                            <input type="file" name="logo" />
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save photo</button>
                                <a href="upload_logo.php" class="btn btn-danger">Cancel</a>
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

