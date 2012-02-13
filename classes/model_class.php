<?php
/** Model class
 * 
 *
 */

class Model{

    //The extention in which page specific model file should be created
    protected $model        ="_mdl.php";

    //To store variouse class variables easily use this array
    protected $self         =array();

    //Mapping of sql(mysql) data taypes to be mapped with dojo
    protected $types=array(
        "TINYINT(1)"    =>"dijit.form.CheckBox",
        "TINYINT"       =>"dijit.form.NumberTextBox",
        "SMALLINT"      =>"dijit.form.NumberSpinner",
        "MEDIUMINT"     =>"dijit.form.NumberTextBox",
        "INT"           =>"dijit.form.NumberTextBox",
        "INTEGER"       =>"dijit.form.NumberTextBox",
        "BIGINT"        =>"dijit.form.NumberTextBox",

        "FLOAT"         =>"dijit.form.ValidationTextBox",
        "DOUBLE"        =>"dijit.form.ValidationTextBox",
        "PRECISION"     =>"dijit.form.ValidationTextBox",
        "REAL"          =>"dijit.form.ValidationTextBox",
        "DECIMAL"       =>"dijit.form.ValidationTextBox",
        "NUMERIC"       =>"dijit.form.ValidationTextBox",

        "DATE"          =>"dijit.form.DateTextBox",
        "DATETIME"      =>"dijit.form.ValidationTextBox",
        "TIMESTAMP"     =>"dijit.form.ValidationTextBox",
        "TIME"          =>"dijit.form.TimeTextBox",
        "YEAR"          =>"dijit.form.DateTextBox",

        "CHAR"          =>"dijit.form.ValidationTextBox",
        "VARCHAR"       =>"dijit.form.ValidationTextBox",

        "TINYBLOB"      =>"dijit.form.ValidationTextBox",
        "BLOB"          =>"dijit.form.ValidationTextBox",
        "MEDIUMBLOB"    =>"dijit.form.ValidationTextBox",
        "LONGBLOB"      =>"dijit.form.ValidationTextBox",

        "TINYTEXT"      =>"dijit.form.ValidationTextBox",
        "TEXT"          =>"dijit.form.SimpleTextarea",
        "MEDIUMTEXT"    =>"dijit.form.ValidationTextBox",
        "LONGTEXT"      =>"dijit.form.SimpleTextarea",
        "ENUM"          =>"dijit.form.ValidationTextBox",
        "SET"           =>"dijit.form.ValidationTextBox"
        );


    /** Constructor of model class
     * 
     */
    function __construct($table=null,$file_name=null) {

         $this->self['table']=$table;

         if(isset($file_name) && $file_name != null ){
            $this->self['model']    = A_MODULES."/".MODULE."/".$file_name.$this->model;
         }else{
            $this->self['model']    = A_MODULES."/".MODULE."/".$table.$this->model;
         }
    }

      /**
       * return default label posisitions for different dojo types
       */
      public function get_label_pos($dojo_type){
         if($dojo_type == 'dijit.form.CheckBox'){
            return 'right';
         }else{
            return 'top';
         }
      }

      /*
       * Return length for the model field according to teble fields after examining the given table length
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
       * Retrurn dojo type for different table fileds which should be associated  in view
       */
      public function get_field_type($type){
         $type=strtoupper($type);

         //Default type set to VARCHAR and acquire dojo field type
         $field_type=$this->types['VARCHAR'];

         //varchar(100) remove brackets 
         $arr=explode("(", $type);

         //Ceck for the available mapping of dojo types and acquire 
         if(isset($this->types[$type])){
            $field_type=$this->types[$type];
         }elseif(isset($this->types[$arr[0]])){
            $field_type=$this->types[$arr[0]];
         }

         //If the text field is very long as in table convert textbox in to a textarea
         if($field_type=="dijit.form.ValidationTextBox" && $this->get_field_width($type,true) > 700 ){
            $type="LONGTEXT";
         }

         return $field_type;
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

      /*
       A data tuple of the given table will be filled to this array
       */
      protected $data=array();

      /*
       Load configuration from the file if exits
       els load default configuration from the raw database
       */

      public function load_model(){
         //Model file for the given  database
         $config=$this->self['model'];

         if(file_exists($config)){
            require_once($config);
            foreach($fields as $field => $value){
               $this->fields[$field]=$value;
            }
         }else{
            $res=exec_query("SHOW COLUMNS FROM ".$this->self['table'],Q_RET_ARRAY);

            /*If no result returned*/
            if(get_num_rows() <= 0){
               echo "Error showing table '".$this->self['table']."' !";   
               return;
            }

            foreach($res as $row) {
               if($row['Extra']!='auto_increment'){
                  $this->fields[$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"    =>$this->get_field_type($row['Type']),
                  "required"    =>($row['Null']=='YES')?"false":"true",
                  "label"       =>style_text($row['Field']),
                  "label_pos"   =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }else{
                  $this->fields[$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"    =>$this->get_field_type($row['Type']),
                  "type"        =>"hidden",
                  "required"    =>($row['Null']=='YES')?"false":"true",
                  "label"       =>style_text($row['Field']),
                  "label_pos"   =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }
            }
            $this->write_config();
         }
      }

      /*
      Write configuration of current table to a php file which can be customized by the user for
      */
      public function write_config(){
         //modle file for the given database will 
         $config=$this->self['model'];

         //If the file is not available create the default model will be created
         if(!file_exists($config)){
            $file_handler = fopen($config, 'w');

            fwrite($file_handler, "<?php\n");
            fwrite($file_handler, "\$fields=array(\n");

            //Generate an array wich implies the model of the given table
            $comma1="";
            foreach($this->fields as $field => $arr){
               $comma2="";
               fwrite($file_handler, $comma1."\t\n\"".$field."\"=>array(");
               foreach($arr as $key => $value){
                  fwrite($file_handler, $comma2."\n\t\t\"".$key."\"=>\"".$value."\"");
                  $comma2=",";
               }
               fwrite($file_handler, ",\n\t\t\"value\"=>\"\")");
               $comma1=",";
            }

            //End of the array and script
            fwrite($file_handler, "\t\n);?>\n");

            //Help tooltips will be included as an array adjesent to the model 
            fwrite($file_handler, "<?php\n");
            fwrite($file_handler, "\$help_array=array(");

            //Generate an help tip for each control
            $comma="";
            foreach($this->fields as $field => $arr){
               fwrite($file_handler, $comma."\n'$field'=>'".$arr['label']."'");
               $comma=",";
            }

            //End of the help array and script
            fwrite($file_handler, ");\n ?>");

            //close the file
            fclose($file_handler);
         }
      }


}


?>
