<?php 
/**
 * This page(functionality) was prepared in order to fulfil a necessity of the developer
 */
/**
 * Database migration form generation function
 * This will include the effective database schema according to the userselection and check wether there are any migrations required for both system tables and program tables
 * and provide functionality to proceed
 */
function db_migration_form(){
   //include the effeicvei schema
   include(effective_schema());

   //Getting the database version form base_data
   $query="SELECT * FROM base_data WHERE base_class='VARIABLE' AND base_key='SYSTEM__DB_VERSION'";

   //If the database is s module database prefix the __DB_VERSION with module name as a convention to understand this is a module databa version
   if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
      $query="SELECT * FROM base_data WHERE base_class='VARIABLE' AND base_key='".strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION'";
   }

   $db_version=-1;
   $arr=exec_query($query,Q_RET_ARRAY);
   log_msg(isset($arr[0]));
   if(isset($arr[0])){//If array is available that means there is a previouse version in base_data
      $arr=$arr[0];
      //The current database version
      $db_version=$arr['base_value'] ;
   }else{//If there is no previous vaersion set set ther version as 0
      $db_version=0;
      exec_query("INSERT INTO `base_data`(`base_value`,`base_class`,`base_key`)VALUES('$db_version','VARIABLE','".strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION')",Q_RET_NONE);
      $arr=array($db_version,'VARIABLE',strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION");
   }


   //requre dojo modules
   d_r('dijit.form.Form');
   d_r('dijit.form.CheckBox');
   d_r('dijit.TitlePane');

   //In the latter part this value will set to true if there are any migrations so according to that layout will be changed
   $migration_avail=false;

   //Starting the form of checklists to select the migrations
   $html= "<h3>Database migration procedures</h3>";
   $html.= "<div dojoType='dijit.form.Form' name='db_migrate' id='db_migrate' jsId='db_migrate' method='POST'>";
   $html.= "<table class='clean' border='1'>";

   //System table migration check list
   //Check for the version mis match and availability of system table migration array
   if($schema_version != $db_version  && isset($system_table_migrate) && isset($system_table_migrate[$schema_version])){
      $migrate=$system_table_migrate;
      $html.= "<tr><td colspan='3'  align='center' style='font-size:16px;color:red'>System database upgrade required from db_version ".$arr['base_value']." to db_version $schema_version </h4></td></tr>";
      $html.= "<tr><th>Database version</th><th>Apply</th></tr>";

      //Iterate through the migration array and display to the user in order to select 
      //There can be multiple steps in the migration procedure but user have only accessibility to select one procedure, not more granular
      for($i=($db_version+1);$i<=$schema_version;$i++ ){
         $html.= "<tr>";
         $html.= "<td>";

         //Migration procedure(sql) will also make available to the users but it is collapse so user can expand and check the procedure if required
         $html.= "<div dojoType='dijit.TitlePane' data-dojo-props=\"title:'From:".($i-1)." To:$i',open:false\"><pre class='code'>";

         //Migration procedure can be in an array with multiple steps
         if(is_array($migrate[$i])){
            $html.= "<ol>";
            foreach($migrate[$i] as $value){
               $html.= "<li><pre class='code'>$value</pre></li>";
            }
            $html.= "</ol>";
         }else{
            //Single step migration procdure
            $html.= "<ol><li><pre class='code'>".$migrate[$i]."</pre></li></ol>";
         }
         $html.= "</div></td>";

         //Check box to select the migration procedure
         $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='migrate__$i' name='migrate__$i'><label for='migrate__$i'>MIGRATE</label></td>";
         $html.= "</tr>";
      }

      $migration_avail=true;
   }else{
      //If any matter migration is not required then this information will be displayed to the user
      $html.= "<tr><td colspan='3'  align='center' style='font-size:16px;color:green'>You are with the latest system db_version no need of migration!</td></tr>";
   }

   //Program table migration check list
   //Program migration form generation is similar to the system table migration form generation so refer the same comments to understand the code
   if($schema_version != $db_version && isset($program_table_migrate) && isset($program_table_migrate[$schema_version])  ){
      $migrate=$program_table_migrate;
      $html.= "<tr><td colspan='3' align='center' style='font-size:16px;color:red'>Program database upgrade required from db_version ".$arr['base_value']." to db_version $schema_version </h4></td></tr>";
      $html.= "<tr><th>Database version</th><th>Apply</th></tr>";

      for($i=($db_version+1);$i<=$schema_version;$i++ ){
         $html.= "<tr>";
         $html.= "<td>";
         $html.= "<div dojoType='dijit.TitlePane' data-dojo-props=\"title:'From:".($i-1)." To:$i',open:false\"><pre class='code'>";
         if(is_array($migrate[$i])){
            $html.= "<ol>";
            foreach($migrate[$i] as $value){
               $html.= "<li><pre class='code'>".str_replace('%s',$schema_prefix."_",$value)."</pre></li>";
            }
            $html.= "</ol>";
         }else{
            $html.= "<ol><li><pre class='code'>".str_replace('%s',$schema_prefix."_",$migrate[$i])."</pre></li></ol>";
         }
         $html.= "</div></td>";
         //Only in here the program database migration form differ from system database migration form
         //id/name.. are prepended  with p_ to denote program migration this flag will be used in switch bellow to choose the migration procedure
         $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='p_migrate__$i' name='p_migrate__$i'><label for='p_migrate__$i'>MIGRATE</label></td>";
         $html.= "</tr>";
      }

      $migration_avail=true;
   }else{
      $html.= "<tr><td colspan='3'  align='center' style='font-size:16px;color:green'>You are with the latest program db_version no need of migration!</td></tr>";
   }

   //If there are any migration available MAIN_TOP will expand to 40% else it will be 10% of the MAIN
   if($migration_avail){
      set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:40%;');
      set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:60%;');
   }else{
      set_layout_properties('app2','MAIN_TOP','style','padding:0px;height:20%;');
      set_layout_properties('app2','MAIN_BOTTOM','style','padding:0px;height:80%;');
   }

   $html.= "</table>";
   $html.="</div>";
   return $html;
}

//This function will generate the form to create/recreate the tables from the database schema
//This will check for the available tables in the database and notify the users with the color which tables are required to be created
function table_creation_form(){
   include(effective_schema());

   d_r('dijit.form.Form');
   d_r('dijit.form.CheckBox');
   d_r('dijit.TitlePane');

   //start the html providing a header
   $html= "<h3>Table creation and recreation</h3>";
   $html.= "<div dojoType='dijit.form.Form' name='create_tables' id='create_tables' jsId='create_tables' method='POST'>";
   $html.= "<table class='clean' border='1'>";

   //Get the total list of tables/views etc.
   $arr=exec_query("SHOW TABLES FROM ".$GLOBALS['DB'],Q_RET_ARRAY,null,'Tables_in_'.$GLOBALS['DB']);
   $tables=array_keys($arr);

   //Check the availability of system table schema and availability of each table in the database and notify if it is to create or recreate
   if(isset($system_table_schemas) && sizeof($system_table_schemas) > 0){
      $schemas=$system_table_schemas;
      $html.= "<tr><td colspan='3' align='center' style='font-size:16px'>System Tables</td></tr>";
      $html.= "<tr><th>Table</th><th>State</th><th>Action</th></tr>";

      //Iterate through the system table schema and check the availability of each table in database
      foreach($schemas as $key => $value){
         $html.= "<tr>";
         
         //check for the availability of the table in database
         if(array_search($key,$tables)===false){
            //database creation sql will be make available to the user, by default it is collapse but the user can expand and investigate the sql
            $html.= "<td><div dojoType='dijit.TitlePane' data-dojo-props=\"title:'$key',open:false\"><pre class='code'>$value</pre></div></td>";
            $html.= "<td style='color:red'>NOT AVAILABLE</td>";
            //not available tables checked by default
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' checked='true' id='create__$key' name='create__$key'><label for='create__$key'>CREATE</label></td>";
         }else{
            $html.= "<td><div dojoType='dijit.TitlePane' data-dojo-props=\"title:'$key',open:false\"><pre class='code'>$value</pre></div></td>";
            $html.= "<td>AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='recreate__$key' name='recreate__$key'><label for='recreate__$key'>RECREATE</label></td>";
         }
         $html.= "</tr>";
      }
   }

   //prgram table creation/recreate form generation is similar to the system table creation other than adding a prefix the each table see bellow
   if(isset($program_table_schemas) && sizeof($program_table_schemas) > 0){
      $schemas=$program_table_schemas;
      $html.= "<tr><td colspan='3' align='center' style='font-size:16px'>Program Tables</td></tr>";
      $html.= "<tr><th>Table</th><th>State</th><th>Action</th></tr>";

      foreach($schemas as $key => $value){
         //program table normally has a prefix, add the prefix to the table name 
         if(isset($schema_prefix)){
            $key  =sprintf($key,$schema_prefix."_");
            $value=str_replace('%s',$schema_prefix."_",$value);
         }
         $html.= "<tr>";
         if(array_search($key,$tables)===false){
            $html.= "<td><div dojoType='dijit.TitlePane' data-dojo-props=\"title:'$key',open:false\"><pre class='code'>$value</pre></div></td>";
            $html.= "<td style='color:red'>NOT AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' checked='true' id='p_create__$key' name='p_create__$key'><label for='p_create__$key'>CREATE</label></td>";
         }else{
            $html.= "<td><div dojoType='dijit.TitlePane' data-dojo-props=\"title:'$key',open:false\"><pre class='code'>$value</pre></div></td>";
            $html.= "<td>AVAILABLE</td>";
            $html.= "<td><input type='checkbox' dojoType='dijit.form.CheckBox' id='p_recreate__$key' name='p_recreate__$key'><label for='p_recreate__$key'>RECREATE</label></td>";
         }
         $html.= "</tr>";
      }
   }

   $html.= "</table>";
   $html.= "</div>";
   return  $html;
}

/**
 * This function will create the tables requested by the user in four deferent cases
 * system
 *    create
 *    recreate
 * program
 *    create
 *    recreate
 * TODO: this function should be extended to support other schema types like function creation and view creation (view creation is partially supporting)
 */
function create_recreate_tables(){
   include(effective_schema());
   //sql execution errors will be gathered
   $info    ="";

   //sql execution stats will be gathered
   $status  ="OK";

   $schemas =array();

   //In order to find the tables requested, Iterate through all the keys sent through the request and check for keys which contain double underscore (__)
   //Then it will concatenate to extract the actual table name and the action
   //action are identified from create,recreate for system tables p_create,p_recreate for program tables
   foreach($_REQUEST as $key => $value){
      $br=explode('__',$key);
      if(isset($br[1])){
         $table=$br[1];
         switch($br[0]){
         case 'create':
            $schemas=$system_table_schemas;
            //creating the table
            exec_query($schemas[$table],Q_RET_NONE);
            //gather the sql execute state(errors)
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="Table $table creation ".get_sql_error().";";
            }else{
               $info.="Table $table creation successfull;";
            }
         break;
         case 'recreate':
            //first drop (backup if the tale contain any data) table
            drop_tables(array($table=>$table));
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="Table $table dropping ".get_sql_error().";";
            }else{
               $info.="Table $table dropping successfull;";
            }
            //create table
            $schemas=$system_table_schemas;
            exec_query($schemas[$table],Q_RET_NONE);
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="Table $table creation ".get_sql_error()."<br>";
            }else{
               $info.="Table $table creation successfull;";
            }
         break;
         case 'p_create':
            $schemas=$program_table_schemas;
            //program table prefix rallback
            if(isset($schema_prefix)){
               //only the first occurence of the given phrase will be replaced
               $table=preg_replace("/".$schema_prefix."_/","%s",$table,1);
            }

            //creating the table
            exec_query($schemas[$table],Q_RET_NONE);
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="Table $table creation ".get_sql_error().";";
            }else{
               $info.="Table $table creation successfull;";
            }
         break;
         case 'p_recreate':
            //drop table
            drop_tables(array($table));
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="Table $table dropping ".get_sql_error().";";
            }else{
               $info.="Table $table dropping successfull;";
            }
            //create table

            //program table prefix rallback
            $schemas=$program_table_schemas;
            if(isset($schema_prefix)){
               $table=preg_replace("/".$schema_prefix."_/","%s",$table,1);
            }

            exec_query($schemas[$table],Q_RET_NONE);
            if(get_sql_error() != false){
               $status  ="ERROR";
               $info.="Table $table creation ".get_sql_error()."<br>";
            }else{
               $info.="Table $table creation successfull;";
            }
         break;
         default:
            $status='ERROR';
         break;
         }
      }
   }
   return_status_json($status,$info);
}
/**
 * This function will execute the user requested migration procedures taken from both system_table_migration  and program_table_migration
 * migrate, p_migrate
 */

function migrate_db(){
   include(effective_schema());
   //var to hold sql execution errors and info
   $info    ="";

   //var to hold status of the sql execution
   $status  ="";

   $version ="0";

   //In order to find the reuqested migration procedures go through the request array and check for the keys have double underscore (__) 
   //Then break it and check whether it is a  program migration procedure or system migration procedure, and accordingly execute the procedure
   foreach($_REQUEST as $key => $value){
      $br=explode('__',$key);
      if(isset($br[1])){

         //Set default status as ok which allow to make the dicision of user have requested any migration
         $status  ="OK";

         $version=$br[1];
         switch($br[0]){
         case 'migrate':
            $migrate=$system_table_migrate;

            //Migration can be consists of multiple steps hence array  and iterate through array and execute
            if(is_array($migrate[$version])){
               foreach($migrate[$version] as $key => $value){
                  exec_multy_query($value,Q_RET_NONE);
                  if(get_sql_error() != false){
                     $status  ="ERROR";
                     $info.="[$version][$key] migration ".get_sql_error().";";
                  }else{
                     $info.="[$version][$key] migration successfull;";
                  }
               }
            }else{
               //if the migration is a single step procedure then this..
               exec_multy_query($migrate[$version],Q_RET_NONE);
               if(get_sql_error() != false){
                  $status  ="ERROR";
                  $info.="[$version] migration ".get_sql_error().";";
               }else{
                  $info.="[$version] migration successfull;";
               }
            }
         break;
         case 'p_migrate':
            //Program database migration is done separately
            //ti is also consists of similar functionality as the system db migration 
            $migrate=$program_table_migrate;

            if(is_array($migrate[$version])){
               foreach($migrate[$version] as $key => $value){
                  //program table/migration rule prefix rallback
                  if(isset($schema_prefix)){
                     //$value=sprintf($value,$schema_prefix."_");
                     $value=str_replace('%s',$schema_prefix."_",$value);
                  }

                  exec_multy_query($value,Q_RET_NONE);
                  if(get_sql_error() != false){
                     $status  ="ERROR";
                     $info.="[$version][$key] migration ".get_sql_error().";";
                  }else{
                     $info.="[$version][$key] migration successfull;";
                  }
               }
            }else{
               $value=$migrate[$version];
               //program table/migration rule prefix rallback
               if(isset($schema_prefix)){
                  //$value=sprintf($value,$schema_prefix."_");
                  $value=str_replace('%s',$schema_prefix."_",$value);
               }
               exec_multy_query($value,Q_RET_NONE);
               if(get_sql_error() != false){
                  $status  ="ERROR";
                  $info.="[$version] migration ".get_sql_error().";";
               }else{
                  $info.="[$version] migration successfull;";
               }
            }
         break;
         default:
            $status="ERROR";
         break;
         }
      }
   }

   //After successful migration the database version (system/program) will be upgraded to the current version
   if($status == 'OK'){
      if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
         exec_query("UPDATE `base_data` SET `base_value` ='$version' WHERE `base_class`='VARIABLE' AND `base_key`='".strtoupper($_SESSION[PAGE]['schema_module'])."__DB_VERSION'",Q_RET_NONE);
      }else{
         exec_query("UPDATE `base_data` SET `base_value` ='$version' WHERE `base_class`='VARIABLE' AND `base_key`='SYSTEM__DB_VERSION'",Q_RET_NONE);
      }
   }
   return_status_json($status,$info);
}

//This function will return the path to the effective database schema for each module
function effective_schema(){
   $schema_file=A_CORE.'/database_schema.php';

   //include the selected module database schema
   if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE]['schema_module']) && $_SESSION[PAGE]['schema_module'] != 'core' && $_SESSION[PAGE]['schema_module'] != ''){
      $schema_file=A_MODULES.'/'.$_SESSION[PAGE]['schema_module'].'/core/database_schema.php';
   }
   return  $schema_file;
}

//Default execution function which will be included in db_admin controller 
function default_(){
   add_to_main_top("<div><center>".db_migration_form()."</center></div>");
   add_to_main_bottom("<div><center>".table_creation_form()."</center></div>");
}

?>
