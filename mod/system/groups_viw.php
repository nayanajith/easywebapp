<?php
add_to_main_left("
<h3>Manage groups</h3>
".get_label('rid').get_field('rid')."
<table>
<tr><td>".get_label('group_name').get_field('group_name')."</td><td>".get_label('file_prefix').get_field('file_prefix')."</td></tr>
<tr><td colspan='2'>".get_label('description').get_field('description')."</td></tr>
</table>");
?>
