<?php
/*
Tables of the system
*/
$system_tables=array(
   'program'         =>'program',          
   'users'           =>'users',              
   'role'            =>'role',              
   'permission'      =>'permission',              
   'base_data'       =>'base_data',         
   'filter'          =>'filter',         
   'news'            =>'news',         
   'log'             =>'log'              
);

$GLOBALS['S_TABLES']=$system_tables;


/*
 * Database connection and disconnection
 */
function opendb($DB=null) {
   $DB=$DB==null?$GLOBALS['DB']:$DB;
   $db_avail=true;

   switch($GLOBALS['DB_TYPE']){
   case 'mssql':
      $GLOBALS['CONNECTION'] = mssql_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS']);
      if(!mssql_select_DB($DB, $GLOBALS['CONNECTION'])){
         $db_avail=false;
      }
   break;
   default:
      $GLOBALS['CONNECTION'] = @mysqli_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'],$DB);
      /*
      if(!@mysqli_select_DB($DB, $GLOBALS['CONNECTION'])){
         $db_avail=false;
      }
      */
   break;
   }
   return $db_avail;
}

/*
 * Disconnect from the database
 */
function closedb() {
   switch($GLOBALS['DB_TYPE']){
   case 'mssql':
      mssql_close($GLOBALS['CONNECTION']);
   break;
   default:
      mysqli_close($GLOBALS['CONNECTION']);
   break;
   }
}

/*
 * connect ta custom database
 */
function opendb2($DB) {
   opendb($DB);
}

/*
 * Execute queries through this function to grab total handl over queris
 Requestabble types to be returned
 $type:
    null    -> array
   1      -> mysql_resource
   2      -> json
   3      -> json_file
   4      -> csv
   5      -> csv_file
*/

define('Q_RET_ARRAY'      ,'0');//default
define('Q_RET_MYSQL_RES','1');
define('Q_RET_NONE'      ,'2');

/*Store affected rows*/
$aff_rows=0;

/*Store num rows*/
$num_rows=0;

/*query ok*/
$query_ok=false;

/*sql error*/
$sql_error=null;

/**
Execute query
@param query: query string
@param type: type of database mysql/mssql
@param db: database to be queried
@param array_key: If this parameter set, this key will be used as the key when returning array
@param deleted: if deleted is true, it will only return the deleted redcords. if deleted=all, it will return all records, if deleted is null it will return only non deleted
*/
function exec_query($query,$type=null,$db=null,$array_key=null,$deleted=null,$no_connect=null){
   global $num_rows;
   global $aff_rows;
   global $query_ok;
   global $sql_error;

   $num_rows=0;
   $aff_rows=0;
   $query_ok=false;
   
   if(is_null($db)){
      $db=$GLOBALS['DB'];
   }

   //Sometimes the database connection is done externally eg: login 
   if($no_connect != true ){
      opendb($db);
   }

   //add filter to deselect recoreds marked as deleted
   //with this array it will only return non deleted
   $deleted_filter=array(
      '/WHERE/'   =>'WHERE deleted=false AND',
      '/ORDER/'   =>'WHERE deleted=false ORDER',
      '/GROUP/'   =>'WHERE deleted=false GROUP',
      '/$/'         =>' WHERE deleted=false'
   );

   //with this array it will only return deleted
   if($deleted==true){
      $deleted_filter=array(
         '/WHERE/'   =>'WHERE deleted=true AND',
         '/ORDER/'   =>'WHERE deleted=true ORDER',
         '/GROUP/'   =>'WHERE deleted=true GROUP',
         '/$/'         =>' WHERE deleted=true'
      );
   }

   //If deleted not set to all query will be edited to return either deleted or non deleted  else return all records(bypass this iteration)
   /*
   if($deleted != 'all'   &&  preg_match('/SELECT/',$query) > 0 ){
      foreach($deleted_filter as $key => $value){
         $edited_query=preg_replace($key,$value,$query);
         if($edited_query!=$query){
            $query=$edited_query;
            break;
         }
      }
   }
    */

   //Enable this to log all the queries
   log_msg('exec_query',$db.":".$query);

   /*Execute query*/
   if(mysqli_multi_query($GLOBALS['CONNECTION'],$query)){
      $query_ok=true;
   }else{
      $query_ok=false;
   }
   
   /*Set affected rows*/
   $aff_rows   =mysqli_affected_rows($GLOBALS['CONNECTION']);
   $sql_error  =mysqli_error($GLOBALS['CONNECTION']);

   /*If result is false then return false*/
   if(mysqli_more_results($GLOBALS['CONNECTION'])){
      if($no_connect != true ){
         closedb();
      }
      /*Not a resource but success query*/
		/*
      if($result){
         return true;   
      }else{
         return false;   
      }
		*/
		//even thouthe query fails 
   	switch($type){
         case Q_RET_MYSQL_RES:
            return $result;
         break;
         case Q_RET_ARRAY:
         default:
            return array();
			break;
		}


   }else{
      /*Set num rows*/
      //$num_rows=mysql_num_rows($result);
   }

   switch($type){
      case Q_RET_MYSQL_RES:
         //Go throgh all the results from multy query and collect them in to an array
         $results=array();
         while(true){
            if ($result = mysqli_store_result($GLOBALS['CONNECTION'])) {
               $results[]=$result;
               while($row=mysqli_fetch_assoc($result)){
                  log_msg($row);
               }
               mysqli_free_result($result);
            }
            if(mysqli_more_results($GLOBALS['CONNECTION'])){
               mysqli_next_result($GLOBALS['CONNECTION']);
            }else{
               return;
            }
         }
         
         //If the results have only on result then return it as value not as array
         if(sizeof($results) > 1){
            return $results;
         }else{
            return $results[0];
         }
      break;
      case Q_RET_NONE:
      break;
      case Q_RET_ARRAY:
      default:
         $res_array   = array();

         /*If there are mayn row create two dimentional array*/
         $i=0;
         while(true){
            if ($result = mysqli_store_result($GLOBALS['CONNECTION'])) {
               $res_res_array=array();
               while ($row = mysqli_fetch_assoc($result)) {
                  if($array_key!=null){
                     $temp_key=$row[$array_key];
                     unset($row[$array_key]);
                     $res_res_array[$temp_key]=$row;
                  }else{
                     $res_res_array[]=$row;
                  }

               }
            $res_array[$i++]=$res_res_array;
            mysqli_free_result($result);
            }
            if(mysqli_more_results($GLOBALS['CONNECTION'])){
               mysqli_next_result($GLOBALS['CONNECTION']);
            }else{
               break;
            }
         }

         //If we have only one element in $res_array  then return just that element
         if(sizeof($res_array) > 1){
            return $res_array;
         }else{
            if(isset($res_array[0])){
               return $res_array[0];
            }else{
               return array();
            }
         }

         if($no_connect != true ){
            closedb();
         }
      break;
   }
}

/*Return sql error*/
//Duplicate entry 'nmla@ucsc.lk' for key 'email'
function get_sql_error(){
   global $sql_error;
   if(!is_null($sql_error)){
      //return mysql_escape_string($sql_error);
      return str_replace("'","`",$sql_error);
   }else{
      return false; 
   }
}

/*Return affected rows from current query*/
function get_affected_rows(){
   global $aff_rows;
   return $aff_rows;
}

/*Return num rows selected from current query*/
function get_num_rows(){
   global $num_rows;
   return $num_rows;
}

function is_query_ok(){
   global $query_ok;
   return $query_ok;
}

/*
function db_to_csv($query,$csv_file,$db=null){
   $query="
   $query   
   INTO OUTFILE '$csv_file'
   FIELDS TERMINATED BY ','
   ENCLOSED BY '\"'
   LINES TERMINATED BY '\\n';";

   return exec_query($query,Q_RET_MYSQL_RES,$db);
}
 */
function db_to_csv($query,$csv_file,$db=null){
   db_to_csv_nr($query,$csv_file,$db=$db);
}

/*
db to csv data export function for non root users
*/
function db_to_csv_nr($query,$csv_file,$header_array=null,$delimiter=",",$enclosure='"',$terminator="\n",$db=null){
   $res    = exec_query($query,Q_RET_MYSQL_RES,$db);
   set_file_header($csv_file);
   $header=false;

   //print column header
   if(!is_null($header_array)){
      echo $enclosure.implode($enclosure.$delimiter.$enclosure,$column_array).$enclosure.$terminator;
      $header=true;
   }

   //print lines
   while($row = mysql_fetch_assoc($res)){
      if(!$header){
         echo $enclosure.implode($enclosure.$delimiter.$enclosure,array_keys($row)).$enclosure.$terminator;
         $header=true;
      }else{
         echo $enclosure.implode($enclosure.$delimiter.$enclosure,array_values($row)).$enclosure.$terminator;
      }
   }
   exit();
}

/**
 * field array should imply the order of columns in csv
 * data import from csv functio for non root users
 */
function csv_to_db($csv_file,$table,$field_array,$delimiter=',',$enclosure="'",$first_line_header=true,$first_line_columns=false,$db=null){
   $first_line =true;
   $comma      ="";
   $query      ="INSERT INTO $table(`".implode($field_array,'`,`')."`)VALUES";
   //Mac support
   ini_set('auto_detect_line_endings',TRUE);
   if (($handle = fopen($csv_file, "r")) !== FALSE) {
      //fgetcsv will not work with null values (eg: for enclosure)
      if (is_null($enclosure) || $enclosure != '') {
         while(($line=fgetcsv($handle,$length=0,$delimiter)) !== FALSE){
            if($first_line_columns && $first_line){
               $field_array=$line;
               $query      ="INSERT INTO $table(`".implode($field_array,'`,`')."`)VALUES";
            }
            if($first_line_header && $first_line){
               $first_line=false;
               continue;
            }
            $query   .=$comma."('".implode($line,"','")."')";
            $comma   =",";
         }
      }else{
          while(($line=fgetcsv($handle,$length=0,$delimiter,$enclosure)) !== FALSE){
            if($first_line_columns && $first_line){
               $field_array=$line;
               $query      ="INSERT INTO $table(`".implode($field_array,'`,`')."`)VALUES";
            }
            if($first_line_header && $first_line){
               $first_line=false;
               continue;
            }
            $query   .=$comma."('".implode($line,"','")."')";
            $comma   =",";
         }
      }
      exec_query($query,Q_RET_NONE,$db);
   }else{
      return false; 
   }
   fclose($handle);
   return true; 
}

function csv_to_db2($csv_file,$table,$field_array,$delimiter,$encloser,$terminator,$first_line_header,$db=null){
   /*If the first line header is true  ignore first line*/
   $ignore_first_line="";
   if($first_line_header){
      $ignore_first_line="IGNORE 1 LINES";
   }
   $query="LOAD DATA LOCAL INFILE '$csv_file' INTO TABLE $table FIELDS TERMINATED BY '$delimiter' ENCLOSED BY '$encloser' LINES TERMINATED BY '$terminator' $ignore_first_line (".implode(',',$field_array).")";
   return exec_query($query,Q_RET_MYSQL_RES,$db);
}

/*Addd prefix to each array entrye*/
function add_prefix(&$value,$key,$prefix){
   $value=sprintf($value,$prefix,$prefix,$prefix,$prefix,$prefix,$prefix,$prefix,$prefix);
}

/*Addd prefix to each table to reflect the module*/
function add_table_prefix(&$schemas,$prefix){
   array_walk($schemas,'add_prefix',$prefix."_");
}

//add_table_prefix($program_table_schemas,'bit');


/*
 * Generic table creation function
 */
function create_tables($schemas=null){
   $state=true;

   foreach($schemas as $key=>$schema){
      if(exec_query($schema,Q_RET_MYSQL_RES)){
         log_msg('create_tables',"[OK] ".$key);
      }else{
         log_msg('create_tables',"[ERROR] ".get_sql_error());
         $state=false;
      }
   }
   return $state;
}


/**
 * chec if the given schema is a table or view
 */
function is_view($schema){
   $arr=exec_query("SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW' AND TABLES_IN_".$GLOBALS['DB']." LIKE '$schema'",Q_RET_ARRAY);
   if(isset($arr[0])){
      return true;
   }else{
      return false;
   }
}

function drop_tables($tables){
   $state=true;
   foreach($tables as $key=>$name){
      if(is_view($name)){
         if(exec_query("DROP VIEW ".$name,Q_RET_MYSQL_RES)){
            log_msg('drop_view',$name);
         }else{
            log_msg('drop_view',get_sql_error());
            $state=false;
         }
      }else{
         $del_res=exec_query("SELECT * FROM ".$name,Q_RET_MYSQL_RES);

         /*IF the table have data backup the table instead of deleting*/
         if(get_num_rows()>0){
            if(exec_query("RENAME TABLE ".$name." TO ".$name."_BAK_".Date('d_m_Y'),Q_RET_MYSQL_RES)){
               log_msg('rename_tables',$name);
            }else{
               log_msg('rename_tables',get_sql_error());
               $state=false;
            }
         }else{
            if(exec_query("DROP TABLE ".$name,Q_RET_MYSQL_RES)){
               log_msg('drop_tables',$name);
            }else{
               log_msg('drop_tables',get_sql_error());
               $state=false;
            }
         }
      }
   }
   return $state;
}

/*
 * Generic mysql function creation function
 */
function create_functions($schemas=null){
   $state=true;

   foreach($schemas as $key=>$schema){
      if(exec_query($schema,Q_RET_MYSQL_RES)){
         log_msg('create_functions',"Creating function:$key");
      }else{
         log_msg('create_functions',get_sql_error());
         $state=false;
      }
   }
   return $state;
}

/*
 * Delete triggers 
 */
function drop_functions($schemas){
   $state=true;
   foreach($schemas as $name=>$sql){
     if(exec_query("DROP TRIGGER ".$name,Q_RET_MYSQL_RES)){
        log_msg('drop_system_tables',"Drop table:$name");
     }else{
        log_msg('drop_system_tables',get_sql_error());
        $state=false;
     }   
   }
   return $state;
}


/**
This function will create all the tables required to manage a program eg: BIT,BICT, BCSC

@param table_prefix prefix to be added when generating program tables eg: bit_, bcsc_, mcs_
DEPRICATED
*/

function create_program_tables($schemas=null){
   global $program_table_schemas;
   $state=true;

   //If a custom schema requested select that
   if($schemas != null){
      $program_table_schemas=$schemas;
   }

   echo "\n";
   foreach($program_table_schemas as $key=>$schema){
      if(exec_query($schema,Q_RET_MYSQL_RES)){
         log_msg('create_program_tables',"Creating table:$key");
      }else{
         log_msg('create_program_tables',get_sql_error());
         $state=false;
      }
   }
   return $state;
}

/**
This function will delete all the tables from the given program eg: BIT,BICT, BCSC
@param table_prefix prefix to be searched when deleting program tables eg: bit_, bcsc_, mcs_
*/



/**
This will create set of tables to be run the system. These tables are common for all programs
*/
function create_system_tables($schemas = null){
   global $system_table_schemas;
   $state=true;

   //If a custom schema requested overwrite default
   if($schemas != null){
      $system_table_schemas=$schemas;
   }

   foreach($system_table_schemas as $key=>$schema){
      if(exec_query($schema,Q_RET_MYSQL_RES)){
         log_msg('create_system_tables',"Creating table:$key");
      }else{
         log_msg('create_system_tables',get_sql_error());
         $state=false;
      }
   }
   return $state;
}

/**
testing
*/

$trigger="delimiter //
CREATE TRIGGER updtrigger BEFORE UPDATE ON Employee
FOR EACH ROW
BEGIN
IF NEW.Salary<=500 THEN
SET NEW.Salary=10000;
ELSEIF NEW.Salary>500 THEN
SET NEW.Salary=15000;
END IF;
END
//
";

$view="
CREATE VIEW myView AS SELECT id, first_name FROM employee WHERE id = 1;
";



$stored_procedure="
delimiter //
DROP PROCEDURE IF EXISTS colavg//
CREATE PROCEDURE colavg(IN tbl CHAR(64), IN col CHAR(64))
READS SQL DATA
COMMENT 'Selects the average of column col in table tbl'
BEGIN
SET @s = CONCAT('SELECT AVG(' , col , ') FROM ' , tbl);
PREPARE stmt FROM @s;
EXECUTE stmt;
END;
//

CALL colavg('Country', 'LifeExpectancy');

";


   
   
?>
