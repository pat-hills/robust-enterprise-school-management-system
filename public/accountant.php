<?php
require_once '../includes/header.php';
require_once '../classes/User.php';

confirm_logged_in();

$user = new User();

$getUserDetails = $user->getUserByUsername($_SESSION["username"]);
$splitAccessPages = explode("//", $getUserDetails["access_pages"]);

for ($i = 0; $i < count($splitAccessPages); $i++) {
    if (!in_array("accountant", $splitAccessPages, TRUE)) {
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
                            <li><a href="ledger.php">Student Ledger</a></li>
                            <li><a href="bill_item.php">Bill Items</a></li>
                            <li><a href="student_bill.php">Class Bill</a></li>
                            <li><a href="single_student_billing.php">Single Student Billing</a></li>
                            <li><a href="debtors_creditors.php">Debtors/Creditors List</a></li>
                            <li><a href="billing.php">Apply Bill to Ledgers</a></li>
                            <li><a href="fees_collected.php">Fees Collected Reports</a></li>
                            <li><a href="print_fees_collected_today.php" target="_blank">Print Fees Collected Today</a></li>
                            <li class="divider"></li>
                            <li><a href="student_charges.php">Student Charges</a></li>
                            <li><a href="canteen.php">Canteen Fees Entries</a></li>
                            <li><a href="study_fees.php">Study Fees Entries</a></li>
                            <li><a href="other_incomes.php">Income Entries</a></li>
                            <li><a href="expenses.php">Expenditure Entries</a></li>
                            <li><a href="edit_other_incomes.php">Edit Income Entries</a></li>
                            <li><a href="edit_expenses.php">Edit Expenditure Entries</a></li>
                            <li><a href="sms_class_bills.php">Send Bills to Parents/Guardians</a></li>
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
                    <h4><?php // echo "{$last_name}";           ?></h4>

                </div>
            </div>
        </div>
    </div>

    <div class="metro panorama">
        <div class="panorama-sections">
            <div class="panorama-section tile-span-4">
                <h2>featured apps 1</h2>
                <a class="tile app bg-color-green" href="ledger.php">
                    <div class="image-wrapper">
                        <span class="icon icon-drawer-3"></span>
                    </div>
                    <span class="app-label">STUDENT LEDGER</span>
                </a>

                <a class="tile app bg-color-blue" href="bill_item.php">
                    <div class="image-wrapper">
                        <span class="icon icon-drawer-4"></span>
                    </div>
                    <span class="app-label">BILL ITEMS</span>
                </a>

                <a class="tile app bg-color-pink" href="student_bill.php">
                    <div class="image-wrapper">
                        <span class="icon icon-archive"></span>
                    </div>
                    <span class="app-label">CLASS BILL</span>
                </a>

                <a class="tile app bg-color-blueDark" href="single_student_billing.php">
                    <div class="image-wrapper">
                        <span class="icon icon-globe-2"></span>
                    </div>
                    <div class="app-label">SINGLE STUDENT BILLING</div>
                </a>

                <a class="tile wide imagetext wideimage bg-color-green" href="debtors_creditors.php">
                    <img src="./img/bkg-win8.jpg" alt=""/>
                    <div class="textover-wrapper transparent">
                        <div class="text2">DEBTORS/CREDITORS LIST</div>
                    </div>
                </a>

                <a class="tile app bg-color-orange" href="billing.php">
                    <div class="image-wrapper">
                        <span class="icon icon-windows"></span>
                    </div>
                    <div class="app-label">APPLY BILL TO LEDGERS</div>
                </a>

                <a class="tile app bg-color-gray" href="fees_collected.php">
                    <div class="image-wrapper">
                        <span class="icon icon-grid"></span>
                    </div>
                    <div class="app-label">FEES COLLECTED REPORTS</div>
                </a>

                <a class="tile app bg-color-darken" href="print_fees_collected_today.php" target="_blank">
                    <div class="image-wrapper">
                        <span class="icon icon-print"></span>
                    </div>
                    <div class="app-label">PRINT FEES COLLECTED TODAY</div>
                </a>
            </div>

            <div class="panorama-section tile-span-4">
                <h2>Featured apps 2</h2>
                <a class="tile app bg-color-yellow" href="student_charges.php">
                    <div class="image-wrapper">
                        <span class="icon icon-list"></span>
                    </div>
                    <span class="app-label">STUDENT CHARGES</span>
                </a>

                <a class="tile app bg-color-blue" href="canteen.php">
                    <div class="image-wrapper">
                        <span class="icon icon-directions"></span>
                    </div>
                    <span class="app-label">CANTEEN FEES</span>
                </a>

                <a class="tile app bg-color-pink" href="study_fees.php">
                    <div class="image-wrapper">
                        <span class="icon icon-renren"></span>
                    </div>
                    <span class="app-label">STUDY FEES</span>
                </a>

                <a class="tile wide imagetext wideimage bg-color-green" href="other_incomes.php">
                    <img src="./img/bkg-3.png" alt=""/>
                    <div class="textover-wrapper transparent">
                        <div class="text2">INCOME ENTRIES</div>
                    </div>
                </a>
                
                <a class="tile app bg-color-blueDark" href="expenses.php">
                    <div class="image-wrapper">
                        <span class="icon icon-images"></span>
                    </div>
                    <div class="app-label">EXPENDITURE ENTRIES</div>
                </a>

                <a class="tile app bg-color-blueDark" href="edit_other_incomes.php">
                    <div class="image-wrapper">
                        <span class="icon icon-books"></span>
                    </div>
                    <div class="app-label">EDIT INCOME ENTRIES</div>
                </a>

                <a class="tile app bg-color-gray" href="edit_expenses.php">
                    <div class="image-wrapper">
                        <span class="icon icon-spinner"></span>
                    </div>
                    <div class="app-label">EDIT EXPENDITURE ENTRIES</div>
                </a>

                <a class="tile app bg-color-purple" href="sms_class_bills.php">
                    <div class="image-wrapper">
                        <span class="icon icon-mail-7"></span>
                    </div>
                    <div class="app-label">SEND BILLS TO PARENTS</div>
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


    