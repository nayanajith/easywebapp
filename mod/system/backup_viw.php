<?php
d_r('dijit.form.Form');
d_r('dijit.form.CheckBox');

add_to_main_left("<form dojoType='dijit.form.Form' jsId='main' id='main' name='main'>");
add_to_main_left(list_backups());
add_to_main_left("</form>");
?>
