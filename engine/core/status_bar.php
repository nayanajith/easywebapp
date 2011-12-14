<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
d_r('dijit.ProgressBar');
?>
<table style='width:100%;border:1px solid silver;height:20px;' cellpadding=0 cellspacing=0>
   <tr>
      <td style='padding-left:5px;width:5%;'>
         Status:
      </td>
      <td style='border-right:1px solid silver;padding-left:5px;width:48%;'>
         <div id='status_bar'>status</div>
      </td>
      <td style='border-right:1px solid silver;padding-left:5px;width:2%;'>
      <div id='busy'><img src='<?php echo IMG."/busy-stopped.gif"; ?>' ></div>
      </td>
      <td style='padding-left:5px;width:5%;text-align:center'>
         Progress:
      </td>
      <td style='padding-left:5px;border-right:1px solid silver;width:15%'>
         <div dojoType="dijit.ProgressBar" style="width:150px;" jsId="jsProgress"
             id="progress_bar" maximum="100" progress="0">
         </div>
      </td>
      <td style='border-right:1px solid silver;width:10%;text-align:center'>
         <?php echo date('d-m-Y'); ?>
      </td>
   </tr>
</table>
