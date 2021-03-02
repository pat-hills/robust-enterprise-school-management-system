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

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("upload_signature", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_signature.php';

    if (!isset($_FILES["sign"]["name"], $_FILES["sign"]["size"])) {
        $_FILES["sign"]["name"] = "";
        $_FILES["sign"]["size"] = "";
    }

    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["sign"]["name"]);
    $extension = end($temp);

    $show_form = TRUE;
    if (isset($_POST["submit"]) && (($_FILES["sign"]["type"] == "image/gif") || ($_FILES["sign"]["type"] == "image/jpeg") || ($_FILES["sign"]["type"] == "image/jpg") || ($_FILES["sign"]["type"] == "image/pjpeg") || ($_FILES["sign"]["type"] == "image/x-png") || ($_FILES["sign"]["type"] == "image/png")) && ($_FILES["sign"]["size"] < 1024000) && in_array($extension, $allowedExts)) {
        $validator = new FormValidator();
        $validator->addValidation("signature_name", "req", "Please, fill in the <strong>SIGNATURE NAME</strong>.");

        if ($validator->ValidateForm()) {
            if (file_exists("../photo_signature/" . $_FILES["sign"]["name"])) {
                echo "<div class='row'>";
                echo "<div class='span12'>";
                echo "<div class='alert alert-warning'>";
                echo "<button type='button' class='close' data-dismiss='alert'></button>";
                echo "<strong>TAKE NOTE:</strong>";
                echo "<ul type = '1'>";
                echo "<li>This <strong>SIGNATURE</strong> has already been uploaded!</li>";
                echo "<li>Please, rename the <strong>SIGNATURE</strong> if you still want to upload!</li>";
                echo "</ul>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            } else {
                move_uploaded_file($_FILES["sign"]["tmp_name"], "../photo_signature/" . $_FILES["sign"]["name"]);
                $photo->insertSignature();
            }
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
        echo "<li><strong>Signature size</strong> must be less than <strong>1MB</strong>.</li>";
        echo "<li>Only upload Signature with one of these extensions: gif, jpeg, jpg, png.</li>";
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
                        <legend style="color: #4F1ACB;"><strong>Head Signature Upload</strong></legend>                        

                        <input class="span2" type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>">

                        <div class="control-group">
                            <label class="control-label" for="signature_name">Signature Name</label>
                            <div class="controls">
                                <input class="span3" type="text" name="signature_name" autocomplete="off" autofocus
                                       value="<?php
                                       if (isset($signature_name)) {
                                           echo $signature_name;
                                       }
                                       ?>">
                            </div>
                        </div>

                        <label class="control-label" for="sign">Select Head Signature</label>
                        <div class="controls">
                            <input type="file" name="sign" />
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save signature</button>
                                <a href="upload_signature.php" class="btn btn-danger">Cancel</a>
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

