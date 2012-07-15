<?php
$GLOBALS['PAGE']=array(
   'name'                =>'users',
   'table'               =>s_t('users'),
   'primary_key'         =>'user_id',
   'filter_table'        =>s_t('filter'),
   'filter_primary_key'  =>'rid',
);

//Common control swithces included
include A_CORE."/ctrl_common.php";

?>
