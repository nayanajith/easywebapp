<script type='text/javascript' >
function open_module(module){
   window.open('<?php echo $GLOBALS['PAGE_GEN']; ?>?module='+module+'&program=<?php echo PROGRAM; ?>','_parent');
}

function open_page(module,page){
   window.open('<?php echo $GLOBALS['PAGE_GEN']; ?>?module='+module+'&page='+page+'&program=<?php echo PROGRAM; ?>','_parent');
}
</script>

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
      $module_name=$modules[$module_key];
      if(is_array($modules[$module_key])){
         $module_name=$modules[$module_key]['MODULE'];
      }
       echo "<a href=\"javascript:open_module('$module_key')\" style='font-weight:bold;text-decoration:none;color:gray'>".$module_name."</a><br>";

      $vbar="";
      foreach($module as $page_key => $page){

         if(is_array($page)){
            $page=$page['PAGE'];
         }
          echo $vbar."<a href=\"javascript:open_page('$module_key','$page_key')\" >$page</a>";
         $vbar=" | ";
      }
      echo "<br><br>";
   }
?>


