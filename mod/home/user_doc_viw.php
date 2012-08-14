<?php
$main_left="<form dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
$main_left.=get_label('doc')." (Write in <a href='http://daringfireball.net/projects/markdown/syntax' target='_NEW'>MARKDOWN</a> syntax)<br>".get_field('doc');
$main_left.="<input type='hidden' name='insec' value='true'>";
$main_left.="</form>";

add_to_main_left($main_left);
include_once "markdown.php";
/*
$doc_arr=exec_query("SELECT * FROM ".s_t('user_doc')." WHERE rid='".get_param('rid')."'",Q_RET_ARRAY);
$doc="";
foreach($doc_arr as $row){
   $doc.=Markdown($row['doc']);
}
 */

//If there is a help file created acordance to the page it will also be loaded
$mod_page=get_param('page_id');
$mod_page=explode('/',$mod_page);
$doc_file=get_doc_file($mod_page[0],$mod_page[1]);
if(file_exists($doc_file) && filesize($doc_file) > 0){
   $fh=fopen($doc_file,'r');
   $content=fread($fh,filesize($doc_file));
   fclose($fh);
   add_to_main_right(
   "<div >Documentation preview:</div><div style='border:1px solid silver;padding:10px'><div width='100%' height='100%' style='background-color:#F5F6CE;padding:5px'>".Markdown($content)."</div></div>"
);


}

set_layout_property('app2','MAIN_LEFT','style','width','48%');
set_layout_property('app2','MAIN_RIGHT','style','width','50%');
?>
