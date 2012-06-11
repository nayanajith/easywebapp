<?php
$main_top="<h2 style='float:left'>Filter Fields</h2>";
$main_top.="<center><form dojoType='dijit.form.Form' id='main' jsId='main' name='main' method='POST' ><table>";

$main_top.="<tr>
   <td>".get_label('proto')."</td><td>".get_field('proto')."      </td>
   <td>".get_label('timestamp')."</td><td>".get_field('timestamp')."            </td>
   <td>".get_label('user_id')."</td><td>".get_field('user_id')."    </td>
   <td>".get_label('ip')."</td><td>".get_field('ip')."    </td>
   </tr><tr>
   <td>".get_label('module_id')."</td><td>".get_field('module_id')."    </td>
   <td>".get_label('page_id')."</td><td>".get_field('page_id')."    </td>
   <td>".get_label('cmid')."</td><td>".get_field('cmid')."    </td>
   <td>".get_label('action_')."</td><td>".get_field('action_')."    </td>
   </tr><tr>
   <td>".get_label('url')."</td><td>".get_field('url')."    </td>
   <td>".get_label('host')."</td><td>".get_field('host')."    </td>
   <td>".get_label('info')."</td><td>".get_field('info')."    </td>
   </tr><tr>
   <td >".get_label('agent')."</td><td colspan='7'>".get_field('agent')."    </td>
   </tr><tr>
   <td >".get_label('request')."</td><td colspan='7'>".get_field('request')."    </td>
   </tr>";

$main_top.="</table></form></center>";

add_to_main_top($main_top);
add_to_main_bottom('<h2>Activity LOG</h2>');
add_to_main_bottom(get_pviw_property(array('GRIDS','GRID')));
add_to_main_bottom("<script>dojo.ready(function(){fill_filter_form()});</script>");

set_layout_property('app2','MAIN_TOP','style','padding:0px;height:28%');
set_layout_property('app2','MAIN_BOTTOM','style','padding:0px;height:72%');
?>
