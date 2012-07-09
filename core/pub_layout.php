<!DOCTYPE html> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >

<html>
   <head>
      <title><?php echo MODULE."&gt;".PAGE.":".$GLOBALS['TITLE']; ?></title>
      <link rel="search" href="/search" >

<!--_________________________________CSS_____________________________________-->
      <?php 
         echo $GLOBALS['VIEW']['CSS'];
      ?>
<!--______________________________FAVICON____________________________________-->
      <link rel="shortcut icon" href="<?php echo $GLOBALS['FAVICON']; ?>" type="image/x-icon" >

<!--______________________DOJO JAVASCRIPT load modules_______________________-->
      <?php 
         echo $GLOBALS['VIEW']['JS'];
      ?>
   </head>

   <!-- body class="<?php echo $GLOBALS['THEME']; ?>" style='background-image:url(<?php echo IMG; ?>/stripes.gif);' -->
   <body class="<?php echo $GLOBALS['THEME']; ?>">

<!--__________________________start loading ________________________________-->
   <?php
      echo $GLOBALS['VIEW']['LOADING'];
   ?>
<!--____________________________end loading ________________________________-->

<div class='web_bg bgTop'>
<table width='100%' cellpadding=5 cellspacing=0 >
<!--header-->
<tr><td colspan=2 style>
            <img src="<?php echo $GLOBALS['LOGO']; ?>" width=80px style='float:left'>
            <span style='float:left;font-size:26px;font-weight:bold;padding:0px;span:0px;'><?php echo $GLOBALS['TITLE_LONG']; ?></span>
            <div style='float:right;padding:20px;'>
<!--__________________________end Login form ________________________________-->
            <?php 
               echo $GLOBALS['VIEW']['LOGIN'];
               ?>
<!--______________________________end Login form ____________________________-->

<!--_________________________start Program selector__________________________-->
            <?php 
               //echo "Change Program:";
               //program_select($program); 
            ?>
<!--__________________________end Program selector___________________________-->
            </div>

</td>
</tr>
<!-- tr><td colspan=2>&nbsp;</td></tr -->
<!--tabs-->
<tr>
<td colspan=2 style='padding:10px;' >
<!--breadcrumb-->
<div style='padding:0px;padding-left:5px;color:black;font-weight:bold;background-color:gray;' class='round'>
   <?php
      echo $GLOBALS['VIEW']['BREADCRUMB'];
   ?>
</div>
</td>
</tr>
<!--body-->
<tr><td width='80%' style='padding:10px;vertical-align:top;valign:top;padding-right:5px;'>
<div style='min-height:350px;border:1px solid #C9D7F1;padding:10px;position:relative;border-bottom:3px solid #C9D7F1;' class='bgTop round' >
<!--____________________Help Print Download buttons__________________________-->
<?php 
         d_r('dijit.form.Button');
         d_r('dijit.form.Form');
/*
<div style='float:right'>
   <span dojoType='dijit.form.Button' label='Help' showLabel='false' iconClass='dijitIcon dijitIconDocuments' onClick='help_dialog()'></span>
   <span dojoType='dijit.form.Button' label='Print' showLabel='false' iconClass='dijitIcon dijitIconPrint' onClick='page_print()'></span>
   <!--span dojoType='dijit.form.Button' label='Pdf' showLabel='false' iconClass='dijitIcon dijitIconFile' onClick='page_pdf()'></span-->
</div>
*/
?>
<!--________________________start data_body area_____________________________-->
                     <table width='100%'>
                        <tr>
                            <td id='MAIN_TOP' colspan='2' valign='top' >
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_TOP'];
                              ?>
                           </td>
                        </tr>
                        <tr>
                           <td id='MAIN_LEFT' valign='top' align='left' width='100%'>
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_LEFT'];
                              ?>
                           </td>
                           <td id='MAIN_RIGHT' valign='top'  align='right'  >
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_RIGHT'];
                              ?>
                           </td>
                        </tr>
                        <tr>
                           <td id='MAIN_BOTTOM' colspan='2' valign='top' >
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_BOTTOM'];
                              ?>
                           </td>
                        </tr>
                     </table>
<!--_________________________end data_body area______________________________-->
</div>
</td>
<!--_________________________link list at right______________________________-->
<td width='20%' style='vertical-align:top;valign:top;padding:10px;padding-left:5px;' >
   <div style='min-height:350px;padding:10px;border:1px solid #C9D7F1;border-top:3px solid #C9D7F1;'class='bgBottom round' >
   <?php
      echo $GLOBALS['VIEW']['NAVIGATOR'];
   ?>
   </div>
</td>
</tr>
<!--footer-->
<tr><td colspan=2 style='padding:0px;color:white;background:gray'>
<div style='padding:10px;height:50px;position:relative'>
<!--______________________________start footer_______________________________-->
            <?php
               echo $GLOBALS['VIEW']['FOOTER'];
            ?>
<!--_______________________________end footer________________________________-->
</div>
</td></tr>
</table>
</div>
<!--_______________________________parse dojo________________________________-->
      <?php parse_dojo(); ?>

</body>
</html>
