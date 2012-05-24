<?php
$GLOBALS['PAGE']=array(
   'name'                =>'db_admin',
   'table'               =>null,
   'primary_key'         =>null,
   'filter_table'        =>null,
   'filter_primary_key'  =>null,
);

//Common control swithces included
include A_CORE."/ctrl_common.php";
if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
   case 'create_tables':
      create_recreate_tables();
   break;
   case 'db_migrate':
      migrate_db();
   break;
   }
   return;
}else{
   default_();
}
?>
