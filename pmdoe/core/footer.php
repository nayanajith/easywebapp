<!--
<a href="">Facts</a>&nbsp;|&nbsp;
<a href="">services</a>&nbsp;|&nbsp;
<a href="">contact</a>&nbsp;|&nbsp;
<a href="">about the <?php echo $GLOBALS['TITLE_SHORT'] ?></a> <br>
-->
University of Colombo School of Computing&nbsp;|&nbsp;
No:35&nbsp;|&nbsp;
Reid Avenue&nbsp;|&nbsp;
Colombo 7,Sri Lanka.<br>
<?php if($GLOBALS['LAYOUT'] == 'pub' ){ ?>
<a href="http://ucsc.lk/" style='color:white'>Home</a>&nbsp;|&nbsp;
<a href="http://ucsc.lk/contact" style='color:white'>Contact Us</a>&nbsp;|&nbsp;
<a href="http://ucsc.lk/about-us" style='color:white'>About Us</a>&nbsp;|&nbsp;
<!-- a href="javascript:open_page('payment','contact')" style='color:white'>Help</a -->

<img src="<?php echo IMG."/ucsc-logo-mono.png"; ?>" width="50" style='position:absolute;bottom:20px;right:2px;'>
<div style='position:absolute;bottom:2px;right:5px;'>
Contact:&nbsp;+94112581245/7
</div>
<?php }else{ ?>
<div style='position:absolute;bottom:2px;right:5px;'>
Contact:&nbsp;+94112581245/7
</div>
<?php } ?>
