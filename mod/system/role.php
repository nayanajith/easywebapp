<?php
$GLOBALS['PAGE']=array(
   'name'                =>'role',
   'table'               =>$GLOBALS['S_TABLES']['role'],
   'primary_key'         =>'rid',
   'filter_table'        =>$GLOBALS['S_TABLES']['filter'],
   'filter_primary_key'  =>'rid',
);

//Common control swithces included
include A_CORE."/ctrl_common.php";

?>
