<?php
if(isset($_REQUEST['fullscreen']) && $_REQUEST['fullscreen'] == 'true'){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >

<html>
   <head>
      <title><?php echo MODULE."&gt;".PAGE.":".$GLOBALS['TITLE']; ?></title>
      <link rel="search" href="/search" >

<!--_________________________________CSS_____________________________________-->
      <?php 
         include A_CORE."/style.php";
      ?>
<!--______________________________FAVICON____________________________________-->
      <link rel="shortcut icon" href="<?php echo $GLOBALS['FAVICON']; ?>"type="image/x-icon" >

   </head>
   <body class="<?php echo $GLOBALS['THEME']; ?>">

<div class='web_bg bgTop'>
<table width='100%' cellpadding=5 cellspacing=0 >
<!--header-->
   <tr>
      <td colspan=2 style>
         <img src="<?php echo $GLOBALS['LOGO']; ?>" width=80px style='float:left'>
         <span style='float:left;font-size:26px;font-weight:bold;padding:0px;span:0px;'><?php echo $GLOBALS['TITLE_LONG']; ?>-User Guide</span>
      </td>
   </tr>
</table>

<?php
}
echo get_help();

if(isset($_REQUEST['fullscreen']) && $_REQUEST['fullscreen'] == 'true'){
   echo "</body></html>";
}

?>
