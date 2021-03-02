<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/Admission.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Guardian.php';
require_once '../classes/User.php';

confirm_logged_in();

$admission = new Admission();
$guardian = new Guardian();
$pupil = new Pupil();
$classMembership = new ClassMembership();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("find_pupil_by_name", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($family_name, $other_names)) {
    $family_name = "";
    $other_names = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_find_pupil_by_name_results.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("family_name", "req", "Please, fill in the family name.");
        $validator->addValidation("other_names", "req", "Please, fill in the other names.");

        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));

        $_SESSION["family_name"] = $family_name;
        $_SESSION["other_names"] = $other_names;

        if ($validator->ValidateForm()) {
//            redirect_to("find_pupil_by_name.php");
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

    $full_name = $pupil->getStudentByFullName($family_name, $other_names);

    if (TRUE == $show_form) {
        ?>

        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Display Student's Details</strong></legend>
                        <div class="wrapper">
                            <div class="control-group">
                                <label class="control-label" for="family_name">Family Name</label>
                                <div class="controls">
                                    <input class="span3" type="text" name="family_name" autocomplete="off" autofocus
                                           value="<?php
                                           if (isset($family_name)) {
                                               echo $family_name;
                                           }
                                           ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="other_names">Other Names</label>
                                <div class="controls">
                                    <input class="span3" type="text" name="other_names" autocomplete="off"
                                           value="<?php
                                           if (isset($other_names)) {
                                               echo $other_names;
                                           }
                                           ?>">
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="controls">
                                    <button type="submit" name="submit" class="btn">Find</button>
                                    <a href="find_pupil_by_name.php" class="btn btn-danger">Clear</a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="row">
                    <div class="span12">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                                    <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 10%;">ID NUMBER</td>
                                    <td style="text-align: center; background-color: #F5F5F5; font-weight: 600;">STUDENTS</td>
                                    <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 10%;">CLASS</td>
                                    <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 10%;">ACTION</td>
                                </tr>
                                <?php
                                $i = 1;
                                while ($records = mysqli_fetch_assoc($full_name)) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i++; ?></td>
                                        <td style="text-align: center;"><?php echo $records["pupil_id"]; ?></td>
                                        <td><?php echo $records["other_names"] . " " . $records["family_name"]; ?></td>
                                        <td style="text-align: center;">
                                            <?php
                                            $getStudentById = $classMembership->getClassMembershipPupilId($records["pupil_id"]);
                                            echo $getStudentById["class_name"];
                                            ?>
                                        </td>
                                        <td><a href="view_details.php?id=<?php echo urlencode($records["unique_url_string"]); ?>" class="btn btn-block btn-primary" style="padding: 2px;">View Details</a></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<?php
//unset($_SESSION["family_name"]);
//unset($_SESSION["other_names"]);

require_once '../includes/footer.php';

