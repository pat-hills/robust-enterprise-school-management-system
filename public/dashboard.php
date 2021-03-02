<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/FormMaster.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$getUserType = $getUserDetails["user_type"];

$name = $getUserDetails["other_names"] . " " . $getUserDetails["family_name"];
$formMaster = new FormMaster();
$getClassTeacher = $formMaster->getFormMasterByFullName($name);

if ($getUserType === "Head-Teacher") {
    redirect_to("head_teacher.php");
} elseif ($getClassTeacher["house_head"]) {
    redirect_to("class_teacher.php");
} elseif ($getUserType === "Teacher") {
    redirect_to("teacher.php");
} elseif ($getUserType === "Accountant") {
    redirect_to("accountant.php");
} elseif ($getUserType === "Account Clerk") {
    redirect_to("clerk.php");
}
?>

<body class="background-color">
    <div>
        <div id="nav-bar">
            <div class="pull-left">
                <div id="header-container">
                    <!--<h3><strong>iSkool</strong></h3>-->
                    <br />
                    <div class="dropdown">
                        <a class="header-dropdown dropdown-toggle accent-color" data-toggle="dropdown" href="#">
                            <p style="color: #fff; margin-bottom: 8px;">Shortcut Menu<b class="caret"></b></p>
                        </a>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <li><a href="class.php">Class</a></li>
                            <li><a href="house.php">House</a></li>
                            <li><a href="form_master.php">Class Teacher</a></li>
                            <li><a href="class_assignment.php">Class Assignment</a></li>
                            <li><a href="subject_combination.php">Subject Combination</a></li>
                            <li><a href="upload_logo.php">Upload School Logo</a></li>
                            <li><a href="upload_signature.php">Upload Head Signature</a></li>
                            <li><a href="upload_picture.php">Upload Students' Photos</a></li>
                            <li><a href="comment.php">Comment</a></li>
                            <li><a href="boarding_status.php">Export Students to Next Term</a></li>
                            <li class="divider"></li>
                            <li><a href="terminal_report_setting.php">Terminal Report Settings</a></li>
                            <li><a href="promotion.php">Promote/Repeat Students</a></li>
                            <li><a href="change_class.php">Change Student's Class</a></li>
                            <li><a href="change_status.php">Change Student's Status</a></li>
                            <li><a href="general_reports.php">Print Reports</a></li>
                            <li><a href="terminal_reports.php">Terminal Reports</a></li>
                            <li><a href="student_sms.php">Send SMS</a></li>
                            <li><a href="bulk_student_sms.php">Send Bulk SMS</a></li>
<!--                            <li><a href="http://localhost/phpMyBackupPro/" target="_blank">Backup Database</a></li>-->
                            <li class="divider"></li>
                            <li><a href="logout.php">Log out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="pull-right margin-right">
            <div id="top-info" class="pull-right">
                <!--                <a id="logged-user" href="index.php" class="win-command pull-right">
                                    <span class="win-commandicon win-commandring icon-user icon-cog-4"></span>
                                </a>-->
                <div class="pull-left">

                    <?php
                    $first_name = $getUserDetails["other_names"];
//                    $last_name = "Boateng";
                    ?>
                    <h3>Hello <?php echo htmlentities($first_name); ?></h3>
                    <h4><?php // echo "{$last_name}";                    ?></h4>

                </div>
            </div>
        </div>
    </div>
    <div class="metro panorama">
        <div class="panorama-sections">
            <div class="panorama-section tile-span-4">
                <h2>Featured apps 1</h2>
                <a class="tile app bg-color-pink" href="user.php">
                    <div class="image-wrapper">
                        <span class="icon icon-users"></span>
                    </div>
                    <div class="app-label">Staff</div>
                </a>

                <a class="tile app bg-color-purple" href="show_data.php">
                    <div class="image-wrapper">
                        <span class="icon icon-vcard"></span>
                    </div>
                    <div class="app-label">Find Student By ID Number</div>
                </a>

                <a class="tile app bg-color-blue" href="find_pupil_by_name.php">
                    <div class="image-wrapper">
                        <span class="icon icon-search-2"></span>
                    </div>
                    <div class="app-label">Find Student By Full Name</div>
                </a>

                <a class="tile wide imagetext bg-color-greenDark" href="pupil.php">
                    <div class="image-wrapper">
                        <span class="icon icon-user-add"></span>
                    </div>
                    <div class="column-text">
                        <div class="text4">Register Student</div>
                    </div>
                    <div class="app-label">Registration</div>
                </a>

                <a class="tile app bg-color-blueDark" href="edit_pupil.php">
                    <div class="image-wrapper">
                        <span class="icon icon-user-3"></span>
                    </div>
                    <div class="app-label">Edit Student's Data</div>
                </a>

                <a class="tile square image bg-color-yellow" href="academic_year.php">
                    <img src="./img/bkg-win8.jp" alt=""/>
                    <div class="textover-wrapper transparent">
                        <div class="text2">Academic Year</div>
                    </div>
                </a>

                <a class="tile square image" href="academic_term.php">
                    <img src="./img/bkg-.png" alt=""/>
                    <div class="textover-wrapper transparent">
                        <div class="text2">Academic Term</div>
                    </div>
                </a>

                <a class="tile app bg-color-red" href="delete_pupil.php">
                    <div class="image-wrapper">
                        <span class="icon icon- icon-delete"></span>
                    </div>
                    <div class="app-label">Delete Student Data</div>
                </a>
            </div>

            <div class="panorama-section tile-span-4">
                <h2>Featured apps 2</h2>
                <a class="tile wide imagetext bg-color-blue" href="show_wsd.php">
                    <div class="image-wrapper">
                        <span class="icon icon-music-3"></span>
                    </div>
                    <div class="column-text">
                        <div class="text">Deleted Students</div>
                        <div class="text">Stopped Students</div>
                        <div class="text">Withdrawn Students</div>
                    </div>
                    <span class="app-label">DELETED/STOPPED/WITHDRAWN STUDENTS</span>
                </a>

                <a class="tile app bg-color-grayLight" href="region.php">
                    <div class="image-wrapper">
                        <span class="icon icon-expand-2"></span>
                    </div>
                    <div class="app-label">REGION</div>
                </a>

                <a class="tile wide imagetext bg-color-blueDark" href="class_menu.php">
                    <div class="image-wrapper">
                        <span class="icon icon-chart"></span>
                    </div>
                    <div class="column-text">
                        <!--                        <div class="text">Typography</div>
                                                <div class="text">Tables</div>
                                                <div class="text">Forms</div>
                                                <div class="text">Buttons</div>-->
                    </div>
                    <span class="app-label">VIEW CLASS MEMBERS</span>
                </a>

                <a class="tile app bg-color-yellow" href="level.php">
                    <div class="image-wrapper">
                        <span class="icon icon-contract"></span>
                    </div>
                    <div class="app-label">LEVEL</div>
                </a>

                <a class="tile app bg-color-pink" href="student_sms.php">
                    <div class="image-wrapper">
                        <span class="icon icon-mail"></span>
                    </div>
                    <span class="app-label">SEND SMS</span>
                </a>

                <a class="tile app bg-color-orange" href="bulk_student_sms.php">
                    <div class="image-wrapper">
                        <span class="icon icon-mail-2"></span>
                    </div>
                    <div class="app-label">SEND BULK SMS</div>
                </a>
                
                <a class="tile app bg-color-greenDark" href="subject.php">
                    <div class="image-wrapper">
                        <span class="icon icon- icon-aperture"></span>
                    </div>
                    <div class="app-label">SUBJECT</div>
                </a>
            </div>

            <div class="panorama-section tile-span-4">
                <h2>Featured apps 3</h2>
                <a class="tile app bg-color-blue" href="class.php">
                    <div class="image-wrapper">
                        <span class="icon icon-sun"></span>
                    </div>
                    <span class="app-label">CLASS</span>
                </a>

                <a class="tile app bg-color-red" href="house.php">
                    <div class="image-wrapper">
                        <span class="icon icon-map-3"></span>
                    </div>
                    <div class="app-label">HOUSE</div>
                </a>

                <a class="tile app bg-color-blueDark" href="form_master.php">
                    <div class="image-wrapper">
                        <span class="icon icon-cog-4"></span>
                    </div>
                    <span class="app-label">CLASS TEACHER</span>
                </a>

                <a class="tile app bg-color-purple" href="class_assignment.php">
                    <div class="image-wrapper">
                        <span class="icon icon-article"></span>
                    </div>
                    <span class="app-label">CLASS ASSIGNMENT</span>
                </a>

                <a class="tile app bg-color-pink" href="terminal_report_setting.php">
                    <div class="image-wrapper">
                        <span class="icon icon-globe-2"></span>
                    </div>
                    <span class="app-label">TERMINAL REPORT SETTINGS</span>
                </a>

                <a class="tile app bg-color-greenDark" href="subject_combination.php">
                    <div class="image-wrapper">
                        <img src="img/RegEdit.png" alt="" />
                    </div>
                    <span class="app-label">SUBJECT COMBINATION</span>
                </a>

                <a class="tile app bg-color-green" href="upload_picture.php">
                    <div class="image-wrapper">
                        <span class="icon icon-instagram"></span>
                    </div>
                    <span class="app-label">UPLOAD PHOTOS</span>
                </a>

                <a class="tile wide imagetext bg-color-blueDark" href="comment.php">
                    <div class="image-wrapper">
                        <span class="icon icon-database-2"></span>
                    </div>
                    <div class="column-text">
                        <div class="text">Conducts</div>
                        <div class="text">Interests</div>
                        <div class="text">Attitudes</div>
                        <div class="text">Remarks</div>
                    </div>
                    <span class="app-label">ENTER COMMENTS AND TYPES</span>
                </a>
            </div>

            <div class="panorama-section tile-span-4">
                <h2>Featured apps 4</h2>
                <a class="tile wide imagetext bg-color-purple" href="boarding_status.php">
                    <div class="image-wrapper">
                        <span class="icon icon-atom"></span>
                    </div>
                    <!--                    <div class="column-text">
                                            <div class="text">Conducts</div>
                                            <div class="text">Interests</div>
                                            <div class="text">Attitudes</div>
                                            <div class="text">Remarks</div>
                                        </div>-->
                    <span class="app-label">EXPORT STUDENTS TO NEXT TERM</span>
                </a>

                <a class="tile app bg-color-gray" href="upload_logo.php">
                    <div class="image-wrapper">
                        <span class="icon icon-instagram"></span>
                    </div>
                    <span class="app-label">UPLOAD LOGO</span>
                </a>

                <a class="tile app bg-color-blue" href="change_class.php">
                    <div class="image-wrapper">
                        <span class="icon icon-target-6"></span>
                    </div>
                    <span class="app-label">CHANGE CLASS</span>
                </a>

                <a class="tile app bg-color-grayLight" href="change_status.php">
                    <div class="image-wrapper">
                        <span class="icon icon-new-window"></span>
                    </div>
                    <div class="app-label">CHANGE STATUS</div>
                </a>

                <a class="tile app bg-color-blueDark" href="general_reports.php">
                    <div class="image-wrapper">
                        <span class="icon icon-printer"></span>
                    </div>
                    <span class="app-label">PRINT REPORTS</span>
                </a>

                <a class="tile app bg-color-greenDark" href="promotion.php">
                    <div class="image-wrapper">
                        <span class="icon icon-thumbs-up-2"></span>
                    </div>
                    <span class="app-label">PROMOTE/REPEAT STUDENTS</span>
                </a>

                <a class="tile app bg-color-orange" href="terminal_reports.php">
                    <div class="image-wrapper">
                        <span class="icon icon-print"></span>
                    </div>
                    <span class="app-label">TERMINAL/CONTINUOUS ASSESSMENT REPORTS</span>
                </a>

                <a class="tile app bg-color-pink" href="edit_institution_details.php">
                    <div class="image-wrapper">
                        <span class="icon icon-wrench"></span>
                    </div>
                    <div class="app-label">EDIT SCHOOL DATA</div>
                </a>
                
                
                 
            </div>
        </div>
    </div>
    <a id="panorama-scroll-prev" href="#"></a>
    <a id="panorama-scroll-next" href="#"></a>
    <div id="panorama-scroll-prev-bkg"></div>
    <div id="panorama-scroll-next-bkg"></div>

    <!--END OF HOMEPAGE-->
    <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
    <script type="text/javascript" src="../public/js/jquery-1.10.0.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.chained.min.js"></script>

    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/bootmetro-panorama.js"></script>
    <script type="text/javascript" src="../public/js/bootmetro-pivot.js"></script>
    <script type="text/javascript" src="../public/js/bootmetro-charms.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap-datepicker.js"></script>

    <script type="text/javascript" src="../public/js/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.touchSwipe.min.js"></script>

    <script type="text/javascript" src="../public/js/custom.js"></script>
    <script type="text/javascript" src="../public/js/holder.js"></script>
    <script type="text/javascript" src="../public/js/perfect-scrollbar.with-mousewheel.min.js"></script>

    <script type="text/javascript">
        $('.panorama').panorama({
            //nicescroll: false,
            showscrollbuttons: true,
            keyboard: true,
            parallax: true
        });

        //      $(".panorama").perfectScrollbar();

        $('#pivot').pivot();
    </script>

</body>
</html>

<?php
if (isset($connection)) {
    mysqli_close($connection);
}


    