<?php
ob_start();
require_once '../includes/header.php';
require_once '../classes/Comment.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

confirm_logged_in();

$comment = new Comment();
$institutionDetail = new InstitutionDetail();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("comment_on", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_school_detail.php';

    $comment->saveCommentTypeSuccessBanner();
    
    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("comment", "req", "Please, fill in the Comment.");
        $validator->addValidation("type", "dontselect=select one", "Please, select Comment Type.");

        if ($validator->ValidateForm()) {
            $comment->insertCommentType();
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
    }

    $institution_set = $institutionDetail->getInstitutionDetails();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Comments and their Types</strong></legend>    

                        <input type="hidden" name="school_number" value="<?php echo $institution_set["school_number"]; ?>" />

                        <div class="control-group">
                            <label class="control-label" for="comment">Comment</label>
                            <div class="controls">
                                <input class="span6" type="text" name="comment" autocomplete="off" autofocus>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="type">Comment Type</label>
                            <div class="controls">
                                <select name="type" class="select">
                                    <option value="select one">--Select One--</option>
                                    <option value="Conduct">Conduct</option>
                                    <option value="Interest">Interest</option>
                                    <option value="Attitude">Attitude</option>
                                    <option value="Remarks">Remark</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <button type="submit" name="submit" class="btn">Save</button>
                                <a href="comment.php" class="btn large btn-danger">Clear</a>
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

