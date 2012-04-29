<?php
d_r("dijit.form.DropDownButton");
?>
<div dojoType="dijit.form.DropDownButton" iconClass="homeIcon" showLabel="false" >
    <span>Home</span>
    <div dojoType="dijit.DropDownMenu">
        <div dojoType="dijit.MenuItem" onClick="show_xhr_dialog('?module=home&page=about&data=dojo','title',400,400)">About</div>
        <div dojoType="dijit.MenuItem" onClick="show_help_dialog()">Help</div>
    </div>
</div>
