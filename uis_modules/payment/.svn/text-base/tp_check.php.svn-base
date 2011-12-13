<?php
include(MOD_class."/crypt.php");
$program_arr		=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['program']." WHERE program_code='BIT'",Q_RET_ARRAY);
$message_crypt 	= new Message_crypt($program_arr[0]['tp_key']);
print_r($message_crypt->getReceipt($_REQUEST['receipt']));
?>
