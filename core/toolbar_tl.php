<?php
d_r("dijit.form.DropDownButton");
?>
<div dojoType="dijit.form.DropDownButton" iconClass="homeIcon" showLabel="false" >
    <span>Home</span>
    <div dojoType="dijit.DropDownMenu">
        <div dojoType="dijit.MenuItem" onClick="show_xhr_dialog('?module=home&page=about&data=dojo','About',400,400)">About</div>
        <div dojoType="dijit.MenuItem" onClick="show_help_dialog()" iconClass="<?php echo get_icon_class('Documents'); ?>">Help</div>
    </div>
</div>
