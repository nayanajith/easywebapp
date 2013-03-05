<?php
$LOAD_DEFAULT_TOOLBAR=false;
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

         "onChange"=>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>null,
         "ref_table"=>s_t('role'),
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
         "onClick"=>'s_f_c_add("ok",reload_main);submit_form("add_backup")',
      ),
     "remove"=>array(
         "dojoType"=>"dijit.form.Button",
			"label"=>"Delete backup",
			"label_pos"=>"left",
         "iconClass"=>get_icon_class('Delete'),
         "showLabbel"=>'false',
         "onClick"=>'s_f_c_add("ok",reload_main);submit_form("del_backup")',
      ),
   ),
);

/**
 * List all the backup files in backup directory
 */

function list_backups(){
	$files = scandir(MOD_BACKUP);
   if($files){
		$files_=array();
      foreach($files as $file){
			$files_[$file]=filemtime(MOD_BACKUP . '/' . $file);
		}

	   arsort($files_);
		$list='<center><table class="clean" border="1"><tr><th>File</th><th>Date/Time</th><th>Size</th><th>Select to delete</th></tr>';
      foreach($files_ as $file => $date){
         if($file == '.' || $file == '..')continue;
			$size=hr_filesize(filesize(MOD_BACKUP . '/' . $file));
         $list.="<tr><td><a href='".MOD_W_BACKUP."/".$file."'>".$file."</a></td><td>".date("M d Y H:i:s",$date)."</td><td>".$size."</td><td><input dojoType='dijit.form.CheckBox' type='checkbox' name='BACK#$file'></td></tr>";
      }
      $list.='</table></center>';
   }
   return $list;
}

/**
 * Backup current database as to sql dump
 */
function backup_now(){
   $backup_file=MOD_BACKUP."/".$GLOBALS['DB']."_".date("j-n-Y_H:m:s").".sql.gz";
   log_msg("mysqldump -f -u".$GLOBALS['DB_USER']." -p".$GLOBALS['DB_PASS']."  ".$GLOBALS['DB']." | gzip > $backup_file");
   exec("mysqldump -f -u".$GLOBALS['DB_USER']." -p".$GLOBALS['DB_PASS']."  ".$GLOBALS['DB']." | gzip > $backup_file");
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

set_layout_property('app2','MAIN_LEFT','style','height','100%');
set_layout_property('app2','MAIN_RIGHT','style','height','0%');
?>
