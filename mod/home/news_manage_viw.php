<?php
$main_left="<form dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
$main_left.=get_field('rid');
$main_left.="<table>
   <tr><td>".get_label('title')."</td><td>".get_field('title')."</td></tr> 
   <tr><td>".get_label('role_id')."</td><td>".get_field('role_id')."</td></tr>
   <tr><td>".get_label('display_from')."</td><td>".get_field('display_from')."</td></tr>
   <tr><td>".get_label('display_until')."</td><td>".get_field('display_until')."</td></tr>
   <tr><td colspan='2'>".get_field('content')."</td></tr>
   </table></form>";

add_to_main_left($main_left);
//content.value=this.value
d_r('dijit.Editor');
$content_editor="<div dojoType='dijit.Editor' jsId='news_editor'
   data-dojo-props=\"plugins:['cut','copy','paste','|','bold','italic','underline','strikethrough','subscript','superscript','|', 'indent', 'outdent', 'justifyLeft', 'justifyCenter', 'justifyRight','|','createLink'],extraPlugins:['foreColor','hiliteColor',{name:'dijit._editor.plugins.FontChoice', command:'fontName', generic:true}]\"
   onMouseOut=\"s_f_c_add('before',read_editor)\"></div>";

js("
function read_editor(){
   content.set('value',news_editor.get('value'));
}

function write_editor(){
   news_editor.set('value',content.get('value'));
}
");

add_to_main_right($content_editor);
add_to_main_bottom(
   get_pviw_property(array('GRIDS','GRID'))
);


set_layout_property('app2','MAIN_LEFT','style','width','48%');
set_layout_property('app2','MAIN_RIGHT','style','width','50%');
set_layout_property('app2','MAIN_BOTTOM','style','height','40%');

?>
