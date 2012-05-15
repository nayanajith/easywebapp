
<?php echo $GLOBALS['FOOTER'] ?>

<?php if($GLOBALS['LAYOUT'] == 'pub' ){ ?>
<a href="http://ucsc.lk/" style='color:white'>Home</a>&nbsp;|&nbsp;
<a href="http://ucsc.lk/contact" style='color:white'>Contact Us</a>&nbsp;|&nbsp;
<a href="http://ucsc.lk/about-us" style='color:white'>About Us</a>&nbsp;|&nbsp;
<!-- a href="javascript:open_page('payment','contact')" style='color:white'>Help</a -->

<img src="<?php echo $GLOBALS['LOGO2']; ?>" height="50" style='position:absolute;bottom:20px;right:2px;'>
<div style='position:absolute;bottom:2px;right:5px;'>
<?php echo $GLOBALS['HOTLINE'] ?>
</div>
<?php }else{ ?>
<div style='position:absolute;bottom:2px;right:5px;'>
<?php echo $GLOBALS['HOTLINE'] ?>
</div>
<?php } ?>
