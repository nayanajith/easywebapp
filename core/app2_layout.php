<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
      <link rel="shortcut icon" href="<?php echo IMG."/".$GLOBALS['FAVICON']; ?>" type="image/x-icon" >
<!--______________________DOJO JAVASCRIPT load modules_______________________-->
      <?php 
         echo $GLOBALS['VIEW']['JS'];
      ?>
   </head>
<!--_____________________BODY with dojo border container_____________________-->
<!--
Three border containers were used 
1) 
top:         Bannar
leading:      Tree menu
center:      [border container 2]
trailing:   ___
bottom:      Organization info

2)
top:         [border container 3]
center:      Body

3)
top:         Menu
bottom:      Tool bar
-->
   <body class="<?php echo $GLOBALS['THEME']; ?>" >
<!--__________________________start loading ________________________________-->
   <?php
      echo $GLOBALS['VIEW']['LOADING'];
      d_r("dijit.layout.BorderContainer");
   ?>
<!--____________________________end loading ________________________________-->
      <div dojoType="dijit.layout.BorderContainer" class='bgTop bContainerApp2'   gutters="false" liveSplitters="true" >

<!--
This contains the login box from core/login.php and program selector from core/program.php
-->
         <div dojoType="dijit.layout.ContentPane" region="top" gutter="false" style="padding:0px;height:40px;overflow:hidden">
            <div id='toolbar_top' jsId='toolbar_top' dojoType='dijit.Toolbar' style='border-left:0px;padding-left:1px;height:35px;'>
               <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                     <td width="30%">
                        <?php
                        echo $GLOBALS['VIEW']['TOOLBAR_TL'];
                        ?>
                     </td>
                     <td width="40%"  align="center">
                        <button dojoType="dijit.form.Button" style="font-size:14px;font-weight:bold">
                        <img src="<?php echo IMG."/".$GLOBALS['LOGO']; ?>" height=30px>
                        <?php echo $GLOBALS['TITLE']; ?>
                        </button>
                     </td>
                     <td width="30%">
                        <div style="float:right" >
                           <?php
                           echo $GLOBALS['VIEW']['TOOLBAR_TR'];
                           ?>
                        </div>
                     </td>
                  </tr>
               </table>
            </div>
        </div>
<!--___________________Leading area with the tree menu_______________________-->
<!--
JSON file for the menu is generated dinamically from mod/module_man/manage_module.php
-->

            <!--CENTER box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="center" style="padding:0px;" >

               <!--BorderContainer-2 (BorderContainer-3 and body)-->
               <div dojoType="dijit.layout.BorderContainer" style="width:100%; height:100%; padding:0px;" gutters="false">
                     
                  <!--TOP box of BorderContainer-2 (BorderContainer-3)-->
                  <div dojoType="dijit.layout.ContentPane" region="top" style="height:57px; padding:0px;">
                                <div dojoType="dijit.layout.BorderContainer"   gutters="false" liveSplitters="true" >
               
               <div dojoType="dijit.layout.ContentPane" region="center" gutter="false" style="padding:0px;">
                     <?php
                     d_r("dijit.MenuBar");
                     d_r("dijit.Menu");
                     d_r("dijit.MenuItem");
                     d_r("dijit.PopupMenuBarItem");
                     echo "<div id='menubar' jsId='menubar' dojoType='dijit.MenuBar' style='height:26px;padding-left:1px;border-right:0px;border-left:0px;border-top:1px solid whitesmoke;'>";
                     echo $GLOBALS['VIEW']['MENUBAR'];
                     echo "</div>";
                     ?>
               </div>
               <div dojoType="dijit.layout.ContentPane" region="bottom" gutter="false" style="padding:0px">
                  <?php
                    d_r("dijit.layout.ContentPane");
                    d_r("dijit.Toolbar");
                    echo "<div id='toolbar' jsId='toolbar' dojoType='dijit.Toolbar'>";
                    echo $GLOBALS['VIEW']['TOOLBAR'];
                    echo "</div>";
                 ?>
               </div>
            </div>
                  </div>
                  <!--end TOP box of BorderContainer-2 (BorderContainer-3)-->

                  <!--CENTER box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="center" id="data_body" class="bgBottom" style="padding:5px;">
<!--________________________start data_body area_____________________________-->
                     <table width='100%'  >
                        <tr>
                           <td id='MAIN_TOP' colspan='2' valign='top' width='100%'>
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_TOP'];
                              ?>
                           </td>
                        </tr>
                        <tr>
                           <td id='MAIN_LEFT' valign='top' align='left' width='50%'>
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_LEFT'];
                              ?>
                           </td>
                           <td id='MAIN_RIGHT' valign='top'  align='right'  width='50%'>
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_RIGHT'];
                              ?>
                           </td>
                        </tr>
                        <tr>
                           <td id='MAIN_BOTTOM' colspan='2' valign='top' width='100%'>
                              <?php 
                              echo $GLOBALS['VIEW']['MAIN_BOTTOM'];
                              ?>
                           </td>
                        </tr>

                     </table>
<!--_________________________end data_body area______________________________-->
                  </div>
                  <!--end CENTER box of BorderContainer-2-->

                  <!--BOTTOM box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" jsId="status_bar" style='padding:0px;'>
                     <?php
                        echo $GLOBALS['VIEW']['STATUSBAR'];
                     ?>
                  </div>
                  <!--end BOTTOM box of BorderContainer-2-->
               </div>
               <!--end of BorderContainer-2-->
            </div>
            <!--end CENTER box of the BorderContainer-1 -->

            <!--BOTTOM box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" style='padding:0px;' >
<!--______________________________start footer_______________________________-->
            <?php
               //echo $GLOBALS['VIEW']['FOOTER'];
            ?>
<!--_______________________________end footer________________________________-->
            </div>
            <!--end BOTTOM box of the BorderContainer-1 -->
      </div>
      <!--end of the BorderContainer-1 -->
<!--_______________________________parse dojo________________________________-->
      <?php parse_dojo(); ?>
   </body>
</html>
