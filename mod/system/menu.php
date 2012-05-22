<?php

$menu_array  = array(
//   "modules"            =>"Modules",
   "users"              =>"Users",
   "role"               =>"Roles",
   "manage_permission"  =>"Users/Group Permission",
   "common_lists"       =>"Common Lists",
   "reset_password"     =>array("PAGE"=>"Reset Password","VISIBLE"=>"false"),
   "activity"           =>"Activity Log",
   "system_log"         =>"System Log",
   "backup"             =>"Backup",
   "db_admin"           =>"Database Administration",
   "init_db"            =>"Regenerate Database",
);

$toolbar   =array(
   "manage_permission"      =>array(
      'Save'   =>array('icon'=>'Save','action'=>'submit_form("modify")'),
   ),
   "system_log"      =>array(
      'Print'   =>array('icon'=>'Print','action'=>'print_data()'),
      'Number of Lines'   =>array('icon'=>'Filter','dojoType'=>'dijit.form.NumberTextBox','name'=>'numLines','id'=>'numLines','style'=>'width:50px','action'=>'null'),
      'Update'   =>array('icon'=>'Process','action'=>'get_lines()')
   )
);


?>
