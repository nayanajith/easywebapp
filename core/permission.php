<?php
//load anonimouse permission from /permission.php
include_once(A_ROOT."/permission.php");


/**
example json: {"P#student_hall_allocation#download_manager":"DENIED/READ/WRITE","M#payment":"DENIED/READ/WRITE"}
*/
function load_permission(){
   $GLOBALS['permission']=array(
      'USER'=>array(),
      'GROUP'=>array(),
   );

   //If not logged in return  the default permission given in /permission.php
   if(!isset($_SESSION['user_id'])){
      load_anon_permission();
      return;
   }

   //There are towo levels of permissions,from users.permission feld and from permission table. If you set the users.permission parameter to ADMIN that user will have supper power
   if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 'SUPER'){
      return;
   }

   //permission inherited from the users group
   if(isset($_SESSION['role_id'])){
      $GLOBALS['permission']['GROUP']=exec_query("SELECT module,page,access_right FROM ".$GLOBALS['S_TABLES']['permission']." WHERE is_user=false && group_user_id='".$_SESSION['role_id']."' AND access_right IN ('WRITE','READ') ", Q_RET_ARRAY);
   }

   //Permission will override from the users permission
   if(isset($_SESSION['username'])){
      $GLOBALS['permission']['USER']=exec_query("SELECT module,page,access_right FROM ".$GLOBALS['S_TABLES']['permission']." WHERE  is_user=true && group_user_id='".$_SESSION['username']."' AND access_right IN ('WRITE','READ') ", Q_RET_ARRAY);
   }
   load_anon_permission();
}


/*------------------------------permission validataion--------------------------------*/
/*
permission array
Array ( 
[0] => Array ( [user_id] => nayanajith [module] => configure [page] => * [access_right] => WRITE ) 
[1] => Array ( [user_id] => nayanajith [module] => course [page] => * [access_right] => WRITE )
*/

//load the permission
load_permission();


/*--------------------------permission ovrrides from /-------------------------------*/


//TODO:

function get_module_access_right($module){

}

function get_page_access_right($module, $page){

}

function is_module_permitted($module){
   //At the first place, if the user is an admin, provide supper power
   if(isset($_SESSION['role_id']) && $_SESSION['role_id']=='SUPER'){
      return true;
   }

   $permission=$GLOBALS['permission'];

   //checking for user permission
   foreach($permission['USER'] as $arr)
   {
      if($arr['module'] == $module && $arr['access_right'] != 'DENIED'){
         return true;
      }
   }

   //checking for group permission
   foreach($permission['GROUP'] as $arr)
   {
      if($arr['module'] == $module && $arr['access_right'] != 'DENIED'){
         return true;
      }
   }
   return false;
}

function is_page_permitted($module,$page){
   //At the first place, if the user is an admin, provide supper power
   if(isset($_SESSION['role_id']) && $_SESSION['role_id']=='SUPER'){
      return true;
   }
   $permission=$GLOBALS['permission'];

   $denied=false;
   $allowd=false;


   //Checking for user permission
   foreach($permission['USER'] as $arr){
      if($arr['module'] == $module && $arr['page'] == $page && $arr['access_right'] == 'DENIED'){
         $denied=true;
      }
      if($arr['module'] == $module && ($arr['page'] == $page || $arr['page'] == '*')){
         $allowd=true;
      }
   }

   //Checking for group permission
   foreach($permission['GROUP'] as $arr){
      if($arr['module'] == $module && $arr['page'] == $page && $arr['access_right'] == 'DENIED'){
         $denied=true;
      }
      if($arr['module'] == $module && ($arr['page'] == $page || $arr['page'] == '*')){
         $allowd=true;
      }
   }

   if(!$denied){
      return $allowd;
   }else{
      return false;
   }
}



/*------------------------------end validataion--------------------------------*/


?>
