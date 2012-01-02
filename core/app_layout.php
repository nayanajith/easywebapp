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
      <link rel="shortcut icon" href="<?php echo IMG."/".$GLOBALS['FAVICON']; ?>"type="image/x-icon" >
<!--______________________DOJO JAVASCRIPT load modules_______________________-->
      <?php 
         include A_CORE."/dojo_require.php";
         include A_CORE."/status_bar_func.php";
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
      include A_CORE."/loading.php";

      d_r("dijit.layout.BorderContainer");
      d_r("dijit.layout.ContentPane");
      d_r("dijit.layout.AccordionContainer");
      d_r("dijit.layout.AccordionPane");
   ?>
<!--____________________________end loading ________________________________-->
      <div dojoType="dijit.layout.BorderContainer" id='jj' class='bgTop' style="width: 100%; height: 100%;"  gutters="false" liveSplitters="true" >

<!--
This contains the login box from core/login.php and program selector from core/program.php
-->
         <div dojoType="dijit.layout.ContentPane" region="top" gutter="false" style='padding:5px;' >
            <!-- bannar -->
            <img src="<?php echo IMG."/".$GLOBALS['LOGO']; ?>" width=60px style='float:left'>
            <h1 style='float:left;font-size:24px;padding:0px;span:0px;'><?php echo $GLOBALS['TITLE']; ?></h1>
            <div style='float:right;'>
<!--__________________________end Login form ________________________________-->
            <?php 

               if (isset($_SESSION['username'])){
                  echo after_login();
               }else{
                  echo before_login();
               }
            ?>
<!--______________________________end Login form ____________________________-->

<!--_________________________start Program selector__________________________-->
            <?php 
               echo "Change Program:";
               program_select($program); 
            ?>
<!--__________________________end Program selector___________________________-->
            </div>
         </div>
<!--___________________Leading area with the tree menu_______________________-->
<!--
JSON file for the menu is generated dinamically from mod/module_man/manage_module.php
-->

         <!--LEADING box of the BorderContainer-1 -->
         <div dojoType="dijit.layout.ContentPane" region="leading"  splitter="true" minSize="20" style="width: 200px;padding:0px;">
            <!--Put tree in accordion container-->
            <div dojoType="dijit.layout.AccordionContainer">
               <div dojoType="dijit.layout.AccordionPane" title="Modules & Functions">
<!--___________________________start module tree_____________________________-->
                  <?php
                     include A_CORE."/module_tree.php";
                  ?>
<!--_____________________________end module tree_____________________________-->
                </div>
               <!--end tree accordion pane -->
             </div>
            <!--end tree accordion container -->

            </div>
            <!--end LEADING box of the BorderContainer-1 -->

            <!--CENTER box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="center" style="padding:0px;" >

               <!--BorderContainer-2 (BorderContainer-3 and body)-->
               <div dojoType="dijit.layout.BorderContainer" id='ll' style="width:100%; height:100%; padding:0px;" gutters="false">
                     
                  <!--TOP box of BorderContainer-2 (BorderContainer-3)-->
                  <div dojoType="dijit.layout.ContentPane" region="top" style="height:55px; padding:0px;">

                     <!--BorderContainer-3 (menubar and toolbar)-->
                     <div dojoType="dijit.layout.BorderContainer" id='kk' style="width: 100%; height: 100%; padding:0px;"  gutters="false">

                        <!--TOP box of BorderContainer-3 (menubar)-->
                        <div dojoType="dijit.layout.ContentPane" region="top" style="padding:0px;">
               
<!--_______________________________start menubar_____________________________-->
                           <?php
                              include A_CORE."/menubar.php";
                           ?>
<!--_________________________________end menubar_____________________________-->

                        </div>
                        <!--end TOP box of BorderContainer-3 (menubar)-->

                        <!--BOTTOM box of BorderContainer-3 (toolbar)-->
                        <div dojoType="dijit.layout.ContentPane" region="center" style="padding:0px;" >

<!--___________________________start toolbar_________________________________-->
                           <?php
                              include A_CORE."/toolbar.php";
                           ?>
<!--___________________________end toolbar___________________________________-->

                        </div>
                        <!--end BOTTOM box of BorderContainer-3 (toolbar)-->

                     </div>
                     <!--end BorderContainer-3 (menubar and titlebar)-->

                  </div>
                  <!--end TOP box of BorderContainer-2 (BorderContainer-3)-->

                  <!--CENTER box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="center" id="data_body" class="bgBottom"  style='padding:5px;'>
<!--________________________start data_body area_____________________________-->
                  <?php 
                     //include page in module
                     if(!isset($_SESSION['username'])){
                              if(file_exists(A_MODULES."/".MODULE."/about.php")){
                           include A_MODULES."/".MODULE."/about.php";
                        }else{
                           include "error.php";
                        }
                                 }elseif(!file_exists(A_MODULES."/".MODULE."/".PAGE.".php")){
                        include "error.php";
                                 }else{
                        
                        include A_MODULES."/".MODULE."/".PAGE.".php";
                                                   }
                  ?>
<!--_________________________end data_body area______________________________-->
                  </div>
                  <!--end CENTER box of BorderContainer-2-->

                  <!--BOTTOM box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" jsId="stb" style="padding:1px;">
<!--___________________________start statusbar_______________________________-->
                     <?php
                        include A_CORE."/status_bar.php";
                     ?>
<!--___________________________end statusbar_________________________________-->
                  </div>
                  <!--end BOTTOM box of BorderContainer-2-->
               </div>
               <!--end of BorderContainer-2-->
            </div>
            <!--end CENTER box of the BorderContainer-1 -->

            <!--TRAILING box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="trailing" splitter="true" style="width:90px;padding:0px;">
               <div dojoType="dijit.layout.AccordionContainer">
                  <div dojoType="dijit.layout.AccordionPane" title="Widgets">
<!--______________________________start widgets______________________________-->
                     <?php
                        include A_CORE."/widget_column.php";
                     ?>
<!--_______________________________end  widgets______________________________-->
                  </div>
                  <!--end tree accordion pane -->
                </div>
               <!--end tree accordion container -->
             </div>
            <!--end TRAILING box of the BorderContainer-1 -->
            <!--BOTTOM box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom"  style='padding:5px;'>
<!--______________________________start footer_______________________________-->
            <?php
               include A_CORE."/footer.php";
            ?>
<!--_______________________________end footer________________________________-->
            </div>
            <!--end BOTTOM box of the BorderContainer-1 -->
      </div>
      <!--end of the BorderContainer-1 -->
<!--___________________________start parse dojo______________________________-->
      <?php parse_dojo(); ?>
<!--____________________________end parse dojo_______________________________-->
   </body>
</html>
