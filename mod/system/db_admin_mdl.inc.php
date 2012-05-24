<?php 
function db_migration_form($schemas,$schema_version,$migrate){
   $arr=exec_query("SELECT * FROM base_data WHERE base_class='VARIABLE' AND base_key='SYSTEM__DB_VERSION'",Q_RET_ARRAY);
   $arr=$arr[0];
   $db_version=$arr['base_value'] ;
   d_r('dijit.form.Form');
   d_r('dijit.form.CheckBox');
   $html= "<div dojoType='dijit.form.Form' name='db_migrate' id='db_migrate' jsId='db_migrate' method='POST'>";
   if($schema_version != $db_version && isset($migrate[$schema_version]) ){
      $html.= "<h4 style='color:red'>Database upgrade required from db_version ".$arr['base_value']." to db_version $schema_version </h4>";
      $html.= "<table class='clean' border='1'><tr><th>Database version</th><th>Migration Procedure</th><th>Apply</th>";
      $arr=exec_query("SHOW TABLES FROM ".$GLOBALS['DB'],Q_RET_ARRAY,null,'Tables_in_'.$GLOBALS['DB']);
      $tables=array_keys($arr);
      for($i=($db_version+1);$i<=$schema_version;$i++ ){
         $html.= "<tr>";
         $html.= "<td style='font-size:16px'>$i</td>";
         $html.= "<td>";
         if(is_array($migrate[$i])){
            $html.= "<ol>";
            foreach($migrate[$i] as $value){
               $html.= "<li><pre class='code'>$value</pre></li>";
            }
            $html.= "</ol>";
         }else{
            $html.= "<ol><li><pre class='code'>".$migrate[$i]."</pre></li></ol>";
         }
         $html.= "</td>";
         $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='migrate__$i' name='migrate__$i'><label for='migrate__$i'>MIGRATE</label></td>";
         $html.= "</tr>";
      }
      $html.= "</table>";
   }else{
      $html.= "<h4 style='color:blue'>You are with the latest db_version no need of migration!</h4>";
   }
   $html.="</div>";
   return $html;
}
function table_creation_form($schemas){
   d_r('dijit.form.Form');
   d_r('dijit.form.CheckBox');
   $html= "<h3>Table creation and recreation</h3>";
   $html.= "<div dojoType='dijit.form.Form' name='create_tables' id='create_tables' jsId='create_tables' method='POST'>";
   $html.= "<table class='clean' border='1'><tr><th>Table</th><th>Schema</th><th>State</th><th>Action</th>";
   $arr=exec_query("SHOW TABLES FROM ".$GLOBALS['DB'],Q_RET_ARRAY,null,'Tables_in_'.$GLOBALS['DB']);
   $tables=array_keys($arr);
   foreach($schemas as $key => $value){
      $html.= "<tr>";
      if(array_search($key,$tables)===false){
         $html.= "<td style='font-size:16px;color:red'>$key</td>";
         $html.= "<td><pre class='code'>$value</pre></td>";
         $html.= "<td>NOT AVAILABLE</td>";
         $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' checked='true' id='create__$key' name='create__$key'><label for='create__$key'>CREATE</label></td>";
      }else{
         $html.= "<td style='font-size:16px'>$key</td>";
         $html.= "<td><pre class='code'>$value</pre></td>";
         $html.= "<td>AVAILABLE</td>";
         $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='recreate__$key' name='recreate__$key'><label for='recreate__$key'>RECREATE</label></td>";
      }
      $html.= "</tr>";
   }
   $html.= "</table>";
   $html.= "</div>";
   return  $html;
}

function create_recreate_tables($schemas){
   $info    ="";
   $status  ="OK";

   foreach($_REQUEST as $key => $value){
      $br=explode('__',$key);
      if(isset($br[1])){
         $table=$br[1];
         switch($br[0]){
         case 'create':
            exec_query($schemas[$table],Q_RET_NON);
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="[$table] creation ".get_sql_error().";";
            }else{
               $info.="[$table] creation successfull;";
            }
         break;
         case 'recreate':
            //delete table
            drop_tables(array($table));
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="[$table] dropping ".get_sql_error().";";
            }else{
               $info.="[$table] dropping successfull;";
            }
            //create table
            exec_query($schemas[$table],Q_RET_NON);
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="[$table] creation ".get_sql_error()."<br>";
            }else{
               $info.="[$table] creation successfull;";
            }
         break;
         }
      }
   }
   return_status_json($status,$info);
}

function migrate_db($migrate){
   $info    ="";
   $status  ="OK";
   $version ="0";
   foreach($_REQUEST as $key => $value){
      $br=explode('__',$key);
      if(isset($br[1])){
         $version=$br[1];
         switch($br[0]){
         case 'migrate':
            if(is_array($migrate[$version])){
               foreach($migrate[$version] as $key => $value){
                  exec_query($value,Q_RET_NON);
                  if(get_sql_error() != false){
                     $status  ="ERROR";
                     $info.="[$version][$key] migration ".get_sql_error().";";
                  }else{
                     $info.="[$version][$key] migration successfull;";
                  }
               }
            }else{
               exec_query($migrate[$version],Q_RET_NON);
               if(get_sql_error() != false){
                  $status  ="ERROR";
                  $info.="[$version] migration ".get_sql_error().";";
               }else{
                  $info.="[$version] migration successfull;";
               }
            }
         break;
         }
      }
   }
   if($status == 'OK'){
      exec_query("UPDATE `base_data` SET `base_value` ='$version' WHERE `base_class`='VARIABLE' AND `base_key`='SYSTEM__DB_VERSION'",Q_RET_NON);
   }
   return_status_json($status,$info);
}

?>
