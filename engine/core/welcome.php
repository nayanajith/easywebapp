<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
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
      ?>
   </head>
   <body class="<?php echo $GLOBALS['THEME']; ?>" >
<!--__________________________start loading ________________________________-->
   <?php
      include A_CORE."/loading.php";
   ?>
<!--____________________________end loading ________________________________-->
      <style type="text/css">
.bContainer_modif{
   -moz-border-radius-bottomleft:0px;
   -moz-border-radius-bottomright:0px;
   -moz-border-radius-topleft:0px;
   -moz-border-radius-topright:0px;
   
   border-bottom-left-radius: 0px 0px;
   border-bottom-right-radius: 0px 0px;
   border-top-left-radius: 0px 0px;
   border-top-right-radius: 0px 0px;


   box-shadow:none;
   -moz-box-shadow:none;
   -webkit-box-shadow:none; 

   width: 99%; 
   height: 99%; 
}

      </style>

      <div dojoType="dijit.layout.BorderContainer" class='bgTop bContainer bContainer_modif'    gutters="false" liveSplitters="true" >
         <div dojoType="dijit.layout.ContentPane" region="top" gutter="false" style="padding:5px;height:200px;">
            <!-- bannar -->
            <img src="<?php echo IMG."/".$GLOBALS['LOGO']; ?>" width=60px style='float:left'>
            <h1 style='float:left;font-size:24px;padding:0px;span:0px;'><?php echo $GLOBALS['TITLE']; ?></h1>
         </div>
         <div dojoType="dijit.layout.ContentPane" region="center" gutter="false" style="padding:5px;">
            <div align=center class='bgTop bContainer' style='padding:10px;width:400px;height:150px;'>
               <h3 style='float:left'>Login to <?php echo $GLOBALS['TITLE']; ?></h3>
               <br><br><br>
               <?php 
                    if (isset($_SESSION['username'])){
                       echo after_login();
                  }else{
                     echo before_login();
                  }
               ?>
            </div>
         </div>
      </div>
   </body>
</html>
