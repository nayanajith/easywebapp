<?php 
/*---------------------------easy generic paths--------------------------------*/
define('MOD_CLASSES',A_MODULES."/".MODULE."/classes");
define('MOD_CORE',A_MODULES."/".MODULE."/core");
define('MOD_DOCS',W_MODULES."/".MODULE."/docs");
define('INVOICE_DIR',A_MODULES."/".MODULE."/invoices");
define('VOUCHER_DIR',A_MODULES."/".MODULE."/vouchers");
define('MOD_A_ROOT',A_MODULES."/".MODULE);

/*---------------------------configure Database--------------------------------*/
/* $GLOBALS['DB']     = 'payment_gateway';
$GLOBALS['DB_HOST']= 'localhost';
$GLOBALS['DB_USER']= 'root';
$GLOBALS['DB_PASS']= 'letmein';
$GLOBALS['DB_TYPE']= 'mysql';
*/

include_once(MOD_CORE."/database_schema.php");
include_once(MOD_CORE."/database.php");

/*
//convocation registration login
$GLOBALS['MOD_TBL_LOGIN']   = array(
   'db_host'		=>'192.248.16.109', 
   'db_user'		=>'uis', 
   //'db_password'	=>base64_decode('bGV0bWVpbgo='), 
   'db_password'	=>'!@#$%', 
   'db'				=>'moodledb_pg', 

   'table'			=>'mdl_user', 
   'username'		=>'username',
   'password'		=>'password',
   'fullname'		=>'lastname',
   'user_id'		=>'email',
   'permission'	=>'auth'       
);
*/

$GLOBALS['AUTH_MOD']       ='MYSQL'; //authentication modes: LDAP,MYSQL,PASSWD
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
   'username'		=>'email_1',
   'password'		=>'NIC',
   'fullname'		=>'last_name',
   'user_id'		=>'registration_no',
   'rec_id'		   =>'rec_id', //somthing extra
   'permission'	=>'status'       
);



/*---------------------------header modification--------------------------------*/
/*
$GLOBALS['TITLE_SHORT'] = 'Convocation rag';
$GLOBALS['TITLE']       = 'Convocation registration form ';
$GLOBALS['TITLE_LONG']  = 'University of Colombo School of Computing Payment Gateway';
$GLOBALS['LOGO']        = 'ucsc-logo.png';
$GLOBALS['FAVICON']     = 'favicon.ico';
*/

/*--------------------email confirmation key addtion----------------------------*/
$GLOBALS['CONFIRM_ADDITION']=md5('#ucsc$');

/*---------------------------mail configuration---------------------------------*/
$GLOBALS['PAYMENT_ADMIN_MAIL'] = 'UCSC information system <info@ucsc.cmb.ac.lk>';
$GLOBALS['PAYMENT_CLAER_MAIL'] = '<nml@ucsc.cmb.ac.lk>';

/*------------------------------online payment-----------------------------------*/
//Convenience fee tax percentage
$GLOBALS['TAX']=3.093;

/*-----------------------------voucher globals-----------------------------------*/
//offline voucher
$GLOBALS['V_ACC']		="086-1001-011-90483";
$GLOBALS['V_TITLE']	="SHORT TERM COURSE REGISTRATION ".date('Y');
$GLOBALS['V_PURPOSE']="REGISTRATION FEE - %s";

?>
