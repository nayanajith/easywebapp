<script type="text/javascript">
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
/*
tab bar genertion for the modules

*/
   /*gen_tabs function is in this file*/
   include A_CORE."/manage_module.php";

   /*Generate array with tabs and sub-tabs for modules and pages*/
   //$modules_array=gen_module_array();
   $modules_array=gen_visible_module_array();

   /*Parent tabcontainer*/
   d_r('dijit.layout.TabContainer');
   echo "<div dojoType='dijit.layout.TabContainer' style='width: 400px; border:0px;'>";

   /*Add nested tab contgainers for each module*/
   foreach($modules_array as $module => $pages){
      $module_label=$modules[$module];

      //If $module is an array this will do 
      if(is_array($modules[$module]))$module_label=$modules[$module]['MODULE'];

      /*Active module is selected*/
      if(MODULE == $module){
         d_r('dijit.layout.TabContainer');
          echo "<div selected='true' dojoType='dijit.layout.TabContainer' title='".$module_label."' nested='true'>";
      }else{
         d_r('dijit.layout.TabContainer');
          echo "<div dojoType='dijit.layout.TabContainer' title='".$module_label."' nested='true'>";
      }

      /*Add a tab inside the nested tab continer for each page*/
      foreach($pages as $page => $name){

         //if $name is an array this will do
         if(is_array($name))$name=$name['PAGE'];

         /*active tab is selected*/
         if(PAGE == $page){
            d_r('dijit.layout.ContentPane');
            echo "<div selected='true' dojoType='dijit.layout.ContentPane' title='".$name."' >";
         }else{
            d_r('dijit.layout.ContentPane');
            echo "<div dojoType='dijit.layout.ContentPane' title='".$name."' onShow=\"load_page('".$module."','".$page."','".PROGRAM."')\">";
         }
           echo"</div>";
      }
      echo "</div>";
   }
   echo "</div>";
?>
