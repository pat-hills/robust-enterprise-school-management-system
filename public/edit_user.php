<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/Region.php';

confirm_logged_in();

$user = new User();
$region = new Region();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("edit_user", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

if (getURL()) {
    $url_hash = trim(escape_value(urldecode(getURL())));
    $userSet = $user->getUserHash($url_hash);
} else {
    redirect_to("user.php");
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
        $validator->addValidation("username", "req", "Please, fill in the Username.");
        $validator->addValidation("password", "req", "Please, fill in the Password.");
        $validator->addValidation("date_of_birth", "req", "Please, fill in the Date of Birth.");
        $validator->addValidation("sex", "req", "Please, fill in the Sex.");
//        $validator->addValidation("contact_number", "req", "Please, fill in the Contact number.");
        $validator->addValidation("date_of_appointment", "req", "Please, fill in the Date of Appointment.");
        $validator->addValidation("hometown", "req", "Please, fill in the Hometown.");
        $validator->addValidation("region", "dontselect=Select Region", "Please, select Region.");
        $validator->addValidation("user_type", "dontselect=User Type", "Please, select User Type.");
        $validator->addValidation("email", "email", "Please, fill in a valid Email address.");
        $validator->addValidation("qualification", "req", "Please, fill in the qualification.");
        $validator->addValidation("post", "req", "Please, select Post.");

        $user_id = trim(ucwords(escape_value($_POST["user_id"])));

        if ($validator->ValidateForm()) {
            $user->updateUser($user_id);
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

    $regionSet = $region->getRegions();
    $getUsers = $user->getUsers();

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Edit Staff Details</strong></legend>
                        <div class="spacer"></div>
                        <table class="table table-condensed table-margin-bottom">
                            <tr>
                                <td>
                                    <div class="control-group">
                                        <label class="control-label" for="user_id">Staff ID Number</label>
                                        <div class="controls">
                                            <input class="span2" type="text" name="user_id" value="<?php echo $userSet["user_id"]; ?>" disabled>
                                            <input type="hidden" name="user_id" value="<?php echo $userSet["user_id"]; ?>">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="family_name">Family Name</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="family_name" value="<?php echo $userSet["family_name"]; ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="other_names">Other Names</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="other_names" value="<?php echo $userSet["other_names"]; ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="username">Username</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="username" autocomplete="off" value="<?php echo $userSet["username"]; ?>" />
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
                                                <input type="text" name="date_of_birth" value="<?php echo htmlentities(date("d-m-Y", strtotime($userSet["date_of_birth"]))); ?>" autocomplete="off">
                                                <span class="add-on"><i class="icon-calendar-5"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="sex">Sex</label>
                                        <div class="controls">
                                            <label class="radio inline">
                                                <input type="radio" name="sex" value="Male"
                                                <?php
                                                if ($userSet["sex"] == "Male") {
                                                    echo "checked";
                                                }
                                                ?>>
                                                <span class="metro-radio">Male</span>
                                            </label>
                                            <label class="radio inline">
                                                <input type="radio" name="sex" value="Female"
                                                <?php
                                                if ($userSet["sex"] == "Female") {
                                                    echo "checked";
                                                }
                                                ?>>
                                                <span class="metro-radio">Female</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="contact_number">Contact Number</label>
                                        <div class="controls">
                                            <input class="span2" type="text" name="contact_number" pattern="^(?:\(\d{3}\)|\d{3})[- ]?\d{3}[- ]?\d{4}$" value="<?php echo $userSet["contact_number"]; ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </td>

                                <!--table second column-->
                                <td>
                                    <div class="control-group">
                                        <label class="control-label" for="date_of_appointment">Date of Appointment</label>
                                        <div class="controls">
                                            <div data-date-format="dd-mm-yyyy" class="input-append date" data-provide="datepicker">
                                                <input type="text" name="date_of_appointment" value="<?php echo htmlentities(date("d-m-Y", strtotime($userSet["date_of_appointment"]))); ?>" autocomplete="off">
                                                <span class="add-on"><i class="icon-calendar-5"></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="hometown">Hometown</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="hometown" value="<?php echo $userSet["hometown"]; ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="region">Region</label>
                                        <div class="controls">
                                            <select name="region">
                                                <option value="<?php echo $userSet["region"]; ?>"><?php echo $userSet["region"]; ?></option>
                                                <?php
                                                while ($region = mysqli_fetch_assoc($regionSet)) {
                                                    ?>
                                                    <option value="<?php echo ucwords($region["name"]); ?>"><?php echo ucwords($region["name"]); ?></option>
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
                                                <option value="<?php echo $userSet["user_type"]; ?>"><?php echo $userSet["user_type"]; ?></option>
                                                <option value="administrator">Administrator</option>
                                                <option value="Head-Teacher">Head-teacher</option>
                                                <option value="teacher">Teacher</option>
                                                <option value="accountant">Accountant</option>
                                                <option value="account clerk">Account Clerk</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="email">E-mail</label>
                                        <div class="controls">
                                            <input class="span3" type="text" name="email" value="<?php echo $userSet["email"]; ?>" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="qualification">Qualification</label>
                                        <div class="controls">
                                            <input class="span5" type="text" name="qualification" value="<?php echo $userSet["qualification"]; ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label" for="post">At Post</label>
                                        <div class="controls">
                                            <label class="radio inline">
                                                <input type="radio" name="post" value="YES"
                                                <?php
                                                if ($userSet["post"] == "YES") {
                                                    echo "checked";
                                                }
                                                ?>>
                                                <span class="metro-radio">Yes</span>
                                            </label>
                                            <label class="radio inline">
                                                <input type="radio" name="post" value="NO"
                                                <?php
                                                if ($userSet["post"] == "NO") {
                                                    echo "checked";
                                                }
                                                ?>>
                                                <span class="metro-radio">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!--table second row-->
                            <tr>
                                <td colspan="2" class="pull-right">
                                    <div class="spacer"></div>
                                    <div class="control-group">
                                        <div class="controls">
                                            <button type="submit" name="submit" class="btn">Save changes</button>
                                            <a href="user.php" class="btn btn-danger">Cancel</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <div class="spacer"></div>

                <div class="row">
                    <div class="span12">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td colspan="11" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>STAFF MEMBERS</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="11">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="width: 3%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 8%;">STAFF ID</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600;">NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 6%;">SEX</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">CATEGORY</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">CONTACT</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 10%;">HOMETOWN</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 14%;">APPOINTMENT DATE</td>
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
                                        <td style="text-align: center;"><?php echo htmlentities($users["hometown"]); ?></td>
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


