<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<html>
   <head>
      <title><?php echo MODULE."&gt;".PAGE.":".$GLOBALS['TITLE']; ?></title>
      <link rel="search" href="/search" >

<!--_________________________________CSS_____________________________________-->
      <?php 
         echo get_css();
      ?>
<!--______________________________FAVICON____________________________________-->
      <link rel="shortcut icon" href="<?php echo $GLOBALS['FAVICON']; ?>" type="image/x-icon" >
<!--______________________DOJO JAVASCRIPT load modules_______________________-->
      <?php 
         echo get_js();
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
      echo get_loading();
      d_r("dijit.layout.BorderContainer");
   ?>
<!--____________________________common dialog ________________________________-->
      <div dojoType="dijit.Dialog" refreshOnShow="true" id='DIALOG' href="<?php echo gen_url() ?>section=DIALOG"></div>
<!--____________________________end loading ________________________________-->
      <div dojoType="dijit.layout.BorderContainer" class='bgTop bContainerApp2'   gutters="false" liveSplitters="true" >

<!--
This contains the login box from core/login.php and program selector from core/program.php
-->
         <div dojoType="dijit.layout.ContentPane" region="top" parseOnLoad=true preventCache=true loadingMessage=" " gutter="false" id='TOOLBAR_TOP' style="padding:0px;height:40px;overflow:hidden" href="<?php echo gen_url() ?>section=TOOLBAR_TOP" action="post">
            <!-- content will be served in switch at the top of this page -->
         </div>
<!--___________________Leading area with the tree menu_______________________-->
<!--
JSON file for the menu is generated dinamically from mod/module_man/manage_module.php
-->

            <!--CENTER box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="center" style="padding:0px;" loadingMessage=" ">

               <!--BorderContainer-2 (BorderContainer-3 and body)-->
               <div dojoType="dijit.layout.BorderContainer" style="width:100%; height:100%; padding:0px;" gutters="false">
                     
                  <!--TOP box of BorderContainer-2 (BorderContainer-3)-->
                  <div dojoType="dijit.layout.ContentPane" region="top" style="height:80px; padding:0px;" loadingMessage=" ">
                     <!-- BorderContainer-4-->
                     <div dojoType="dijit.layout.BorderContainer"   gutters="false" liveSplitters="true" >
                        <!-- Center box of BorderContainer-4 menubar-->
                        <div dojoType="dijit.layout.ContentPane" parseOnLoad=true preventCache=true loadingMessage=" " region="top" gutter="false" id='MENUBAR' style="padding:0px;height:28px;" href="<?php echo gen_url() ?>section=MENUBAR" action="post">
                           <!-- content will be served in switch at the top of this page -->
                        </div>
                        <!-- Bottom box of BorderContainer-4 toolbar-->
                        <div dojoType="dijit.layout.ContentPane"  parseOnLoad=true preventCache=true loadingMessage=" " region="center" gutter="false" id='TOOLBAR' style="padding:0px" href="<?php echo gen_url() ?>section=TOOLBAR" action="post" onLoad="toolbar_load_selected()" onMouseOver="reloading_on();set_param_on();">
                           <!-- content will be served in switch at the top of this page -->
                        </div>
                     </div>
                  </div>
                  <!--end TOP box of BorderContainer-2 (BorderContainer-3)-->

                  <!--CENTER box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="center"  parseOnLoad=true preventCache=true loadingMessage=" "  id='MAIN' class="bgBottom" style="padding:5px;" action="post" >
                  <!-- div dojoType="dijit.layout.ContentPane" region="center"  id='MAIN' class="bgBottom" style="padding:5px;" action="post" -->
                     <!--________________________start data_body area_____________________________-->
                     <div dojoType="dijit.layout.BorderContainer" gutters="false" design="headline" style="width:100%;height:100%;padding:0px;">
                        <div dojoType="dijit.layout.ContentPane" region="top" id="MAIN_TOP" preventCache=true loadingMessage=" " <?php echo get_layout_property('app2','MAIN_TOP'); ?> href="<?php echo gen_url() ?>section=MAIN_TOP">
                        </div>
                        <div dojoType="dijit.layout.ContentPane" region="center" id="MAIN_LEFT"  preventCache=true loadingMessage=" " <?php echo get_layout_property('app2','MAIN_LEFT'); ?> href="<?php echo gen_url() ?>section=MAIN_LEFT">
                        </div>
                        <div dojoType="dijit.layout.ContentPane" region="right" id="MAIN_RIGHT"   preventCache=true loadingMessage=" " <?php echo get_layout_property('app2','MAIN_RIGHT'); ?> href="<?php echo gen_url() ?>section=MAIN_RIGHT">
                        </div>
                        <div dojoType="dijit.layout.ContentPane" region="bottom" id="MAIN_BOTTOM"   preventCache=true loadingMessage=" " class="bgBottom" <?php echo get_layout_property('app2','MAIN_BOTTOM'); ?> href="<?php echo gen_url() ?>section=MAIN_BOTTOM">
                        </div>
                     </div>
                     <!--_________________________end data_body area______________________________-->
                  </div>
                  <!--end CENTER box of BorderContainer-2-->

                  <!--BOTTOM box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" style='padding:0px;' loadingMessage=" ">
                     <?php echo get_statusbar(); ?>
                  </div>
                  <!--end BOTTOM box of BorderContainer-2-->
               </div>
               <!--end of BorderContainer-2-->
            </div>
            <!--end CENTER box of the BorderContainer-1 -->

            <!--BOTTOM box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" style='padding:0px;'  loadingMessage=" ">
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
      <?php 
      parse_dojo(); 
      ?>
   </body>
</html>
