<?php
$main_left="<form dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
$main_left.=get_field('rid');
$main_left.="<table>
   <tr><td>".get_label('role_id')."</td><td>".get_field('role_id')."</td></tr>
   <tr><td>".get_label('program_id')."</td><td>".get_field('program_id')."</td></tr>
   <tr><td>".get_label('module_id')."</td><td>".get_field('module_id')."</td></tr>
   <tr><td>".get_label('page_id')."</td><td>".get_field('page_id')."</td></tr>
   <tr><td colspan='2'>".get_label('doc')."<br>".get_field('doc')."</td></tr>
   </table></form>";

add_to_main_left($main_left);
d_r('dijit.Editor');
//$doc_editor="User doc documentation:<div dojoType='dijit.Editor' jsId='doc_editor' onMouseOut=\"s_f_c_add('before',read_editor)\"></div>";
$doc_editor="User doc documentation:<button dojoType='dijit.form.Button' onClick=\"var editor = new EpicEditor(opts).load();\" >Open Editor</button><br><div id='doc_editor' onMouseOut=\"s_f_c_add('before',read_editor)\"></div>";
js(file_get_contents(A_JS.'/epiceditor/js/epiceditor.js','r'));
js("
//-----------------------------------epic editor----------------------------------------
var opts = {
  container: 'doc_editor',
  basePath: '".JS."/epiceditor',
  clientSideStorage: false,
  localStorageName: 'epiceditor',
  parser: marked,
  file: {
    name: 'epiceditor',
    defaultContent: '',
    autoSave: 100
  },
  theme: {
    base:'/themes/base/epiceditor.css',
    preview:'/themes/preview/preview-dark.css',
    editor:'/themes/editor/epic-dark.css'
  },
  focusOnLoad: false,
  shortcut: {
    modifier: 18,
    fullscreen: 70,
    preview: 80,
    edit: 79
  }
}

//-----------------------------------epic editor----------------------------------------

function read_editor(){
   doc.set('value',editor.getElement('editor').body.innerHTML);
}

function write_editor(){
   var myObject = JSON.parse(localStorage.epiceditor);
   console.log(myObject.epiceditor.content);
   myObject.epiceditor.content='-----------';
   console.log(JSON.stringify(myObject));
   //localStorage.epiceditor=JSON.stringify(myObject);
   //localStorage.epiceditor.content=doc.get('value');
   //editor.getElement('editor').body.innerHTML=doc.get('value');
}
");

//add_to_main_bottom($doc_editor);
/*
add_to_main_right(
   get_pviw_property(array('GRIDS','GRID'))
);
 */

//$doc_arr=exec_query("SELECT * FROM ".s_t('user_doc')." WHERE module_id='"get_param('module_id')."' AND page_id='".get_param('page_id')."' AND role_id='".get_param('role_id')."' AND program_id='".get_param('program_id')."'",Q_RET_ARRAY);
$doc_arr=exec_query("SELECT * FROM ".s_t('user_doc')." WHERE module_id='".get_param('module_id')."' AND page_id='".get_param('page_id')."'",Q_RET_ARRAY);
$doc="";
include_once "markdown.php";
foreach($doc_arr as $row){
   $doc.=Markdown($row['doc']);
}
add_to_main_right(
   "<div><div width='100%' height='100%' style='background-color:white'>$doc</div></div>"
);


set_layout_property('app2','MAIN_LEFT','style','width','48%');
set_layout_property('app2','MAIN_RIGHT','style','width','50%');
//set_layout_property('app2','MAIN_BOTTOM','style','height','50%');

//set_layout_property('app2','MAIN_TOP','style','height','10%');
//add_to_main_top("Introduction to the page");


?>
