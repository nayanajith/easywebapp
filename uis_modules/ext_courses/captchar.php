<?php
//php_info();
include A_CLASSES."/captchar_image_class.php";
$width 		= '100';
$height 		= '30';
$characters = '5';
$captcha 	= new Captcha_images_class($width,$height,$characters);
$captcha->gen_captcha();
?>
