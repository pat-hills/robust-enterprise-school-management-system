<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/Admission.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/Guardian.php';
require_once '../classes/Photo.php';
require_once '../classes/User.php';

confirm_logged_in();

$admission = new Admission();
$guardian = new Guardian();
$pupil = new Pupil();
$classMembership = new ClassMembership();
$photo = new Photo();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("view_details", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $studentId = trim(escape_value(urldecode(getURL())));
    $studentID = $pupil->getPupilByUniqeUrlString($studentId);
    $student_id = $studentID["pupil_id"]; 
//    echo $student;
    $full_name_set = $pupil->getPupilById($student_id);
    $admission_set = $admission->getAdmissionById($student_id);
    $guardian_set = $guardian->getGuardianByPupilId($student_id);
    $getStudentPhoto = $photo->getPhotoById($student_id);
} else {
    $student_id = "";
//    redirect_to("find_pupil_by_name.php");
}
?>

<div class="container">
    <?php require_once '../includes/breadcrumb_for_view_details.php'; ?>

    <div class="row">
        <div class="span12">

            <div class="spacer-medium"></div>

            <?php // $getStudentPhoto = $photo->getPhotoById($student_id); ?>

            <div id="alignPhoto-2">
                <img src="<?php echo "../" . htmlentities($getStudentPhoto["photo_url"]); ?>" width="200">
            </div>

            <div class="center-table">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td colspan="2" style="text-align: center; background-color: #F5F5F5; font-weight: 600;"><strong>STUDENT DETAILS</strong></td>
                        </tr>
                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">ID Number</td><td><?php echo $full_name_set["pupil_id"]; ?></td>
                        </tr>

                        <tr>
                            <td class="td-width" style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Full Name</td><td><?php echo $full_name_set["other_names"] . " " . $full_name_set["family_name"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Date of Birth</td><td>
                                <?php
                                if (empty($full_name_set["date_of_birth"])) {
                                    echo "";
                                } else {
                                    echo date("F j, Y", strtotime($full_name_set["date_of_birth"]));
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Sex</td><td><?php echo $full_name_set["sex"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Hometown</td><td><?php echo $full_name_set["hometown"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Region</td><td><?php echo $full_name_set["region"]; ?></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="text-align: right; background-color: #fff;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center; background-color: #F5F5F5; font-weight: 600;"><strong>ADMISSION DETAILS</strong></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Admission Date</td><td>
                                <?php
                                if (empty($admission_set["admission_date"])) {
                                    echo "";
                                } else {
                                    echo date("F j, Y", strtotime($admission_set["admission_date"]));
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Class</td><td><?php echo $admission_set["class_admitted_to"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Boarding Status</td><td><?php echo $admission_set["boarding_status"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">House</td><td><?php echo $admission_set["house"]; ?></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="text-align: right; background-color: #fff;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center; background-color: #F5F5F5;"><strong>GUARDIAN DETAILS</strong></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Family Name</td><td><?php echo $guardian_set["guardian_family_name"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Other Names</td><td><?php echo $guardian_set["guardian_other_names"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Sex</td><td><?php echo $guardian_set["guardian_sex"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Occupation</td><td><?php echo $guardian_set["occupation"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Relation To Pupil</td><td><?php echo $guardian_set["relation_to_pupil"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Contact Numbers</td><td><?php echo $guardian_set["telephone_1"] . " " . " " . " " . $guardian_set["telephone_2"] . " " . " " . " " . $guardian_set["telephone_3"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">Postal Address</td><td><?php echo $guardian_set["postal_address"]; ?></td>
                        </tr>

                        <tr>
                            <td style="text-align: right; background-color: #F5F5F5; font-weight: 600;">House Number</td><td><?php echo $guardian_set["house_number"]; ?></td>
                        </tr>
                    </tbody>
                </table>
            <a href="find_pupil_by_name.php" class="btn btn-primary pull-right">Back</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

