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
    if (!in_array("books", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
}

//if (!isset($date_of_birth, $date_of_appointment)) {
//    $date_of_birth = "";
//    $date_of_appointment = "";
//}
?>

<div class="container">
    <?php
    require_once '../includes/bread_lib.php';

    $show_form = TRUE;
    if (isset($_POST["submit"])) {
        
         $pages = clean($_POST['pages']);  
        
            $quantity = clean($_POST['quantity']);
            
            if(!is_numeric($quantity)||!is_numeric($pages)){
                echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-error'>";
            echo "<ul type = 'none'>";
            echo "<li>All <strong>FIELDS</strong> are required! Note that, the <strong>Pages, Quantity</strong> accepts  numbers only.</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
            } 
            else {
                
                  if( $library ->populateBooks()){
                      
                      
                        echo "<div class='row'>";
            echo "<div class='span12'>";
            echo "<div class='alert alert-success'>";
            echo "<ul type = 'none'>";
            echo "<li>All <strong>FIELDS</strong> are required! Note that, the <strong>Pages, Quantity</strong> accepts  numbers only.</li>";
            echo "</ul>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
                  }
                
               
                
            }
 
     
        
            
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

    $id_number = generate_id_numbers();
    $institutionSet = $institutionDetail->getInstitutionDetails();
    $region_set = $region->getRegions();
    $getUsers = $user->getUsers();
    
    
    $getBooks = $library ->getBooks();
    
    $id = generate_id_numbers();
    
    $hash_id = md5($id);

    if (TRUE == $show_form) {
        ?>
        <div class="row">
            <div class="span12">
                <form class="form-horizontal" method="post">
                    <fieldset>
                        <legend style="color: #4F1ACB;"><strong>Enter Book Details</strong></legend>
                        <div class="spacer"></div>
                        <table class="table table-condensed table-margin-bottom">
                            <tr>
                                <td>
<!--                                    <input class="span2" type="hidden" name="school_number" value="<?php //echo $institutionSet["school_number"]; ?>">-->
                                    <input class="span2" width="" type="hidden" required="true" name="book_id" value="<?php echo $hash_id; ?>">
                                       
<!--                                    <div class="control-group">
                                        <label class="control-label" for="book_id">Book ID Number</label>
                                        <div class="controls">
                                          
                                        </div>
                                    </div>-->

                                    <div class="control-group">
                                        <label class="control-label" for="book_name">Book Name</label>
                                        <div class="controls">
                                            <input class="span3" required="true" type="text" name="book_name" autofocus autocomplete="off" 
                                                   value="<?php
                                                   if (isset($book_name)) {
                                                       echo $book_name;
                                                   }
                                                   ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="author">Author</label>
                                        <div class="controls">
                                            <input class="span3" type="text" required="true" name="author" autocomplete="off" value="<?php
                                            if (isset($author)) {
                                                echo $author;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="edition">Edition</label>
                                        <div class="controls">
                                            <input class="span3" required="true" type="text" name="edition" autocomplete="off" value="<?php
                                            if (isset($edition)) {
                                                echo $edition;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Total Number Of Pages">Total Number Of Pages</label>
                                        <div class="controls">
                                            <input class="span3" required="true" type="text" name="pages" autocomplete="off" />
                                        </div>
                                    </div>

                                   

                                   

                                 
                                </td>

                                <!--table second column-->
                                <td>
                                    
                           <div class="control-group">
                                        <label class="control-label" for="Book Color">Book Color</label>
                                        <div class="controls">
                                            <input class="span2" required="true" type="text" name="color"   autocomplete="off" value="<?php
                                            if (isset($color)) {
                                                echo $color;
                                            }
                                            ?>" />
                                        </div>
                                    </div>
                                   

                                    <div class="control-group">
                                        <label class="control-label" for="Quantity">Quantity</label>
                                        <div class="controls">
                                            <input class="span3" required="true" type="text" name="quantity" autocomplete="off" value="<?php
                                            if (isset($quantity)) {
                                                echo $quantity;
                                            }
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Subject Of Book">Subject Of Book</label>
                                        <div class="controls">
                                            <select name="subject" required="true">
                                             
                                                <option value="">--Select Book Subject---</option>
                                                
                                                <?php  $library ->getAllSubjects(); ?>
                                                
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Category">Category</label>
                                        <div class="controls">
                                            <label class="radio inline">
                                                <input type="radio" required="true" name="category" id="elective" value="Elective">
                                                <span class="metro-radio">Elective</span>
                                            </label>
                                            <label class="radio inline">
                                                <input type="radio" required="true" name="category" id="core" value="Core">
                                                <span class="metro-radio">Core</span>
                                            </label>
                                        </div>
                                    </div>

                                     
                                    
                                  
                                </td>
                            </tr>
                        </table>
                        <div class="controls" style="margin-left: 500px;">
                            <button type="submit" name="submit" class="btn">Save</button>
                            <a href="books.php" class="btn btn-danger">Clear</a>
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
                                    <td colspan="11" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIBRARY CATALOGUE</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="11">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="width: 3%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; ">NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600;">AUTHOR</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">CATEGORY</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 18%;">SUBJECT</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">EDITION</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">PAGES</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">COLOUR</td>
                                     <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">QUANTITY</td>
                                   
                                    <td colspan="2" style="background-color: #F5F5F5; font-weight: 600; width: 14%; text-align: center;">ACTION</td>
                                </tr>
                                <?php
                                $i = 1;

                                while ($users = mysqli_fetch_assoc($getBooks)) {
                                    ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <?php
                                            echo $i++;
                                            ?>
                                        </td>
                                        <td style="text-align: center;"><?php echo htmlentities($users["name_of_book"]); ?></td>
                                        <td><?php echo htmlentities( $users["author_of_book"]); ?></td>
                                      
                                      
                                        <td style="text-align: center;"><?php echo htmlentities($users["category_of_book"]); ?></td>
                                        <td style="text-align: left;"><?php echo htmlentities($users["subject_of_book"]); ?></td>
                                        <td style="text-align: center;"><?php echo htmlentities($users["edition_of_book"]); ?></td>
                                        <td style="text-align: center;"><?php echo htmlentities($users["total_pages_of_book"]); ?></td>
                                          <td><?php echo htmlentities($users["color_of_book"]); ?></td>
                                          <td><?php echo htmlentities($users["quantity"]); ?></td>
                                          <td><a href="edit_book.php?id=<?php echo urlencode($users["id_of_book"]); ?>" class="btn btn-primary btn-block" style="padding: 2px;">Edit</a></td>
                                          <td><a href="delete_book.php?id=<?php echo urlencode($users["id_of_book"]); ?>" class="btn btn-danger btn-block" style="padding: 2px;" onclick="return confirm('Are you sure you want to delete? If YES, click on OK, otherwise click on CANCEL')">Delete</a></td>
                                   
                                    
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


