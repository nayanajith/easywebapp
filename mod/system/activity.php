<?php
$GLOBALS['PAGE']=array(
   'name'                =>'activity',
   'table'               =>s_t('log'),
   'primary_key'         =>'rid',
   'filter_table'        =>s_t('filter'),
   'filter_primary_key'  =>'rid',
);

//Common control swithces included
include A_CORE."/ctrl_common.php";

?>
