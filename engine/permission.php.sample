<?php
/*These modules have default read access unless expressed externally*/
$permission[]=array( 'module' => 'home', 'page' => '*', 'access_right' => 'WRITE' );

//public access to  module
if(!(isset($_SESSION['loged_module']) && $_SESSION['loged_module']=='home')){
   //public access to registration_pg module
   $permission[]=array( 'module' => 'registration_pg', 'page' => '*', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'registration_pg', 'page' => 'manage_config', 'access_right' => 'DENIED' );
   $permission[]=array( 'module' => 'registration_pg', 'page' => 'manage_programs', 'access_right' => 'DENIED' );
   $permission[]=array( 'module' => 'registration_pg', 'page' => 'reports', 'access_right' => 'DENIED' );

   //public access to payment module
   $permission[]=array( 'module' => 'payment', 'page' => '*', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'payment', 'page' => 'manage_programs', 'access_right' => 'DENIED' );
   $permission[]=array( 'module' => 'payment', 'page' => 'manage_banks', 'access_right' => 'DENIED' );
   $permission[]=array( 'module' => 'payment', 'page' => 'manage_config', 'access_right' => 'DENIED' );
   $permission[]=array( 'module' => 'payment', 'page' => 'manage_pay_for', 'access_right' => 'DENIED' );
   $permission[]=array( 'module' => 'payment', 'page' => 'reports', 'access_right' => 'DENIED' );

   //public access to donation module
   $permission[]=array( 'module' => 'donations', 'page' => 'registration', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'donation_to', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'pay_online', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'pay_offline', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'offline_voucher', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'login', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'captcha', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'reset_password', 'access_right' => 'WRITE' );
   $permission[]=array( 'module' => 'donations', 'page' => 'email_verification', 'access_right' => 'WRITE' );

   //public access to courses 
   $permission[]=array( 'module' => 'ext_courses', 'page' => '*', 'access_right' => 'WRITE' );

}

?>
