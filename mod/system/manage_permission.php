<?php
include A_CLASSES."/data_entry_class.php";
$table            ='users';
$key1               ='username';
$formgen          = new Formgenerator($table,$key1);

if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
   /*Select permission information for the given user from the db*/
   $arr=exec_query("SELECT * FROM ".$GLOBALS['S_TABLES']['permission']." WHERE user_id='".$_REQUEST['id']."'", Q_RET_ARRAY);
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

         /*Save json*/
         //$res=exec_query("UPDATE ".$GLOBALS['S_TABLES']['users']." SET permission='".json_encode($permission_array)."' WHERE username='".$_REQUEST['username']."' ",Q_RET_MYSQL_RES);
         /*Delete all permission before set new settings TODO: recovery plan*/
         if(!exec_query("DELETE FROM ".$GLOBALS['S_TABLES']['permission']." WHERE user_id='".$_REQUEST['username']."'", Q_RET_MYSQL_RES)){
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
                  if(!exec_query("REPLACE INTO ".$GLOBALS['S_TABLES']['permission']."(user_id,module,page,access_right) values('".$_REQUEST['username']."','$module','*','$value')",Q_RET_MYSQL_RES)){
                     return_status_json('ERROR','error updating permission');
                     return;
                  }
               break;
               case "P":
                  $page      =$break_down[2];
                  if(isset($modules_permitted[$module])){
                     /*Prevent redundent permission ruls eg: module->w then page should not re assign 'w'*/
                     if($modules_permitted[$module] != $value){
                        if(!exec_query("REPLACE INTO ".$GLOBALS['S_TABLES']['permission']."(user_id,module,page,access_right) values('".$_REQUEST['username']."','$module','$page','$value')",Q_RET_MYSQL_RES)){
                           return_status_json('ERROR','error updating permission');
                           return;
                        }
                     }
                  }else{
                     if(!exec_query("REPLACE INTO ".$GLOBALS['S_TABLES']['permission']."(user_id,module,page,access_right) values('".$_REQUEST['username']."','$module','$page','$value')",Q_RET_MYSQL_RES)){
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
   global $modules;
   d_r('dijit.form.ComboBox');
   d_r('dijit.form.Form');
   foreach ($GLOBALS['MODULES'] as $mod_key => $mod) {
      $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
      if(is_array($mod)){
         $mod=$mod['MODULE'];
      }
      echo "<table border=0 style='border-collapse:collapse;width:200px;border:1px solid silver;'>
      <tr>
      <th align='left'>Module/Page</th>
      <th align='left'>PERMISSION</th>
      </tr>
      <tr>
      <td style='background-color:silver' >".$mod."</td>
      <td style='background-color:silver' align='right'>
      <select dojoType='dijit.form.ComboBox' name='M#$mod_key' id='DM#$mod_key' value='DENIED' style='width:70px' >
         <option value='D'>DENIED</option>
         <option value='R'>READ</option>
         <option value='W'>WRITE</option>
      </select>
      </td>
      </tr>\n";
      if(file_exists($module_menu_file)){
         include($module_menu_file);
         foreach($menu_array as $page_key => $page){

            //Handle array type pages
            if(is_array($page)){
               $page=$page['PAGE'];
            }

            echo "<tr>
            <td style='background-color:whitesmoke'>".$page."</td>
            <td style='background-color:whitesmoke' align='right'>
            <select dojoType='dijit.form.ComboBox' name='P#".$mod_key."#".$page_key."' id='DP#".$mod_key."#".$page_key."' value='DENIED' style='width:70px' >
               <option value='D'>DENIED</option>
               <option value='R'>READ</option>
               <option value='W'>WRITE</option>
            </select>   
            </td>
            </tr>\n";
         }
      }
      echo "</table><br>";
   }

}
echo "<div  align='center'>";
echo  "<div dojoType='dijit.form.Form' id='permission_frm' jsId='permission_frm'
         encType='multipart/form-data'
         action='".$GLOBALS['PAGE_GEN']."';
         method='GET' >
         ";

echo "Select User: <select name='username' id='username' dojoType='dijit.form.ComboBox' jsId='username' onChange='fill_form(this.get(\"displayedValue\"));'
>";

echo "<option value='none'>-select-</option>";
$res=exec_query("SELECT username FROM ".$GLOBALS['S_TABLES']['users'],Q_RET_MYSQL_RES);
while($row=mysql_fetch_assoc($res)){
echo "<option value='".$row['username']."'>".$row['username']."</option>";
}

echo "</select><br><br>";

gen_permission_tree();
?>
</div>
</div>

<script type='text/javascript'>
function fill_form(key) {
   if(!(key == '' || key == 'new')){
   dojo.xhrGet({
      url       : '<?php echo gen_url(); ?>&data=json&id='+key+'&form=main',
      handleAs :'json',
      load       : function(response, ioArgs) {        

         /*Reset form*/
         dojo.forEach(dijit.byId('permission_frm').getDescendants(), function(widget) {
            if(widget.attr('value') != key)
            {
               widget.setValue("DENIED");
            }
         });

         /*Fill form with values returned*/   
           permission_frm.setValues(response); 
      },
      error : function(response, ioArgs) {
           alert(response);
      }
   });
   }
}


function submit_form(action){
   update_status_bar('OK','...');
   update_progress_bar(10);
   //alert(dojo.toJson(dijit.byId('".$table."_frm').getValues(), true));
   /*User should confirm deletion*/
   if(action=='delete' && !confirm('Confirm Deletion!')){
      update_status_bar('ERROR','deletion canceled');
      update_progress_bar(0);
      return;   
   }
   if (dijit.byId('permission_frm').validate()) {
      dojo.xhrGet({
      url         : '<?php echo gen_url(); ?>&form=main&action='+action, 
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
         update_status_bar('OK','rquest sent successfully');
         update_progress_bar(50);
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
</script>
