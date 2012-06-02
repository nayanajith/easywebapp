<?php
/*
link list genertion for the modules

*/
   /*gen_module_array function is in this file*/
   include A_CORE."/manage_module.php";
   /*Generate array with modules and pages */
   $modules_array=gen_visible_module_array();

   /*Add nested tab contgainers for each module*/
   foreach($modules_array as $module_key => $module){
      $module_name=$GLOBALS['MODULES'][$module_key];
      if(is_array($GLOBALS['MODULES'][$module_key])){
         $module_name=$GLOBALS['MODULES'][$module_key]['MODULE'];
      }
       echo "<a href=\"javascript:load_page('$module_key',null,null)\" style='font-weight:bold;text-decoration:none;color:gray'>".$module_name."</a><br>";

      $vbar="";
      foreach($module as $page_key => $page){

         if(is_array($page)){
            $page=$page['PAGE'];
         }
          echo $vbar."<a href=\"javascript:load_page('$module_key','$page_key')\" >$page</a>";
         $vbar=" | ";
      }
      echo "<br><br>";
   }
?>


