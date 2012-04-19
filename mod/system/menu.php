<?php

$menu_array  = array(
//   "modules"            =>"Modules",
   "manage_users"       =>"Users",
   "users"              =>"Users2",
   "groups"             =>"Groups",
   "manage_permission"  =>"Users/Group Permission",
   "common_lists"       =>"Common Lists",
   "reset_password"     =>array("PAGE"=>"Reset Password","VISIBLE"=>"false"),
   "activity"           =>"Activity Log",
   "system_log"         =>"System Log",
   "backup"             =>"Backup",
   "init_db"            =>">Regenerate Database<"
);

$toolbar   =array(
   "manage_users"      =>array(
      'Add'      =>array('icon'=>'NewPage','action'=>'submit_form("add")'),
      'Save'   =>array('icon'=>'Save','action'=>'submit_form("modify")'),
      'Delete'   =>array('icon'=>'Delete','action'=>'submit_form("delete")'),
   ),
   "manage_permission"      =>array(
      'Save'   =>array('icon'=>'Save','action'=>'submit_form("modify")'),
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
