<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php
$GLOBALS['VIEW']=array(
   'CSS'       =>'',
   'JS'        =>'',
   'LOADING'   =>'',
   'LOGIN'     =>'',
   'PROGRAM'   =>'',
   'BREADCRUMB'=>'',
   'BODY'      =>'',
   'NAVIGATOR' =>'',
   'TOOLBAR'   =>'',
   'STATUSBAR' =>'',
   'FOOTER'    =>''
);

/**
 * Include the file and generated contet will be putting in global view array
 */
function fill_view($key,$file){
   if(isset($GLOBALS['VIEW'][$key])){
      ob_start();
      //ob_start('view');
      include $file;
      $GLOBALS['VIEW'][$key] .= ob_get_contents();
      ob_end_clean();
   }else{
      return "key[$key] error!"; 
   }
}


fill_view('FOOTER',A_CORE."/footer.php");
fill_view('TOOLBAR',A_CORE."/toolbar.php");
fill_view('STATUSBAR',A_CORE."/status_bar.php");

$body='';
if(!isset($_SESSION['username'])){
   if(file_exists(A_MODULES."/".MODULE."/about.php")){
      $body=A_MODULES."/".MODULE."/about.php";
   }else{
      $body="error.php";
   }
}elseif(!file_exists(A_MODULES."/".MODULE."/".PAGE.".php")){
   $body="error.php";
}else{
   $body=A_MODULES."/".MODULE."/".PAGE.".php";
}


ob_start();
program_select($program); 
$GLOBALS['VIEW']['PROGRAM'] .= ob_get_contents();
ob_end_clean();

fill_view('NAVIGATOR', A_CORE."/module_tab_bar.php");
fill_view('BODY',$body);

ob_start();
if (isset($_SESSION['username'])){
   echo after_login();
}else{
   echo before_login();
}
$GLOBALS['VIEW']['LOGIN'] .= ob_get_contents();
ob_end_clean();

fill_view('LOADING', A_CORE."/loading.php");
fill_view('JS', A_CORE."/dojo_require.php");
fill_view('JS', A_CORE."/status_bar_func.php");
fill_view('CSS', A_CORE."/style.php");
?>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<html>
   <head>
      <title><?php echo MODULE."&gt;".PAGE.":".$GLOBALS['TITLE']; ?></title>
      <link rel="search" href="/search" >

<!--_________________________________CSS_____________________________________-->
      <?php 
         echo $GLOBALS['VIEW']['CSS'];
         //include A_CORE."/style.php";
      ?>
<!--______________________________FAVICON____________________________________-->
      <link rel="shortcut icon" href="<?php echo IMG."/".$GLOBALS['FAVICON']; ?>"type="image/x-icon" >

<!--______________________DOJO JAVASCRIPT load modules_______________________-->
      <?php 
         echo $GLOBALS['VIEW']['JS'];
         /*
         include A_CORE."/dojo_require.php";
         include A_CORE."/status_bar_func.php";
          */
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
      //include A_CORE."/loading.php";
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

               /*
               if (isset($_SESSION['username'])){
                  echo after_login();
               }else{
                  echo before_login();
               }
               */
            ?>
<!--______________________________end Login form ____________________________-->

<!--_________________________start Program selector__________________________-->
            <?php 
               echo "Change Program:";
               echo $GLOBALS['VIEW']['PROGRAM'];
               //program_select($program); 
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
                        //include A_CORE."/module_tab_bar.php";
                     ?>
                  </div>
                  <!--end TOP box of BorderContainer-2 (BorderContainer-3)-->

                  <!--CENTER box of BorderContainer-2-->
                  <div dojoType="dijit.layout.ContentPane" region="center" id="data_body" class="bgBottom" style="padding:5px;">
<!--________________________start data_body area_____________________________-->
                     <?php 

                     echo $GLOBALS['VIEW']['BODY'];
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

                        echo $GLOBALS['VIEW']['TOOLBAR'];
                        echo $GLOBALS['VIEW']['STATUSBAR'];
                        //include A_CORE."/toolbar.php";
                        //include A_CORE."/status_bar.php";
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
               //include A_CORE."/footer.php";
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
