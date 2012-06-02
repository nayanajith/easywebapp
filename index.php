<?php
/**
Author: nayanajith mahendra laxaman -> nml@ucsc.lk
*/
/*
if($_SERVER['REMOTE_ADDR'] != '10.16.76.100'){
   echo "<br><br><br><center>System down for maintenance!<br>will be back in few minutes</center>";
   exit();
}
*/
/*redirec through https*/
/*
if($_SERVER['HTTPS']!="on")
{
   $redirect= "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   header("Location:$redirect");
}

*/
/*
if($_SERVER['HTTP_HOST']!="ucsc.lk"){
   $redirect= "https://ucsc.lk".$_SERVER['REQUEST_URI'];
   header("Location:$redirect");
}
*/
//session_cache_limiter('private');
//session_cache_expire(120);
session_start();

//timezone fix for old php versions
date_default_timezone_set('UTC');

/*-----------Dummy session for testing comment at deployment------------------*/
/*
if(isset($_REQUEST['test_key']) && $_REQUEST['test_key']=='1400c95dd934343957b5247e7ef1d19b'){
   $_SESSION['username']       = 'nayanajith';
   $_SESSION['permission']     = '';
   $_SESSION['user_id']        = '0';
   $_SESSION['fullname']        = 'admin user';
}
*/
/*--------------------------Enable disable Errors ----------------------------*/
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors',0);

/*-----------------------advanced php configuration---------------------------*/
ini_set('memory_limit','1024M');
ini_set('max_execution_time','600');
ini_set('max_input_time','120');

/*-------------------Load configuration and common needs ---------------------*/
include "config.php";
include A_CORE."/common.php";

/*--------------------------Set global error log -----------------------------*/
ini_set('error_log',ERROR_LOG);

/*-------if the database configuration file not found install the system------*/
if(file_exists(DB_CONF)){
   include DB_CONF;
}else{ /*Drop to install option*/
   include INSTALLER;
   exit();   
}
/*--------------------------Prevent SQL injection ----------------------------*/
include A_CORE."/security.php";
$secure = new Secure();
$secure->secureGlobals();

/*-----------------Load permission and database functions --------------------*/
include A_CORE."/database.php";
include A_CORE."/permission.php";

/*-----------------Check availability of the database-------------------------*/
if(!opendb()){
   echo "<div style='color:red' align='center' >Database does not available!</div>";
   exit();
}

/*-----------------------------Program selector-------------------------------*/
include A_CORE."/program.php";

/*---------------------------------Load modules-------------------------------*/
include ("modules.php");

///////////////////////////////MIGRATION TO REST////////////////////////////////
if(array_key_exists('PATH_INFO', $_SERVER)) {
   $resource   = $_SERVER['PATH_INFO'];
   $method     = $_SERVER['REQUEST_METHOD'];

   /*
   if($method == 'POST' || $method == 'PUT'){
      parse_str(file_get_contents('php://input'), $_DATA);
   }else{
      $_DATA = $_REQUEST;
   }   

   $_REQUEST            =$_DATA;
    */

   $res=explode('/',$resource);
   $_REQUEST['module']  =$res[1];
   $_REQUEST['page']    =$res[2];
   $_REQUEST['program'] =$res[3];
}

///////////////////////////////////////////////////////////////////////////////

/*--------------------------validate module request---------------------------*/
//System user login exception [ page=slogin is used to login as system user ]
if(isset($_REQUEST['page'])&&$_REQUEST['page']=='slogin'){
   $_REQUEST['module']='home';
}

if (!isset($module)){
   global $module;
   if (!isset($page)){
      global $page;
   }
   if (isset($_REQUEST['module']) && is_module_permitted($_REQUEST['module'])){
      $module = $_REQUEST['module'];
/*---------------------------validate page request----------------------------*/
      if (isset($_REQUEST['page']) && is_page_permitted($module,$_REQUEST['page'])){
         $page = $_REQUEST['page'];
      }else{
         $page = '';
      }
   }else{
      //$module = "Home";
      //In any arror first permitted module will be loaded by default
      foreach($GLOBALS['MODULES'] as $mod => $arr){
         if(is_module_permitted($mod)){
            $module = $mod;
            break;   
         }
      }
   }
}

$GLOBALS['module']   =$module;
define('MODULE'   , $module);
/*-------------------include menu  of the the active module-------------------*/
include (A_MODULES."/".MODULE."/menu.php");
//Set the meny array as a global array $menu_array is from menu.php
$GLOBALS['MENU_ARRAY']=$menu_array;


/*-----If th page is blank load the first permitted page of the module--------*/
if ($page == '' && isset($menu_array)){
   foreach($menu_array as $pg => $arr){
      if(is_page_permitted($module,$pg)){
         $page = $pg;
         break;   
      }
   }
   reset($menu_array);
}

//Define  PAGE  constant
define('PAGE'   , $page);

//get the toolbar items of selected page $toolbar is from menu.php
if(isset($toolbar)&&isset($toolbar[PAGE])){
   $GLOBALS['TOOLBAR_ITEMS']=$toolbar[PAGE];
}


/*--------------------------validate program request--------------------------*/
if (!isset($program)){
   global $program;
   if (isset($_REQUEST['program']) && $_REQUEST['program'] != ''){
      $program = $_REQUEST['program'];
   }else{
      $program = key($programs);
   }
}

/*---------------Generate program tables for the selected program-------------*/
define('PROGRAM'   , $program);
//gen_program_tables($program);


/*----------------include configuration of the the active module--------------*/
include (A_MODULES."/".MODULE."/config.php");

/*---------------------------Global log action manager------------------------*/
include A_CORE."/activity_log.php";

/*-----------------------Login functions are from here------------------------*/
include LOGIN;


/*--------------------------Check for print request---------------------------*/
$GLOBALS['PRINT']=false;
if (isset($_REQUEST['print']) && $_REQUEST['print']=='true'){
   $GLOBALS['PRINT']== true;
}

/*-------------------------Check for data/XHR request-------------------------*/
$GLOBALS['DATA']=false;
if(isset($_REQUEST['data']) || (isset($_REQUEST['action']) )){
   $GLOBALS['DATA']=true;
}


/*----------------------execute data/print request----------------------------*/
//CSV generation request sent to particular page and stop further execution in this page
if($GLOBALS['DATA']||$GLOBALS['PRINT']){
   /*Disable any worning or error messages while providing data*/
   //ini_set('display_errors',0);
   //Special case to retrieve the json for the tree menu
   if(isset($_REQUEST['mod_tree']) && $_REQUEST['mod_tree'] == 'true'){
      include A_CORE."/manage_module.php";
   }else{
      include A_MODULES."/".MODULE."/".PAGE.".php";
   }
   return;
}

/*---------------------------Check for help request---------------------------*/
$GLOBALS['HELP']=false;
if (isset($_REQUEST['help']) && $_REQUEST['help']=='true'){
   $GLOBALS['HELP']=true;
   include A_CORE."/help_layout.php";
   return;
}
/*-------------------------get and set group theme and layout ----------------*/
//will override by user theme and layout
if(isset($_SESSION['role_id'])){
   $group_layout_theme=exec_query("SELECT layout,theme FROM ".s_t('role')." WHERE group_name='".$_SESSION['role_id']."'",Q_RET_ARRAY);

   if(!is_null($group_layout_theme[0]['theme']) && $group_layout_theme[0]['theme'] != "" && $group_layout_theme[0]['theme'] != "NULL"){
      $GLOBALS['THEME']=$group_layout_theme[0]['theme'];
   }

   if(!is_null($group_layout_theme[0]['layout']) && $group_layout_theme[0]['layout'] != "" && $group_layout_theme[0]['layout'] != "NULL"){
      $GLOBALS['LAYOUT']=$group_layout_theme[0]['layout'];
   }
}
/*--------------------------get and set users theme and layout----------------*/
if(isset($_SESSION['user_id'])){
   $user_layout_theme=exec_query("SELECT layout,theme FROM ".s_t('users')." WHERE user_id='".$_SESSION['user_id']."'",Q_RET_ARRAY);

   if(!is_null($user_layout_theme[0]['theme']) && $user_layout_theme[0]['theme'] != "" && $user_layout_theme[0]['theme'] != "NULL"){
      $GLOBALS['THEME']=$user_layout_theme[0]['theme'];
   }

   if(!is_null($user_layout_theme[0]['layout']) && $user_layout_theme[0]['layout'] != "" && $user_layout_theme[0]['layout'] != "NULL"){
      $GLOBALS['LAYOUT']=$user_layout_theme[0]['layout'];
   }
}

/*custom layout can be set from url for testing*/
$GLOBALS['LAYOUT']=isset($_REQUEST['layout'])?$_REQUEST['layout']:$GLOBALS['LAYOUT'];


/*---------------------Redirect if requested in session------------------------*/
if(isset($_SESSION['REDIRECT']) && $_SESSION['REDIRECT'] != ''){
   $redirect=$_SESSION['REDIRECT'];
   unset($_SESSION['REDIRECT']);
   header("Location:".$redirect);
}

/*-----------------------------------------------------------------------------*/
/**
 * Process the request related to page and insert into view array which will be used in layouts
 */
include A_CORE."/assembler.php";

/*----------------Nothing will printed/echoed above this line-----------------*/
/*----------------------------------------------------------------------------*/
/*----------------------------HTML started below------------------------------*/
/*--------------start output buffering for html compression-------------------*/
if(COMPRESS=='YES'){
   ob_start('ob_gzhandler');
}

/*---------------------include layout web,app or pub--------------------------*/
include A_CORE."/".$GLOBALS['LAYOUT']."_layout.php";

/*------------IF compression enabled compress the page and out----------------*/
if(COMPRESS=='YES'){
   /*extract output to $out*/
   $out = ob_get_contents();

   /*end output buffering*/
   ob_end_clean();

   /*echo compressed output*/
   echo gzencode(trim($out,"\n\t"));

   //echo trim($out,"\n\t");
}

?>
