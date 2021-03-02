<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/User.php';
require_once '../classes/InstitutionDetail.php';

ob_start();

confirm_logged_in();

$institutionDetail = new InstitutionDetail();
$region = new Region();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("user", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (!isset($date_of_birth, $date_of_appointment)) {
    $date_of_birth = "";
    $date_of_appointment = "";
}
?>

<div class="container">
    <?php
    require_once '../includes/breadcrumb_with_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        $validator = new FormValidator();
        $validator->addValidation("user_id", "req", "Please, fill in the Staff ID.");
        $validator->addValidation("family_name", "req", "Please, fill in the Family name.");
        $validator->addValidation("other_names", "req", "Please, fill in the Other names.");
        $validator->addValidation("username", "req", "Please, fill in the Other names.");
        $validator->addValidation("password", "req", "Please, fill in the Other names.");
        $validator->addValidation("password", "alnum", "Please, enter a combination of alphabets and numbers only in the password field.");
        $validator->addValidation("date_of_birth", "req", "Please, fill in the Date of Birth.");
        $validator->addValidation("sex", "req", "Please, fill in the Sex.");
//        $validator->addValidation("contact_number", "req", "Please, fill in the Contact number.");
        $validator->addValidation("date_of_appointment", "req", "Please, fill in the Date of Appointment.");
        $validator->addValidation("hometown", "req", "Please, fill in the Hometown.");
        $validator->addValidation("region", "dontselect=--Select Region--", "Please, select Region.");
        $validator->addValidation("user_type", "dontselect=--Select User Type--", "Please, select User Type.");
        $validator->addValidation("qualification", "req", "Please, fill in the qualification.");
        $validator->addValidation("post", "req", "Please, select Post.");

        $family_name = trim(ucwords(escape_value($_POST["family_name"])));
        $other_names = trim(ucwords(escape_value($_POST["other_names"])));
        $contact_number = trim(escape_value($_POST["contact_number"]));
        $hometown = trim(ucwords(escape_value($_POST["hometown"])));
        $username = trim(strtolower(escape_value($_POST["username"])));
        $region_selected = trim(ucwords(escape_value($_POST["region"])));
        $email = trim(strtolower(escape_value($_POST["email"])));
        $qualification = trim(ucwords(escape_value($_POST["qualification"])));
        $user_type = trim(ucwords(escape_value($_POST["user_type"])));
        $date_of_birth = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_birth"]))));
        $date_of_appointment = trim(escape_value(date("Y-m-d", strtotime($_POST["date_of_appointment"]))));

        if ($validator->ValidateForm()) {
            $user->insertUser();
        } else {
            echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type = 'none'>";
            echo "<li>All <strong>FIELDS</strong> are required! Note that, the <strong>PASSWORD</strong> field accepts alphabets and numbers only.</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
//            $get_errors = $validator->GetErrors();
//
//            foreach ($get_errors as $input_field_name => $error_msg) {
//                echo "<div class='row'>";
//                echo "<div class='span12'>";
//                echo "<div class='alert alert-error'>";
//                echo "<ul type = 'none'>";
//                echo "<li>$error_msg</li>";
//                echo "</ul>";
//                echo "</div>";
//                echo "</div>";
//                echo "</div>";
//            }
        }
    }

    $id_number = generate_id_numbers();
    $institutionSet = $institutionDetail->getInstitutionDetails();
    $region_set = $region->getRegions();
    $getUsers = $user->getUsers();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Staff Details</strong></legend>
                        <div class="spacer"></div>
                        <table class="table table-condensed table-margin-bottom">
                            <tr>
                                <td>
                                    <input class="span2" type="hidden" name="school_number" value="<?php echo $institutionSet["school_number"]; ?>">

                                    <div class="control-group">
                                        <label class="control-label" for="user_id">Staff ID Number</label>
                                        <div class="controls">
                                            <input class="span2" type="text" name="user_id" value="<?php echo $id_number; ?>">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="family_name">Family Name</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="family_name" autofocus autocomplete="off" 
                                                   value="<?php
                                                   if (isset($family_name)) {
                                                       echo $family_name;
                                                   }
                                                   ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="other_names">Other Names</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="other_names" autocomplete="off" value="<?php
                                            if (isset($other_names)) {
                                                echo $other_names;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="username">Username</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="username" autocomplete="off" value="<?php
                                            if (isset($username)) {
                                                echo $username;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="password">Password</label>
                                        <div class="controls">
                                            <input class="span3" type="password" name="password" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="date_of_birth">Date of Birth</label>
                                        <div class="controls">
                                            <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                                <input type="text" name="date_of_birth" autocomplete="off" value="<?php
                                                if ($date_of_birth !== "1970-01-01" && $date_of_birth === "01-01-1970") {
                                                    echo date("d-m-Y", strtotime($date_of_birth));
                                                }
                                                ?>">
                                                <span class="add-on"><i class="icon-calendar-5"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="sex">Sex</label>
                                        <div class="controls">
                                            <label class="radio inline">
                                                <input type="radio" name="sex" id="male" value="Male">
                                                <span class="metro-radio">Male</span>
                                            </label>
                                            <label class="radio inline">
                                                <input type="radio" name="sex" id="female" value="Female">
                                                <span class="metro-radio">Female</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="contact_number">Contact Number</label>
                                        <div class="controls">
                                            <input class="span2" type="text" name="contact_number" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" autocomplete="off" value="<?php
                                            if (isset($contact_number)) {
                                                echo $contact_number;
                                            }
                                            ?>" />
                                        </div>
                                    </div>
                                </td>

                                <!--table second column-->
                                <td>
                                    <div class="control-group">
                                        <label class="control-label" for="date_of_appointment">Date of Appointment</label>
                                        <div class="controls">
                                            <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                                <input type="text" name="date_of_appointment" autocomplete="off" value="<?php
                                                if ($date_of_appointment !== "1970-01-01" && $date_of_appointment === "01-01-1970") {
                                                    echo date("d-m-Y", strtotime($date_of_appointment));
                                                }
                                                ?>">
                                                <span class="add-on"><i class="icon-calendar-5"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="hometown">Hometown</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="hometown" autocomplete="off" value="<?php
                                            if (isset($hometown)) {
                                                echo $hometown;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="region">Region</label>
                                        <div class="controls">
                                            <select name="region">
                                                <option value="<?php
                                                if (isset($region_selected)) {
                                                    echo $region_selected;
                                                } else {
                                                    echo "--Select Region--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($region_selected)) {
                                                                echo $region_selected;
                                                            } else {
                                                                echo "--Select Region--";
                                                            }
                                                            ?>
                                                </option>
                                                <?php
                                                while ($regionRow = mysqli_fetch_assoc($region_set)) {
                                                    ?>
                                                    <option value="<?php echo ucwords($regionRow["name"]); ?>"><?php echo ucwords($regionRow["name"]); ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="user_type">User Category</label>
                                        <div class="controls">
                                            <select name="user_type">
                                                <option value="<?php
                                                if (isset($user_type)) {
                                                    echo $user_type;
                                                } else {
                                                    echo "--Select User Type--";
                                                }
                                                ?>">
                                                            <?php
                                                            if (isset($user_type)) {
                                                                echo $user_type;
                                                            } else {
                                                                echo "--Select User Type--";
                                                            }
                                                            ?>
                                                </option>
                                                <option value="administrator">Administrator</option>
                                                <!--<option value="Head-Teacher">Head-teacher</option>-->
                                                <option value="teacher">Teacher</option>
                                                <option value="accountant">Accountant</option>
                                                <option value="account clerk">Account Clerk</option>
                                                 <option value="librarian">Librarian</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="email">E-mail</label>
                                        <div class="controls">
                                            <input class="span4" type="text" name="email" autocomplete="off" value="<?php
                                            if (isset($email)) {
                                                echo $email;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="qualification">Qualification</label>
                                        <div class="controls">
                                            <input class="span5" type="text" name="qualification" autocomplete="off" value="<?php
                                            if (isset($qualification)) {
                                                echo $qualification;
                                            }
                                            ?>" />
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label" for="post">At Post</label>
                                        <div class="controls">
                                            <label class="radio inline">
                                                <input type="radio" name="post" id="male" value="yes">
                                                <span class="metro-radio">Yes</span>
                                            </label>
                                            <label class="radio inline">
                                                <input type="radio" name="post" id="female" value="no">
                                                <span class="metro-radio">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class="controls" style="margin-left: 500px;">
                            <button type="submit" name="submit" class="btn">Save</button>
                            <a href="user.php" class="btn btn-danger">Clear</a>
                        </div>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <!--<div class="spacer"></div>-->

                <div class="row">
                    <div class="span12" style="padding-top: 10px;">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td colspan="11" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>STAFF</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="11">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="width: 3%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 7%;">STAFF ID</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600;">NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">SEX</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 8%;">CATEGORY</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">MOBILE No.</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 19%;">QUALIFICATION</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">DATE</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 6%;">AT POST</td>
                                    <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 14%; text-align: center;">ACTION</td>
                                </tr>
                                <?php
                                $i = 1;

                                while ($users = mysqli_fetch_assoc($getUsers)) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php
                                            echo $i++;
                                            ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo htmlentities($users["user_id"]); ?></td>
                                        <td><?php echo htmlentities($users["other_names"] . " " . $users["family_name"]); ?></td>
                                        <td><?php echo htmlentities($users["sex"]); ?></td>
                                        <td><?php echo htmlentities($users["user_type"]); ?></td>
                                        <td style="text-align: center;"><?php echo htmlentities($users["contact_number"]); ?></td>
                                        <td style="text-align: left;"><?php echo htmlentities($users["qualification"]); ?></td>
                                        <td style="text-align: center;"><?php echo htmlentities(date("d-m-Y", strtotime($users["date_of_appointment"]))); ?></td>
                                        <td style="text-align: center;"><?php echo htmlentities($users["post"]); ?></td>
                                        <td><a href="edit_user.php?id=<?php echo urlencode($users["user_hash"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                        <td><a href="delete_user.php?id=<?php echo urlencode($users["user_hash"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
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
require_once '../includes/footer.php';


