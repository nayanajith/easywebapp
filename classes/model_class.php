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
      function __construct($table,$name) {
			//New approach with multiple tables for multiple actions
			if(is_array($table)){
				$this->insert_table		=$table['insert'];
				$this->update_table		=$table['update'];
				$this->view_table			=$table['view'];
			}else{
				$this->insert_table     =$table;
				$this->update_table	   =$table;
				$this->view_table		   =$table;
			}

         $this->filter_table        =s_t('filter');
         $this->filter_primary_key  ='rid';

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
         if(isset($_SESSION['role_id'])){
            $arr=exec_query("SELECT file_prefix FROM ".s_t('role')." WHERE group_name='".$_SESSION['role_id']."'",Q_RET_ARRAY);
            $group_prefix='_'.$arr[0]['file_prefix'];
            $file=sprintf($this->model,$group_prefix);

            if(file_exists($file)){
               $this->model=$file;
            }else{
               $this->model=sprintf($this->model,'');
            }
         }else{
            $this->model=sprintf($this->model,'');
         }

         $this->load_model();
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
      //Primary key and unique keys
      protected $keys      =array();

      //form, grid, toolbar, widget arrays loaded from model
      protected $form      =array();
      protected $grids     =array();
      protected $toolbar   =array();
      protected $widgets   =array();

      //Callback functions fr the model class
      protected $callbacks =array();

      //A data tuple of the given table will be filled to this array
      protected $data=array();

      /**
       * Load configuration from the file if exits
       * els load default configuration from the raw database
       */

      public function load_model(){
         $config=$this->model;
         if(file_exists($config)){
            require_once($config);

            //Default model array will be applied for the  
            include_once A_CORE."/mdl_common.php";

            if(isset($GLOBALS['MODEL'])){
               //Load all the arrays into class variables
               $this->keys    =get_mdl_property('KEYS');
               $this->form    =get_mdl_property('FORM');
               $this->grids   =get_mdl_property('GRIDS');
               $this->toolbar =get_mdl_property('TOOLBAR');
               $this->widgets =get_mdl_property('WIDGETS');
               $this->callbacks =get_mdl_property('CALLBACKS');

               $this->primary_key   =get_pri_keys();
            }
         }else{
            $res=exec_query("SHOW COLUMNS FROM ".$this->insert_table,Q_RET_ARRAY);

            /*If no result returned*/
            if(sizeof($res) <= 0){
               echo "Error showing table '".$this->insert_table."' !";   
               return;
            }

            foreach($res as $row) {
               if(strtoupper($row['Extra'])!='AUTO_INCREMENT'){
                  $this->form[$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"    =>$this->get_field_type($row['Type']),
                  "required"    =>($row['Null']=='YES')?"false":"true",
                  "label"       =>style_text($row['Field']),
                  //"label_pos"   =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }else{
                  $this->form[$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"    =>$this->get_field_type($row['Type']),
                  "type"        =>"hidden",
                  "required"    =>"false",
                  "label"       =>style_text($row['Field']),
                  //"label_pos"   =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }
            }
            $arr=exec_query("SELECT COLUMN_NAME,REFERENCED_COLUMN_NAME,CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='".$this->insert_table."' and CONSTRAINT_SCHEMA='".$GLOBALS['DB']."'",Q_RET_ARRAY);
            /**
             * +---------------+------------------------+--------------------+
             * | COLUMN_NAME   | REFERENCED_COLUMN_NAME | CONSTRAINT_NAME    |
             * +---------------+------------------------+--------------------+
             * | rid           | NULL                   | PRIMARY            | <- primary key
             * | bid           | NULL                   | PRIMARY            | <- primary key
             * | group_user_id | NULL                   | group_user_id      | <- unique key first
             * | module        | NULL                   | group_user_id      | <- unique key secondary
             * | page          | NULL                   | group_user_id      | <- unique key secondary
             * | department_id | rid                    | cadre_cadre_ibfk_2 | <- foreign key1
             * | institute_id  | rid                    | cadre_cadre_ibfk_3 | <- foreign key2
             * +---------------+------------------------+--------------------+
             */
            //phase 1 : detection of first level keys
            foreach($arr as $key => $row){
               if($row['CONSTRAINT_NAME']=='PRIMARY'){//Find the primary key
                  $this->keys['PRI'][]=$row['COLUMN_NAME'];
               }elseif($row['COLUMN_NAME']==$row['CONSTRAINT_NAME']){//Find first level unique keys
                  $this->keys['UNI'][$row['COLUMN_NAME']]=array();
               }elseif($row['REFERENCED_COLUMN_NAME']!='NULL'){//Find foreign keys (references)
                  $this->keys['FOR'][]=$row['COLUMN_NAME'];
               }
            }

            //phase 2: detection of second level unique keys
            foreach($arr as $key => $row){
               if(isset($this->keys['UNI'][$row['CONSTRAINT_NAME']])){//Find first unique key
                  $this->keys['UNI'][$row['CONSTRAINT_NAME']][]=$row['COLUMN_NAME'];
               }
            }

            $this->write_config();
         }
      }

      /*
       Write configuration of current table to a php file which can be customized by the user for

       */


      public function write_config(){
         $grids=<<<EOE
   'GRIDS'=>array(
      'GRID'=>array(
         'columns'      =>array('rid'=>array('hidden'=>'true'),'timestamp'=>array('width'=>'50')),
         'filter'       =>get_filter(),
         'selector_id'  =>'toolbar__rid',
         'ref_table'    =>m_p_t(''),
         'event_key'    =>'rid',
         'order_by'     =>'ORDER BY timestamp DESC',
         'group_by'     =>'GROUP BY timestamp',
         'dojoType'     =>'dojox.grid.EnhancedGrid',
         'query'        =>'{ "rid": "*" }',
         'clientSort'   =>'true',
         'style'        =>'width:100%;height:400px',
         'onClick'      =>'load_grid_item',
         'columnReordering'=>'true',
         'headerMenu'   =>'gridMenu',
       ),
    ),
EOE;
         $common_toolbar_buttons=<<<EOE
      "rid"=>array(
         "length"       =>"100",
         "dojoType"     =>"dijit.form.FilteringSelect",
         "required"     =>"false",
         "label"        =>"Label",
         "onChange"     =>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"   =>"label",
         "pageSize"     =>"10",
         "store"        =>"rid_store",
         "filter"       =>get_filter(),
         "ref_table"    =>m_p_t(''),
         "ref_key"      =>'rid',
         "order_by"     =>'ORDER BY timestamp DESC',
         "vid"          =>array('rid'),
      ),  
EOE;

         $callbacks=<<<EOE
   'CALLBACKS'=>array(
      "add_record"=>array(
         "OK"     =>array(
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),  
         "ERROR"  =>array(
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),
      ),  
      "update_record"=>array(
         "OK"     =>array( 
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),
         "ERROR"  =>array(
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),
      ),  
      "delete_record"=>array(
         "OK"     =>array(
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),  
         "ERROR"  =>array(
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),
      ),   
   ),  
EOE;
    
         $config=$this->model;
         if(!file_exists($config)){
            $file_handler = fopen($config, 'w');
            if(!$file_handler)return;

            fwrite($file_handler, "<?php\n");
            fwrite($file_handler, "//--------------------------MODEL-------------------------------\n");
            fwrite($file_handler, "\$LOAD_DEFAULT_TOOLBAR=true;\n");
            fwrite($file_handler, "\$GLOBALS['MODEL']=array(\n");
            fwrite($file_handler, "//-----------------KEY FIELDS OF THE MODEL----------------------\n");
            fwrite($file_handler, tab(1)."'KEYS'=>array(\n");
            fwrite($file_handler, tab(2)."'PRI'\t=>array('".(isset($this->keys['PRI'])?implode("','",$this->keys['PRI']):'')."'),\n");
            fwrite($file_handler, tab(2)."'UNI'\t=>array(");
            if(isset($this->keys['UNI'])){
               foreach($this->keys['UNI'] as $key => $arr){
                  fwrite($file_handler, tab(2)."'$key'\t=>array('".implode("','",$arr)."'),\n");
               }
            }
            fwrite($file_handler, tab(2)."),\n");
            fwrite($file_handler, tab(2)."'FOR'\t=>array('".(isset($this->keys['FOR'])?implode("','",$this->keys['FOR']):'')."'),\n");
            fwrite($file_handler, tab(1)."),\n");

            fwrite($file_handler, "//--------------FIELDS TO BE INCLUDED IN FORM-------------------\n");
            //write in to form related fields which reflect the form
            fwrite($file_handler, tab(1)."'FORM'=>array(");

            $comma1="";
            foreach($this->form as $field => $arr){
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

            
            fwrite($file_handler, "//---------------------GRID CONFIGURATION-----------------------\n");
            fwrite($file_handler, $grids."\n");
            //write the toolbar related fields
            fwrite($file_handler, "//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------\n");
            fwrite($file_handler, tab(1)."'TOOLBAR'=>array(\n".$common_toolbar_buttons."\n".tab(1)."),\n");
            fwrite($file_handler, "//--------------------CALLBACK FUNCTIONS------------------------\n");
            fwrite($file_handler, $callbacks."\n");
            fwrite($file_handler, ");");
            fwrite($file_handler, "\n?>\n");
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

         $res=exec_query("SELECT * FROM ".$this->insert_table." $where",Q_RET_ARRAY);
         if(isset($res[0])){
            $row=$res[0];
            foreach($this->form as $field => $value ){
               /*Ignore custom field names*/
               if(isset($row[$field])){
                  $this->data[$field]=$row[$field];
               }
            }
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
         $table=$table==null?$this->insert_table:$table;   

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

         $json_fileW=W_MODULES."/".MODULE."/".$this->insert_table.$this->data_json;
         $json_fileA=A_MODULES."/".MODULE."/".$this->insert_table.$this->data_json;

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
			$limit   = "";
         if(isset($_REQUEST['count'])){
            $limit   .= " LIMIT " . $_REQUEST['count'];
         }

         if(isset($_REQUEST['start'])){
            $limit   .= " OFFSET " . $_REQUEST['start'];
         }

         $table      =$this->insert_table;
         $filter_str ='';
         $query      ='';
         $grid       =$this->grids['GRID'];

         if(isset($grid['filter']) && $grid['filter']){
            $filter_str =" WHERE ".$grid['filter'];
         }
         
         $order_by="";
         if(isset($grid['order_by'])){
            $order_by=" ".$grid['order_by'];
         }

			$group_by="";
         if(isset($grid['group_by'])){
            $group_by=" ".$grid['group_by'];
         }

         if(!isset($grid['sql'])){
            if(isset($grid['columns'])){
               //Compatible with any array (associative or normal)
               foreach($grid['columns'] as $key => $value){
                  if(is_array($value)){
                     $columns[]=$key;
                  }else{
                     $columns[]=$value;
                  }
               }
            }else{
               $columns =array_keys($this->form);
            }
         
            if(isset($grid['ref_table'])){
               $table=$grid['ref_table'];
            }
         
            $fields  =implode(",",$columns);
            $query="SELECT $fields FROM ".$table.$filter_str.$group_by.$order_by.$limit;
         }else{
            $query   =$grid['sql'].$filter_str.$group_by.$order_by.$limit;
         }
         
         $csv_file= $table.".csv";
         db_to_csv_nr($query,$csv_file);
         return;
      }

      /**
       * Generate json to be work with grids
       */

      public function gen_grid_json(){
         $limit   = "";
         if(isset($_REQUEST['count'])){
            $limit   .= " LIMIT " . $_REQUEST['count'];
         }

         if(isset($_REQUEST['start'])){
            $limit   .= " OFFSET " . $_REQUEST['start'];
         }


         $table      =$this->insert_table;
         $filter_str ='';
         $query      ='';
         $grid       =$this->grids['GRID'];

         if(isset($grid['filter']) && $grid['filter']){
            $filter_str =" WHERE ".$grid['filter'];
         }
         
         $order_by="";
         if(isset($grid['order_by'])){
            $order_by=" ".$grid['order_by'];
         }

			$group_by="";
         if(isset($grid['group_by'])){
            $group_by=" ".$grid['group_by'];
         }


         if(!isset($grid['sql'])){
            if(isset($grid['columns'])){
               //Compatible with any array (associative or normal)
               foreach($grid['columns'] as $key => $value){
                  if(is_array($value)){
                     $columns[]=$key;
                  }else{
                     $columns[]=$value;
                  }
               }
            }else{
               $columns =array_keys($this->form);
            }

            if(isset($grid['ref_table'])){
               $table=$grid['ref_table'];
            }

                  
            $fields  =implode(",",$columns);
         
            $query="SELECT $fields FROM ".$table.$filter_str.$group_by.$order_by.$limit;
         }else{
            $query   =$grid['sql'].$filter_str.$group_by.$order_by.$limit;
         }

         //Find the total count returned for the given query
         $count=exec_query("SELECT count(*) count FROM ".$table.$filter_str,Q_RET_ARRAY);
         $count=$count[0]['count'];

         $data=exec_query($query,Q_RET_ARRAY);
         $file_name='gird.json';
         set_file_header($file_name);
         print '{}&&'.json_encode(array('numRows'=>$count,'items'=>$data,'identity'=>'id'));  
      }


      /**
       * Generate csv for the given query
       */
      public function gen_csv($field_list){
         $filter_str="";
			if(trim($filter_str) != ''){
				$filter_str=" WHERE ".get_filter();
			}
         $columns=array();
         $headers=array();
         if(is_null($field_list)){
				foreach($this->form as $key=>$arr){
					if((isset($arr['custom']) && strtolower($arr['custom']) == 'true') || isset($arr['disabled']) && strtolower($arr['disabled']) == 'true' ){
					}else{
						$columns[]=$key;

                  //Set header as lable if available
						if(isset($arr['label'])){
							   $headers[]=$arr['label'];
                  }else{
							   $headers[]=$key;
                  }
					}
				}
         }else{
            //When the user have provided the field list
				foreach($field_list as $key){
               if(isset($this->form[$key])){
                  $arr=$this->form[$key];
                  if((isset($arr['custom']) && strtolower($arr['custom']) == 'true') || isset($arr['disabled']) && strtolower($arr['disabled']) == 'true' ){
						}else{
							$columns[]=$key;

                     //Set header as lable if available
                     if(isset($arr['label'])){
							   $headers[]=$arr['label'];
                     }else{
							   $headers[]=$key;
                     }
						}
               }
				}
         }

			//If the table is not provided use default table
		   $table=$this->insert_table;
		 	if(isset($GLOBALS['PAGE']['csv_table'])){
				$table=$GLOBALS['PAGE']['csv_table'];
		 	}
         
         $fields=implode(",",$columns);
         $query="SELECT $fields FROM ".$table.$filter_str;
			log_msg($query);
         
         $csv_file= $table.".csv";
         db_to_csv_nr($query,$csv_file);
         return;
      }


     
      /*
       Provide data to  gen_xhrt_filtering_select
       @$table: custom table to be queried
       @$key: custom key to be presented
       @$filter: custom filter to be applied
       */
      public function xhr_filtering_select_data($key,$section=null){
         $vid        =null;
         $key_       =null;
         $field_array=null;
  
         if(!is_null($section)){
            $field_array=$GLOBALS['MODEL'][$section][$key];
         }else{
            $field_array=$this->form[$key];
         }

         //If the select is set to get the data from a function call the function and return 
         $data_function=$key."_data";
         if(isset($field_array['data_function'])){
            $data_function=$field_array['data_function'];
         }
         if(function_exists($data_function)){
            //set_file_header($key.".json");
            echo call_user_func($data_function);
            return;
         }

         //Reference key of the table
         if(isset($field_array['ref_key'])){
            $key=$field_array['ref_key'];
         }

         //Visible id (label)
         if(isset($field_array['vid'])){
            $key_=array($key=>$field_array['vid']);
         }else{
            $key_=$key; 
         }

         //Custom table if available 
         $table   =$this->insert_table;
         if(isset($field_array['ref_table'])){
            $table   =$field_array['ref_table'];
         }

         //Filter the list according to this filter
         $filter  =null;
         if(isset($field_array['filter'])){
            $filter=$field_array['filter'];
         }

         //Result will be order by this statement
         $order_by  =null;
         if(isset($field_array['order_by'])){
            $order_by=$field_array['order_by'];
         }

         //Result will be group by this statement
         $group_by  =null;
         if(isset($field_array['group_by'])){
            $group_by=$field_array['group_by'];
         }


         //Default value to be listed at the end of the values
         $default  ="-none-";
         if(isset($field_array['default'])){
            if($field_array['default'] === false){
               $default  =null;
            }else{
               $default  =$field_array['default'];
            }
         }

         //Default value to be listed at the end of the values
         $all_selector  ="-all-";
         if(isset($field_array['all_selector'])){
            if($field_array['all_selector'] === false){
               $all_selector  =null;
            }else{
               $all_selector  =$field_array['all_selector'];
            }
         }


         //header('Content-Type', 'application/json');
			//TODO: add $group_by to the function
         include 'qread_store_class.php';
         $query_read_store = new Query_read_store($table,$key_,$filter,$order_by,$key,$default,$all_selector);
         echo $query_read_store->gen_json_data();
      }
      /*
       $question: A value provided for  $key field  fo the table
       return    : JSON formatted tupple related to the given $key from the given table
       */

      public function xhr_form_filler_data($qustion,$cus_table=null,$cus_key=null){
         $table   =$this->view_table;
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
         foreach( $this->form as $key => $arr){
            //if(isset($arr['custom']) || isset($arr['store'])){
            //if(isset($arr['custom']) || isset($arr['isolate']) || in_array(strtolower($key),$this->pwd_field_guess)){
            if(isset($arr['custom']) || in_array(strtolower($key),$this->pwd_field_guess)){
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
         foreach( $this->form as $key => $arr){
            //if(isset($arr['custom']) || isset($arr['store'])){
            if(isset($arr['custom']) || in_array(strtolower($key),$this->pwd_field_guess)){
               continue;   
            }else{
               if($arr['dojoType']=="dijit.form.DateTextBox"){
                  //$ret_array[$key]="{_type:'Date',_value:'".$row[$key]."'}";
                  //Dates have issues with dojo it is speacially formatted as bellow
                  $ret_array[$key]=array('_type'=>'Date','_value'=>$row[$key]);
               }else{
                  //apply read function if specified in the model
                  if(isset($arr['read_func']) && $arr['read_func'] != ""){
                     $ret_array[$key]=call_user_func($arr['read_func'],$row[$key]);
                  }else{
                     $ret_array[$key]=$row[$key];
                  }
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
      check for duplicates for unique keys
      */
      public function is_duplicate(){

			$uni_keys=$this->keys['UNI'];
			foreach($uni_keys as $key => $key_arr){
				$and="";
				$dupq="SELECT * FROM ".$this->insert_table." WHERE ";
				foreach($key_arr as $field){
					if(isset($_REQUEST[$field])){
						if($_REQUEST[$field] == 'NULL' || $_REQUEST[$field] == ''){
							$dupq.=$and." (ISNULL(`".$field."`) OR `".$field."`='')";
						}else{
							$dupq.=$and."`".$field."`='".$_REQUEST[$field]."'";
						}
						$and=" AND ";
					}
				}
				log_msg($dupq);
				exec_query($dupq);
				if(get_num_rows() > 0){
					return true;
				}
			}
			return false;
		}


      protected $pwd_field_guess=array('password','passwd','pwd');
      /*Validate and add record to the table*/
      public function add_record(){

			if($this->is_duplicate()){
            return_status_json('ERROR','Duplicate record!');
				return false;
			}

         //Log users activity
         act_log();

         /*vefiry captcha if it is set*/
         if(!verify_captcha()){
            return_status_json('ERROR','error verifying security code');
            callback(__FUNCTION__,'ERROR');
            return false;
         }

         $errors=array();
         
         $cols      =""; //coumns of the table
         $values   =""; //value for each column of the table
         $comma   ="";
         /*set columns and values for each column*/
         foreach( $this->form as $key => $arr){
            //Primary key value is an auto generated number os it will be removed for new inserts 
            if( get_pri_keys() == $key){
               continue; 
            }

            //File names are not modified here but while uploading
        		if(isset($arr['dojoType'] ) && $arr['dojoType'] == 'dojox.form.Uploader'){
               continue; 
            }

            //If the foreign keys does not have values ignore them
            if(in_array($key,get_for_keys()) && (!isset($_REQUEST[$key]) || is_null($_REQUEST[$key]) || $_REQUEST[$key] == '' || $_REQUEST[$key] == 'NULL' || $_REQUEST[$key] == null)){
               continue; 
            }

            //Handle the isolated fields adding
            if(isset($arr['isolate']) && is_array($arr['isolate']) && isset($arr['isolate']['add'])){
               //For comboboxes, if user request to add a value 
               if($arr['dojoType']=='dijit.form.ComboBox'){
                  //check if the value already available using get_key query
                  $res=exec_query(sprintf($arr['key_sql'],$_REQUEST[$key]),Q_RET_ARRAY);
                  $errors[]=get_sql_error();
                  if(isset($res[0])){
                     //If there is a value in database set it in request array
                     $_REQUEST[$key]=$res[0][$arr['ref_key']];
                  }else{
                     //If there is no value in database add the value to the database and get it's key and set it as the request[key]
                     exec_query(sprintf($arr['isolate']['add'],$_REQUEST[$key]),Q_RET_NONE);
                     $errors[]=get_sql_error();
                     $res=exec_query(sprintf($arr['key_sql'],$_REQUEST[$key]),Q_RET_ARRAY);
                     $errors[]=get_sql_error();
                     $_REQUEST[$key]=$res[0][$arr['ref_key']];
                  }
               }else{
                  exec_query(sprintf($arr['isolate']['add'],$_REQUEST[$key]),Q_RET_NONE);
                  $errors[]=get_sql_error();
                  unset($_REQUEST[$key]);
                  continue; 
               }
				}elseif(isset($arr['isolate'])){
					continue; 
				}

            /*Trying to ignore auto incrementing fields and custom fields(custom fields were handled below)*/
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

               //Fix the time value -> remove 'T' in front
        			if(isset($arr['dojoType'] ) && $arr['dojoType'] == 'dijit.form.TimeTextBox'){
                  $value=substr($value,1,strlen($value));       
               }

               //Fix the check box value on,true -> 1
        			if(isset($arr['dojoType'] ) && $arr['dojoType'] == 'dijit.form.CheckBox'){
                  if(in_array(strtolower($value),array('on','true'))){   
                     $value=1;
                  }else{
                     $value=0;
                  }
                }

               
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

         $insert_query  ="INSERT INTO ".$this->insert_table."(%s) VALUES(%s)";
         $insert_query  =sprintf($insert_query,$cols,$values);
         $res           =exec_query($insert_query,Q_RET_MYSQL_RES);
         $errors[]      =get_sql_error();

         /*report error/success*/
         if(get_affected_rows() > 0){
            return_status_json('OK','record inserted successfully');
            callback(__FUNCTION__,'OK');
            return true;
         }else{
            return_status_json('ERROR',implode(';',$errors));
            callback(__FUNCTION__,'ERROR');
            //return_status_json('ERROR',get_sql_error());
            return false;
         }
      }


      /*Validate and update record in the table */
      public function update_record(){
         $sql="SELECT * FROM ".$this->update_table." WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";

         //Log users activity
         act_log(null,$sql);

         $res=exec_query($sql,Q_RET_ARRAY);

         $errors=array();

         if(sizeof($res)>0){
            //key available  -> modify
            $values  =""; //valus to be changes in the tupple
            $comma   ="";
            /*generate values string*/
            foreach( $this->form as $key => $arr){
               /*handle custom fields from form submission*/
               if(isset($arr['custom']) && $arr['custom'] == 'true'){
                  continue; 
               }

               //File names are not modified here but while uploading
        			if(isset($arr['dojoType'] ) && $arr['dojoType'] == 'dojox.form.Uploader'){
                  continue; 
               }

               //bypass password fields if value is blank or null
               if(in_array(strtolower($key),$this->pwd_field_guess) && ($_REQUEST[$key] == '' || $_REQUEST[$key] == 'NULL' | is_null( $_REQUEST[$key]))){
                  continue; 
               }

               //Primary key not included in set list
               if(get_pri_keys()==$key){
                  continue; 
               }

               //Custom and disabled fields will be excluded do not update
					/*
					if(
						(!isset($_REQUEST[$key]) || 
						is_null($_REQUEST[$key]) || 
						$_REQUEST[$key] == '' || 
						$_REQUEST[$key] == 'NULL' || 
						$_REQUEST[$key] == null) &&
						$arr['dojoType'] != 'dijit.form.CheckBox'
						){
                  continue; 
               }
					*/

               //If the foreign keys does not have values ignore them
               if(in_array($key,get_for_keys()) && (!isset($_REQUEST[$key]) || $_REQUEST[$key] == null || is_null($_REQUEST[$key]) || $_REQUEST[$key]=='' || $_REQUEST[$key] == 'NULL')){
                  continue; 
               }

               //Handle the isolated fields updates
               if(isset($arr['isolate'])){
                  if(is_array($arr['isolate']) && isset($arr['isolate']['update'])){
                     exec_query(sprintf($arr['update'],$_REQUEST[$key]),Q_RET_NONE);
                     $errors[]=get_sql_error();
                  }else{
                     //exec_query("UPDATE ".$this->update_table." SET $key='".$_REQUEST[$key]."' WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'",Q_RET_NONE);
                     //$errors[]=get_sql_error();
                  }
                  unset($_REQUEST[$key]);
                  continue; 
               }

               /*check for valid json strings to use as json strings in database*/
               $value=isset($_REQUEST[$key])?$_REQUEST[$key]:'';
               $value=str_replace(
                  array('&quot;','NaN','\n'),
                  array('"','""',''),
                  $value
               );

               //Fix the time value -> remove 'T' in front
        			if(isset($arr['dojoType'] ) && $arr['dojoType'] == 'dijit.form.TimeTextBox'){
                  $value=substr($value,1,strlen($value));       
               }

               //For CheckBoxes all SET values will be caset to true(1)
               if(isset($arr['dojoType'] ) && $arr['dojoType'] == 'dijit.form.CheckBox'){
                  if(in_array(strtolower($value),array('on','true'))){   
                     $value=1;
                  }else{
                     $value=0;
                  }
                }
               
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

            $update_query  ="UPDATE ".$this->update_table." SET %s WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
            $update_query  =sprintf($update_query,$values);
            $res           =exec_query($update_query,Q_RET_MYSQL_RES);

            $errors[]      =get_sql_error();

            /*report error/success */
            if(implode('',$errors)==''){
               return_status_json('OK','record updated successfully');
               callback(__FUNCTION__,'OK');
               return true;
            }else{
               return_status_json('ERROR',implode(';',$errors));
               callback(__FUNCTION__,'ERROR');
               return false;
            }
         }else{
            return_status_json('ERROR',implode(';',$errors));
            callback(__FUNCTION__,'ERROR');
            return false;
         }
      }

      /*
       delete record from the table
      @ $purge: delete everithing instead of placing delete flag (caution: can not recover)
       */
      public function delete_record($purge=false){
         $delete="UPDATE ".$this->update_table." SET deleted=true WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
      
         if($purge){
            $delete="DELETE FROM ".$this->insert_table." WHERE ".$this->primary_key."='".$_REQUEST[$this->primary_key]."'";
         }

         //Log users activity
         act_log(null,$delete);

         $res=exec_query($delete,Q_RET_MYSQL_RES);
         /*report error/success */
         if(get_affected_rows() > 0){
            return_status_json('OK','Record deleted successfully!');
            callback(__FUNCTION__,'OK');
            return true;
         }else{
            return_status_json('ERROR','Error deleting record!');
            callback(__FUNCTION__,'ERROR');
            return false;
         }
      }

      /*
       Upload file 
      Array
(
    [uploadedfiles] => Array
        (
            [name] => Array
                (
                    [0] => MCS_31-60.csv
                )

            [type] => Array
                (
                    [0] => text/csv
                )

            [tmp_name] => Array
                (
                    [0] => /tmp/phpuED4Mn
                )

            [error] => Array
                (
                    [0] => 0
                )

            [size] => Array
                (
                    [0] => 34387
                )

        )
 
      */
      public function upload_file(){
         if(isset($_REQUEST['file_id']) && isset($_REQUEST[$_REQUEST['file_id']."_rid"]) && isset($_FILES[$_REQUEST['file_id']."s"])){
            $fid=$_REQUEST['file_id'];
            $rid=$_REQUEST[$_REQUEST['file_id']."_rid"];

            $mod_arr=$this->form[$fid];
            $up_arr=$_FILES[$fid."s"];

            if(in_array(strtolower($up_arr["type"][0]),$mod_arr['valid_types']) && $up_arr["size"][0] <= $mod_arr['max_size'] && $up_arr["error"][0] <= 0){
               $type=pathinfo($up_arr["name"][0],PATHINFO_EXTENSION);
               $f_name=$fid."_".$rid.".".$type;
               $w_path=$mod_arr['w_path']."/".$f_name;
               $save_path=$mod_arr['path']."/".$f_name;

               $msg="file=".$w_path.",name=".$f_name.",width=320,height=240,type=".$type;
            
               if(file_exists($save_path)){
                  if(isset($mod_arr['overwrite']) && $mod_arr['overwrite'] == true){
                     unlink($save_path);
                     move_uploaded_file($up_arr["tmp_name"][0],$save_path);
                     exec_query("UPDATE ".$this->update_table." SET $fid='$f_name' WHERE rid='$rid'",Q_RET_NONE);
                     log_msg('File exists, overwritten!');
                     echo $msg;
                     return;
                  }else{
                     log_msg('File exists, not deleted!');
                     echo $msg.",error=File exists, not deleted";
                     return;
                  }
               }else{
                  move_uploaded_file($up_arr["tmp_name"][0],$save_path);
                  exec_query("UPDATE ".$this->update_table." SET $fid='$f_name' WHERE rid='$rid'",Q_RET_NONE);
                  log_msg('File uploaded!');
                  echo $msg;
                  return;
               }
            }else{
               log_msg('File Type or size error!');
               echo $msg.",error=Type or size error";
               return;
            }
         }else{
            echo "error='Prerequisit error'";
         }
      }

      public function delete_file(){
         log_msg($_FILES);
         return_status_json('OK','File deleted!');
      }
   }
?>
