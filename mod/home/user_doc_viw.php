<?php
$main_left="<form dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
$main_left.=get_field('rid');
$main_left.="<table>
   <tr><td>".get_label('role_id')."</td><td>".get_field('role_id')."</td></tr>
   <tr><td>".get_label('program_id')."</td><td>".get_field('program_id')."</td></tr>
   <tr><td>".get_label('module_id')."</td><td>".get_field('module_id')."</td></tr>
   <tr><td>".get_label('page_id')."</td><td>".get_field('page_id')."</td></tr>
   <tr><td colspan='2'>".get_label('doc')." (Write in <a href='http://daringfireball.net/projects/markdown/syntax' target='_NEW'>MARKDOWN</a> syntax)<br>".get_field('doc')." </td></tr>
   </table></form>";

add_to_main_left($main_left);

$doc_arr=exec_query("SELECT * FROM ".s_t('user_doc')." WHERE rid='".get_param('rid')."'",Q_RET_ARRAY);
$doc="";
include_once "markdown.php";
foreach($doc_arr as $row){
   $doc.=Markdown($row['doc']);
}
add_to_main_right(
   "<div style='border:1px solid silver;padding:10px'>Documentation preview:<div width='100%' height='100%' style='background-color:#F5F6CE;padding:5px'>$doc</div></div>"
);


set_layout_property('app2','MAIN_LEFT','style','width','48%');
set_layout_property('app2','MAIN_RIGHT','style','width','50%');
?>
