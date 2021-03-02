<?php
require_once '../includes/header.php';
require_once '../classes/Pupil.php';
require_once '../classes/ClassMembership.php';
require_once '../classes/User.php';

confirm_logged_in();

$pupil = new Pupil();
$classMembership = new ClassMembership();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("show_wsd", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_wsd.php';

    $getDeletedStudents = $pupil->getDeletedStudents();
    ?>

    <div class="spacer"></div>

    <div class="row">
        <div class="span12">
            <table class="table table-bordered table-condensed">
                <tbody>
                    <tr>
                        <td colspan="5" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;;"><strong>LIST OF STOPPED/WITHDRAWN STUDENTS</strong></td>
                    </tr>
                    <tr>
                        <td colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 3%;">S/N</td>
                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 10%;">ID NUMBER</td>
                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600;">STUDENTS</td>
                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 10%;">CLASS</td>
                        <td style="text-align: center; background-color: #F5F5F5; font-weight: 600; width: 10%;">ACTION</td>
                    </tr>
                    <?php
                    $i = 1;
                    while ($records = mysqli_fetch_assoc($getDeletedStudents)) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i++; ?></td>
                            <td style="text-align: center;"><?php echo $records["pupil_id"]; ?></td>
                            <td><?php echo $records["other_names"] . " " . $records["family_name"]; ?></td>
                            <td style="text-align: center;">
                                <?php
                                $getStudentById = $classMembership->getClassMembershipId($records["pupil_id"]);
                                $getPupilDetails = $pupil->getDeletedPupilById($records["pupil_id"]);
                                
                                echo $getStudentById["class_name"];
                                $getHash = $getPupilDetails["unique_url_string"];
                                ?>
                            </td>
                            <td><a href="restore.php?id=<?php echo urlencode($getHash); ?>" class="btn btn-block btn-primary" style="padding: 2px;" onclick="return confirm('Are you sure you want to RE-ADMIT this student ? If YES, click on OK, otherwise click on CANCEL')">Restore</a></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';

