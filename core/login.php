<?php

/*
This will include in to $GLOBALS['PAGE_GEN']
*/
$user    =null;
$password=null;
$logout  =null;

if(isset($_REQUEST['user']) && isset($_REQUEST['password'])){
   $user       = $_REQUEST['user'];
   $password    = $_REQUEST['password'];
}

if(isset($_REQUEST['logout'])){
   $logout       = $_REQUEST['logout'];
}

/*
If the login was done by a module and if the user try to escape out from it end the session
if(isset($_SESSION['login_module']) && $_SESSION['login_module'] != MODULE)
{
   $_SESSION['username']    = null;
   $_SESSION['permission'] = null;
   $_SESSION['fullname']    = null;
   $_SESSION['user_id']    = null;
   $_SESSION['login_module']    = null;
   session_destroy();
}
*/

function before_login() {
   d_r("dijit.form.ValidationTextBox");
   d_r("dijit.form.Form");
   d_r("dijit.form.Button");
   $page=PAGE;
   if(isset($GLOBALS['MOD_TBL_LOGIN']['target'])){
      $page=$GLOBALS['MOD_TBL_LOGIN']['target'];
   }


   $server=$_SERVER['HTTP_HOST'];

   //If the system behind proxy bind proxy address for the login form
   if(isset($GLOBALS['PROXY_HOSTS']) && is_array($GLOBALS['PROXY_HOSTS'])){
      foreach($GLOBALS['PROXY_HOSTS'] as $ip => $hostname){
         if($_SERVER['REMOTE_ADDR'] == $ip){
            $server=$hostname;
         }
      }
   }

   return '
   <div dojoType="dijit.form.Form" id="loginForm" jsId="loginForm" encType="multipart/form-data"
   action="'.$GLOBALS['AUTH_PROTOCOLE'].'://'.$server.$_SERVER['SCRIPT_NAME'].'?module='.MODULE.'&page='.$page.'" method="POST"
   onSubmit="if(loginForm.validate()){return true;}else{return false}"
   >
'.
(isset($_REQUEST['user'])?'<div style="padding:5px;color:red;">Invallid login please try again...</div>':"")
.'
            <input type="hidden" name="module" value="'.MODULE.'" >
            <input type="hidden" name="page" value="'.$page.'" >
            <table cellpadding=0 cellspacing=0 >
                <tr>
                    <td>
                        <label for="user">
                            Username:
                        </label>
                    </td>
                    <td>
                        <input type="text" id="user" name="user" required="true" style="color:black;width:60px" 
                        dojoType="dijit.form.ValidationTextBox"
                        value="'.(isset($_REQUEST['user'])?$_REQUEST['user']:"").'"
                        >
                    </td>
                    <td>
                        <label for="password">
                            &nbsp;Password:
                        </label>
                    </td>
                    <td>
                        <input type="password" id="password" name="password" required="true" style="color:black;width:60px" 
                        dojoType="dijit.form.ValidationTextBox"
                        value="'.(isset($_REQUEST['password'])?$_REQUEST['password']:"").'"
                        >
                    </td>
                    <td>
                        <button dojoType="dijit.form.Button" type="submit" name="loginBtn" value="Submit" >
                            Login   
                        </button>
                    </td>
            </table>

        </div>';   

/*Set redirect url to redirect page to previouse location*/
$_SESSION['REDIRECT']="?page=".$_SESSION['PREV_PAGE']."&module=".$_SESSION['PREV_MODULE'];
/*Unset prev module and pages from session*/
unset($_SESSION['PREV_PAGE']);
unset($_SESSION['PREV_MODULE']);
}

function after_login() {
   global $prev_page;
   global $module;
   if (!$prev_page)
   $prev_page = 'home';

   d_r("dijit.form.ValidationTextBox");
   d_r("dijit.form.Form");
   d_r("dijit.form.Button");
   d_r("dijit.form.FilteringSelect");

   //Super users have privilege to change the users to check whtat the user can do so they have special varialble to notify the system 
   //that he is a super user
   if($_SESSION['group_id']=='SUPER'){
      $_SESSION['SUPER_USER']=true;
   }

   //User changing select box
   $user_changer="";
   if(isset($_SESSION['SUPER_USER']) && $_SESSION['SUPER_USER']){
      $arr=exec_query('SELECT username,user_id FROM '.$GLOBALS['S_TABLES']['users'],Q_RET_ARRAY,null,'user_id');
      $inner=gen_select_inner($arr,'username',true);
      $user_changer="Switch user<select dojoType='dijit.form.FilteringSelect' value='".$_SESSION['user_id']."' style='width:90px' id='switch_user' onChange='switch_user(this.value)'>$inner</select>";
   }

   return "
   <div dojoType='dijit.form.Form' id='loginForm' jsId='loginForm' encType='multipart/form-data' 
   action='".$GLOBALS['PAGE_GEN']."?page=".PAGE."&module=".MODULE."' method='REQUEST' >
   <span>Loged in as ".$_SESSION['fullname']."</span>
   <button dojoType='dijit.form.Button' style='color:black;' type=submit name=logout value=logout>Logout</button>
   $user_changer
   </div>";
}

$RESULT      =null;
$RESULT_ARR   =null;
$LOGIN      =false;

if (isset($_SESSION['username'])) {
   if ($logout == "logout") {
      unset($_SESSION['username']);
      unset($_SESSION['permission']);
      unset($_SESSION['group']);
      unset($_SESSION['fullname']);
      unset($_SESSION['user_id']);
      unset($_SESSION['login_module']);
      session_destroy();

      //After logout take the user to home
      header('Location: ?module=home');
   }
} elseif(isset($_REQUEST['loginBtn'])){
   //Log users activity
   act_log(null,null);


   //login database and other information can be configured modulewise 
   if(isset($GLOBALS['MOD_TBL_LOGIN'])){
      $GLOBALS['TBL_LOGIN']=$GLOBALS['MOD_TBL_LOGIN'];
      $_SESSION['login_module']    = MODULE;
   }

      //do externel database based authentication using custom database connection if requested in the configuration
      if(isset($GLOBALS['TBL_LOGIN']['db_host'])){
         $GLOBALS['CONNECTION'] = mysql_connect($GLOBALS['TBL_LOGIN']['db_host'], $GLOBALS['TBL_LOGIN']['db_user'], $GLOBALS['TBL_LOGIN']['db_password']);
         if(!mysql_select_DB($GLOBALS['TBL_LOGIN']['db'], $GLOBALS['CONNECTION'])){
            $db_avail=false;
         }
      }

      //Check where the authentication from externel server
      switch($GLOBALS['AUTH_MOD']){
      case 'LDAP':

         //Getting user information for the given ldap user from the given login table
         $SQL = "SELECT * FROM ".$GLOBALS['TBL_LOGIN']['table']." WHERE ldap_user_id='$user'";
         $RESULT_ARR    = exec_query($SQL, Q_RET_ARRAY);

         //If there is a user for the given ldap user id check the password from the ldap server
         if (isset($RESULT_ARR[0])) {
        
            //Connect to the ldap server and authenticate the user
            $LDAPConnection    =    ldap_connect($GLOBALS['LDAP_SERVER'],$GLOBALS['LDAP_PORT']);
            ldap_set_option($LDAPConnection,LDAP_OPT_PROTOCOL_VERSION,3);

            //If bind successful that means authenticated
            if(!@ldap_bind($LDAPConnection,sprintf($GLOBALS['LDAP_BIND_RDN'],$user), $password)){
               //If the ldap authentication faild make the RESULT=false so it will considered as un authorized
               $RESULT_ARR=null;
               $LOGIN=false;
            }else{
               $LOGIN=true;
            }
         }
      break;
      case 'PASSWD':
         //TODO:authenticate using system password file
      break;
      case 'MYSQL':
      default:
         //Filters can be applied to extract only the relavant category
         $filter="";
         if(isset($GLOBALS['TBL_LOGIN']['filter']) && $GLOBALS['TBL_LOGIN']['filter']!=''){
            $filter="AND ".$GLOBALS['TBL_LOGIN']['filter'];
         }

         //Login using native database from mysql user database
         if(isset($GLOBALS['TBL_LOGIN']['md5']) && $GLOBALS['TBL_LOGIN']['md5']=='false'){
            $password=$password;
         }else{
            $password=md5($password);
         }
         $SQL = "SELECT * FROM ".$GLOBALS['TBL_LOGIN']['table']." WHERE ".$GLOBALS['TBL_LOGIN']['username']."='$user' AND ".$GLOBALS['TBL_LOGIN']['password']."='".$password."' $filter";
         $RESULT_ARR   = null;

         //If the database is external then do not reconnect inside exec_auery function
         if(isset($GLOBALS['TBL_LOGIN']['db_host'])){
            $RESULT_ARR    = exec_query($SQL, Q_RET_ARRAY,null,null,null,true);
         }else{
            $RESULT_ARR    = exec_query($SQL, Q_RET_ARRAY);
         }
         log_msg('login_result',$SQL);
         if(isset($RESULT_ARR[0])){
            $LOGIN=true;   
         }else{
            $LOGIN=false;   
            $RESULT_ARR=null;
         }
      break;
      }
   }
   
   //If login successful $RESULT should be true then set the session variables
   if ($LOGIN) {
      $ROW=$RESULT_ARR[0];
      foreach($GLOBALS['TBL_LOGIN'] as $key => $value){
         if(isset($ROW[$value])){
            $_SESSION[$key]   = $ROW[$value];
         }
      }
      $_SESSION['loged_module']    = MODULE;
   }else{
      //echo "<div style='float:left;padding:5px;color:brown;'>Invallid login please try again...</div>";
      //echo "<script type='text/javascript' language=javascript>alert('Invalid login please try again...');</script>";
   }
?>
