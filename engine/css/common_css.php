<?php
include('../config.php');
include(A_CORE."/common.php");


header("Content-type: text/css");
header("Content-Disposition: attachment; filename=\"common.css\"");
//$menu_active="#C9D7F1";
//$menu_active         ="#5b92c8";
$menu_active         ="#5b92c8";
$menu_activeText      ="white";
$menu_inactive         ="whitesmoke";
$menu_inactiveText   ="black";
$light_bg_color      ="whitesmoke";
$border_color        ="silver";

/*Backgrounds for body and other controls*/
/*
$backgrounds[$GLOBALS['THEME']][0] -> top bg
$backgrounds[$GLOBALS['THEME']][1] -> bottom bg
*/
$backgrounds=array(
   'claro'   =>array(IMG.'/bodyBg.gif',IMG.'/bodyBgBot.gif'),
   'nihilo'   =>array('',''),
   'soria'   =>array(IMG.'/bodyBg.gif',IMG.'/bodyBgBot.gif'),
   'tundra'   =>array('','')
);

?>
/*-------------------------Common styles------------------------------*/
*{
   font-size:12px;
   font-family:Arial,Verdana,'Bitstream Vera Sans',Helvetica,sans-serif;
}

body{ 
   background: #fff; 
   color: #000; 
   margin: 10px; 
   padding: 0; 
}

body, th, td {
   font: normal 13px Verdana,Arial,'Bitstream Vera Sans',Helvetica,sans-serif;
}

th{
   font-weight:bold;
}

h1, h2, h3, h4 {
   font-family: Arial,Verdana,'Bitstream Vera Sans',Helvetica,sans-serif;
   font-weight: bold;
   letter-spacing: -0.018em;
   page-break-after: avoid;
}

.coolh{
   background-color:#C9D7F1;
   padding:2px;
   text-align:center;
   background-image:url(<?php echo $backgrounds[$GLOBALS['THEME']][1]; ?>);
   background-repeat:repeat-x;
   background-position:center center;
}

h1{ 
   font-size: 19px; 
   margin: .15em 1em 0.5em 0; 
}

h2{ 
   font-size: 16px;
}

h3{ 
   font-size: 14px;
}

hr{ 
   border: none;  
   border-top: 1px solid <?php echo $menu_active; ?>; 
   margin: 2em 0;
}

address { 
   font-style: normal; 
}

img { 
   border: none; 
}



/*-------------------------Paper size setup------------------------------*/
.a4 {
   background: white;
   padding: 20mm;
   width: 170mm;
   /*width: 210mm;*/ /*height:297mm;*/
   margin-left: auto;
   margin-right: auto;
   /*border:1px solid black;*/
}


.a4l {
   background: white;
   padding: 20mm;
   height: 170mm;
   width: 257mm;
   /* width: 210mm;*/ /*height:297mm;*/
   margin-left: auto;
   margin-right: auto;
   /*border:1px solid black;*/
}

/*-------------------------Page breaks at print------------------------------*/

.break { 
   page-break-before: always; 
}

.break_before { 
   page-break-before: always; 
}

.break_after { 
   page-break-after:always; 
}

/*-------------------------Container of the page------------------------------*/
#container{
   border: 0px solid whitesmoke; 
   margin-left: auto; 
   margin-right: auto; 
   max-width: 900px;
   min-height:500px;
   position:relative;
   z-index:0;
   margin-top:10px;
}

/*-----------------------header(banner) of the page---------------------------*/
#header{
   padding: 5px;
}

/*------------------------title(banner) of the page---------------------------*/
#title{
   padding: 0px;
}

#title td{
   padding  :0px;
   margin   :0px;
}

#title h1{
   color      : black;
   font-size: 24px;
   margin   : 0px;
}

#title h2{
   color      : black;
   font-size: 14px;
   margin   : 0px;
}

#title hr{
   border-top: 2px solid <?php echo $menu_active; ?>; 
   margin:0px;
}

#title input.field{
   border:0px;
}

/*----------------------------Container of Data div---------------------------*/
#dataContainer{
   margin: 10px;
   margin-top: -15px;
}

/*--------------------Data presentation and retrieval-------------------------*/
#data{
   border: 1px solid silver; 
   min-width: 50px; 
   position: relative; 
   background-color: white; 
   z-index: 1;
   padding: 10px; 
   position: relative; 
   min-height: 300px;
}

/*------------------------Friendly item browser-------------------------------*/
#browser_box{
   border: 1px solid <?php echo $menu_active; ?>;
   width:800px;
   padding:5px;
   overflow:auto;
   margin-left:auto;
   margin-right:auto;
}
   
.items_div
{
   width: 150px;
   height: 150px;
   overflow:auto;
   padding:5px;
   overflow: -moz-scrollbars-vertical;
   overflow-x: auto;
   border:1px solid <?php echo $menu_inactive; ?>;
}

.items
{
   list-style-type: none;
   margin:auto;
}
.items li{
   padding:5px;
   color:black;
   float:left;
}

.items div:hover{
   background-color:<?php echo $menu_inactive; ?>;
}

.normal_folder{
   padding:1px;
   color:black;
   text-align:center;
   border: 1px solid <?php echo $menu_active; ?>;
   padding:5px;
   min-width:60px;
}

.selected_folder{
   padding:5px;
   background-color:<?php echo $menu_active; ?>;
   color:blue;
   min-width:60px;
   text-align:center;
   border:1px solid <?php echo $menu_inactive; ?>;
}
/*------------------------------Clean Table-----------------------------------*/
.clean{
   border-collapse:collapse;
   border:1px solid black;
}

.clean td{
   padding:5px;
}

.data_table{
   width:80%;
   margin-left:auto;
   margin-right:auto;
}

.data_table td{
   vertical-align:top;
}

/*------------------------------Colors----------------------------------------*/
.color0{
   color:black;
}
.color1{
   color:blue;
}
.color2{
   color:green;
}
.color3{
   color:orange;
}
.color4{
   color:red;
}

.ul_menu {
   list-style-type: none;
   margin-left:auto;
   margin-right:auto;
}

.ul_menu a{
   padding-right: 10px;
   padding-left: 10px;
   text-decoration: none;
} 

.ul_menu li {
   float: left;
}

.summery{
   height:320px;
   overflow:auto;
   border:1px solid <?php echo $menu_active; ?>;
   padding:10px;
}

.detail{
   height:320px;
   overflow:auto;
   border:1px solid <?php echo $menu_active; ?>;
   padding:10px;
}

/*-------------------------------menu tabs------------------------------*/
.menu ul {
   margin-left: 0;
   padding-left: 0;
   display: inline;
} 

.menu ul li {
   margin-left: 0;
   margin-bottom: 0px;
   background:<?php echo $menu_inactive; ?>;   
   list-style: none;
   display: inline;
   border:1px solid <?php echo $menu_active; ?>;
   padding: 1px 1px 2px;
}
      
.menu ul li.active {
   background:<?php echo $menu_active; ?>;   
   list-style: none;
   display: inline;
   padding: 1px 1px 2px;
}
   
.menu ul li.active a{
   text-decoration:none;
   padding: 2px 15px 5px;
   color:<?php echo $menu_activeText; ?>;
}

.menu ul li a{
   text-decoration:none;
   padding: 2px 15px 5px;
   color:<?php echo $menu_inactiveText; ?>;
}
   
/* UPward tabs */
.up ul li {
   margin-right: 1px;
   -moz-border-radius-topleft:5px;
   -moz-border-radius-topright:5px;
   border-top-left-radius: 5px 5px;
   border-top-right-radius: 5px 5px;
   border-bottom:0px;
}
.up ul li:hover{
   background-color:<?php echo $menu_active; ?>;
}
/* Downward tabs */
.down ul li{
   margin-left: 1px;
   -moz-border-radius-bottomleft:5px;
   -moz-border-radius-bottomright:5px;
   border-bottom-left-radius: 5px 5px;
   border-bottom-right-radius: 5px 5px;
}   
.down ul li:hover{
   background-color:<?php echo $menu_active; ?>;
}
/* Downward tabs */
.down ul li.active{
   z-index:2;
   position:relative;
}

/*--------------------------------search box----------------------------------*/   
#search{
   border:1px solid <?php echo $menu_active; ?>;
   width:150px;
   padding-left:5px;

}
/*--------------------------------login bar-----------------------------------*/   
#loginBar{
   margin-top: -12px; 
   width: 100%; 
   height: 30px; 
   background: <?php echo $menu_active; ?>; 
   position: relative;
}

.login_form{
   color:<?php echo $menu_activeText; ?>;
   margin-right:5px;
   padding-top:4px;
} 

.login_input{
   width:50px;
   font-size:11px;
   color:black;
}

.login_input_btn{
   width:50px;
   color:black;
}

/*----------------------------------footer------------------------------------*/   

#spacer{
   height:50px;
}

#footer{
   background: <?php echo $menu_active; ?>; 
   color:<?php echo $menu_activeText; ?>;
   font-size:10px;
   
   -moz-border-radius-bottomleft:5px;
   -moz-border-radius-bottomright:5px;
   border-bottom-left-radius: 5px 5px;
   border-bottom-right-radius: 5px 5px;
   
   width:100%;
   position:absolute;
   bottom:0px;
}

#footer a{
   color:<?php echo $menu_activeText; ?>;
   text-decoration:none;
}

#footer p{
   margin:10px;
}

/*---------------------------------print csv----------------------------------*/
.dataAction{
   background: <?php echo $menu_active; ?>; 
   color:<?php echo $menu_activeText; ?>;
   text-decoration:none;
   padding-left:5px;
   padding-right:5px;
}
/*---------------------------------help cage----------------------------------*/
.help{
   background-color:#F5F6CE;
}

.help div{
   text-align:left;
   padding:10px;
}

.help h3,.help h4{
   border:1px solid silver;
   background-color:#C9D7F1;
   padding:2px;
   padding-left:10px;
   text-align:left;
   background-image:url(<?php echo $backgrounds[$GLOBALS['THEME']][1]; ?>);
   background-repeat:repeat-x;
   background-position:center center;
}



.help a{
   color:brown;
   text-decoration:overline;
}

/*------------------------shadow and round corners----------------------------*/
.round10,#search,.dataAction,.mid ul li ,.help h3,.help h4{
<?php 
 if(is_opera()||is_chrome()){
    echo "
   border-bottom-left-radius: 10px 10px;
   border-bottom-right-radius: 10px 10px;
   border-top-left-radius: 10px 10px;
   border-top-right-radius: 10px 10px;
   ";
 }else{
    echo "
  -moz-border-radius-bottomleft:10px;
  -moz-border-radius-bottomright:10px;
  -moz-border-radius-topleft:10px;
  -moz-border-radius-topright:10px;
  ";
 }
?> 
}

.round,.items div,.browser,#browser_box,#container,#data {
<?php 
 if(is_opera()||is_chrome()){
    echo "
   border-bottom-left-radius: 5px 5px;
   border-bottom-right-radius: 5px 5px;
   border-top-left-radius: 5px 5px;
   border-top-right-radius: 5px 5px;
   ";
 }else{
    echo "
  -moz-border-radius-bottomleft:5px;
  -moz-border-radius-bottomright:5px;
  -moz-border-radius-topleft:5px;
  -moz-border-radius-topright:5px;
  ";
 }
?> 
}

.round10bot{
<?php 
 if(is_opera()||is_chrome()){
    echo "
   border-bottom-left-radius: 10px 10px;
   border-bottom-right-radius: 10px 10px;
   ";
 }else{
    echo "
  -moz-border-radius-bottomleft:10px;
  -moz-border-radius-bottomright:10px;
  ";
 }
?> 
}


.shadow,.selected_folder,.menu ul li.active,#loginBar{
<?php 
if(is_msie()){
   echo "box-shadow: 0px 4px 8px #C8C8C8;";
   //echo "filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='8', MakeShadow='true', ShadowOpacity='0.10');";
 }elseif(is_chrome()){ 
   echo "-webkit-box-shadow: #C8C8C8 0px 4px 8px;";
 }elseif(is_opera()){
     echo "box-shadow: 0px 4px 8px #C8C8C8;";
 }else{
     echo "box-shadow: 0px 4px 8px #C8C8C8;";
    echo "-moz-box-shadow:0 4px 8px #C8C8C8;";
 }
?>
}

.shadow10,#container{
<?php 
if(is_msie()){
   echo "box-shadow: 1px 3px 10px #C8C8C8;";
   //echo "filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='8', MakeShadow='true', ShadowOpacity='0.10');";
 }elseif(is_chrome()){ 
   echo "-webkit-box-shadow: #C8C8C8 1px 3px 10px;";
 }elseif(is_opera()){
     echo "box-shadow: 1px 3px 10px #C8C8C8;";
 }else{
    echo "-moz-box-shadow:1px 3px 10px #C8C8C8;";
 }
?>
}

/*--------------------------IE shadow---------------------------------------*/
.ieShadow{
    display:block;
    position:absolute;
    top:10px;
    left:10px;
    bottom:-10px;
    right:-10px;
    background:#fff;/* Here must be set of base layer background color */
    filter:progid:DXImageTransform.Microsoft.Blur(pixelradius=20);
    -ms-filter:"progid:DXImageTransform.Microsoft.Blur(pixelradius=20)";
}


/*--------------------------Item Analysis report----------------------------*/
.a4stat {
   background: white;
   width: 210mm;
   /*width: 210mm;*/ /*height:297mm;*/
   margin-left: auto;
   margin-right: auto;
}

#itemAnalysis{
   border-collapse:collapse;
   font-size:10px;
}

#itemAnalysis th{
   color:black;
}

#itemAnalysis td{
   position:relative;
}

#itemAnalysis td div.left{
   float:left;   
   color:black;
}

#itemAnalysis td div.right{
   float:right;   
   color:black;
}

/*--------------------------Baloon styles----------------------------*/
<?php
$baloon_bg      ="khaki";
$baloon_font   ="11px arial,sans-serif;";
$baloon_border   ="1px solid red";
$page_bg         =$menu_active;

if (isset($_SESSION['username'])) {
   $page_bg         ="white";
}
   $page_bg         ="white";

?>

.baloon_body{
   border:<?php echo $baloon_border; ?>;
   border-left:0px;
   
 <?php 
 if(is_opera()||is_chrome()){
    echo "
   border-bottom-right-radius: 20px 20px;
   border-top-right-radius: 20px 20px;
   border-top-left-radius: 20px 20px;
   border-bottom-left-radius: 20px 20px;
   ";
 }else{
     echo " 
  -moz-border-radius-topright:20px;
  -moz-border-radius-bottomright:20px;
  -moz-border-radius-topleft:20px 20px;
  -moz-border-radius-bottomleft:20px 20px;
   ";
 }
 ?>
   
   width:20px;
   height:19px;
   
   position:relative;
   padding:1px;
   padding-left:10px;
   padding-right:4px;
   margin-left:10px;
   margin-right:10px;
   
   background:<?php echo $baloon_bg; ?>;
   
   /*
   -webkit-box-shadow: #C8C8C8 0px 1px 8px;
   */
   
   font:<?php echo $baloon_font; ?>;
   color:black;
}
.baloon_tail_bg{
   width:10px;
   height:14px;
   position:absolute;
   top:0px;
   left:-7px;
   background:<?php echo $baloon_bg; ?>;
}
.baloon_tail_margin{
   position:absolute;
   width:10px;
   height:6px;
   background:<?php echo $page_bg; ?>;
}
.baloon_tail_top_margin{
   top:0px;
 <?php 
 if(is_opera()||is_chrome()){
    echo "border-bottom-right-radius: 50px 50px;";
 }else{
    echo "-moz-border-radius-bottomright:50px 50px;";
 }
 ?>
   border-bottom:<?php echo $baloon_border; ?>;
   border-right:<?php echo $baloon_border; ?>;
}
.baloon_tail_bottom_margin{
   bottom:0px;
   <?php 
 if(is_opera()||is_chrome()){
    echo "border-top-right-radius: 50px 50px;";
 }else{
    echo "-moz-border-radius-topright:50px 50px;";
 }
 ?>
   border-top:<?php echo $baloon_border; ?>;
   border-right:<?php echo $baloon_border; ?>;
}


body {
   font: 12px Myriad,Helvetica,Tahoma,Arial,clean,sans-serif;
   *font-size: 75%;
}

.dojoxGrid table {
   margin: 0; 
} 

html, body { 
   width: 100%; 
   height: 100%;
   margin: 0; 
}

#borderContainerTwo { 
   width: 100%; 
   height: 100%; 
   }

.bgTop{
   background-image:url(<?php echo $backgrounds[$GLOBALS['THEME']][0]; ?>);
   background-repeat:repeat-x;
   background-position:center top;
}

.bgBottom{
   background-image:url(<?php echo $backgrounds[$GLOBALS['THEME']][1]; ?>);
   background-repeat:repeat-x;
   background-position:center bottom;
}

.bgCenter{
   background-image:url(<?php echo $backgrounds[$GLOBALS['THEME']][1]; ?>);
   background-repeat:repeat-x;
   background-position:center center;
}

.buttonBar{
   background-image:url(<?php echo $backgrounds[$GLOBALS['THEME']][1]; ?>);
   background-repeat:repeat-x;
   background-position:center center;
   padding:10px;
   position:absolute;
   width:95%;
   bottom:10px;
}

/*style of main border container  for layout-2 (web layout)*/
.bContainer{
/*
   -moz-border-radius-bottomleft:10px;
   -moz-border-radius-bottomright:10px;
   -moz-border-radius-topleft:10px;
   -moz-border-radius-topright:10px;
   
   border-bottom-left-radius: 10px 10px;
   border-bottom-right-radius: 10px 10px;
   border-top-left-radius: 10px 10px;
   border-top-right-radius: 10px 10px;
*/

   box-shadow: 0px 4px 8px #C8C8C8;
   -moz-box-shadow:0 4px 8px #C8C8C8;
   -webkit-box-shadow: #C8C8C8 1px 3px 10px;

   width: 1000px; 
   height: 97%;
   margin-left:auto;
   margin-right:auto; 
   border:1px solid silver;
}

.web_bg{
/*
   -moz-border-radius-bottomleft:10px;
   -moz-border-radius-bottomright:10px;
   -moz-border-radius-topleft:10px;
   -moz-border-radius-topright:10px;
   
   border-bottom-left-radius: 10px 10px;
   border-bottom-right-radius: 10px 10px;
   border-top-left-radius: 10px 10px;
   border-top-right-radius: 10px 10px;
*/

   box-shadow: 0px 4px 8px #C8C8C8;
   -moz-box-shadow:0 4px 8px #C8C8C8;
   -webkit-box-shadow: #C8C8C8 1px 3px 10px;

   width: 1000px; 
   margin-left:auto;
   margin-right:auto; 
   border:1px solid silver;
   background-color:white;
}

/*Tis is the style of the form table*/
.form_bg{
   background-color:whitesmoke;
   padding:10px;
   -moz-border-radius-bottomleft:5px;
   -moz-border-radius-bottomright:5px;
   -moz-border-radius-topleft:5px;
   -moz-border-radius-topright:5px;
   
   border-bottom-left-radius: 5px 5px;
   border-bottom-right-radius: 5px 5px;
   border-top-left-radius: 5px 5px;
   border-top-right-radius: 5px 5px;
   border: 1px solid silver;

}

.round{
   -moz-border-radius-bottomleft:5px;
   -moz-border-radius-bottomright:5px;
   -moz-border-radius-topleft:5px;
   -moz-border-radius-topright:5px;
   
   border-bottom-left-radius: 5px 5px;
   border-bottom-right-radius: 5px 5px;
   border-top-left-radius: 5px 5px;
   border-top-right-radius: 5px 5px;

}

.shadow{
   box-shadow: 0px 4px 8px #C8C8C8;
   -moz-box-shadow:0 4px 8px #C8C8C8;
   -webkit-box-shadow: #C8C8C8 1px 3px 10px;
}

