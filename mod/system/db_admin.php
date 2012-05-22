<?php 
include_once(A_CORE.'/database_schema.php');
$arr=exec_query("SELECT * FROM base_data WHERE base_class='VARIABLE' AND base_key='SYSTEM__DB_VERSION'",Q_RET_ARRAY);
$arr=$arr[0];

if($system_table_schema_version != $arr['base_value'] && isset($system_table_migrate[$system_table_schema_version]) ){
   echo "<h4 style='color:red'>Database upgrade required from db_version ".$arr['base_value']." to db_version $system_table_schema_version </h4>";
}else{
   echo "<h4 style='color:blue'>You are with the latest db_version no deeds of upgrades!</h4>";
}
echo "<form>";
echo "<h3>Table</h3>";
$arr=exec_query("SHOW TABLES",Q_RET_ARRAY,null,'Tables_in_'.$GLOBALS['DB']);
$tables=array_keys($arr);
foreach($system_table_schemas as $key => $value){
   if(array_search($key,$tables)){
      echo "<h4>Table <u>$key</u> available</h4>";
      echo "<input type='checkbox' id='regen_$key'><label for='regen_$key'>Backup and recreate $key table</label>";
   }else{
      echo "<h4 style='color:red'>Table <u>$key</u> not available</h4>";
      echo "<input type='checkbox' id='create_$key'><label for='create_$key'>Create $key table</label>";
   }
   echo "<pre class='code'>$value</pre>";
}
echo "</form>";
?>
