<?php

//Generate the json related to the user/group permission
if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
   $user_group_id   =explode(':',$_REQUEST['id']);
   $is_user    =0;
   if($user_group_id[0]=='U')$is_user=1;
   $user_group   =$user_group_id[1];

   /*Select permission information for the given user from the db*/
   $arr=exec_query("SELECT * FROM ".s_t('permission')." WHERE is_user=$is_user && group_user_id='".$user_group."'", Q_RET_ARRAY);
   $return_array=array();

   /*Convert permission into form ids at the frontend*/
   foreach($arr as $row){
      if($row['page'] == '*'){
         $return_array["M#".$row['module']] = $row['access_right'];
      }else{
         $return_array["P#".$row['module']."#".$row['page']] = $row['access_right'];
      }   
   }

   /*Convert array into json and return as json file*/
   header('Content-Type', 'application/json');
   echo json_encode($return_array);
   exit();
}

if(isset($_REQUEST['form'])){
   switch($_REQUEST['action']){
      case 'modify':
         $permission_array=$_REQUEST;

         /*Elements to substract from the request array*/
         $unset_element=array("module","page","program","form","action","username");

         /*Subtract elements form the request array to cleanup and getn only permission parameters*/
         foreach($unset_element as $key){
            unset($permission_array[$key]);   
         }

         $user_group_id   =explode(':',$_REQUEST['username']);
         $is_user    =0;
         if($user_group_id[0]=='U')$is_user=1;
         $user_group   =$user_group_id[1];

         /*Save json*/
         //$res=exec_query("UPDATE ".s_t('users')." SET permission='".json_encode($permission_array)."' WHERE username='".$_REQUEST['username']."' ",Q_RET_MYSQL_RES);
         /*Delete all permission before set new settings TODO: recovery plan*/
         if(!exec_query("DELETE FROM ".s_t('permission')." WHERE is_user=$is_user && group_user_id='".$user_group."'", Q_RET_MYSQL_RES)){
            return_status_json('ERROR','error resetting permission');
            return;
         }

         /*Keep trac of modules permitted to prevent redundent permission ruls*/
         $modules_permitted=array();

         foreach($permission_array as $key => $value){
            $value = strToUpper($value);

            /*Filter only following permission types*/
            if(!($value == 'WRITE' || $value == 'READ' || $value == 'DENIED')){
               continue;   
            }

            /*Break down the id to retrieve (M/P,<module_name>,[<page_name>])*/
            $break_down      =explode("#",$key);
            
            /*Get module name the element ad index 1*/
            $module         =$break_down[1];
         
            $status_of_query=false;
            /*Switch upon M/P -> MODULE/PAGE*/
            switch($break_down[0]){
               case "M":
                  $modules_permitted[$module]=$value;   
                  if(!exec_query("REPLACE INTO ".s_t('permission')."(group_user_id,module,page,access_right,is_user) values('".$user_group."','$module','*','$value',$is_user)",Q_RET_MYSQL_RES)){
                     return_status_json('ERROR','error updating permission');
                     return;
                  }
               break;
               case "P":
                  $page      =$break_down[2];
                  if(isset($modules_permitted[$module])){
                     /*Prevent redundent permission ruls eg: module->w then page should not re assign 'w'*/
                     if($modules_permitted[$module] != $value){
                        if(!exec_query("REPLACE INTO ".s_t('permission')."(group_user_id,module,page,access_right,is_user) values('".$user_group."','$module','$page','$value',$is_user)",Q_RET_MYSQL_RES)){
                           return_status_json('ERROR','error updating permission');
                           return;
                        }
                     }
                  }else{
                     if(!exec_query("REPLACE INTO ".s_t('permission')."(group_user_id,module,page,access_right,is_user) values('".$user_group."','$module','$page','$value',$is_user)",Q_RET_MYSQL_RES)){
                        return_status_json('ERROR','error updating permission');
                        return;
                     }
                  }
               break;
            }
         }

      /*Report OK if no problem occured*/
      return_status_json('OK','permission updated successfully');

      break;
      case 'delete':
      break;
      case 'add':
      break;
      default:
      break;
   }
exit();
}


function gen_permission_tree(){
   d_r('dijit.form.ComboBox');
   d_r('dijit.form.Select');
   d_r('dijit.form.Form');
   foreach ($GLOBALS['MODULES'] as $mod_key => $mod) {
      $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
      if(is_array($mod)){
         $mod=$mod['MODULE'];
      }
      echo "<table border=0 style='border-collapse:collapse;width:400px;font:inherit'>
      <tr>
      <th align='left' style='font:inherit;font-weight:bold'>Module/Page</th>
      <th align='right' style='font:inherit;font-weight:bold'>PERMISSION</th>
      </tr>
      <tr>
      <td style='background-color:silver;font:inherit' >".$mod." (module)</td>
      <td style='background-color:silver;font:inherit' align='right'>
      <div dojoType='dijit.form.Select' name='M#$mod_key' id='DM#$mod_key' value='DENIED' style='width:70px;font:inherit' >
         <option value='DENIED'><span style='color:red'>DENIED</span></option>
         <option value='READ'><span style='color:green'>READ</span></option>
         <option value='WRITE'><span style='color:blue'>WRITE</span></option>
      </div>
      </td>
      </tr>\n";
      if(file_exists($module_menu_file)){
         include($module_menu_file);
         foreach($menu_array as $page_key => $page){

            //Handle array type pages
            if(is_array($page)){
               $page=$page['label'];
            }

            echo "<tr>
            <td style='background-color:whitesmoke;font:inherit'>&nbsp;-".$page."</td>
            <td style='background-color:whitesmoke;font:inherit' align='right'>
            <div dojoType='dijit.form.Select' name='P#".$mod_key."#".$page_key."' id='DP#".$mod_key."#".$page_key."' value='DENIED' style='width:70px;font:inherit' >
               <option value='DENIED'><span style='color:red'>DENIED</span></option>
               <option value='READ'><span style='color:green'>READ</span></option>
               <option value='WRITE'><span style='color:blue'>WRITE</span></option>
            </div>   
            </td>
            </tr>\n";
         }
      }
      echo "</table><br>";
   }

}
echo "<h3>Select role or user to assign permission </h3>";
echo "<div  align='center'>";
echo  "<div dojoType='dijit.form.Form' id='permission_frm' jsId='permission_frm'
         encType='multipart/form-data'
         action='".$GLOBALS['PAGE_GEN']."';
         method='GET' >
         ";
echo "Select User/Role: <select name='username' id='username' dojoType='dijit.form.FilteringSelect' pageSize='20' jsId='username' onChange='fill_form_(this.value);'>";

//List of groups
echo "<option value='none'>-roles-</option>";
$res=exec_query("SELECT group_name FROM ".s_t('role'),Q_RET_MYSQL_RES);
while($row=mysql_fetch_assoc($res)){
echo "<option value='G:".$row['group_name']."'>".$row['group_name']."</option>";
}

//List of users
echo "<option value='none'>-users-</option>";
$res=exec_query("SELECT username FROM ".s_t('users'),Q_RET_MYSQL_RES);
while($row=mysql_fetch_assoc($res)){
echo "<option value='U:".$row['username']."'>".$row['username']."</option>";
}

echo "</select><br><br>";

gen_permission_tree();

set_layout_property('app2','MAIN_LEFT','style','width','100%');
set_layout_property('app2','MAIN_RIGHT','style','width','0%');
?>
</div>
</div>
<?php
js("
function fill_form_(key) {
   if(!(key == '' || key == 'new')){
   dojo.xhrGet({
      url       : '".gen_url()."&data=json&id='+key+'&form=main',
      handleAs  :'json',
      load      : function(response, ioArgs) {        

         /*Reset form*/
         dojo.forEach(dijit.byId('permission_frm').getDescendants(),function(widget){
            if(widget.get('value') != key){
               widget.set('value','DENIED');
            }
         });

         /*Fill form with values returned*/   
         permission_frm.setValues(response); 
      },
      error    :function(response, ioArgs) {
         alert(response);
      }
   });
   }
}


function submit_form_(action){
   update_status_bar('OK','...');
   update_progress_bar(10);
   /*User should confirm deletion*/
   if(action=='delete' && !confirm('Confirm Deletion!')){
      update_status_bar('ERROR','deletion canceled');
      update_progress_bar(0);
      return;   
   }
   if (dijit.byId('permission_frm').validate()) {
      dojo.xhrGet({
      url         : '".gen_url()."&form=main&action='+action, 
      handleAs      : 'json',
      form         : 'permission_frm', 

      handle: function(response,ioArgs){
         var status=response.status.toUpperCase();
         switch(status){
            case 'OK':
               update_status_bar(status,response.info);
            break;
            case 'ERROR':
               update_status_bar(status,response.info);
            break;
            case 'NOT_DEFINED':
               update_status_bar(status,response.info);
            break;
            default:
               update_status_bar('ERROR','unknown state');
            break;
         }
         update_progress_bar(100);
      },

      load: function(response) {
         update_status_bar(status,response.info);
         //update_status_bar('OK','rquest sent successfully');
         //update_progress_bar(50);
      }, 
      error: function() {
         update_status_bar('ERROR','error on submission');
         update_progress_bar(0);
      }
   });

   return false;
}else{
   update_status_bar('ERROR','Form contains invalid data.  Please correct first');
   return false;
}
return true;
}
");
?>
