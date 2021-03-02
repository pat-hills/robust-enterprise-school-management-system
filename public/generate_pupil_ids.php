<?php
require_once '../includes/header.php';
require_once '../classes/Comment.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/User.php';

//confirm_logged_in();

$comment = new Comment();
$institutionDetail = new InstitutionDetail();
$user = new User();

//$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
//$splitAccessPages = explode("//", $getUserDetails["access_pages"]);
//for ($i = 0; $i < count($splitAccessPages); $i++) {
//    if (!in_array("comment", $splitAccessPages, TRUE)) {
//        redirect_to("logout_access.php");
//    }
//}
?>

<div class="container">
    <div class="spacer"></div>
    <?php
//    require_once '../includes/breadcrumb_school_detail.php';

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
        <!--body here-->
        <?php
        echo "<table>";
        echo "<tr>";
        echo "<td></td>";
        echo "</tr>";
        echo "</table>";
        ?>
        <table class="table table-bordered table-condensed span4">
            <?php
//            $i = 0;
            for ($i = 1; $i <= 25; $i++) {
                $student_id = generate_id_numbers();

                $random_numbers = generate_id_numbers() . microtime();
                $url_string = md5($random_numbers);
                ?>
                <tr>
                    <!--<td><?php // echo $i; ?></td>-->
                    <td><?php echo $student_id; ?></td>
                    <td><?php echo $url_string; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
    ?>
</div>

<?php
require_once '../includes/footer.php';

