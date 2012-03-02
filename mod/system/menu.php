<?php

$menu_array  = array(
   "manage_users"       =>"System Users",
   "groups"              =>"System Groups",
   "manage_permission"  =>"System Users Permission",
   "activity"           =>"Activity Log",
   "system_log"         =>"System Log",
   "init_db"            =>">Regenerate Database<"
);

$toolbar   =array(
   "manage_users"      =>array(
      'Add'      =>array('icon'=>'NewPage','action'=>'submit_form("add")'),
      'Save'   =>array('icon'=>'Save','action'=>'submit_form("modify")'),
      'Delete'   =>array('icon'=>'Delete','action'=>'submit_form("delete")'),
      'Search'   =>array('icon'=>'Search'),
      'CSV'      =>array('icon'=>'Database','action'=>'get_csv()'),
      'Grid'   =>array('icon'=>'Table','action'=>'grid()'),
      'Print'   =>array('icon'=>'Print'),
      'Add Filter'   =>array('icon'=>'Filter','action'=>'show_dialog()','dojoType'=>'dijit.form.Button','label'=>'Add Filter')
   ),
   "manage_permission"      =>array(
      'Save'   =>array('icon'=>'Save','action'=>'submit_form("modify")'),
      'CSV'      =>array('icon'=>'Database','action'=>'get_csv()'),
      'Grid'   =>array('icon'=>'Table','action'=>'grid()'),
      'Print'   =>array('icon'=>'Print'),
      'Add Filter'   =>array('icon'=>'Filter','action'=>'show_dialog()','dojoType'=>'dijit.form.Button','label'=>'Add Filter')
   ),
   "activity"      =>array(
      'CSV'      =>array('icon'=>'Database','action'=>'get_csv()'),
      'Grid'   =>array('icon'=>'Table','action'=>'grid()'),
      'Print'   =>array('icon'=>'Print'),
      'Add Filter'   =>array('icon'=>'Filter','action'=>'show_dialog()','dojoType'=>'dijit.form.Button','label'=>'Add Filter')
   ),
   "system_log"      =>array(
      'Print'   =>array('icon'=>'Print','action'=>'print_data()'),
      'Number of Lines'   =>array('icon'=>'Filter','dojoType'=>'dijit.form.NumberTextBox','name'=>'numLines','id'=>'numLines','style'=>'width:50px','action'=>'null'),
      'Update'   =>array('icon'=>'Process','action'=>'get_lines()')
   )
);


?>
