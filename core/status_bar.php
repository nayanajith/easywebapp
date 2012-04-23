<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
d_r('dijit.ProgressBar');
?>
<table style='width:100%;border:1px solid silver;height:20px;' cellpadding=0 cellspacing=0>
   <tr>
      <td style='padding-left:5px;width:30px;font:inherit'>Status:</td>
      <td style='border-right:1px solid silver;padding-left:5px;'>
         <div id='status_bar'>status</div>
      </td>
      <td style='border-right:1px solid silver;padding-left:2px;width:17px;'>
      <div id='busy'><img src='<?php echo IMG."/busy-stopped.gif"; ?>' title='Not busy'></div>
      </td>
      <td style='padding-left:5px;width:60px;text-align:center;font:inherit;'>Progress:</td>
      <td style='padding-left:5px;border-right:1px solid silver;width:155px'>
         <div dojoType="dijit.ProgressBar" style="width:150px;" jsId="jsProgress"
             id="progress_bar" maximum="100" progress="0">
         </div>
      </td>
      <td style='border-right:1px solid silver;width:80px;text-align:center;font:inherit'>
         <?php echo date('d-m-Y'); ?>
      </td>
   </tr>
</table>
