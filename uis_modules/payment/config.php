<?php 
/*---------------------------easy generic paths--------------------------------*/
define('MOD_CLASSES',A_MODULES."/".MODULE."/classes");
define('MOD_A_CLASSES',A_MODULES."/".MODULE."/classes");
define('MOD_CORE',A_MODULES."/".MODULE."/core");
define('MOD_A_ROOT',A_MODULES."/".MODULE);
define('BANK_A_ROOT',A_MODULES."/".MODULE."/banks");
define('INVOICE_DIR',A_MODULES."/".MODULE."/invoice");

/*---------------------------configure Database--------------------------------*/
/*
$GLOBALS['DB']     = 'payment_gateway';
$GLOBALS['DB_HOST']= 'localhost';
$GLOBALS['DB_USER']= 'root';
$GLOBALS['DB_PASS']= 'letmein';
$GLOBALS['DB_TYPE']= 'mysql';
*/
include_once(MOD_CORE."/database_schema.php");
include_once(MOD_CORE."/database.php");

$GLOBALS['MOD_TBL_LOGIN']   = array(
   'table'     =>$GLOBALS['MOD_S_TABLES']['employee'], 
   'username'  =>'email',
   'password'  =>'password',
   'fullname'  =>'first_name',
   'user_id'  	=>'employee_id',
   'permission'=>'registration_type'       
);


/*---------------------------header modification--------------------------------*/
$GLOBALS['TITLE_SHORT'] = 'PAYMENTGW';
//$GLOBALS['TITLE']       = 'Payment Gateway';
$GLOBALS['TITLE']       = 'University of Colombo School of Computing';
$GLOBALS['TITLE_LONG']  = 'University of Colombo School of Computing Payment Gateway';
$GLOBALS['LOGO']        = 'ucsc-logo.png';
$GLOBALS['FAVICON']     = 'favicon.ico';

/*------------------------------mail information--------------------------------*/
$GLOBALS['PAYMENT_ADMIN_MAIL'] = 'UCSC information system <info@ucsc.cmb.ac.lk>';
$GLOBALS['PAYMENT_CLAER_MAIL'] = '<nml@ucsc.cmb.ac.lk>';
$GLOBALS['PAYMENT_CLAER_MAIL'] = '<nih@ucsc.cmb.ac.lk>';

/*---------------------------path to bank config--------------------------------*/
include(BANK_A_ROOT."/config.php");
$GLOBALS['TAX'] = 3.093;


/*--------------------email confirmation key addtion----------------------------*/
$GLOBALS['CONFIRM_ADDITION']=md5('#ucsc$');

?>
