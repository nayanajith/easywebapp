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
include_once(A_CORE.'/database_schema.php');
if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
   case 'create_tables':
      create_recreate_tables($system_table_schemas);
   break;
   case 'db_migrate':
      migrate_db($system_table_migrate);
   break;
   }
   return;
}else{
   set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:50%;');
   set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:50%;');

   add_to_main_top("<div><center>".db_migration_form($system_table_schemas,$schema_version,$system_table_migrate)."</center></div>");
   add_to_main_bottom("<div><center>".table_creation_form($system_table_schemas)."</center></div>");
}
?>
