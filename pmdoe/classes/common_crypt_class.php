<?php
/*
 * common cryptographic function class
 */
class Common_crypt{
   function __construct(){
   
   }

   /*
    * return a secure random key in given length
    */
   function secure_rand($length=null){
      $length=$length!=null?$length:32;
      return trim(shell_exec("openssl rand -base64 ".$length));
   }
}
?>
