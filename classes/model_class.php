<?php
/*
 class to generate the gui components of the form using dojo and php
 */
class Model{

   protected $model      ="_mdl%s.php";

   /*
    * Field types array
    */

   protected $types=array(
      "TINYINT(1)"  =>"dijit.form.CheckBox",
      "TINYINT"     =>"dijit.form.NumberTextBox",
      "SMALLINT"    =>"dijit.form.NumberSpinner",
      "MEDIUMINT"   =>"dijit.form.NumberTextBox",
      "INT"         =>"dijit.form.NumberTextBox",
      "INTEGER"     =>"dijit.form.NumberTextBox",
      "BIGINT"      =>"dijit.form.NumberTextBox",

      "FLOAT"       =>"dijit.form.ValidationTextBox",
      "DOUBLE"      =>"dijit.form.ValidationTextBox",
      "PRECISION"   =>"dijit.form.ValidationTextBox",
      "REAL"        =>"dijit.form.ValidationTextBox",
      "DECIMAL"     =>"dijit.form.ValidationTextBox",
      "NUMERIC"     =>"dijit.form.ValidationTextBox",

      "DATE"        =>"dijit.form.DateTextBox",
      "DATETIME"    =>"dijit.form.ValidationTextBox",
      "TIMESTAMP"   =>"dijit.form.ValidationTextBox",
      "TIME"        =>"dijit.form.TimeTextBox",
      "YEAR"        =>"dijit.form.DateTextBox",

      "CHAR"        =>"dijit.form.ValidationTextBox",
      "VARCHAR"     =>"dijit.form.ValidationTextBox",

      "TINYBLOB"    =>"dijit.form.ValidationTextBox",
      "BLOB"        =>"dijit.form.ValidationTextBox",
      "MEDIUMBLOB"  =>"dijit.form.ValidationTextBox",
      "LONGBLOB"    =>"dijit.form.ValidationTextBox",

      "TINYTEXT"    =>"dijit.form.ValidationTextBox",
      "TEXT"        =>"dijit.form.SimpleTextarea",
      "MEDIUMTEXT"  =>"dijit.form.ValidationTextBox",
      "LONGTEXT"    =>"dijit.form.SimpleTextarea",
      "ENUM"        =>"dijit.form.ValidationTextBox",
      "SET"         =>"dijit.form.ValidationTextBox"
      );

      protected   $table               ="";      //effective teble
      protected   $primary_key         ="";      //key/primary key field of the table

      protected   $filter_table        ="";      //effective filter teble
      protected   $filter_primary_key  ="";      //effective filter table key

      /*Form will be pre filled with the data correspond to this key*/
      protected   $data_load_key=null;

      /*
      Constructure
      */
      function __construct($table,$primary_key,$name,$filter_table=null,$filter_primary_key=null) {
         $this->table               =$table;
         $this->primary_key         =$primary_key;
         $this->filter_table        =$filter_table;
         $this->filter_primary_key  =$filter_primary_key;

         if(isset($data_load_key) && $data_load_key != null ){
            $this->data_load_key=$data_load_key;
         }
         
         /*Check and aply custom file name to save modifier and help files to save*/
         if(isset($name) && $name != null ){
            $this->model     = A_MODULES."/".MODULE."/".$name.$this->model;
         }else{
            $this->model     = A_MODULES."/".MODULE."/".$table.$this->model;
         }

         //Setting group wise model file if available else drop to default
         if(isset($_SESSION['group_id'])&&file_exists(sprintf($this->model,$_SESSION['group_id']))){
            $this->model=sprintf($this->model,$_SESSION['group_id']);
         }else{
            $this->model=sprintf($this->model,'');
         }

         $this->load_modifier();
      }

      
      /*Change default table and key*/
      function set_table($table=null,$key=null){
         if($table != null){
            $this->table =$table;
         }

         if($key != null){
            $this->primary_key = $key;
         }
      }

      /*
       fields of the table

       array(
       [0] => IndexNo
       [Field] => IndexNo
       [1] => varchar(8)
       [Type] => varchar(8)
       [2] => YES
       [Null] => YES
       [3] =>
       [Key] =>
       [4] =>
       [Default] =>
       [5] =>
       [Extra] =>
       )

       converted to ----->

       $fields=array(
       "IndexNo"=>array(
         "length"=>"56",
         "type"=>"dijit.form.ValidationTextBox",
         "required"=>"true",
         "name"=>"IndexNo",
         "value"=>""
         ),
         "RegNo"=>array(
         "length"=>"140",
         "type"=>"dijit.form.ValidationTextBox",
         "required"=>"true",
         "name"=>"RegNo",
         "value"=>""
         ),
         */
      protected $fields=array();

      //Primary key and unique keys
      protected $keys=array();

      /*
       A data tuple of the given table will be filled to this array
       */
      protected $data=array();

      /*
       Load configuration from the file if exits
       els load default configuration from the raw database
       */

      public function load_modifier(){
         $config=$this->model;
         if(file_exists($config)){
            require_once($config);
            if(isset($GLOBALS['MODEL'])){
               $GLOBALS['MODEL']['MAIN_LEFT']=$GLOBALS['MODEL']['MAIN_LEFT'];
            }
         }else{
            $res=exec_query("SHOW COLUMNS FROM ".$this->table,Q_RET_ARRAY);

            /*If no result returned*/
            if(get_num_rows() <= 0){
               echo "Error showing table '".$this->table."' !";   
               return;
            }

            foreach($res as $row) {
               //Find the primary key
               if(strtoupper($row['Key'])=='PRI'){
                  $this->keys['PRIMARY_KEY']=$row['Field'];
               }elseif(strtoupper($row['Key'])=='UNI'){
                  //Find the unique key
                  if(!isset($this->keys['UNIQUE_KEY'])){
                     $this->keys['UNIQUE_KEY']=array();
                  }
                  $this->keys['UNIQUE_KEY'][]=$row['Field'];
               }elseif(strtoupper($row['Key'])=='MUL'){
                  //Find the multiple key
                  if(!isset($this->keys['UNIQUE_KEY'])){
                     $this->keys['MULTY_KEY']=array();
                  }
                  $this->keys['MULTY_KEY'][]=$row['Field'];
               }
               if(strtoupper($row['Extra'])!='AUTO_INCREMENT'){
                  $GLOBALS['MODEL']['MAIN_LEFT'][$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"    =>$this->get_field_type($row['Type']),
                  "required"    =>($row['Null']=='YES')?"false":"true",
                  "label"       =>style_text($row['Field']),
                  "label_pos"   =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }else{
                  $GLOBALS['MODEL']['MAIN_LEFT'][$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"    =>$this->get_field_type($row['Type']),
                  "type"        =>"hidden",
                  "required"    =>"false",
                  "label"       =>style_text($row['Field']),
                  "label_pos"   =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }
            }
            $this->write_config();
            $this->generate_help_file();
         }
      }

      /*
       Write configuration of current table to a php file which can be customized by the user for

       */


      public function write_config(){
         $main_right=<<<EOE
   'MAIN_RIGHT'=>array(
       'GRID'=>array(
          'columns'      =>array('rid','class','name','status'),
          'filter'       =>'',
          'selector_id'  =>'toolbar__rid',
          'dojoType'     =>'dojox.grid.DataGrid',
          'jsId'         =>'grid3',
          'store'        =>'store3' ,
          'query'        =>'{ "rid": "*" }',
          'rowsPerPage'  =>'40',
          'clientSort'   =>'true',
          'style'        =>'width:100%;height:400px',
          'onClick'      =>'load_grid_item',
          'rowSelector'  =>'20px',
          'columnReordering'=>'true',
          'headerMenu'   =>'gridMenu',
       ),
    ),
EOE;
         $common_toolbar_buttons=<<<EOE
      "rid"=>array(
         "length"=>"70",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"false",
         "label"=>"Label",
         "label_pos"=>"left",

         "onChange"=>'set_param(this.name,this.value);fill_form(this.value,"main")',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset(\$_SESSION[PAGE]['rid'])?"student_year='".\$_SESSION[PAGE]['rid']."'":null,
         "ref_table"=>\$GLOBALS['MOD_P_TABLES']['batch'],
         "ref_key"=>'rid',
         "order_by"=>'ORDER BY rid DESC',
         "vid"=>array('rid','rid'),
      ),  

      "add"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Add",
         "iconClass"=>get_icon_class('NewPage'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("add")',
      ),  
      "modify"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Modify",
         "iconClass"=>get_icon_class('Save'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("modify")',
      ),  
      "remove"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Delete",
         "iconClass"=>get_icon_class('Delete'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("delete")',
      ),
     "filter"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"FIlter",
         "iconClass"=>get_icon_class('Filter'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("add_filter")',
      ),  
      "reset_ filter"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Reset Filter",
         "iconClass"=>get_icon_class('Delete'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("delete_filter")',
      ),
EOE;
    
         $config=$this->model;
         if(!file_exists($config)){
            $file_handler = fopen($config, 'w');

            fwrite($file_handler, "<?php\n");
            fwrite($file_handler, "\$GLOBALS['MODEL']=array(\n");
            fwrite($file_handler, "//-----------------KEY FIELDS OF THE MODEL----------------------\n");
            fwrite($file_handler, tab(1)."'KEYS'=>array(\n");
            fwrite($file_handler, tab(2)."'PRIMARY_KEY'\t=>'".$this->keys['PRIMARY_KEY']."',\n");
            fwrite($file_handler, tab(2)."'UNIQUE_KEY'\t=>array('".(isset($this->keys['UNIQUE_KEY'])?implode("','",$this->keys['UNIQUE_KEY']):'')."'),\n");
            fwrite($file_handler, tab(2)."'MULTY_KEY'\t=>array('".(isset($this->keys['MULTY_KEY'])?implode("','",$this->keys['MULTY_KEY']):'')."'),\n");
            fwrite($file_handler, tab(1)."),\n");

            fwrite($file_handler, "//--------------FIELDS TO BE INCLUDED IN FORM-------------------\n");
            fwrite($file_handler, "//---------------THIS ALSO REFLECT THE TABLE--------------------\n");
            //write in to form related fields which reflect the form
            fwrite($file_handler, tab(1)."'MAIN_LEFT'=>array(");

            $comma1="";
            foreach($GLOBALS['MODEL']['MAIN_LEFT'] as $field => $arr){
               $comma2="";
               fwrite($file_handler, $comma1."\n".tab(2)."\"".$field."\"=>array(");
               foreach($arr as $key => $value){
                  fwrite($file_handler, $comma2."\n".tab(3)."\"".$key."\"\t=>\"".$value."\"");
                  $comma2=",";
               }
               fwrite($file_handler, ",\n".tab(3)."\"value\"=>\"\"\n".tab(2).")");
               $comma1=",";
            }
            fwrite($file_handler, "\n".tab(1)."),\n");

            
            fwrite($file_handler, "//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------\n");
            //write the toolbar related fields
            fwrite($file_handler, $main_right."\n");
            fwrite($file_handler, tab(1)."'TOOLBAR'=>array(\n".$common_toolbar_buttons."\n".tab(1)."),\n");
            fwrite($file_handler, tab(1)."'WIDGETS'=>array(\n".tab(1)."),\n");
            fwrite($file_handler, ");");
            fwrite($file_handler, "\n?>\n");
            fclose($file_handler);
         }
      }

      /*generate help file to provide help tooltip for each field of the form*/
      public function generate_help_file(){
         $config=$this->model;
         if(!file_exists($config)){
            $file_handler = fopen($config, 'w');
            fwrite($file_handler, "<!--Auto generated by form_gen.php-->\n");
            fwrite($file_handler, "<?php\n");
            //fwrite($file_handler, "\$help_".$this->table."=array(");
            fwrite($file_handler, "\$help_array=array(");

            $comma="";
            foreach($GLOBALS['MODEL']['MAIN_LEFT'] as $field => $arr){
               fwrite($file_handler, $comma."\n'$field'=>'".$arr['label']."'");
               $comma=",";
            }
            fwrite($file_handler, ");\n ?>");
            fclose($file_handler);
         }
      }

      /**
       * return default label posisition
       */
      public function get_label_pos($dojo_type){
         if($dojo_type == 'dijit.form.CheckBox'){
            return 'right';
         }else{
            return 'top';
         }
      }

      /*
       * Return length of a field of the table
       */
      public function get_field_width($type,$actual=false){
         $width="100";

         //varchar(100)
         $arr=explode("(", $type);

         if(isset($arr[1])){
            $width=str_replace(")", "", $arr[1]);
            if(!$actual){
               if($width<5){
                  $width=10;
               }elseif($width>80){
                  $width=50;
               }
            }
            $width*=7;
         }
         return $width;
      }


      /*
       * Retrurn dojo type of the filed which should be associated
       */
      public function get_field_type($type){
         $type=strtoupper($type);

         $field_type=$this->types['VARCHAR'];

         //varchar(100) remove brackets 
         $arr=explode("(", $type);

         if(isset($this->types[$type])){
            $field_type=$this->types[$type];
         }elseif(isset($this->types[$arr[0]])){
            $field_type=$this->types[$arr[0]];
         }

         //If the text field is very long generate a text area
         if($field_type=="dijit.form.ValidationTextBox" && $this->get_field_width($type,true) > 700 ){
            $type="LONGTEXT";
         }

         //log_msg('type',$type);
         return $field_type;
      }


      /*
       Populate data from the table to the array
       */
      public function get_data($filter=null){
         $where="";

         /*
         if(isset($_REQUEST[$this->primary_key])){
            $where="WHERE ".$this->primary_key." = '".$_REQUEST[$this->primary_key]."'";
         }
         */

         if($this->data_load_key != null){
            $where="WHERE ".$this->primary_key." = '".$this->data_load_key."'";
         }

         $res=exec_query("SELECT * FROM ".$this->table." $where",Q_RET_ARRAY);
         if(isset($res[0])){
            $row=$res[0];
            foreach($GLOBALS['MODEL']['MAIN_LEFT'] as $field => $value ){
               /*Ignore custom field names*/
               if(isset($row[$field])){
                  $this->data[$field]=$row[$field];
               }
            }
         }
      }

      /**
       * Generate temporary filter for the submitted values
       */
      public function get_temp_filter(){
         $filter="";
         $and="";
         foreach(array_keys($GLOBALS['MODEL']['MAIN_LEFT']) as $key){
            if($key != $this->primary_key && isset($_REQUEST[$key]) && $_REQUEST[$key] != '' && $_REQUEST[$key] != 'NULL' ){
               $filter.=$and."`".$key."` LIKE '%".$_REQUEST[$key]."%'";
               $and=' AND ';
            }
         }
         return $filter; 
      }

      /*retrieve filter from the database*/
      public function ret_filter($filter_name,$table=null){

         $select="SELECT filter FROM ".$this->filter_table." WHERE filter_name='".$filter_name."'";

         if($table != null){
            $select="SELECT filter FROM ".$table." WHERE filter_name='".$filter_name."'";
         }

         $res=exec_query($select,Q_RET_ARRAY);
         /*if no value returned stop execution*/
         if(!$res)return;

         /*read the json string to  an array*/
         $filter_array=json_decode($res[0]['filter'],true);

         /*remove filter name form the array*/
         unset($filter_array['filter_name']);

         $filter="";
         $and="";
         foreach($filter_array as $key => $value){
            if(!preg_match("/_and$|_exact$|filter_filter_name$/",$key))
            {
               if($value != ''){
                  if($filter_array[$key."_exact"]=="null"){
                     $filter.="$and".str_replace('filter_','',$key)."='$value'";   
                  }else{
                     //$filter.="$and".str_replace('filter_','',$key)." LIKE '%$value%'";   
                     $filter.="$and".str_replace('filter_','',$key)." LIKE '$value%'";   
                  }

                  if($filter_array[$key."_and"]=="null"){
                     $and=" OR ";
                  }else{
                     $and=" AND ";
                  }
               }
            }
         }
         return $filter;
      }
      
      /*
       store filte in filter table
       */

      public function add_filter($table=null){
         $fields=array(
            "table_name",
            "user_id",
            "filter",
            "filter_name"
         );

         $_REQUEST['table_name']   =$this->table;
         $_REQUEST['user_id']      =$_SESSION['user_id'];

         /*
         format the json string before storing in db
         [] and ["on"] are from check boxes replace them with null or any value will do the job
         */
         $_REQUEST['filter']      =str_replace(
            array('&quot;','NaN','false','[]','["on"]'),
            array('"','""','null','null','"on"'),
            $_REQUEST['filter']
         );

         /*extract filter_name from json string and store it seperately in column to make ease */
         $filter=json_decode($_REQUEST['filter']);
         $_REQUEST['filter_name']      =$filter->{'filter_filter_name'};

         $insert="INSERT INTO ".$this->filter_table."(%s) VALUES(%s)";

         if($table != null){
            $insert="INSERT INTO ".$table."(%s) VALUES(%s)";
         }

         $cols="";
         $values="";
         $comma="";

         /*generate column and value string for the query */
         foreach( $fields as $key){
            $cols.=$comma.$key;
            $values.=$comma."'".$_REQUEST[$key]."'";
            $comma=",";
         }

         $insert=sprintf($insert,$cols,$values);
         $res=exec_query($insert,Q_RET_MYSQL_RES);

         /*report error/success*/
         if(get_affected_rows() != 0){
            return_status_json('OK','record inserted successfully');
            return true;
         }else{
            return_status_json('ERROR','error inserting record');
            return false;
         }
      }

      /*Delete filter from the filter table following the filter_id*/
      public function delete_filter($table=null,$purge=false){
          /*extract filter id from json*/
         $filter=json_decode(str_replace(array("&quot;","NaN"),array('"','""'),$_REQUEST['filter']));
         $filter_name=$filter->{'filter_name'};

         $delete="UPDATE ".$this->filter_table." SET deleted=TRUE WHERE filter_name='".$filter_name."'";

         if($purge){
            $delete="DELETE FROM ".$this->filter_table." WHERE filter_name='".$filter_name."'";
         }

         if($table != null){
            $delete="UPDATE ".$this->filter_table." SET deleted=TRUE WHERE filter_name='".$filter_name."'";
            if($purge){
               $delete="DELETE FROM ".$this->filter_table." WHERE filter_name='".$filter_name."'";
            }
         }


         $res=exec_query($delete,Q_RET_MYSQL_RES);

         /*report error/success*/
         if(get_affected_rows()!= 0){
            return_status_json('OK','record deleted successfully');
            return true;
         }else{
            return_status_json('ERROR','error deleting record');
            return false;
         }
       }

      //TODO:
      public function modify_filter($table=null){
         $_REQUEST['filter']=str_replace(
            array('&quot;','NaN','false','[]','["on"]'),
            array('"','""','null','null','"on"'),
            $_REQUEST['filter']
         );

         $filter         =json_decode($_REQUEST['filter'],true);
         $filter_name   =$filter['filter_name'];

         /*remove filter_name from filter json string*/
         unset($filter['filter_name']);

         /*decode back the arry to json*/
         $filter         =json_encode($filter);

         /*update query*/
         $update="UPDATE ".$this->filter_table." SET  filter='$filter' WHERE filter_name='$filter_name''";

         if($table != null){
            $update="UPDATE ".$table." SET  filter='$filter' WHERE filter_name='$filter_name''";
         }

         $res=exec_query($update,Q_RET_MYSQL_RES);
         
         /*report error/success*/
         if(get_affected_rows() != 0){
            return_status_json('OK','record updated successfully');
         }else{
            return_status_json('OK','error updating record');
         }
      }

       /*
       $key_array: the list of fields to be included in json file
       filter: filter to be applied in WHERE of the query
       return: if this is true function will return the value, else it will echo
       return/Generate: JSON from the table with given fields in key_array
       */
      public function gen_json($key_array,$filter=null,$return,$table=null){
         $where=" WHERE ";

         if($filter != null){
            $where.=$filter;
         }else{
            $where="";
         }

         /*Custom table*/
         $table=$table==null?$this->table:$table;   

         $res=exec_query("SELECT ".implode(",",$key_array)." FROM ".$table." $where",Q_RET_ARRAY);

         /*No results returned*/
         if(get_affected_rows() < 0){
            //return_status_json('ERROR',"Erorr selecting table $table");
            log_msg('ERROR',"Erorr selecting table $table");
            return;   
         }

         /*-----------------generate json-------------------*/
         $json = "{\nidentifier:'".$key_array[0]."',\n";
         $json .=   "label: '".$key_array[0]."',\n";
         $json .= "items: [\n";
         $comma1="";
         foreach ($res as $row) {
            $comma2="";
            $json .="$comma1{";
            foreach($key_array as $key){
               $json .=$comma2.$key.":'".$row[$key]."'";
               $comma2=",";
            }
            $comma1=",";
            $json .="}\n";
         }
         $json .="]\n}\n";

         /*-----------------generate json-------------------*/

         //Return JSON if requested to return else return the file
         if($return){
            echo  $json;
            return;
         }

         $json_fileW=W_MODULES."/".MODULE."/".$this->table.$this->data_json;
         $json_fileA=A_MODULES."/".MODULE."/".$this->table.$this->data_json;

         //Save JSON to file
         $file_handler = fopen($json_fileA, 'w');
         fwrite($file_handler,$json);
         fclose($file_handler);
         return  $json_fileW;
      }


     /**
       * Generate csv for the given query
       */
      public function gen_grid_csv(){
         $columns    =array_keys($GLOBALS['MODEL']['MAIN_LEFT']);
         $table      =$this->table;
         $filter_str =isset($_SESSION[PAGE]['FILTER'])?" WHERE ".$_SESSION[PAGE]['FILTER']:"";

         if(isset($GLOBALS['MODEL']['MAIN_RIGHT']['GRID']['columns'])){
            $columns=$GLOBALS['MODEL']['MAIN_RIGHT']['GRID']['columns'];
         }
         if(isset($GLOBALS['MODEL']['MAIN_RIGHT']['GRID']['ref_table'])){
            $table=$GLOBALS['MODEL']['MAIN_RIGHT']['GRID']['ref_table'];
         }

         $fields=implode(",",$columns);

         $query="SELECT $fields FROM ".$table.$filter_str;
         
         $csv_file= $table.".csv";
         db_to_csv_nr($query,$csv_file);
         return;
      }


      /**
       * Generate csv for the given query
       */
      public function gen_csv(){
         $filter_str=isset($_SESSION[PAGE]['FILTER'])?" WHERE ".$_SESSION[PAGE]['FILTER']:"";
         $columns=array_keys($GLOBALS['MODEL']['MAIN_LEFT']);
         
         $fields=implode(",",$columns);
         $query="SELECT $fields FROM ".$this->table.$filter_str;
         
         $csv_file= $this->table.".csv";
         db_to_csv_nr($query,$csv_file);
         return;
      }


     
      /*
       Provide data to  gen_xhrt_filtering_select
       @$table: custom table to be queried
       @$key: custom key to be presented
       @$filter: custom filter to be applied
       */
      //public function xhr_filtering_select_data($table=null,$key=null,$filter=null,$order_by=null){
      public function xhr_filtering_select_data($key,$section=null){
         $vid        =null;
         $key_       =null;
         $field_array=null;
  
         if(!is_null($section)){
            $field_array=$GLOBALS['MODEL'][$section][$key];
         }else{
            $field_array=$GLOBALS['MODEL']['MAIN_LEFT'][$key];
         }

         if(isset($field_array['ref_key'])){
            $key=$field_array['ref_key'];
         }

         if(isset($field_array['vid'])){
            $key_=array($key=>$field_array['vid']);
         }else{
            $key_=$key; 
         }

         $table   =$this->table;
         if(isset($field_array['ref_table'])){
            $table   =$field_array['ref_table'];
         }

         $filter  =null;
         if(isset($field_array['filter'])){
            $filter=$field_array['filter'];
         }

         $order_by  =null;
         if(isset($field_array['order_by'])){
            $order_by=$field_array['order_by'];
         }

         header('Content-Type', 'application/json');
         include 'qread_store_class.php';
         $query_read_store = new Query_read_store($table,$key_,$filter,$order_by,$key);
         echo $query_read_store->gen_json_data();
      }
      /*
       $question: A value provided for  $key field  fo the table
       return    : JSON formatted tupple related to the given $key from the given table
       */

      public function xhr_form_filler_data($qustion,$cus_table=null,$cus_key=null){
         $table   =$this->table;
         $f_key   =$this->primary_key;

         if($cus_table != null){
            $table=$cus_table;   
         }
         if($cus_key != null){
            $f_key=$cus_key;   
         }

      
         $comma="";
         $cols="";

         //Dates request formatted from MySQL
         foreach( $GLOBALS['MODEL']['MAIN_LEFT'] as $key => $arr){
            //if(isset($arr['custom']) || isset($arr['store'])){
            if(isset($arr['custom'])){
               continue;
            }else{
               if($arr['dojoType']=="dijit.form.DateTextBox"){
                  //$cols.=$comma."UNIX_TIMESTAMP(".$key.") as $key";
                  //$cols.=$comma."DATE_FORMAT(".$key.",'%Y-%m-%dT%h:%i:%s.789') as $key";
                  //$cols.=$comma."DATE_FORMAT(".$key.",'%Y-%m-%d') as $key";
                  //ISO standard time should be provided to dojo to create javascript Date object
                  $cols.=$comma."DATE_FORMAT(".$key.",GET_FORMAT(DATE,'ISO')) as $key";
               }else{
                  $cols.=$comma.$key;
               }
               $comma=",";
            }
         }

         header('Content-Type', 'application/json');
         $res=exec_query("SELECT $cols FROM ".$table." WHERE ".$f_key." = '$qustion'",Q_RET_ARRAY);
         if(!isset($res[0])){
            return_status_json('ERROR','No entry found!');
            return;
         }
         $row=$res[0];
         $ret_array=array();
         foreach( $GLOBALS['MODEL']['MAIN_LEFT'] as $key => $arr){
            //if(isset($arr['custom']) || isset($arr['store'])){
            if(isset($arr['custom'])){
               continue;   
            }else{
               if($arr['dojoType']=="dijit.form.DateTextBox"){
                  //$ret_array[$key]="{_type:'Date',_value:'".$row[$key]."'}";
                  //Dates have issues with dojo it is speacially formatted as bellow
                  $ret_array[$key]=array('_type'=>'Date','_value'=>$row[$key]);
               }else{
                  $ret_array[$key]=$row[$key];
               }
            }
         }
         echo json_encode($ret_array);
      }

      /*
       $question: A value provided for $key field fo the filter table
       return    : JSON formatted tupple related to the given $key from the given table
       */

      public function xhr_filter_filler_data($qustion){
         $table=$this->filter_table;

         $res=exec_query("SELECT * FROM ".$table." WHERE filter_name = '$qustion'",Q_RET_ARRAY);
         $row = $res[0];
         header('Content-Type', 'application/json');
         echo $row['filter'];
      }

      /*
      check for duplicates
      */
      public function is_duplicate(){
         $filter='';
         //for multiple keys
         if(isset($this->self['keys'])){
            foreach($this->self['keys'] as $key){
               $filter.=$key."='".$_REQUEST[$key]."'";
            }
         }elseif(isset($_REQUEST[$this->primary_key])){
            $filter=$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
         }

         $sql="SELECT * FROM ".$this->table." WHERE ".$filter;
         $res=exec_query($sql,Q_RET_MYSQL_RES);
         if(get_num_rows() > 0){
            return true;
         }else{
            return false;
         }
      }


      protected $pwd_field_guess=array('password','passwd','pwd');
      /*Validate and add record to the table*/
      public function add_record(){

         //Log users activity
         act_log();

         /*vefiry captcha if it is set*/
         if(!verify_captcha()){
            return_status_json('ERROR','error verifying security code');
            return false;
         }

         
         if($this->is_duplicate()){
            return_status_json('ERROR',"Duplicate key exists");
            return false;
         }else{//key not available  ->  add
            $cols      =""; //coumns of the table
            $values   =""; //value for each column of the table
            $comma   ="";
            /*set columns and values for each column*/
            foreach( $GLOBALS['MODEL']['MAIN_LEFT'] as $key => $arr){
               /*Trying to ignore auto incrementing fields and custom fields(custom fields were handled below)*/
               //if( !( isset($arr['type']) && $arr['type'] == 'hidden') && !(isset($arr['custom']) && $arr['custom'] == 'true') && !(isset($arr['disabled']) && $arr['disabled'] == 'true')){
               if( !(isset($arr['custom']) && $arr['custom'] == 'true') && !(isset($arr['disabled']) && $arr['disabled'] == 'true')){
                  $cols      .=$comma.$key;
               
                  /*check for valid json strings to use as json strings in database*/
                  if(isset($_REQUEST[$key])){
                     $value=$_REQUEST[$key];
                  }else{
                     $_REQUEST[$key]="";
                     $value="";
                  }

                  $value=str_replace(
                     array('&quot;','NaN','\n'),
                     array('"','""',''),
                     $value
                  );
                  
                  /*apply md5 to the password fields*/
                  if(in_array(strtolower($key),$this->pwd_field_guess)){   
                     $_REQUEST[$key]=md5($value);
                  }
               
                  /*if the values is valid json then store clean string */
                  if(json_decode($value) != null ){
                     $_REQUEST[$key]=$value;
                  }
               
                  $values   .=$comma."'".$_REQUEST[$key]."'";
                  $comma   =",";
               }else{
                  log_msg('kk','lll');   
               }

               /*handle custom fields from form submission*/
               if(isset($arr['custom']) && $arr['custom'] == 'true' && !(isset($arr['disabled']) && $arr['disabled'] == 'true')){
                  if(isset($_REQUEST[$key]) && $_REQUEST[$key] != ''){
                     $cols      .=$comma.$key;
                     /*apply md5 to the password fields*/
                     if(in_array(strtolower($key),$this->pwd_field_guess)){   
                        //$_REQUEST[$key]=md5($value);
                        $_REQUEST[$key]=md5($_REQUEST[$key]);
                     }
                     $values   .=$comma."'".$_REQUEST[$key]."'";
                     $comma   =",";
                  }
               }
            }

            $insert_query   ="INSERT INTO ".$this->table."(%s) VALUES(%s)";
            $insert_query   =sprintf($insert_query,$cols,$values);
            $res            =exec_query($insert_query,Q_RET_MYSQL_RES);

            /*report error/success*/
            if(get_affected_rows() > 0){
               return_status_json('OK','record inserted successfully');
               return true;
            }else{
               return_status_json('ERROR',get_sql_error());
               return false;
            }
         }
      }


      /*Validate and update record in the table */
      public function modify_record(){
         $sql="SELECT * FROM ".$this->table." WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";

         //Log users activity
         act_log(null,$sql);

         $res=exec_query($sql,Q_RET_MYSQL_RES);
         if(get_affected_rows() > 0){
            //key available  -> modify
            $values   =""; //valus to be changes in the tupple
            $comma   ="";
            /*generate values string*/
            foreach( $GLOBALS['MODEL']['MAIN_LEFT'] as $key => $arr){

               /*Trying to ignore auto incrementing fields*/
               //if(!( isset($arr['type']) && $arr['type'] == 'hidden') && !(isset($arr['custom']) && $arr['custom'] == 'true') && !(isset($arr['disabled']) && $arr['disabled'] == 'true')){
               if(!(isset($arr['custom']) && $arr['custom'] == 'true') && !(isset($arr['disabled']) && $arr['disabled'] == 'true')){

                  /*check for valid json strings to use as json strings in database*/
                  $value=isset($_REQUEST[$key])?$_REQUEST[$key]:'';
                  $value=str_replace(
                     array('&quot;','NaN','\n'),
                     array('"','""',''),
                     $value
                  );
               
                  /*apply md5 to the password fields*/
                  if(in_array(strtolower($key),$this->pwd_field_guess)){   
                     $_REQUEST[$key]=md5($value);
                  }
               
                  /*if the values is valid json then store clean string */
                  if(json_decode($value) != null ){
                     $_REQUEST[$key]=$value;
                  }
               
                  if(isset($_REQUEST[$key])){
                     $values   .=$comma.$key."='".$_REQUEST[$key]."'";
                  }else{
                     $values   .=$comma.$key."=''";
                  }
                  $comma   =",";
               }

               /*handle custom fields from form submission*/
               if(isset($arr['custom']) && $arr['custom'] == 'true'){
                  if(isset($_REQUEST[$key]) && $_REQUEST[$key] != ''){

                     /*apply md5 to the password fields*/
                     if(in_array(strtolower($key),$this->pwd_field_guess)){   
                        //$_REQUEST[$key]=md5($value);
                        $_REQUEST[$key]=md5($_REQUEST[$key]);
                     }
                     $values   .=$comma.$key."='".$_REQUEST[$key]."'";
                     $comma   =",";
                  }
               }

            }
            $update_query   ="UPDATE ".$this->table." SET %s WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
            $update_query   =sprintf($update_query,$values);
            $res            =exec_query($update_query,Q_RET_MYSQL_RES);

            /*report error/success */
            if(get_affected_rows() > 0){
               return_status_json('OK','record updated successfully');
               return true;
            }else{
               return_status_json('OK','zero records affected');
               return false;
            }
         }else{
            return_status_json('ERROR','error updating record key does not exists');
            return false;
         }
      }

      /*
       delete record from the table
      @ $purge: delete everithing instead of placing delete flag (caution: can not recover)
       */
      public function delete_record($purge=false){
         $delete="UPDATE ".$this->table." SET deleted=true WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
      
         if($purge){
            $delete="DELETE FROM ".$this->table." WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
         }

         //Log users activity
         act_log(null,$delete);

         $res=exec_query($delete,Q_RET_MYSQL_RES);
         /*report error/success */
         if(get_affected_rows() > 0){
            return_status_json('OK','record deleted successfully');
            return true;
         }else{
            return_status_json('OK','error deleting record');
            return false;
         }
      }
   }
?>
