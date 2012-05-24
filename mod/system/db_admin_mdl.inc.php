<?php 
function db_migration_form(){
   include(effective_schema());

   //Getting the database version form base_data
   $query="SELECT * FROM base_data WHERE base_class='VARIABLE' AND base_key='SYSTEM__DB_VERSION'";

   //query for module uther than system
   if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
      $query="SELECT * FROM base_data WHERE base_class='VARIABLE' AND base_key='".strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION'";
   }

   $arr=exec_query($query,Q_RET_ARRAY);
   $arr=$arr[0];
   $db_version=$arr['base_value'] ;

   //requre dojo modules
   d_r('dijit.form.Form');
   d_r('dijit.form.CheckBox');

   //Starting the form of checklists to select the migrations
   $html= "<div dojoType='dijit.form.Form' name='db_migrate' id='db_migrate' jsId='db_migrate' method='POST'>";
   $html.= "<table class='clean' border='1'>";
   //System table migration check list
   if($schema_version != $db_version  && isset($system_table_migrate) && isset($system_table_migrate[$schema_version])){
      $migrate=$system_table_migrate;
      $html.= "<tr><td colspan='3'  align='center'><h4 style='color:red'>System database upgrade required from db_version ".$arr['base_value']." to db_version $schema_version </h4></td></tr>";
      $html.= "<tr><th>Database version</th><th>Migration Procedure</th><th>Apply</th></tr>";

      for($i=($db_version+1);$i<=$schema_version;$i++ ){
         $html.= "<tr>";
         $html.= "<td style='font-size:16px'>From:".($i-1)." To:$i</td>";
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
      set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:40%;');
      set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:60%;');
   }else{
      $html.= "<tr><td colspan='3'  align='center'><h4 style='color:blue'>You are with the latest system db_version no need of migration!</h4></td></tr>";
      set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:0%;');
      set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:100%;');
   }

   //Program table migration check list
   if($schema_version != $db_version && isset($program_table_migrate) && isset($program_table_migrate[$schema_version])  ){
      $migrate=$program_table_migrate;
      $html.= "<tr><td colspan='3' align='center'><h4 style='color:red'>Program database upgrade required from db_version ".$arr['base_value']." to db_version $schema_version </h4></td></tr>";
      $html.= "<tr><th>Database version</th><th>Migration Procedure</th><th>Apply</th></tr>";

      for($i=($db_version+1);$i<=$schema_version;$i++ ){
         $html.= "<tr>";
         $html.= "<td style='font-size:16px'>From:".($i-1)." To:$i</td>";
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
      set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:40%;');
      set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:60%;');
   }else{
      $html.= "<tr><td colspan='3'  align='center'><h4 style='color:blue'>You are with the latest program db_version no need of migration!</h4></td></tr>";
      set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:0%;');
      set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:100%;');
   }
   $html.= "</table>";
   $html.="</div>";
   return $html;
}
function table_creation_form(){
   include(effective_schema());

   d_r('dijit.form.Form');
   d_r('dijit.form.CheckBox');

   $html= "<h3>Table creation and recreation</h3>";
   $html.= "<div dojoType='dijit.form.Form' name='create_tables' id='create_tables' jsId='create_tables' method='POST'>";
   $html.= "<table class='clean' border='1'>";

   $arr=exec_query("SHOW TABLES FROM ".$GLOBALS['DB'],Q_RET_ARRAY,null,'Tables_in_'.$GLOBALS['DB']);
   $tables=array_keys($arr);
   if(isset($system_table_schemas) && sizeof($system_table_schemas) > 0){
      $schemas=$system_table_schemas;
      $html.= "<tr><td colspan='3' align='center'><h4>System Tables</h4></td></tr>";
      $html.= "<tr><th>Table</th><th>Schema</th><th>State</th><th>Action</th></tr>";
      foreach($schemas as $key => $value){
         $html.= "<tr>";
         if(array_search($key,$tables)===false){
            $html.= "<td style='font-size:16px;color:red'>$key</td>";
            $html.= "<td><pre class='code' style='width:600px;overflow:scroll' >$value</pre></td>";
            $html.= "<td>NOT AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' checked='true' id='create__$key' name='create__$key'><label for='create__$key'>CREATE</label></td>";
         }else{
            $html.= "<td style='font-size:16px'>$key</td>";
            $html.= "<td><pre class='code' style='width:600px;overflow:scroll'>$value</pre></td>";
            $html.= "<td>AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='recreate__$key' name='recreate__$key'><label for='recreate__$key'>RECREATE</label></td>";
         }
         $html.= "</tr>";
      }
   }

   if(isset($program_table_schemas) && sizeof($program_table_schemas) > 0){
      $schemas=$program_table_schemas;
      $html.= "<tr><td colspan='3' align='center'><h4>Program Tables</h4></td></tr>";
      $html.= "<tr><th>Table</th><th>Schema</th><th>State</th><th>Action</th></tr>";

      foreach($schemas as $key => $value){
         //program table normally has a prefix, add the prefix to the table name 
         if(isset($schema_prefix)){
            $key=sprintf($key,$schema_prefix."_");
         }
         $html.= "<tr>";
         if(array_search($key,$tables)===false){
            $html.= "<td style='font-size:16px;color:red'>$key</td>";
            $html.= "<td><pre class='code' style='width:600px;overflow:scroll'>$value</pre></td>";
            $html.= "<td>NOT AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' checked='true' id='create__$key' name='create__$key'><label for='create__$key'>CREATE</label></td>";
         }else{
            $html.= "<td style='font-size:16px'>$key</td>";
            $html.= "<td><pre class='code' style='width:600px;overflow:scroll'>$value</pre></td>";
            $html.= "<td>AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='recreate__$key' name='recreate__$key'><label for='recreate__$key'>RECREATE</label></td>";
         }
         $html.= "</tr>";
      }
   }

   $html.= "</table>";
   $html.= "</div>";
   return  $html;
}

function create_recreate_tables(){
   include(effective_schema());
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

            //program table prefix rallback
            if(isset($schema_prefix)){
               $table=str_replace('%s',$schema_prefix."_",$table);
            }

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
   if($status == 'OK'){
      if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
         exec_query("REPLACE INTO `base_data` SET `base_value` ='$schema_version' WHERE `base_class`='VARIABLE' AND `base_key`='".strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION'",Q_RET_NON);
      }else{
         exec_query("REPLACE INTO `base_data` SET `base_value` ='$schema_version' WHERE `base_class`='VARIABLE' AND `base_key`='SYSTEM__DB_VERSION'",Q_RET_NON);
      }
   }
   return_status_json($status,$info);
}

function migrate_db(){
   include(effective_schema());
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
      if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
         exec_query("UPDATE `base_data` SET `base_value` ='$version' WHERE `base_class`='VARIABLE' AND `base_key`='".strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION'",Q_RET_NON);
      }else{
         exec_query("UPDATE `base_data` SET `base_value` ='$version' WHERE `base_class`='VARIABLE' AND `base_key`='SYSTEM__DB_VERSION'",Q_RET_NON);
      }
   }
   return_status_json($status,$info);
}

function effective_schema(){
   $schema_file=A_CORE.'/database_schema.php';

   //include the selected module database schema
   if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
      $schema_file=A_MODULES.'/'.$_SESSION[PAGE]['schema_module'].'/core/database_schema.php';
   }
   return  $schema_file;
}

function default_(){
   include(effective_schema());

   if(!isset($program_table_schemas)){
      $program_table_schemas=array();
   }

   if(!isset($program_table_migrate)){
      $program_table_migrate=array();
   }

   add_to_main_top("<div><center>".db_migration_form()."</center></div>");
   add_to_main_bottom("<div><center>".table_creation_form()."</center></div>");
}

?>
