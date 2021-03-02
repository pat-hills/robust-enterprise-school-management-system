<div class="row">
    <div class="span12 footer">
        <p class="footer">
            <span style="font-size: 11px;">Copyright &copy; <?php echo strftime("%Y", time()) . " Linutek Ghana, Mobile: 027-479-8046 / 026-764-2898"; ?></span>
        </p>
    </div>
</div>

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
