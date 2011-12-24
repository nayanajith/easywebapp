<?php

$menu_array  = array(
   "process"      =>"Process ALL",
   "push"         =>"Push students"
);

$toolbar   =array(
   "process"         =>array(
      'Process'      =>array('action'=>'submit_form("process")'),
   ),
   "push"  =>array(
      'CSV'   =>array('icon'=>'Database','action'=>'submit_form("csv")'),
      'Print'   =>array('icon'=>'Print','action'=>'submit_form("print")')
   )
);

?>
