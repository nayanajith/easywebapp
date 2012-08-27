<?php
function act_log($table=null,$info=null){
   $proto="http";
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on'){
      $proto="https";
   }
   $table   =($table!=null)?$table:'log';
   $username=isset($_SESSION['username'])?$_SESSION['username']:"";

   $log_array=array(
      'ip'        =>$_SERVER['REMOTE_ADDR'],
      'proto'     =>$proto,
      'host'      =>$_SERVER['HTTP_HOST'],
      'request'   =>$_SERVER['REQUEST_URI'],
      'user_id'   =>(isset($_SESSION['user_id'])?$_SESSION['user_id']:"").":".$username,
      'module_id' =>isset($_REQUEST['module'])?$_REQUEST['module']:MODULE,
      'page_id'   =>isset($_REQUEST['page'])?$_REQUEST['page']:PAGE,
      'action_'   =>isset($_REQUEST['action'])?$_REQUEST['action']:"",
      'agent'     =>$_SERVER['HTTP_USER_AGENT'],
      'info'      =>($info!=null)?$info:""
   );

   exec_query("INSERT INTO ".$table."(".implode(",",array_keys($log_array)).") VALUES('".implode("','",array_values($log_array))."')",Q_RET_NONE);
}

if(isset($_REQUEST['action'])){
   act_log();
}


?>
