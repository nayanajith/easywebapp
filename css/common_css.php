<?php
include_once('../config.php');
include_once(A_CORE."/common.php");

header("Content-type: text/css");
header("Content-Disposition: attachment; filename=\"common.css\"");
$light_color         ="#C9D7F1";
$light_color         ="#5b92c8";
$light_colorText     ="white";
$dark_color          ="whitesmoke";
$dark_colorText      ="black";
$light_bg_color      ="whitesmoke";
$border_color        ="silver";

/*Backgrounds for body and other controls*/
/*
$backgrounds[$GLOBALS['THEME']][0] -> top bg
$backgrounds[$GLOBALS['THEME']][1] -> bottom bg
*/
$backgrounds=array(
   'claro'   =>array(IMG.'/bodyBg.gif',IMG.'/bodyBgBot.gif'),
   'nihilo'  =>array('',''),
   'soria'   =>array(IMG.'/bodyBg.gif',IMG.'/bodyBgBot.gif'),
   'tundra'  =>array('','')
);

//Set effective theme
$theme=$GLOBALS['THEME'];
if(isset($_SESSION['THEME'])){
   $theme=$_SESSION['THEME'];
}

//Bottom and top images of the background
$BodyBg     =$backgrounds[$theme][0];
$BodyBgBot  =$backgrounds[$theme][1];

include "common.css";

?>

