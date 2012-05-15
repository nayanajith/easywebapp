<?php
$main_left="<center><form dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
$main_left.=get_field('rid');
$main_left.="<table>
   <tr>
   <td>".get_label('title').":".get_field('title')."</td>
   </tr>
   <tr>
   <td>".get_label('display_from').":".get_field('display_from').get_label('display_to').":".get_field('display_to')."</td>
   </tr>
   <tr>
   <td>".get_field('content')."</td>
   </tr></table></form>";

add_to_main_left($main_left);
//content.value=this.value
d_r('dijit.Editor');
$content_editor="
<div dojoType='dijit.Editor' jsId='news_editor' ></div></center>
<script>
   function read_editor_value(){
      content.setValue(news_editor.get(\"value\"));
   }
   function load_editor_value(){
      news_editor.setValue(content.getValue());
   }
submit_form_callback['before']=read_editor_value;
fill_form_callback['ok']=load_editor_value;
</script>
";
add_to_main_left($content_editor);
add_to_main_right(
   $GLOBALS['PREVIEW']['MAIN_RIGHT']['GRID']
);

?>
