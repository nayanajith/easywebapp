<?php
$mod_arr      = $modules;
$page_arr   = $menu_array;
$page         ='';

if(isset($_SESSION['BREADCRUMB'][MODULE])){
   $key=array_search(PAGE,$_SESSION['BREADCRUMB'][MODULE]);
   if($key >= 0){
      $_SESSION['BREADCRUMB'][MODULE]=array_slice($_SESSION['BREADCRUMB'][MODULE],0,$key);
   }else{
      $_SESSION['BREADCRUMB'][MODULE][]=PAGE;
   }
}else{
   $_SESSION['BREADCRUMB'][MODULE]=array();
}


if(isset($page_arr[PAGE])){
   $page=$page_arr[PAGE];
}

if(is_array($page)){
   $page=$page['PAGE'];
}

?>
<table cellpadding=0 cellspacing=0 style='color:white;'><tr><td>
<?php echo $mod_arr[MODULE]; ?>
</td>
<td>
<!-- img src='<?php echo IMG ?>/breadcrumb_arrow.gif' style='margin:0px;padding:0px;opacity: 0.20;filter:alpha(opacity=20);' / -->
<img src='<?php echo IMG ?>/breadcrumb_arrow.png' style='margin:0px;padding:0px;vertical-align:bottom' >
</td>
<td>
<?php 
echo $page; 
?>
</td></tr>
</table>
