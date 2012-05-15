<?php
$GLOBALS['PAGE']=array(
   'name'                =>'news_manage',
   'table'               =>s_t('news'),
   'primary_key'         =>'rid',
   'filter_table'        =>s_t('filter'),
   'filter_primary_key'  =>'',
);

//Common control swithces included
include A_CORE."/ctrl_common.php";

?>
