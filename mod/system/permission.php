<?php
$GLOBALS['PAGE']=array(
   'name'                =>'permission',
   'table'               =>s_t('permission'),
   'primary_key'         =>'rid',
   'filter_table'        =>s_t('filter'),
   'filter_primary_key'  =>'rid',
);

//Common control swithces included
include A_CORE."/ctrl_common.php";
?>
