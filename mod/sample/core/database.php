<?php
//table mapping
$program_tables=array(
   'test'         =>'%stest',          
);

$GLOBALS["MOD_P_TABLES"]=$program_tables;
add_table_prefix($GLOBALS["MOD_P_TABLES"],$schema_prefix);
?>
