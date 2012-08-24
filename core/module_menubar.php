<?php
include A_CORE."/manage_module.php";

/*Generate array with menus and sub-menus for modules and pages*/
$modules_array=gen_visible_module_array();

/*Add nested tab contgainers for each module*/
foreach($modules_array as $module => $pages){
   $module_label=$GLOBALS['MODULES'][$module];

   //If $module is an array this will do 
   if(is_array($GLOBALS['MODULES'][$module]))$module_label=$GLOBALS['MODULES'][$module]['MODULE'];

   /*Active module is selected*/
   if(MODULE == $module){
      echo "<ol dojoType='dijit.PopupMenuBarItem' style='font-weight:bold;' id='menu__".$module."' label='".$module_label."'>
   <ol dojoType='dijit.Menu'>\n";
   }else{
    echo "<ol dojoType='dijit.PopupMenuBarItem' id='menu__".$module."' label='".$module_label."'>
   <ol dojoType='dijit.Menu'>\n";
   }

   /*Add a tab inside the nested tab continer for each page*/
   foreach($pages as $page => $name){
      $tooltip="";
      //if $name is an array this will do
      if(is_array($name)){
         //set tooltip ondemand
         if(isset($name['tooltip']) || isset($name['TOOLTIP'])){
            $tooltip="<div dojoType='dijit.Tooltip' connectId='".$module."__$page' ><div style='max-width:400px;text-align:justify'>".(isset($name['TOOLTIP'])?$name['TOOLTIP']:$name['tooltip'])."</div></div>";
         }
         $name=isset($name['LABEL'])?$name['LABEL']:$name['label'];
      }

      /*active tab is selected*/
      if(PAGE == $page && MODULE == $module){
         //echo "      <li dojoType='dijit.MenuItem' id='".$module."__$page' style='font-weight:bold;'>".$name."</li>$tooltip\n";
         echo "      <li dojoType='dijit.MenuItem' id='".$module."__$page' style='font-weight:bold;' onClick=\"p_m_p('".$module."','".$page."')\">".$name."</li>$tooltip\n";
      }else{
         echo "      <li dojoType='dijit.MenuItem' id='".$module."__$page' onClick=\"p_m_p('".$module."','".$page."')\">".$name."</li>$tooltip\n";
      }
   }
   echo "   </ol>\n";
   echo "</ol>\n";
}
?>
