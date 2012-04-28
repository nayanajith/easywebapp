<script type='text/javascript'>
//var first_time=true;

/*function will not execute for the fist load*/
function load_page(module,page,program){
//   if(!first_time){
      /*open page*/
      window.open('<?php echo $GLOBALS['PAGE_GEN']; ?>?module='+module+'&page='+page+'&program='+program,'_parent');
//   }else{
//      first_time=false;
//   }
}
</script>

<?php
/*gen_tabs function is in this file*/
include A_CORE."/manage_module.php";

/*Generate array with tabs and sub-tabs for modules and pages*/
$modules_array=gen_visible_module_array();

/*Add nested tab contgainers for each module*/
foreach($modules_array as $module => $pages){
   $module_label=$GLOBALS['MODULES'][$module];

   //If $module is an array this will do 
   if(is_array($GLOBALS['MODULES'][$module]))$module_label=$GLOBALS['MODULES'][$module]['MODULE'];

   /*Active module is selected*/
   if(MODULE == $module){
      echo "<div dojoType='dijit.PopupMenuBarItem'>
         <span>".$module_label."</span>
         <div dojoType='dijit.Menu' id='menubar__".$module."'>\n";
   }else{
    echo "<div dojoType='dijit.PopupMenuBarItem'>
         <span>".$module_label."</span>
         <div dojoType='dijit.Menu' id='menubar__".$module."'>\n";
   }

   /*Add a tab inside the nested tab continer for each page*/
   foreach($pages as $page => $name){

      //if $name is an array this will do
      if(is_array($name))$name=$name['PAGE'];

      /*active tab is selected*/
      if(PAGE == $page){
         echo "<div dojoType='dijit.MenuItem' onClick='alert(\"edit 2\")'>
           ".$name." 
         </div>\n";
      }else{
         echo "<div dojoType='dijit.MenuItem' onClick=\"load_page('".$module."','".$page."','".PROGRAM."')\">
           ".$name." 
         </div>\n";
      }
   }
   echo "</div>\n";
   echo "</div>\n";
}
?>
