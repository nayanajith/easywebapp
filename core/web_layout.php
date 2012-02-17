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
      <link rel="shortcut icon" href="<?php echo IMG."/".$GLOBALS['FAVICON']; ?>"type="image/x-icon" >

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
   ?>
<!--____________________________end loading ________________________________-->
      <div dojoType="dijit.layout.BorderContainer" class='bgTop bContainer'    gutters="false" liveSplitters="true" >

<!--
This contains the login box from core/login.php and program selector from core/program.php
-->
         <div dojoType="dijit.layout.ContentPane" region="top" gutter="false" style="padding:5px;">
            <!-- bannar -->
            <img src="<?php echo IMG."/".$GLOBALS['LOGO']; ?>" width=60px style='float:left'>
            <h1 style='float:left;font-size:24px;padding:0px;span:0px;'><?php echo $GLOBALS['TITLE']; ?></h1>
            <div style='float:right;'>
<!--__________________________end Login form ________________________________-->
            <?php 
               echo $GLOBALS['VIEW']['LOGIN'];
            ?>
<!--______________________________end Login form ____________________________-->

<!--_________________________start Program selector__________________________-->
            <?php 
               echo $GLOBALS['VIEW']['PROGRAM'];
            ?>
<!--__________________________end Program selector___________________________-->
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
                  <div dojoType="dijit.layout.ContentPane" region="top" style="height:58px; padding:0px;">
                     <?php
                        d_r("dijit.layout.BorderContainer");
                        echo $GLOBALS['VIEW']['NAVIGATOR'];
                     ?>
                  </div>
                  <!--end TOP box of BorderContainer-2 (BorderContainer-3)-->

                  <!--CENTER box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="center" id="data_body" class="bgBottom" style="padding:5px;">
<!--________________________start data_body area_____________________________-->
                     <?php 
                     echo $GLOBALS['VIEW']['MAIN'];
                     //include page in module
                     /*
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
                      */
                  ?>

<!--_________________________end data_body area______________________________-->
                  </div>
                  <!--end CENTER box of BorderContainer-2-->

                  <!--BOTTOM box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" jsId="status_bar">
<!--___________________________start statusbar_______________________________-->
                     <?php
                        d_r("dijit.layout.ContentPane");
                        d_r("dijit.Toolbar");

                        echo "<div id='toolbar' jsId='toolbar' dojoType='dijit.Toolbar'>";
                        echo $GLOBALS['VIEW']['TOOLBAR'];
                        echo "</div>";

                        echo $GLOBALS['VIEW']['STATUSBAR'];
                     ?>
<!--___________________________end statusbar_________________________________-->
                  </div>
                  <!--end BOTTOM box of BorderContainer-2-->
               </div>
               <!--end of BorderContainer-2-->
            </div>
            <!--end CENTER box of the BorderContainer-1 -->

            <!--BOTTOM box of the BorderContainer-1 -->
            <div dojoType="dijit.layout.ContentPane" region="bottom" class="bgBottom" style='padding:5px;' >
<!--______________________________start footer_______________________________-->
            <?php
               echo $GLOBALS['VIEW']['FOOTER'];
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
