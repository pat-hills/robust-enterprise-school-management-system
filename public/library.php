<?php
require_once '../includes/header.php';
require_once '../classes/User.php';
require_once '../classes/FormMaster.php';


confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("library", $splitAccessPages, TRUE)) {
        redirect_to("logout_access.php");
    }
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
                   // $first_name = $getUserDetails["other_names"];
//                    $last_name = "Boateng";
                    ?>
                    <h3>Hello <?php// echo htmlentities($first_name); ?></h3>
                    <h4><?php // echo "{$last_name}";                    ?></h4>

                </div>
            </div>
        </div>
    </div>
    <div class="metro panorama">
        <div class="panorama-sections">
            <div class="panorama-section tile-span-4">
                <h2>Featured apps 1</h2>
                <a class="tile app bg-color-pink" href="books.php">
                    <div class="image-wrapper">
                        <span class="icon icon-archive"></span>
                    </div>
                    <div class="app-label">Library Catalogue</div>
                </a>

                <a class="tile app bg-color-purple" href="search_student_to_library_tray.php">
                    <div class="image-wrapper">
                        <span class="icon icon-vcard"></span>
                    </div>
                    <div class="app-label">Library Tray</div>
                </a>

                <a class="tile app bg-color-blue" href="library_records.php">
                    <div class="image-wrapper">
                        <span class="icon icon-search-2"></span>
                    </div>
                    <div class="app-label">LIBRARY RECORDS</div>
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


    