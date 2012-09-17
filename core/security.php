<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/

if(!isset($_SESSION['username'])){return;}
/*
 * SQL injection prevention
 */
class Secure{

   function secureSuperGlobalGET(&$value, $key)   {

      //If the requested to process the request insecurely ignore the security application
      if(isset($_REQUEST['insec']) && $_REQUEST['insec'] == 'true')return;

      //$value = htmlspecialchars(stripslashes($value));
      if (get_magic_quotes_gpc()){
         $value = stripslashes($value);
      }
      $value = str_ireplace("script", "blocked", $value);

      if($GLOBALS['DB_TYPE']=='mysql'){
         //$value = mysql_escape_string($value);
         $value = mysql_real_escape_string($value);
      }
   }

   function secureGlobals(){
      array_walk($_REQUEST, array($this,'secureSuperGlobalGET'));
   }
}
?>
