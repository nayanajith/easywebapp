<?php
d_r("dijit.form.Button");
?>
<div dojoType="dijit.form.DropDownButton" iconClass="<?php echo get_icon_class('Users'); ?>" showLabel="true">
<span><?php echo $_SESSION['username'];?></span>
  <div dojoType="dijit.TooltipDialog" style="width:200px;">
<?php 
   if (isset($_SESSION['username'])){
      echo after_login();
   }
?>
  </div>
</div>

<div dojoType="dijit.form.DropDownButton" iconClass="GFIcon" showLabel="false">
  <span>Register</span>
  <div dojoType="dijit.TooltipDialog" style="width:200px;">
     <input dojoType="dijit.form.TextBox" id="hobby" name="hobby"><button dojoType="dijit.form.Button" type="submit">Find</button>
  </div>
</div>
