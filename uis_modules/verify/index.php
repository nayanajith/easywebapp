<?php
session_start();
$_SESSION['username'] 	= 'anonymouse';
$_SESSION['permission'] = 'user';
//Enable disable Errors
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

include "config.php";
include "common.php";

//echo $_SERVER["REQUEST_URI"];


/*
 * validate page request
 */
if (!isset($page)){
   global $page;
   if (isset($_GET['page'])){
	   $page = $_GET['page'];
   }elseif (isset($_POST['page'])){
	   $page = $_POST['page'];
   }else{
      $page = '';
   }
}


/*
 * validate module request
 */
if (!isset($module)){
   global $module;
   if (isset($_GET['module'])){
	   $module = $_GET['module'];
   }elseif (isset($_POST['module'])){
	   $module = $_POST['module'];
   }else{
      $module = "Home";
   }
}

//Login functions are from here
//include "login.php";

/*
 * Check for print request
 */
$print=false;
if (isset($_GET['print'])){
	$print = true;
}elseif (isset($_POST['print'])){
	$print = true;
}

/*
 * Check for csv request
 */
$csv=false;
if (isset($_GET['csv'])){
	$csv = true;
}elseif (isset($_POST['csv'])){
	$csv = true;
}

//$page=A_ROOT."/".$page;

if (!$page || !file_exists("$page.php")){
	$page = 'error';
}

/*
INCLUDE MENUS and MENU LOGIC
*/
include "menus.php";

/*
 * CSV generation request sent to particular page and stop further execution in this page
 */
if($csv){
   include "$page.php";
   return;	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $GLOBALS['title']; ?></title>

<link 
   rel   ="search" 
   href  ="/search" 
>

<link 
   rel   ="stylesheet"
   href  ="<?php echo $GLOBALS['css']; ?>/common_css.php" 
   type  ='text/css' 
>

<link 
   rel   ="shortcut icon" 
   href  ="<?php echo $GLOBALS['favicon']; ?>"
   type  ="image/x-icon" 
>

<SCRIPT 
   language ='javascript'
   src      ="<?php echo $GLOBALS['js']; ?>/validate.js" 
   type     ='text/javascript'
></script>

<SCRIPT 
   language ='javascript'
	src      ="<?php echo $GLOBALS['js']; ?>/common_js.php"
   type     ='text/javascript'
></script>

</head>

<?php 
//Print request will print the page and stop further processing
if($print){
   print_header($_GET['title']);
   include "$page.php";
   print_footer();
   return;	
}
?>

<body onload="realtime_validator()" alink="black" vlink="black" link="black">

<!-- START CONTAINER -->
<div	id=container >

<!-- START HEADER -->
<div id="header" >

<!-- START TITLE -->
<div id='title'>
<table width=100%>
	<tr>
		<td rowspan=3>
			<img 
				src	='<?php echo $GLOBALS['logo']; ?>' 
				alt	='<?php echo $GLOBALS['title']; ?>' 
				width	=90 
			>
		</td>
		<td valign=bottom>
		<h1><?php echo $GLOBALS['title']; ?></h1>
		</td>
		<td>
			<form action='/search'>
				<div id=search >
					<input type=text size=10 class=field>
					<input type=submit value=search >
				</div>
			</form>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<hr >
		</td>
	</tr>
	<tr>
		<td>
		<h2><?php echo TITLE_LONG; ?></h2>
		</td>
		<td class='menu mid'>
		<ul>
			<li>Help</li>
		</ul>
		</td>
	</tr>
</table>
</div>
<!-- END TITLE -->

</div>
<!-- END HEADER -->

<!-- START MAIN MENU -->
<div class='menu up'>
<ul>
<?php
foreach ($menu_array as $title =>$valuee) {
	if ($title == $module) {
		print "<li class=active><a href='".$GLOBALS['PAGE_GEN']."?module=$title'>$title</a></li>";
	} else {
		print "<li><a href='".$GLOBALS['PAGE_GEN']."?module=$title'>$title</a></li>";
	}
}
?>
</ul>
</div>
<!-- END MAIN MENU -->
<!-- START LOGIN BAR -->
<div id=loginBar align=right>
<ul>
<div align=left style='color:white;padding:0px;padding-top:5px;letter-spacing:10px;font-weight:bold;'>
Student&nbsp;&nbsp;&nbsp;Information&nbsp;&nbsp;&nbsp;System
</div>
<?php
/*
if (isset($_SESSION['username'])){
	echo after_login();
}else{
	echo before_login();
}
*/
?>
</ul>
</div>
<!-- END LOGIN BAR -->

<!-- START SUB MENU -->
<div class='menu down' align=right>
<ul>
<?php
if (isset($_SESSION['username']) && $_SESSION['permission'] == 'admin') {
	foreach ($admin_menu_array as $key => $value) {
      echo $page;
		if ($page == $value) {
			print "<li class=active><a  href='".$GLOBALS['PAGE_GEN']."?page=$value&module=$module'>".$key."</a></li>";
		} else {
			print "<li><a  href='".$GLOBALS['PAGE_GEN']."?page=$value&module=$module'>$key</a></li>";
		}
	}

} elseif (isset($_SESSION['username'])) {

	foreach ($sub_menu_array as $key => $value) {
		if ($page == $value) {
			print "<li class=active><a class='trac' href='".$GLOBALS['PAGE_GEN']."?page=$value&module=$module'>".$key."</a></li>";
		} else {
			print "<li><a class='trac' href='".$GLOBALS['PAGE_GEN']."?page=$value&module=$module'>$key</a></li>";
		}
	}
}
?>
</ul>
</div>
<!-- END SUB MENU -->

<br>
<!-- START DATA REPRESENTATION BODY -->
<div id=dataContainer >
<div id=data  style='padding:10px;position:relative;'>
<?php 
include "$page.php";
?>
</div>
</div>
<!-- END DATA REPRESENTATION BODY -->


<!-- START FOOTER -->
<div id=spacer>
<!-- SPACER -->
</div>
<div align=left  id='footer'>
   <p>
   	<a href="">facts</a>&nbsp;|&nbsp;
   	<a href="">services</a>&nbsp;|&nbsp;
   	<a href="">contact</a>&nbsp;|&nbsp;
   	<a href="">about the SIS</a>
   	<br>
    	University of Colombo School of Computing&nbsp;|&nbsp;No: 35&nbsp;|&nbsp;Reid Avenue&nbsp;|&nbsp;Colombo 7,
Sri Lanka.
   </p> 
<!-- END FOOTER -->
</div>
<!-- END CONTAINER --></div>
</body>
</html>
