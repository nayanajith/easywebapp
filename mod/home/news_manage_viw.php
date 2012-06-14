<?php
$main_left="<form dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
$main_left.=get_field('rid');
$main_left.="<table>
   <tr>
   <td>".get_label('title').":".get_field('title')."</td>
   </tr>
   <tr>
   <td>".get_label('display_from').":".get_field('display_from')." ".get_label('display_until').":".get_field('display_until')."</td>
   </tr>
   <tr>
   <td>".get_field('content')."</td>
   </tr></table></form>";

add_to_main_left($main_left);
//content.value=this.value
d_r('dijit.Editor');
$content_editor="
<div dojoType='dijit.Editor' jsId='news_editor' ></div>
<script>
function read_editor(){
   content.setValue(news_editor.get(\"value\"));
}

function write_editor(){
   news_editor.setValue(content.getValue());
}

s_f_c_add('before',read_editor);
f_f_c_add('ok',write_editor);

</script>
";
add_to_main_left($content_editor);
add_to_main_right(
   get_pviw_property(array('GRIDS','GRID'))
);


set_layout_property('app2','MAIN_LEFT','style','width','48%');
set_layout_property('app2','MAIN_RIGHT','style','width','50%');

?>
