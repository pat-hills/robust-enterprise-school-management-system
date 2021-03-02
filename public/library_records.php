<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/User.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Library.php';

ob_start();

confirm_logged_in();

$library = new Library();

//$lib_new_code = $library ->generate_user_id();
$institutionDetail = new InstitutionDetail();
$region = new Region();
$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("library_records", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

    $name_of_books = "";
    $name_of_author = "";
    $edition_of_book = "";
    $pages_book = "";
    $color = "";
    $quantity = "";
    $sub = "";
    $cat = "";
    $col = "";
    $id_of_book ="";
if(getURL()){
    
    
    
       $id = trim(ucwords(escape_value(urldecode(getURL()))));
       
       $load_books = $library ->getBookHash($id);
       
       
       $id_of_book = $load_books['id_of_book'];
       $name_of_books = $load_books['name_of_book'];
        $name_of_author = $load_books['author_of_book'];
        $edition_of_book = $load_books['edition_of_book'];
        $pages_book = $load_books['total_pages_of_book'];
        $color = $load_books['color_of_book'];
        $quantity = $load_books['quantity'];
        $sub = $load_books['subject_of_book'];
        $cat = $load_books['category_of_book'];
        $col = $load_books['no_of_times_collected'];
}

?>

<div class="container">
    <?php
require_once '../includes/breadcrumb_with_library_home.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        
       $library->populate_tray();
       
 
     
        

        
    }

    $id_number = generate_id_numbers();
    $institutionSet = $institutionDetail->getInstitutionDetails();
    $region_set = $region->getRegions();
    $getUsers = $user->getUsers();
    
    
    $getBooks = $library ->getBooks();
    
    $get_tray_status = $library->getTrayRecordsforToday();
    
    $id = generate_id_numbers();
    
    $hash_id = md5($id);

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Library Tray Records</strong></legend>
                        <div class="spacer"></div>
                       
                       
                    </fieldset>
                    <legend class="legend"></legend>
                </form>

                <!--<div class="spacer"></div>-->

                <div class="row">
                    <div class="span12" style="padding-top: 10px;">
                        <table class="table table-bordered table-condensed">
                            <tbody>
                                <tr>
                                    <td colspan="11" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIBRARY'S TRAY RECORDS</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="11">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="width: 3%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; ">NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 18%;">AUTHOR</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">STUDENT ID</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; ">STUDENT NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">STUDENT CLASS</td>
                                    
                                       <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">ACTION</td>
                                   
                                </tr>
                                <?php
                                $i = 1;

                                while ($userss = mysqli_fetch_assoc($get_tray_status)) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php
                                            echo $i++;
                                            ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo htmlentities($userss["name_of_book"]); ?></td>
                                        <td><?php echo htmlentities( $userss["author_of_book"]); ?></td>
                                      
                                      
                                        <td style="text-align: center;"><?php echo htmlentities($userss["id_of_student"]); ?></td>
                                        <td style="text-align: left;"><?php echo htmlentities($userss["name_of_student"]); ?></td>
                                        <td style="text-align: center;"><?php echo htmlentities($userss["class_of_student"]); ?></td>
                                   
                                          <td><a href="clear_student_from_tray_records.php?id=<?php echo urlencode($userss["id_of_student"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to clear student? If YES, click on OK, otherwise click on CANCEL')">Clear</a></td>
                                       
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


