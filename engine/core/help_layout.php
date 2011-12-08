<?php
if(isset($_REQUEST['fullscreen']) && $_REQUEST['fullscreen'] == 'true'){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<html>
   <head>
      <title><?php echo MODULE."&gt;".PAGE.":".$GLOBALS['TITLE']; ?></title>
      <link rel="search" href="/search" />

<!--_________________________________CSS_____________________________________-->
      <?php 
         include A_CORE."/style.php";
      ?>
<!--______________________________FAVICON____________________________________-->
      <link rel="shortcut icon" href="<?php echo IMG."/".$GLOBALS['FAVICON']; ?>"type="image/x-icon" />

   </head>
   <body class="<?php echo $GLOBALS['THEME']; ?>">

<div class='web_bg bgTop'>
<table width='100%' cellpadding=5 cellspacing=0 >
<!--header-->
   <tr>
      <td colspan=2 style>
         <img src="<?php echo IMG."/".$GLOBALS['LOGO']; ?>" width=80px style='float:left'/>
         <span style='float:left;font-size:26px;font-weight:bold;padding:0px;span:0px;'><?php echo $GLOBALS['TITLE_LONG']; ?> <font color='green' >Help+Guide</font></span>
      </td>
   </tr>
</table>

<?php
}
echo "<div class='help' id='help'><div>";
echo "<h2>Help and Guide for the <font color='green' size='15px' >".PAGE."</font> page of the <font color='green'>".MODULE."</font> module</h2>";

$help_file=A_MODULES."/".MODULE."/".PAGE."_help.php";
if(file_exists($help_file)){
   include $help_file;
}else{
   echo "Sorry help does not available for this page"; 
}

echo "</div></div>";

if(isset($_REQUEST['fullscreen']) && $_REQUEST['fullscreen'] == 'true'){
   echo "</body></html>";
}

?>
