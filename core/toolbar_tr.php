<div dojoType="dijit.form.DropDownButton" iconClass="notifyIcon" showLabel="true" id='notify_ddb' >
  <span ></span>
  <div 
   dojoType="dijit.TooltipDialog" 
   style="max-width:400px;overflow:wrap" 
   jsId='notify_ttd' 
   loadingMessage="" 
   refreshOnShow=true 
   preventCache=true 
   href="<?php echo gen_url() ?>section=NOTIFY">
      <!-- content will load dynamically -->
  </div>
</div>

<?php
d_r("dijit.form.Button");

echo get_program();
?>
<div dojoType="dijit.form.DropDownButton" iconClass="<?php echo get_icon_class('Users'); ?>" showLabel="true" title='USER (ROLE)'>
<span><?php echo $_SESSION['username'];?> (<?php echo $_SESSION['role_id']?>)</span>
  <div dojoType="dijit.TooltipDialog" style="width:300px;">
<?php 
   if (isset($_SESSION['username'])){
      echo after_login();
   }
?>
  </div>
</div>
<?php
//Generate the page array
$page_array=array();
foreach ($GLOBALS['MODULES'] as $mod_key => $mod) {
   $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
   if(file_exists($module_menu_file)){
      include($module_menu_file);
      foreach($menu_array as $page_key => $page){
         //Get the page name
         if(is_array($page)){
            $page=$page['label'];
         }
         
         //Get the module name
         $module=$GLOBALS['MODULES'][$mod_key];
         if(is_array($module)){
            $module=$module['MODULE'];
         }
         $page_array[$mod_key."/".$page_key]=$module."/".$page;
      }
   }
}
?>

<div dojoType="dijit.form.DropDownButton" iconClass="GFIcon" showLabel="false">
  <span>Quick Access</span>
  <div dojoType="dijit.TooltipDialog" style="width:250px;">
      <select 
      data-dojo-props="placeHolder:'Quick Access'"
      dojoType='dijit.form.FilteringSelect'
      required='false'
      hasDownArrow="false"
      autoComplete="false"
      onChange='alert("TODO:load selected page("+this.value+")")'
      pageSize='10'
      title='Quick Access'
      style='width:220px;'>
         <?php echo gen_select_inner($page_array,null,true) ?>
      </select>
  </div>
</div>
