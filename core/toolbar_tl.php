<?php
d_r("dijit.form.DropDownButton");

//call p_m_p and reload the enviorenment when clicking on the breadcrumb
$pmp="onClick=\"p_m_p('".MODULE."','".PAGE."','".PROGRAM."')\"";

?>
<div dojoType="dijit.form.DropDownButton" iconClass="homeIcon" showLabel="false" >
    <span>Home</span>
    <div dojoType="dijit.DropDownMenu">
        <div dojoType="dijit.MenuItem" onClick="show_xhr_dialog('?module=home&page=about&data=dojo','About',400,320,true)" iconClass="<?php echo get_icon_class('Documents'); ?>">About</div>
        <div dojoType="dijit.MenuItem" onClick="show_help_dialog()" iconClass="<?php echo get_icon_class('Documents'); ?>">Help</div>
        <div dojoType="dijit.MenuItem" onClick="show_xhr_dialog('?module=home&page=about_framework&data=dojo','About',400,320,true)" iconClass="<?php echo get_icon_class('Documents'); ?>">About Framework</div>
    </div>
</div>
<button dojoType="dijit.form.Button" style="font-weight:bold;color:gray" id="breadcrumb" <?php echo $pmp ?>  iconClass="<?php echo get_icon_class('Package') ?>">
<?php  
//Limit the max length of the label
$max_length=35;
$bc=module_name(MODULE)." / ".page_name(PAGE);
if(strlen($bc) > $max_length){
   //Last letters
   $ll=substr($bc,strlen($bc)-2,strlen($bc));
   $bc=substr($bc,0,$max_length)."~$ll";
}
echo $bc;  
?>
</button>
