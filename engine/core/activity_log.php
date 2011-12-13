<?php
/*
$system_table_schemas['log']="CREATE TABLE `log` (
  `id`                bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `time`             bigint(10) unsigned NOT NULL DEFAULT '0',
  `timestamp`          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id`          INT NOT NULL,
  `ip`                varchar(15) NOT NULL DEFAULT '',
  `page`             bigint(10) unsigned NOT NULL DEFAULT '0',
  `module`             varchar(20) NOT NULL DEFAULT '',
  `cmid`             bigint(10) unsigned NOT NULL DEFAULT '0',
  `action`             varchar(40) NOT NULL DEFAULT '',
  `url`                varchar(100) NOT NULL DEFAULT '',
  `info`             text,
  `deleted`          boolean     DEFAULT false,
  `note`             varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

*/

function act_log($table=null,$info=null){
   $proto="http";
   if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on'){
      $proto="https";
   }
   $table   =($table!=null)?$table:'log';
   $username=isset($_SESSION['username'])?$_SESSION['username']:"";

   $log_array=array(
      'ip'      =>$_SERVER['REMOTE_ADDR'],
      'proto'   =>$proto,
      'host'   =>$_SERVER['HTTP_HOST'],
      'request'=>$_SERVER['REQUEST_URI'],
      'user_id'=>(isset($_SESSION['user_id'])?$_SESSION['user_id']:"").":".$username,
      'module'   =>isset($_REQUEST['module'])?$_REQUEST['module']:MODULE,
      'page'   =>isset($_REQUEST['page'])?$_REQUEST['page']:PAGE,
      'action'   =>isset($_REQUEST['action'])?$_REQUEST['action']:"",
      'agent'   =>$_SERVER['HTTP_USER_AGENT'],
      'info'   =>($info!=null)?$info:""
   );

   exec_query("INSERT INTO ".$table."(".implode(",",array_keys($log_array)).") VALUES('".implode("','",array_values($log_array))."')",Q_RET_NON);
}

if(isset($_REQUEST['action'])){
   act_log();
}


?>
