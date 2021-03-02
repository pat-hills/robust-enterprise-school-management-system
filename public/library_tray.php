<?php
require_once '../includes/header.php';
require_once '../classes/Region.php';
require_once '../classes/User.php';
require_once '../classes/InstitutionDetail.php';
require_once '../classes/Library.php';

//ob_start();

//confirm_logged_in();

$library = new Library();

//$lib_new_code = $library ->generate_user_id();
$institutionDetail = new InstitutionDetail();
$region = new Region();
$user = new User();

//$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
//$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

//for ($i = 0; $i < count($splitAccessPages); $i++) {
//    if (!in_array("user", $splitAccessPages, TRUE)) {
//        redirect_to("logout_access.php");
//    }
//}

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
                        <legend style="color: #4F1ACB;"><strong>Search Book Details</strong></legend>
                        <div class="spacer"></div>
                        <table class="table table-condensed table-margin-bottom">
                            <tr>
                                <td>
                               
                                    <div style="float: left;">
                                    
                                    
<!--                                    <input class="span2" type="hidden" name="school_number" value="<?php //echo $institutionSet["school_number"]; ?>">-->
                                        <input id="search" type="text" style="" placeholder="SEARCH BOOK"/>
         
            <div id="results">    </div>
            
            <script type="text/javascript" src="js/jquery.js"></script>
            <script type="text/javascript" src="js/search.js"></script>
            
                                    </div>  
                                    
                                    
                                    
                                       
                                    
                                    
                                    
                                    
                                </td>  
                                
                            <input class="span3" required="true" type="hidden" name="id_of_book" autofocus autocomplete="off" 
                                                   value="<?php
                                                    echo  $id_of_book;
                                                   ?>" />
<!--                                    <div class="control-group">
                                        <label class="control-label" for="book_id">Book ID Number</label>
                                        <div class="controls">
                                          
                                        </div>
                                    </div>-->

<td>

                                    <div class="control-group">
                                        <label class="control-label" for="book_name">Book Name</label>
                                        <div class="controls">
                                            <input class="span3" required="true"  type="text" readonly name="book_name" autofocus autocomplete="off" 
                                                   value="<?php
                                                    echo  $name_of_books;
                                                   ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="author">Author</label>
                                        <div class="controls">
                                            <input class="span3" type="text" readonly required="true" name="author" autocomplete="off" value="<?php
                                             echo  $name_of_author
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="edition">Edition</label>
                                        <div class="controls">
                                            <input class="span3" readonly required="true" type="text" name="edition" autocomplete="off" value="<?php
                                            echo $edition_of_book;
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Total Number Of Pages">Quantity</label>
                                        <div class="controls">
                                            <input class="span3" readonly required="true" value="<?php echo $quantity ?>" type="text" name="pages" autocomplete="off" />
                                        </div>
                                    </div>

                                   
                                <div class="control-group">
                                        <label class="control-label" for="book_name">Times Collected</label>
                                        <div class="controls">
                                            <input class="span3" readonly required="true" type="text" name="collected" autofocus autocomplete="off" 
                                                   value="<?php
                                                    echo  $col;
                                                   ?>" />
                                        </div>
                                    </div>
                                   

                                 
                                </td>

                                <!--table second column-->
                                <td>
                                    
                           <div class="control-group">
                                        <label class="control-label" for="Book Color">Book Color</label>
                                        <div class="controls">
                                            <input class="span2" readonly required="true" type="text" name="color"   autocomplete="off" value="<?php
                                          
                                                echo $color;
                                       
                                            ?>" />
                                        </div>
                                    </div>
                                   

                                    <div class="control-group">
                                        <label class="control-label" for="Quantity">Total Pages</label>
                                        <div class="controls">
                                            <input class="span3" readonly required="true" type="text" name="quantity" autocomplete="off" value="<?php
                                           
                                                echo $pages_book;
                                       
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Subject Of Book">Subject Of Book</label>
                                        <div class="controls">
                                            <input class="span3" readonly required="true" type="text" name="quantity" autocomplete="off" value="<?php
                                           
                                                echo $sub;
                                       
                                            ?>" />
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="Category">Category</label>
                                        <div class="controls">
                                            <input class="span3" readonly required="true" type="text" name="quantity" autocomplete="off" value="<?php
                                           
                                                echo $cat;
                                       
                                            ?>" />
                                        </div>
                                    </div>
                                    
                                     
                                        <div class="controls">
                                            <input class="span3" readonly required="true" type="hidden" name="quantity_collected" autocomplete="off" value="1" />
                                        </div>
                                 

                                     
                                    
                                  
                                </td>
                                
                                
                                <td></td>
                            </tr>
                            
                            
                            
                        </table>
                        <div class="controls" style="margin-left: 500px;">
                            <button type="submit" name="submit" class="btn">Save</button>
                            <a href="library_tray.php" class="btn btn-danger">Clear</a>
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
                                    <td colspan="11" style="text-align: center; background-color: #F5F5F5; font-weight: 600; font-size: 20px; padding: 10px;"><strong>LIBRARY'S TRAY</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="11">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="width: 3%; text-align: center; font-weight: 600; background-color: #F5F5F5;">S/N</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; ">NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600;">AUTHOR</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">STUDENT ID</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 18%;">STUDENT NAME</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">STUDENT CLASS</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 9%;">DATE</td>
                                    <td style="background-color: #F5F5F5; text-align: center; font-weight: 600; width: 5%;">TIME</td>
                                     
                                   
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
                                        <td style="text-align: center;"><?php echo htmlentities($userss["date_collected"]); ?></td>
                                          <td><?php echo htmlentities($userss["time_collected"]); ?></td>
                                          
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


