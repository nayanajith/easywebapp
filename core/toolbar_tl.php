<?php
d_r("dijit.form.DropDownButton");
?>
<div dojoType="dijit.form.DropDownButton" iconClass="homeIcon" showLabel="false" >
    <span>Home</span>
    <div dojoType="dijit.DropDownMenu">
        <div dojoType="dijit.MenuItem" onClick="show_xhr_dialog('?module=home&page=about&data=dojo','About',400,320,true)" iconClass="<?php echo get_icon_class('Documents'); ?>">About</div>
        <div dojoType="dijit.MenuItem" onClick="show_help_dialog()" iconClass="<?php echo get_icon_class('Documents'); ?>">Help</div>
        <div dojoType="dijit.MenuItem" onClick="show_xhr_dialog('?module=home&page=about_framework&data=dojo','About',400,320,true)" iconClass="<?php echo get_icon_class('Documents'); ?>">About Framework</div>
    </div>
</div>
<button dojoType="dijit.form.Button" style="font-weight:bold;color:gray" iconClass="<?php echo get_icon_class('Package') ?>">
<?php echo $GLOBALS['MODULES'][MODULE]." / ".$GLOBALS['MENU_ARRAY'][PAGE]?>
</button>
