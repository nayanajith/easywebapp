<?php
$GLOBALS['PAGE']=array(
   'name'                =>'backup',
   'table'               =>null,
   'primary_key'         =>null,
   'filter_table'        =>null,
   'filter_primary_key'  =>null,

);

//Common control swithces included
include A_CORE."/ctrl_common.php";


if(isset($_REQUEST['action']) && $_REQUEST['action']=='add_backup'){
   log_msg('in');
   backup_now();
}


?>
