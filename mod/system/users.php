<?php
$GLOBALS['PAGE']=array(
   'name'                =>'users',
   'table'               =>$GLOBALS['S_TABLES']['users'],
   'primary_key'         =>'user_id',
   'filter_table'        =>$GLOBALS['S_TABLES']['filter'],
   'filter_primary_key'  =>'rid',
);

//Common control swithces included
include A_CORE."/ctrl_common.php";

?>
