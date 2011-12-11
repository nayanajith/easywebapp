<?php
/*---------------------------easy generic paths--------------------------------*/
define('MOD_CLASSES',A_MODULES."/".MODULE."/classes");
define('MOD_CORE',A_MODULES."/".MODULE."/core");
define('MOD_DOCS',W_MODULES."/".MODULE."/docs");
define('INVOICE_DIR',A_MODULES."/".MODULE."/invoice");
define('VOUCHER_DIR',A_MODULES."/".MODULE."/voucher");


include_once(MOD_CORE."/database.php");

$GLOBALS['MOD_TBL_LOGIN']   = array(
		  /*
   'db_host'		=>'192.248.16.109', 
   'db_user'		=>'uis', 
   //'db_password'	=>base64_decode('bGV0bWVpbgo='), 
   'db_password'	=>'!@#$%', 
   'db'				=>'moodledb_pg', 
			*/
	'md5'				=>'false',
   'table'			=>$GLOBALS['MOD_P_TABLES']['student'], 
   'username'		=>'email',
   'password'		=>'NIC',
   'fullname'		=>'last_name',
   'user_id'		=>'student_id',
   'home'         => 'personal',
   'permission'	=>'status'       
);

?>
