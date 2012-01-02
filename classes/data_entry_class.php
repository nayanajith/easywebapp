<?php
/*
function __construct($table=null,$key=null) 
public function load_modifier()
public function write_config()
public function get_field_width($type,$actual=false)
public function get_field_type($type)
public function get_data($filter=null)
public function gen_field_entry($field)
public function gen_filter_field_entry($field)
public function gen_form()
public function gen_filter()
public function ret_filter($filter_name)
public function add_filter()
public function delete_filter()
public function modify_filter()
public function gen_json($key_array,$filter=null,$return,$table=null)
public function gen_data_grid($key_array,$json_file,$key=null)
public function displayLinks(e)
public function gen_filtering_select($json_file)
public function xhr_filtering_select_data($table=null,$key=null,$filter=null)
public function gen_xhr_filtering_select($js_function,$key=null,$filter=null)
public function xhr_form_filler_data($qustion)
public function xhr_filter_filler_data($qustion)
public function gen_xhr_form_filler($js_function,$table=null,$key=null,$filter=null)
public function add_record()
public function modify_record()
public function delete_record($purge=true)
*/

/*
 class to generate the gui components of the form using dojo and php
 */
class Formgenerator {

   protected $modifier      ="_modif.php";
   protected $help_file     ="_help.php";
   protected $data_json     ="_data.json";

   /*
    * Field types array
    */

   protected $types=array(
      "TINYINT(1)"   =>"dijit.form.CheckBox",
      "TINYINT"   =>"dijit.form.NumberTextBox",
      "SMALLINT"   =>"dijit.form.NumberSpinner",
      "MEDIUMINT"   =>"dijit.form.NumberTextBox",
      "INT"         =>"dijit.form.NumberTextBox",
      "INTEGER"   =>"dijit.form.NumberTextBox",
      "BIGINT"      =>"dijit.form.NumberTextBox",

      "FLOAT"      =>"dijit.form.ValidationTextBox",
      "DOUBLE"      =>"dijit.form.ValidationTextBox",
      "PRECISION"   =>"dijit.form.ValidationTextBox",
      "REAL"      =>"dijit.form.ValidationTextBox",
      "DECIMAL"   =>"dijit.form.ValidationTextBox",
      "NUMERIC"   =>"dijit.form.ValidationTextBox",

      "DATE"      =>"dijit.form.DateTextBox",
      "DATETIME"   =>"dijit.form.ValidationTextBox",
      "TIMESTAMP"   =>"dijit.form.ValidationTextBox",
      "TIME"      =>"dijit.form.TimeTextBox",
      "YEAR"      =>"dijit.form.DateTextBox",

      "CHAR"      =>"dijit.form.ValidationTextBox",
      "VARCHAR"   =>"dijit.form.ValidationTextBox",

      "TINYBLOB"   =>"dijit.form.ValidationTextBox",
      "BLOB"      =>"dijit.form.ValidationTextBox",
      "MEDIUMBLOB"=>"dijit.form.ValidationTextBox",
      "LONGBLOB"   =>"dijit.form.ValidationTextBox",

      "TINYTEXT"   =>"dijit.form.ValidationTextBox",
      "TEXT"      =>"dijit.form.SimpleTextarea",
      "MEDIUMTEXT"=>"dijit.form.ValidationTextBox",
      "LONGTEXT"   =>"dijit.form.SimpleTextarea",
      "ENUM"      =>"dijit.form.ValidationTextBox",
      "SET"         =>"dijit.form.ValidationTextBox"
      );



      /**/
      protected $self=array(
         "table"      =>"",    //effective teble
         "key"         =>"",      //key/primary key field of the table
         "multi_key"   =>"false",      //true/false if multikey table set this to true
         "modifier"   =>"",      //modifer script of the form
         "help_file"   =>""      //help file of the form
      );

      /*Form will be pre filled with the data correspond to this key*/
      protected $data_load_key=null;

      /*
      Constructure
      */
      function __construct($table=null,$key=null,$file_name=null,$data_load_key=null,$filter_table=null) {
      /*
         if(isset($GLOBALS['P_TABLES'][$table])){
            $this->self['table']      = $GLOBALS['P_TABLES'][$table];
         }else{
            $this->self['table']      = $GLOBALS['S_TABLES'][$table];
         }
      */

         $this->self['table']=$table;
         $this->self['filter_table']=$filter_table;

         if(isset($data_load_key) && $data_load_key != null ){
            $this->data_load_key=$data_load_key;
         }

         if(is_array($key)){
            $this->self['key']      = $key[0];
            $this->self['keys']      = $key;
         }else{
            $this->self['key']      = $key;
         }   


         
         /*Check and aply custom file name to save modifier and help files to save*/
         if(isset($file_name) && $file_name != null ){
            $this->self['modifier']   = A_MODULES."/".MODULE."/".$file_name.$this->modifier;
            $this->self['help_file']   = A_MODULES."/".MODULE."/".$file_name.$this->help_file;
         }else{
            $this->self['modifier']   = A_MODULES."/".MODULE."/".$table.$this->modifier;
            $this->self['help_file']   = A_MODULES."/".MODULE."/".$table.$this->help_file;
         }

         $this->load_modifier();
      }

      
      /*Change default table and key*/
      function set_table($table=null,$key=null){
         if($table != null){
            $this->self['table'] =$table;
         }

         if($key != null){
            $this->self['key'] = $key;
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

      /*
       A data tuple of the given table will be filled to this array
       */
      protected $data=array();

      /*
       Load configuration from the file if exits
       els load default configuration from the raw database
       */

      public function load_modifier(){
         $config=$this->self['modifier'];
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
                  "dojoType"   =>$this->get_field_type($row['Type']),
                  "required"   =>($row['Null']=='YES')?"false":"true",
                  "label"      =>style_text($row['Field']),
                  "label_pos" =>$this->get_label_pos($this->get_field_type($row['Type']))
                  );
               }else{
                  $this->fields[$row['Field']]=array(
                  "length"      =>$this->get_field_width($row['Type'],false),
                  "dojoType"   =>$this->get_field_type($row['Type']),
                  "type"      =>"hidden",
                  "required"   =>($row['Null']=='YES')?"false":"true",
                  "label"      =>style_text($row['Field']),
                  "label_pos" =>$this->get_label_pos($this->get_field_type($row['Type']))
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
         $config=$this->self['modifier'];
         if(!file_exists($config)){
            $file_handler = fopen($config, 'w');

            fwrite($file_handler, "<?php\n");
            fwrite($file_handler, "/*Auto generated by form_gen.php*/\n");
            fwrite($file_handler, "\$fields=array(\n");

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

            fwrite($file_handler, "\t\n);\n");
            fwrite($file_handler, "?>\n");

            //fwrite($file_handler,json_encode($this->fields));
            fclose($file_handler);
         }
      }

      /*generate help file to provide help tooltip for each field of the form*/
      public function generate_help_file(){
         $config=$this->self['help_file'];
         if(!file_exists($config)){
            $file_handler = fopen($config, 'w');
            fwrite($file_handler, "<!--Auto generated by form_gen.php-->\n");
            fwrite($file_handler, "<?php\n");
            //fwrite($file_handler, "\$help_".$this->self['table']."=array(");
            fwrite($file_handler, "\$help_array=array(");

            $comma="";
            foreach($this->fields as $field => $arr){
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
         if(isset($_REQUEST[$this->self['key']])){
            $where="WHERE ".$this->self['key']." = '".$_REQUEST[$this->self['key']]."'";
         }
         */

         if($this->data_load_key != null){
            $where="WHERE ".$this->self['key']." = '".$this->data_load_key."'";
         }

         $res=exec_query("SELECT * FROM ".$this->self['table']." $where",Q_RET_ARRAY);
         if(isset($res[0])){
            $row=$res[0];
            foreach($this->fields as $field => $value ){
               /*Ignore custom field names*/
               if(isset($row[$field])){
                  $this->data[$field]=$row[$field];
               }
            }
         }
      }


      protected $form_controls=array(
         'dijit.form.FilteringSelect'   =>"<select %s>%s</select>",
         'dijit.form.ComboBox'         =>"<select %s>%s</select>",
         'dijit.form.Select'            =>"<select %s>%s</select>",
         'dijit.form.MultiSelect'      =>"<select %s>%s</select>",
         'dijit.form.SimpleTextarea'   =>"<textarea %s>%s</textarea>",
         "dijit.form.NumberTextBox"      =>"<input %s>",
         "dijit.form.NumberSpinner"      =>"<input %s>",
         "dijit.form.ValidationTextBox"=>"<input %s>",
         "dijit.form.DateTextBox"      =>"<input %s constraints=\"{datePattern:'yyyy-MM-dd'}\" promptMessage='yyyy-MM-dd' invalidMessage='Invalid date. Please use yyyy-MM-dd format.' >",
         "dijit.form.TimeTextBox"      =>"<input %s constraints=\"{'timePattern':'hh:mm:ss'}\" promptMessage='hh:mm:ss' invalidMessage='Invalid time. Please use hh:mm:ss format.' >",
         "dijit.form.CheckBox"         =>"<div %s ></div>",
         "dijit.form.RadioButton"      =>"<div %s ></div>",
         "dijit.InlineEditBox"         =>"<span %s ></span>"
      );

/*
Set session parameters using xhr requests
*/
   public function param_setter(){
        return "
      function set_param(key,value) {
         dojo.xhrPost({
            url       : '".gen_url()."&form=main&action=param&data=json&param='+key+'&'+key+'='+value,
              handleAs :'json',
              load       : function(response, ioArgs) {        
               update_status_bar(response.status,response.info);
                  if(response.status == 'OK'){
                     update_progress_bar(100);
                  }
               },
               error : function(response, ioArgs) {
                        update_status_bar('ERROR',response);
               }
         });
      }   
        ";
   }    

/*
request html from the backend
*/
   public function html_requster($source_array,$target){
      return "
      function request_html() {
         var content_obj   =document.getElementById('$target');
         var sourse_array   =new Array('".implode("','",$source_array)."');
         var url='';   
         for(var i=0;i<sourse_array.length;i++){
            var tmp_val   =document.getElementById(sourse_array[i]).value;
            if(tmp_val=='')return;
            url+='&'+sourse_array[i]+'='+tmp_val;
         }

         //If index number is blank return 
         dojo.xhrPost({
            url       : '".gen_url()."&form=main&data=json&action=html'+url,
            handleAs :'text',
            load       : function(response, ioArgs) {        
                 update_status_bar('OK','Done');
               content_obj.innerHTML=response;
               dojo.parser.parse(content_obj);
            },
            error : function(response, ioArgs) {
                 update_status_bar('ERROR',response);
            }
         });
      }
      ";
   }

/*
submit the given form
*/
   public function form_submitter($form){
      $submit_form_app="
      function submit_form(action){
         //var json_req=dojo.toJson(dijit.byId('$form').getValues(), true);
         //alert(json_req);

         if(action=='csv'||action=='pdf'){
            //window.open('".gen_url()."&form=main&action='+action);
            download('".gen_url()."&form=main&action='+action);
            return;
         }

         update_status_bar('OK','...');
         update_progress_bar(10);

         /*User should confirm deletion*/
         if(action=='delete' && !confirm('Confirm Deletion!')){
            update_status_bar('ERROR','deletion canceled');
            update_progress_bar(0);
            return;   
         }

         if (action=='delete' || dijit.byId('$form').validate()) {
            dojo.xhrPost({
               url         : '".gen_url()."&form=main&action='+action, 
               handleAs      : 'json',
               form         : '$form', 
            
               handle: function(response,ioArgs){
                  update_status_bar(response.status_code,response.info);
                  if(response.status_code == 'OK'){
                     update_progress_bar(100);
                  }else{
                     update_status_bar('ERROR',response.info);
                     if(document.getElementById('captcha_image'))reload_captcha();
                     //update_status_bar('ERROR','Duplicate Entry!');
                  }
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
            update_status_bar('ERROR','Form contains invalid data.  Please correct them and submit');
            return false;
         }
         return true;
      }
   ";

      $submit_form_pub="
      function submit_form(action,module,page){
         //var json_req=dojo.toJson(dijit.byId('$form').getValues(), true);
         //alert(json_req);

         if(action=='csv'||action=='pdf'){
            //window.open('".gen_url()."&form=main&action='+action);
            download('".gen_url()."&form=main&action='+action);
            return;
         }

         //update_status_bar('OK','...');
         //update_progress_bar(10);

         /*User should confirm deletion*/
         if(action=='delete' && !confirm('Confirm Deletion!')){
            update_status_bar('ERROR','deletion canceled');
            update_progress_bar(0);
            return;   
         }

         if (action=='delete' || dijit.byId('$form').validate()) {
            dojo.xhrPost({
               url      : '".gen_url()."&form=main&action='+action, 
               handleAs : 'json',
               form     : '$form', 
               handle   : function(response,ioArgs){
                  if(response.status_code == 'OK'){
                     window.open('?module='+module+'&page='+page,'_parent');
                  }else{
                     update_status_bar('ERROR',response.info);
                  }
               },
            
               load     : function(response) {
                  //update_status_bar('OK','Request sent successfully');
                  //update_progress_bar(50);
               }, 
               error    : function() {
                  //update_status_bar('ERROR','Error on submission');
                  //update_progress_bar(0);
               }
            });
            return false;
         }else{
            update_status_bar('ERROR','Form contains invalid data.  Please correct and submit');
            return false;
         }
         return true;
      }
   ";

      if($GLOBALS['LAYOUT'] == 'pub'){
         return $submit_form_pub;
      }else{
         return $submit_form_app;
      }
   }



      /*
       Generate entry for the given table field
       select-> data for select box/combo box
       data-> data for text area
       */
      public function gen_field_entry($field){
         
         /*fill data from data array*/
         $fill            ="";

         if($this->data_load_key != null){
            if($field != 'password' && isset($this->data[$field])){
               $fill=$this->data[$field];
            }
         }

         /*filed parameter arry for current field*/
         $field_array=$this->fields[$field];

         /*set fill externelly when loading with data*/
         if($fill != ''){
            $field_array['value']=$fill;
         }

         /*entry for the given field will be filled to this var*/
         $entry         ="";
         $form_control   ="";
         $options         ="";

         /*inner value of the field*/
         $inner   =isset($field_array['inner'])?$field_array['inner']:"";

         /*if required=true put  * by the label */
         $required      ="";
         if(isset($field_array['required']) && $field_array['required'] == "true"){
            $required      ="<font color='red'>*</font>";
         }

         //If the field require a stor add a store
         if(isset($field_array['store'])){
            echo "
            <span dojoType='dojox.data.QueryReadStore' 
               url='".gen_url()."&data=json&action=combo&form=main&id=".$field_array['searchAttr']."'
               jsId='".$field_array['store']."'
               >
            </span>";
         }

         /*Handl custom form input method or generic one*/
         if(isset($field_array['custom']) && $field_array['custom'] == 'true' ){
            $entry         ="<div id='td_$field' jsId='td_$field' style='padding:10px;'>";
            $entry         .="<label for='$field' >".$field_array['label']."$required</label>";
            $entry         .=$inner;
            $entry         =sprintf($entry,$fill);
            $entry         .="<div id='td_in_$field'></div></div>\n";
         }else{
            d_r($field_array['dojoType']);
            $form_control   =$this->form_controls[$field_array['dojoType']];
            $options         =" jsId='$field' id='$field' name='$field' ";

            /*Fields to bypass when creating forms*/
            $bypass=array('inner','label','section','style','label_pos','type');

            /*all paremeters will be inserted to the options string*/
            foreach($field_array as $key => $value){
               if(!in_array($key,$bypass)){
                  $options.=$key."='$value'\n";
               }
            }


            if(isset($field_array['type']) && $field_array['type'] == "hidden"){
               $options     .="style='width:0px;border:0px;height:0px;overflow:hidden;display:non;'\n";
               $entry       .=sprintf($form_control,$options,$inner);
            }else{

               //Set style and length of the field
               $style        ="";
               if(isset($field_array['length']) && $field_array['dojoType'] != 'dijit.form.CheckBox' ){
                  $style         .="width:".$field_array['length']."px;";
               }

               if(isset($field_array['style'])){
                  $style         .=$field_array['style'];
               }

               if($style != ''){
                  $options            .="style='".$style."'";
               }

               $entry            =sprintf($form_control,$options,$inner);
               $entry_div_start   ="<div id='td_$field' jsId='td_$field' style='padding:10px;'>";
               $entry_div_end      ="<div id='td_in_$field'></div></div>";

               //Set label position
               $entry_label   ="<label for='$field' >".$field_array['label']."$required</label>";
               if(isset($field_array['label_pos'])){
                  switch($field_array['label_pos']){
                     case 'left':
                        $entry         =$entry_div_start.$entry_label.$entry.$entry_div_end;
                     break;
                     case 'right':
                        $entry         =$entry_div_start.$entry.$entry_label.$entry_div_end;
                     break;
                     case 'top':
                     default:
                        $entry         =$entry_div_start.$entry_label."<br>".$entry.$entry_div_end;
                     break;
                  }
               }else{
                  $entry         =$entry_div_start.$entry_label."<br>".$entry.$entry_div_end;
               }
            }
         }
         return $entry;
      }


      /*
       Generate fields for the search dialogbox
       */
      public function gen_filter_field_entry($field){
         $field_array;

         /*custom field array can be provied to the function else retrieve it from fields array*/
         if(is_array($field)){
            $field_array=$field;
            $field=$field_array['jsId'];
         }else{
            $field_array=$this->fields[$field];
            /*filtering fields can be empty*/
            $field_array['required']="false";
         }

         /*Set section header/footer as requested*/
         $section_start         ="";
         $section_end         ="";
         if(isset($field_array['section'])){
            switch($field_array['section']){
               case 'start':
                  if(isset($field_array['section_label']) && $field_array['section_label']){
                     $section_start      ="<td>".$field_array['section_label']."</td>";
                  }else{
                     $section_start      ="<td>SECTION</td>";
                  }
               break;
               case 'end':
                     $section_end      ="<td>SECTION</td>";
               break;
            }
         }


         $entry         =$section_start."<td style='padding-top:10px;'><label for='filter_$field'>".$field_array['label']."</label><br>";

         /*generate form control structure to be filled bellow in sprintf()*/
         $form_control   =$this->form_controls[$field_array['dojoType']];
         d_r($field_array['dojoType']);

         /*option string will be inserted to form control below in sprintf()*/
         $options         ="id='filter_$field' name='filter_$field'";

         /*if the form_control accepted inner html this string will placed in there*/
         $inner         =isset($field_array['inner'])?$field_array['inner']:"";

         /*Fields to bypass when creating forms*/
         $bypass=array('inner','label','section','disabled','label_pos','type');

         /*all paremeters will be inserted to the options string*/
         foreach($field_array as $key => $value){
            if(!in_array($key,$bypass)){
               $options.=$key."='$value'\n";
            }
         }

         //Set style and length of the field
         $style      ="";
         if(isset($field_array['length'])){
            $style   .="width:".$field_array['length']."px;";
         }

         if(isset($field_array['style'])){
            $style   .=$field_array['style'];
         }


         if($style != ''){
            $options .="style='".$style."'";
         }



         $entry         .=sprintf($form_control,$options,$inner);

         //$entry         .="<button dojoType='dijit.form.Button' iconClass='dijitEditorIcon dijitEditorIconSave' showLabel='false' onClick='alert($field)'>help</button>";
         $entry         .="</td>\n".$section_end;
         return $entry;
      }



      /*
       return :Form  for the provided table with using custom configuration of each field
       */
      public function gen_form($captchar=null,$filter_selector=null){
         $table=$this->self['table'];

         if($this->data_load_key != null){
            $this->get_data();
         }

         d_r('dijit.form.Form');
         $form= "<div dojoType='dijit.form.Form' id='".$table."_frm' jsId='$table'_frm
            encType='multipart/form-data'
            method='GET' >";

         $form.="<div >Required fields marked as <font color='red'>*</font>";

         /*Find first and last elements of the fields array*/
         reset($this->fields);
         $keys      =array_keys($this->fields);
         $first   =current($keys);
         $last      =end($keys);

         /*Set form table background and padding/spacing*/
         foreach($this->fields as $field => $field_array){

            if($field != ""){
               
               /*IF the section ended in previouse field drow section header*/
               /*IF the field is the first field of the form drow section header*/
               if($field==$first && !isset($field_array['section'])){
                  $field_array['section']=' ';
               }

                           
               /*Set section header/footer as requested*/
               $section         ="";

               if(isset($field_array['section'])){
                  $section      ="</div><br>";

                  /*For first field remove </div>*/
                  if($field==$first){
                     $section      ="";
                  }

                  if($field_array['section'] !=''){
                     //d_r('dijit.layout.ContentPane');
                     //$section      .="<div dojoType='dijit.layout.ContentPane' title='".$field_array['section']."'>";
                     if($field_array['section'] ==' '){
                        //$section      .="<div style='border:1px dotted #C9D7F1'>";
                        $section      .="<div >";
                     }else{
                        $section      .="<div ><div style='font-weight:bold;background-color:#C9D7F1;padding:4px;text-align:center' class='bgCenter'>".$field_array['section']."</div>";
                     }
                  }else{
                     //d_r('dijit.layout.ContentPane');
                     //$section      .="<div dojoType='dijit.layout.ContentPane' title='section'>";
                     $section      .="<div>";
                  }
               }
            
               $form.=$section;
               $form.= $this->gen_field_entry($field);

               /*If the element is 'last' set section as end*/
               if($field==$last && !isset($field_array['section'])){
                  $form.= "</div><br>";
               }
            }
         }
         
         if($filter_selector){
            $form=$this->gen_xhr_form_filler('fill_form').$form;
            $form=$this->gen_xhr_filtering_select('fill_form').$form;
         }

         //form ends hear
         $form.= "</div>";
         

         //Buttons of the form
         $form.= "
            <script type='text/javascript' >
            ".$this->form_submitter($table."_frm")."
            </script>
         ";

         return $form;
      }

      /*
       generate the filter dialog box to be used with the above table
       the grid will be generated according to the filter

       return: filter dialog
       */
      public function gen_filter(){
         d_r('dijit.Dialog');
         d_r('dijit.form.Form');
         d_r('dijit.form.ValidationTextBox');
         $dialog="
         <div dojoType='dijit.Dialog' id='filterDialog' jsId='filterDialog' title='Filter' >
         ".$this->gen_xhr_filtering_select('fill_filter','filter_name',true)
          .$this->gen_xhr_form_filler('fill_filter','filter','filter_name',true);
      
          $dialog.="
         <div dojoType='dijit.form.Form' jsId='filter_frm' id='filter_frm'>
            <table cellspacing='0px' cellspacing='0px'>";
      
          /*alien field from the original table to hold the meaningful filter name */
         $filter_name=array(
            "length"=>"70",
            "dojoType"=>"dijit.form.ValidationTextBox",
            "required"=>"true",
            "label"=>"Filter name",
            "value"=>"",
            "jsId"=>"filter_name",
            );
          $dialog.= "<tr>".$this->gen_filter_field_entry($filter_name)."</tr>";

          /*generate filter table according to the original table*/
          foreach($this->fields as $field => $arr){

            /*Ignore blank and custom fields*/
             if(!($field == ""  || (isset($this->fields[$field]['custom']) && $this->fields[$field]['custom'] == 'true'))){
                $dialog.= "<tr>";
                $dialog.= $this->gen_filter_field_entry($field);
               /*if checked exact value will be filterd else any value contained this phrase will be selected*/
               d_r('dijit.form.CheckBox');
               $dialog.= "<td><label for='filter_".$field."_exact'>Exact:</label><input dojoType='dijit.form.CheckBox' value='on' jsId='filter_".$field."_exact' id='filter_".$field."_exact'i name='filter_".$field."_exact' ></td>";
               /*AND to the others or OR to the others*/
               $dialog.= "<td><label for='filter_".$field."_exact'>And:</label><input dojoType='dijit.form.CheckBox'  value='on' jsId='filter_".$field."_and' id='filter_".$field."_and' name='filter_".$field."_and'></td>";
                $dialog.= "</tr>";
             }
          }
      
          $dialog.=  "<tr>
                          <td align='center' colspan='2' >
                               <button dojoType='dijit.form.Button'  onClick='dialog_submit(filter_frm.getValues(),\"select\");'>
                                  Select
                              </button>
                              <button dojoType='dijit.form.Button'  onClick='dialog_submit(filter_frm,\"add\");'>
                                  Add
                              </button>
                              <button dojoType='dijit.form.Button' onClick='dialog_submit(filterDialog,\"modify\");'>
                                  Modify
                              </button>
                              <button dojoType='dijit.form.Button'  onClick='dialog_submit(fill_filter_frm,\"delete\");'>
                                  Delete
                              </button>
                              <button dojoType='dijit.form.Button' type='button' onClick='dijit.byId(\"filterDialog\").hide();'>
                                  Cancel
                              </button>
                              <button dojoType='dijit.form.Button' type='button' onClick='clear_form(filterDialog)'>
                                 Clear
                              </button>
                          </td>
                      </tr>
                  </table>
                 </div>   
            </div>   
         <script type='text/javascript'>
         /**/
         function show_dialog(){
            formDlg = dijit.byId('filterDialog');
            formDlg.show();
         }

         /*clear the form first*/
         function clear_form(frm){
            dojo.forEach(dijit.byId(frm).getDescendants(),function(widget){
               widget.attr('value', null);
             });
         }   

         /*
         Sending filter data as json   
         */
         function dialog_submit(arg_form,action){
            /*User should confirm deletion*/
            if(action=='delete'&&!confirm('Confirm deletion!')){
               return;
            }
            if(arg_form.validate()){
               var json_req=dojo.toJson(arg_form.getValues(), true);
               dojo.xhrPost({
                  url: '".gen_url()."&xhr=true&form=filter&filter='+json_req+'&action='+action, 
                  handleAs:'text',
                  handle: function(response){
                     update_status_bar('OK',response.info);
                  },
      
                  load: function(response) {
                     update_status_bar('OK','form successfully submitted');
                  }, 
                  error: function() {
                     update_status_bar('ERROR','error on submission');
                  }
               });
      
               /*
               for(var key in arg_array){
                  update_status_bar(key);   
               }
               */
            }else{
               update_status_bar('ERROR','found invalid filed values');      
            }
         }
         </script>";
         return $dialog;
      }

      /*retrieve filter from the database*/
      public function ret_filter($filter_name,$table=null){

         $select="SELECT filter FROM ".$this->self['filter_table']." WHERE filter_name='".$filter_name."'";

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

         $_REQUEST['table_name']   =$this->self['table'];
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

         $insert="INSERT INTO ".$this->self['filter_table']."(%s) VALUES(%s)";

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

         $delete="UPDATE ".$this->self['filter_table']." SET deleted=TRUE WHERE filter_name='".$filter_name."'";

         if($purge){
            $delete="DELETE FROM ".$this->self['filter_table']." WHERE filter_name='".$filter_name."'";
         }

         if($table != null){
            $delete="UPDATE ".$this->self['filter_table']." SET deleted=TRUE WHERE filter_name='".$filter_name."'";
            if($purge){
               $delete="DELETE FROM ".$this->self['filter_table']." WHERE filter_name='".$filter_name."'";
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
         $update="UPDATE ".$this->self['filter_table']." SET  filter='$filter' WHERE filter_name='$filter_name''";

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
         $table=$table==null?$this->self['table']:$table;   

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

         $json_fileW=W_MODULES."/".MODULE."/".$this->self['table'].$this->data_json;
         $json_fileA=A_MODULES."/".MODULE."/".$this->self['table'].$this->data_json;

         //Save JSON to file
         $file_handler = fopen($json_fileA, 'w');
         fwrite($file_handler,$json);
         fclose($file_handler);
         return  $json_fileW;
      }

      public function gen_csv($key_array,$filter=null,$return,$table=null){
         $where=" WHERE ";

         if($filter != null){
            $where.=$filter;
         }else{
            $where="";
         }

         /*Custom table*/
         $table=$table==null?$this->self['table']:$table;   

         $res=exec_query("SELECT ".implode(",",$key_array)." FROM ".$table." $where",Q_RET_ARRAY);

         header('Content-Type', 'application/vnd.ms-excel');
         header('Content-Disposition: attachment; filename='.$csv_file);
         //header("Content-type: application/octet-stream");
         //header("Content-Disposition: attachment; filename=your_desired_name.xls");
         header("Pragma: no-cache");
         header("Expires: 0");


         $header=false;
         while($row = mysql_fetch_assoc($res)){
            if(!$header){
               echo '"'.implode('","',array_keys($row))."\"\n";
               $header=true;
            }
            echo '"'.implode('","',array_values($row))."\"\n";
         }
         exit();
      }


      /*
       $key_array: the key array to be visible in data grid
       json_file: data from the server
       return: data grid containing the key fields provided in $key_array
       */
      public function gen_data_grid($key_array,$json_url,$key=null){
         if($key==null){
            $key=$this->self['key'];
         }
         //d_r('dojo.data.ItemFileWriteStore');
         d_r('dojox.data.CsvStore');
         d_r('dojox.widget.PlaceholderMenuItem');
         d_r('dojox.grid.DataGrid');
         //echo "<span dojoType='dojo.data.ItemFileWriteStore' jsId='store3' url='$json_url'></span>
         echo "<span dojoType='dojox.data.CsvStore' jsId='store3' url='".gen_url()."&form=grid&data=csv'></span>
         <div dojoType='dijit.Menu' jsid='gridMenu' id='gridMenu' style='display: none;'>
            <div dojoType='dojox.widget.PlaceholderMenuItem' label='GridColumns'></div>
         </div>

         <table 
            dojoType='dojox.grid.DataGrid' 
            jsId='grid3' 
            store='store3' 
            query='{ ".$key_array[0].": \"*\" }'
            rowsPerPage='40' 
            clientSort='true' 
            style='width:100%;height:400px' 
            onClick='displayLinks'
            rowSelector='20px'
            columnReordering='true'
            headerMenu='gridMenu'
         >
         <thead>
            <tr>";
            /*Set labels for the table header if available in fileds array*/
            foreach($key_array as $h_key){
               echo "<th width='auto' field='$h_key'>
                  ".(isset($this->fields[$h_key]['label'])?$this->fields[$h_key]['label']:$h_key)."
               </th>";
            }
            //<th width='auto' field='sex' cellType='dojox.grid.cells.Select' options='Male,Female' editable='true'>Sex</th>
            echo "</tr>
         </thead>
         </table>";
            echo "
            <script type='text/javascript'>
            function displayLinks(e){
               var selectedValue = grid3.store.getValue(grid3.getItem(e.rowIndex),'".$key_array[0]."');
               //alert('selected cell Value is '+selectedValue);
               //fill_form(selectedValue);
               dijit.byId('fs_$key').setValue(selectedValue);
            }
         </script>";
      }


      /*
       key_array: key column of the configured table of the class
       json_file: source json file to extract the datafor the filtered selection box
       return: filtering select of dojo
       */
      public function gen_filtering_select($json_file){
         d_r('dojo.data.ItemFileReadStore');
         d_r('dijit.form.FilteringSelect');
         return "<div dojoType='dojo.data.ItemFileReadStore'
      jsId='stateStore' 
      url='$json_file'>
      </div>
      <input dojoType='dijit.form.FilteringSelect' 
         value='KY' 
      store='stateStore' 
      searchAttr='".$this->self['key']."' 
      name='state' 
      id='stateInput' >";
      }

      /*
       Provide data to  gen_xhrt_filtering_select
       @$table: custom table to be queried
       @$key: custom key to be presented
       @$filter: custom filter to be applied
       */
      public function xhr_filtering_select_data($table=null,$key=null,$filter=null,$order_by=null){
         $key   =$key==null?$this->self['key']:$key;
         if($this->self['key'] != $key)
         {
            $this->self['key2']=$key;
         }
         $table=$table==null?$this->self['table']:$table;
         //$filter=$filter==null?"":" AND table_name='".$this->self['table']."' ";
         $filter=$filter==null?"":" AND $filter";

         header('Content-Type', 'application/json');
         include 'qread_store_class.php';
         $query_read_store = new Query_read_store($table,$key,$filter,$order_by);
         echo $query_read_store->gen_json_data();
      }

      /*
       key   : column of the table given for the class
       filter: choose filter table or the other table
       return: realtime updated selection box
       Note   : This will generate [key]_query_read_store.php to realtime provide the data to the selection box
       */
      public function gen_xhr_filtering_select($js_function,$key=null,$filter=null){
         $key      =$key==null?$this->self['key']:$key;
         $label   =$key==null?$this->fields[$this->self['key']]['label']:$key;
         $form      =$filter==null?'&form=main':'&form=filter';
         $value   =isset($_REQUEST[$key])?$_REQUEST[$key]:'';

         /*If filter is attached to the url procede with the filter*/
         $filter_name=isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:'';
         d_r('dijit.form.Form');
         d_r('dojox.data.QueryReadStore');
         d_r('dijit.form.FilteringSelect');

         return "
      <div dojoType='dijit.form.Form' jsId='".$js_function."_frm' id='".$js_function."_frm' >
      <div dojoType='dojox.data.QueryReadStore' 
         url='".gen_url().$filter_name."&data=json$form'
         jsId='".$js_function."_select_store'
         >
      </div>
      Select ".$label."<br>
      <select dojoType='dijit.form.FilteringSelect' 
         store='".$js_function."_select_store' 
         searchAttr='".$key."' 
         pageSize='40' 
         required='false' 
         query='{ ".$key.":\"*\" }'  
         onChange='$js_function(this.get(\"displayedValue\"));'
         value='".$value."'   
         name='$key',
         id='fs_$key',
         jsId='fs_$key'
         >
      </select>
      </div>";
      }

      /*
       $question: A value provided for  $key field  fo the table
       return    : JSON formatted tupple related to the given $key from the given table
       */

      public function xhr_form_filler_data($qustion,$cus_table=null,$cus_key=null){
         $table=$this->self['table'];
         $f_key   =$this->self['key'];

         if($cus_table != null){
            $table=$cus_table;   
         }
         if($cus_key != null){
            $f_key=$cus_key;   
         }

      
         $comma="";
         $cols="";

         //Dates request formatted from MySQL
         foreach( $this->fields as $key => $arr){
            if(isset($arr['custom']) || isset($arr['store'])){
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
         foreach( $this->fields as $key => $arr){
            if(isset($arr['custom']) || isset($arr['store'])){
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
         $table=$this->self['filter_table'];

         $res=exec_query("SELECT * FROM ".$table." WHERE filter_name = '$qustion'",Q_RET_ARRAY);
         $row = $res[0];
         header('Content-Type', 'application/json');
         echo $row['filter'];
      }

      /*
       Generate the realtime form filler with dojo.xhrPost
       @key            : key value to be filtered from the database
       @table         : the table which should generate
       @js_function   : rename javascript function to be used in the onChange of the select

       @return         : form filler javascript code
       */
      public function gen_xhr_form_filler($js_function,$table=null,$key=null,$filter=null){
         $key      =$key==null?$this->self['key']:$key;
         $table   =$table==null?$this->self['table']:$table;
         $form      =$filter==null?'&form=main':'&form=filter';

         $id_prefix="";

         if($filter){
            $id_prefix="'filter_'+";
         }

         return "
      <script type='text/javascript' type='text/javascript'>
      function $js_function(".$key.") {
         if(!(".$key." == '' || ".$key." == 'new')){
         dojo.xhrPost({
            url       : '".gen_url()."&data=json&id='+".$key."+'$form',
            handleAs :'json',
            load       : function(response, ioArgs) {        
               if(response.status && response.status == 'ERROR'){
                  update_status_bar(response.status,response.info);
                  update_progress_bar(50);
                  return;
               }

                 //dijit.byId('".$table."_frm').attr('value', response); 
                 //dijit.byId('".$table."_frm').setValues(response); 
               /*reset form*/
               dojo.forEach(dijit.byId('".$table."_frm').getDescendants(), function(widget) {
                  if(!widget.store){
                     widget.attr('value', null);
                  }
               });
               /*fill the form with returned values from json*/
               for(var key in response){
                  if(response[key]){
                     if(response[key]['_type']=='Date'){
                        //Convert ISO standard date string to javascript Date object
                            dijit.byId(key).setValue(dojo.date.stamp.fromISOString(response[key]['_value'])); 
                     }else{
                        //Handle different types of fields
                        switch(dijit.byId(key).type){
                           case 'checkbox':
                              switch(response[key]){
                                 case '1':
                                 case 'on':
                                    dijit.byId(key).attr('checked',true); 
                                 break;
                                 case '0':
                                 case 'off':
                                 default:
                                    dijit.byId(key).attr('checked',false); 
                                 break;
                              }
                           break;
                           case 'radio':
                           break;
                           default:
                              dijit.byId(key).setValue(response[key]); 
                           break;
                        }
                     }
                  }
               }
            },
            error : function(response, ioArgs) {
                 update_status_bar('ERROR',response);
            }
         });
         }
      }
      </script>
      ";
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
         }else{
            $filter=$this->self['key']."='".$_REQUEST[$this->self['key']]."'";
         }

         $sql="SELECT * FROM ".$this->self['table']." WHERE ".$filter;
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
            foreach( $this->fields as $key => $arr){
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

            $insert_query   ="INSERT INTO ".$this->self['table']."(%s) VALUES(%s)";
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
         $sql="SELECT * FROM ".$this->self['table']." WHERE ".$this->self['key']."='".$_REQUEST[$this->self['key']]."'";

         //Log users activity
         act_log(null,$sql);

         $res=exec_query($sql,Q_RET_MYSQL_RES);
         if(get_affected_rows() > 0){
            //key available  -> modify
            $values   =""; //valus to be changes in the tupple
            $comma   ="";
            /*generate values string*/
            foreach( $this->fields as $key => $arr){

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
            $update_query   ="UPDATE ".$this->self['table']." SET %s WHERE ".$this->self['key']."='".$_REQUEST[$this->self['key']]."'";
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
         $delete="UPDATE ".$this->self['table']." SET deleted=true WHERE ".$this->self['key']."='".$_REQUEST[$this->self['key']]."'";
      
         if($purge){
            $delete="DELETE FROM ".$this->self['table']." WHERE ".$this->self['key']."='".$_REQUEST[$this->self['key']]."'";
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


   function set_help_tips($help_array){
      echo "<style type='text/css'>.helptt{max-width:400px;text-align:justify;color:green;}</style>";
      foreach( $help_array as $key => $value){
         if($value == '')continue;
         //possible positions of tooltio: before,above,after,below
         echo "<div dojoType='dijit.Tooltip' connectId='$key' position='after' >
         <!--b>HELP:</b-->
         <div class='helptt'>
            $value
         </div>
         </div>";
      }
   }

function filter_selector(){
if($GLOBALS['LAYOUT'] == 'pub')
{
   return;
}
?>

<!--_____________________________start filter select___________________________-->

<script type='text/javascript' type='text/javascript'>
dojo.addOnLoad(function() {
   toolbar = new dijit.byId("toolbar");
   var filterStore = new dojox.data.QueryReadStore({
      url: "<?php echo gen_url(); ?>&data=json&form=select_filter"
   });
   var filteringSelect = new dijit.form.FilteringSelect({
       id: "filter_select",
       name: "state",
       value: "<?php echo isset($_REQUEST['filter_name'])?$_REQUEST['filter_name']:"aa"; ?>",
       store: filterStore,
       searchAttr: "filter_name",
       pageSize: '20',
       style: 'width:100px',
       onChange:function(){change_filter(this.get("displayedValue"))},
       onClick:function(){change_filter(this.get("displayedValue"))},
   },"stateSelect");

   var filter_reset_button=new dijit.form.Button({
         label: "Reset Filter",
       iconClass:"dijitIcon dijitIconClear",
       onClick:function(){reset_filter()},
   });


   toolbar.addChild(filteringSelect);
   filteringSelect.setValue("<?php echo isset($_REQUEST['filter_name'])?$_REQUEST['filter_name']:"aa"; ?>");
   toolbar.addChild(filter_reset_button);
});

function change_filter(filter_name){
   if(filter_name != ''){
      URL='<?php echo gen_url().(isset($_REQUEST['form'])?"&form=".$_REQUEST['form']:"");?>&filter_name='+filter_name;
      open(URL,'_self');
   }
}

function reset_filter(){
   if(get_request_value('filter_name')){
      URL='<?php echo gen_url(NO_FILTER);?>';
      open(URL,'_self');
   }else{
      info_dialog('No filter set!');   
   }
}

function get_csv(){
   url='<?php echo gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:""); ?>&data=csv';
   open(url,'_self');
}

function grid(){
   url='<?php echo gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:""); ?>&form=grid';
   open(url,'_self');
}
</script>

<!--_______________________________end filter select___________________________-->
<?php
}
}
?>
