<?php
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'FORM'=>array(
	
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      /*
      "rid"=>array(
			"length"=>"100",
         "dojoType"=>"dijit.form.FilteringSelect",
			"required"=>"true",
			"label"=>"Group",
			"label_pos"=>"left",

         "onChange"=>'set_param(this.name,this.value);fill_form(this.value,"main")',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>null,
         "ref_table"=>$GLOBALS['S_TABLES']['role'],
         "order_by"=>'ORDER BY group_name DESC',
         "vid"=>array('group_name'),
      ),
       */
      "add"=>array(
         "dojoType"=>"dijit.form.Button",
			"label"=>"Create a backup",
			"label_pos"=>"left",
         "iconClass"=>get_icon_class('NewPage'),
         "showLabbel"=>'false',
         "onClick"=>'submit_form("add_backup")',
      ),
     "remove"=>array(
         "dojoType"=>"dijit.form.Button",
			"label"=>"Delete backup",
			"label_pos"=>"left",
         "iconClass"=>get_icon_class('Delete'),
         "showLabbel"=>'false',
         "onClick"=>'submit_form("del_backup")',
      ),

   ),
);

/**
 * List all the backup files in backup directory
 */

function list_backups(){
	$files = scandir(MOD_BACKUP);
   if($files){
		$list='<ol>';
      foreach($files as $file){
         if($file == '.' || $file == '..')continue;
         $list.="<li><a href='".MOD_W_BACKUP."/".$file."'>".$file."</a><input dojoType='dijit.form.CheckBox' type='checkbox' name='BACK#$file'>";
      }
      $list.='</ol>';
   }
   return $list;
}

/**
 * Backup current database as to sql dump
 */
function backup_now(){
   $backup_file=MOD_BACKUP."/".$GLOBALS['DB']."_".date("j-n-Y_H:m:s").".sql.gz";
   log_msg("mysqldump -u".$GLOBALS['DB_USER']." -p".$GLOBALS['DB_PASS']."  ".$GLOBALS['DB']." | gzip > $backup_file");
   exec("mysqlnump -u".$GLOBALS['DB_USER']." -p".$GLOBALS['DB_PASS']."  ".$GLOBALS['DB']." | gzip > $backup_file");
   if(file_exists($backup_file)){
      return_status_json('OK','Backup successful!');
   }else{
      return_status_json('ERROR','Backup error!');
   }
}

/**
 * Delete the selected backups from disk
 */
function del_backup(){
   $msg="Deleted:";
   foreach($_REQUEST as $id => $value){
      $arr=explode('#',$id);
      if(sizeof($arr)==2 && $arr[0]=='BACK'){
         $msg.=$arr[1].",";
         $path=MOD_BACKUP."/".str_replace('_sql_gz','.sql.gz',$arr[1]);
         log_msg($path);
         $err=unlink($path);
      }
   }
   return_status_json('OK',$msg);
}

?>


