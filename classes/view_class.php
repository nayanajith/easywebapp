<?php
/**
 *
 */

class View{
   //The view file
   protected $view   ="_viw%s.php";

   //The model file
   protected $model  ="_mdl%s.php";
   protected $fields =array();

   //Table 
   protected $table;

   //Key to load data 
   protected $data_load_key;

   /** Constructor of model class
    * 
    */
   function __construct($table=null,$name=null) {
        $this->table=$table;
        
        //Setting the path to model and view files according to the given parameters
        if(isset($name) && $name != null ){
           $this->view = A_MODULES."/".MODULE."/".$name.$this->view;
           $this->model = A_MODULES."/".MODULE."/".$name.$this->model;
        }else{
           $this->view = A_MODULES."/".MODULE."/".$table.$this->view;
           $this->model = A_MODULES."/".MODULE."/".$table.$this->model;
        }

        //Determine group prefix according to the group of the user
        $group_prefix='';
        if(isset($_SESSION['group_id'])){
            $arr=exec_query("SELECT file_prefix FROM ".$GLOBALS['S_TABLES']['groups']." WHERE group_name='".$_SESSION['group_id']."'",Q_RET_ARRAY);
            $group_prefix='_'.$arr[0]['file_prefix'];
        }
         $model=sprintf($this->model,$group_prefix);
         $view=sprintf($this->view,$group_prefix);

        //Setting group wise view file if available else drop to default 
        if(file_exists($view)){
            $this->view=$view;
        }else{
            $this->view=sprintf($this->view,'');
        }

        //Setting group wise model file if available else drop to default
        if(file_exists($model)){
            $this->model=$model;
        }else{
            $this->model=sprintf($this->model,'');
        }

        if(file_exists($this->model)){
         include_once $this->model;

        /*
         if(isset($GLOBALS['MODEL'])){
           $GLOBALS['MODEL']['MAIN_LEFT']=$GLOBALS['MODEL']['MAIN_LEFT'];
         }
         */

        }

        /**
         * Set toolbar fields to class variable
         */
        if(isset($tb_fields)){
            $this->tb_fields=$tb_fields;
        }
   }

   /**
    * Add store to the page
    * This will allow to use one store among many fields
    */
   protected $stores=array();
   public function add_store($field_id,$store_id){
      d_r('dojox.data.QueryReadStore');
      if(!isset($this->stores[$store_id])){
          add_to_main(
            "<span dojoType='dojox.data.QueryReadStore' 
            url='".gen_url()."&data=json&action=combo&form=main&field=".$field_id."'
            jsId='".$store_id."'
            >
            </span>"
         );
         $this->stores[]=$store_id;
      }
   }

   //Mapping interlel html chuncks for each dojo type
   //
   protected $form_controls=array(
       "dijit.form.FilteringSelect"   =>"<select %s>%s</select>",
       "dijit.form.ComboBox"          =>"<select %s>%s</select>",
       "dijit.form.Select"            =>"<select %s>%s</select>",
       "dijit.form.MultiSelect"       =>"<select %s>%s</select>",
       "dijit.form.SimpleTextarea"    =>"<textarea %s>%s</textarea>",
       "dijit.form.NumberTextBox"     =>"<input %s>",
       "dijit.form.TextBox"           =>"<input %s>",
       "dijit.form.NumberSpinner"     =>"<input %s>",
       "dijit.form.ValidationTextBox" =>"<input %s>",
       "dijit.form.DateTextBox"       =>"<input %s constraints=\"{datePattern:'yyyy-MM-dd'}\" promptMessage='yyyy-MM-dd' invalidMessage='Invalid date. Please use yyyy-MM-dd format.' >",
       "dijit.form.TimeTextBox"       =>"<input %s constraints=\"{'timePattern':'hh:mm:ss'}\" promptMessage='hh:mm:ss' invalidMessage='Invalid time. Please use hh:mm:ss format.' >",
       "dijit.form.CheckBox"          =>"<div %s></div>",
       "dijit.form.RadioButton"       =>"<div %s></div>",
       "dijit.InlineEditBox"          =>"<span %s></span>",
       "dijit.form.Button"            =>"<button %s>%s</button>",
   );



   /*
    Generate entry for the given table field
    select-> data for select box/combo box
    data-> data for text area
   */
   public function gen_field_entry($field,$field_array,$customizable=false){
      /*fill data from data array*/
      $fill ="";

      //if customizable true then retuen label and field seperately as an array
      $custom_arr=array(
           'label'=>'', 
           'field'=>'', 
      );

      if($this->data_load_key != null){
         if($field != 'password' && isset($this->data[$field])){
            $fill=$this->data[$field];
         }
      }

      /*set fill externelly when loading with data*/
      if($fill != ''){
         $field_array['value']=$fill;
      }

      /*html for the given field will be filled to this var*/
      $html         ="";
      $form_control ="";
      $options      ="";

      /*inner value of the field (innerhtml)*/
      $inner   =isset($field_array['inner'])?$field_array['inner']:"";

      /*if required=true put  * by the label */
      $required      ="";
      if(isset($field_array['required']) && $field_array['required'] == "true"){
         $required      ="<font color='red'>*</font>";
      }

      //If the field require a store add a store to the page
      if(isset($field_array['store'])){
         $this->add_store($field,$field_array['store']);
      }

      /*Handl custom form input method or generic one*/
      if(isset($field_array['custom']) && $field_array['custom'] == 'true' ){
         $html.="<div id='td_$field' jsId='td_$field' style='padding:10px;'>";
         if($customizable){
            $custom_arr['label']="<label for='$field' >".$field_array['label']."$required</label>";
         }else{
            $html.="<label for='$field' >".$field_array['label']."$required</label>";
         }
         $html.=$inner;
         $html.="<div id='td_in_$field'></div></div>\n";
      }else{
         d_r($field_array['dojoType']);
         $form_control   =$this->form_controls[$field_array['dojoType']];
         $options         =" jsId='$field' id='$field' name='$field' ";

         /*Fields to bypass when creating forms*/
         $bypass=array('inner','iconClass','label','section','style','label_pos','type','vid','filter','ref_table','ref_key','order_by');

         /*all paremeters will be inserted to the options string*/
         foreach($field_array as $key => $value){
            if(!in_array($key,$bypass)){
               $options.=$key."='$value'\n";
            }
         }


         //hidden fields make not visible
         if(isset($field_array['type']) && $field_array['type'] == "hidden"){
            $options .="style='width:0px;border:0px;height:0px;overflow:hidden;display:non;'\n";
            $html .=sprintf($form_control,$options,$inner);
         }else{

            //Set style and length of the field
            $style ="";
            if(isset($field_array['length']) && $field_array['dojoType'] != 'dijit.form.CheckBox' ){
               $style .="width:".$field_array['length']."px;";
            }

            //custum style is applied 
            if(isset($field_array['style'])){
               $style .=$field_array['style'];
            }

            //additional style is applied
            if($style != ''){
               $options .="style='".$style."'";
            }

            //combining the dojo type mapping in above array with the generated content
            $html            .=sprintf($form_control,$options,$inner);
            $field_div_start   ="<div id='td_$field' jsId='td_$field' style='padding:10px;'>";
            $field_div_end     ="<div id='td_in_$field'></div></div>";

            //Set label position
            if($customizable){
               $custom_arr['label']="<label for='$field' >".$field_array['label']."$required</label>";
               $custom_arr['field']=$field_div_start.$html.$field_div_end;
            }else{
               $field_label   ="<label for='$field' >".$field_array['label']."$required</label>";
               if(isset($field_array['label_pos'])){
                  switch($field_array['label_pos']){
                     case 'left':
                        $html =$field_div_start.$field_label.$html.$field_div_end;
                     break;
                     case 'right':
                        $html =$field_div_start.$html.$field_label.$field_div_end;
                     break;
                     case 'top':
                     default:
                        $html =$field_div_start.$field_label."<br>".$html.$field_div_end;
                     break;
                  }
               }else{
                  $html =$field_div_start.$field_label."<br>".$html.$field_div_end;
               }
            }
         }
      }
      if($customizable){
         $custom_arr['field']=$html;
         return $custom_arr;
      }
      return $html;
   }

   public  function finish_view(){
      if(file_exists($this->view)){
         //add_to_main_left("<div dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >");
         include $this->view;
         //add_to_main_left("</div>"); 
      }
   }

   /*
   Generating form for using ghe fields array which was generated in model-class 
   */
   public function gen_form($captchar=null,$filter_selector=null){
      $table=$this->table;

      if($this->data_load_key != null){
         $this->get_data();
      }

      if(file_exists($this->view)){
         $GLOBALS['PREVIEW']['MAIN_LEFT']=array();
         foreach($GLOBALS['MODEL']['MAIN_LEFT'] as $field => $field_array){
             $GLOBALS['PREVIEW']['MAIN_LEFT'][$field]=$this->gen_field_entry($field,$field_array,true);
         }
         return;
      }

      d_r('dijit.form.Form');
      //$html= "<div dojoType='dijit.form.Form' id='".$table."_frm' jsId='$table'_frm encType='multipart/form-data' method='POST' >";
      $html= "<div dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' >";
      $html.="<div >Required fields marked as <font color='red'>*</font>";

      /*Find first and last elements of the fields array*/
      reset($GLOBALS['MODEL']['MAIN_LEFT']);
      $keys    =array_keys($GLOBALS['MODEL']['MAIN_LEFT']);
      $first   =current($keys);
      $last    =end($keys);

      /*Set html table background and padding/spacing*/
      foreach($GLOBALS['MODEL']['MAIN_LEFT'] as $field => $field_array){
         if($field != ""){
            /*IF the section ended in previouse field drow section header*/
            /*IF the field is the first field of the html drow section header*/
            if($field==$first && !isset($field_array['section'])){
               $field_array['section']=' ';
            }

                        
            /*Set section header/footer as requested*/
            $section="";

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
         
            $html.=$section;
            $html.=$this->gen_field_entry($field,$field_array);

            /*If the element is 'last' set section as end*/
            if($field==$last && !isset($field_array['section'])){
               $html.= "</div><br>";
            }
         }
      }
      
      //html ends hear
      $html.= "</div>";
      $html.= "</div>";
      add_to_main_left($html);
   }

   /*
    Generate entry for the given table field
    select-> data for select box/combo box
    data-> data for text area
   */
   public function gen_toolbar_entry($field,$field_array){

      //if the entry/control type is a button the execute gen_toolbar_button instead
      if($field_array['dojoType']=='dijit.form.Button'){
         return $this->gen_toolbar_button($field,$field_array);
      }
      //Add toolbar prefix to identify the ids in toolbar

      /*fill data from data array*/
      $fill ="";

      if(isset($_SESSION[PAGE]) && isset($_SESSION[PAGE][$field])){
         $fill=$_SESSION[PAGE][$field];
      }

      //original field id
      $field_=$field;

      //toolbar field id
      $field="toolbar__".$field;

      /*set fill externelly when loading with data*/
      if($fill != ''){
         $field_array['value']=$fill;
      }


      /*html for the given field will be filled to this var*/
      $html         ="";
      $form_control ="";
      $options      ="";

      /*inner value of the field (innerhtml)*/
      $inner   =isset($field_array['inner'])?$field_array['inner']:"";

      /*if required=true put  * by the label */
      $required      ="";
      if(isset($field_array['required']) && $field_array['required'] == "true"){
         //$required      ="<font color='red'>*</font>";
      }

      //If the field require a stor add a store
      if(isset($field_array['store'])){
         $this->add_store($field,$field_array['store']);
      }

      /*Handl custom form input method or generic one*/
      if($field_array['dojoType'] == 'dijit.form.Button' ){
         d_r($field_array['dojoType']);
      }elseif(isset($field_array['custom']) && $field_array['custom'] == 'true' ){
         $html.="<div id='td_$field' jsId='td_$field' >";
         //$html.="<label for='$field' >".$field_array['label']."$required</label>";
         $html.=$inner;
         $html.=sprintf($html,$fill);
         $html.="<div id='td_in_$field'></div></div>\n";
      }else{
         d_r($field_array['dojoType']);
         $form_control   =$this->form_controls[$field_array['dojoType']];
         $options         =" jsId='$field' id='$field' name='$field'  title='".$field_array['label']."' ";

         /*Fields to bypass when creating forms*/
         $bypass=array('inner','icon','label','section','style','label_pos','type','vid','filter','ref_table','ref_key','order_by');

         /*all paremeters will be inserted to the options string*/
         foreach($field_array as $key => $value){
            if(!in_array($key,$bypass)){
               $options.=$key."='$value'\n";
            }
         }


         //hidden fields make not visible
         if(isset($field_array['type']) && $field_array['type'] == "hidden"){
            $options .="style='width:0px;border:0px;height:0px;overflow:hidden;display:non;'\n";
            $html .=sprintf($form_control,$options,$inner);
         }else{

            //Set style and length of the field
            $style ="";
            if(isset($field_array['length']) && $field_array['dojoType'] != 'dijit.form.CheckBox' ){
               $style .="width:".$field_array['length']."px;";
            }

            //custum style is applied 
            if(isset($field_array['style'])){
               $style .=$field_array['style'];
            }

            //additional style is applied
            if($style != ''){
               $options .="style='".$style."'";
            }

            //combining the dojo type mapping in above array with the generated content
            $html            .=sprintf($form_control,$options,$inner);
            $field_div_start  ="<div id='td_$field' jsId='td_$field'>";
            $field_div_end    ="<div id='td_in_$field'></div></div>";
            $field_div_start  ="";
            $field_div_end    ="";


            //Set label position
            $html =$field_div_start.$html.$field_div_end;
         }
      }
      if(isset($field_array['store']) && ($field_array['dojoType'] == 'dijit.form.FilteringSelect' || $field_array['dojoType'] == 'dijit.form.Select' || $field_array['dojoType'] == 'dijit.form.Select')){
      $html.="
<script>
//Set the previouse value in drop down box
dojo.ready(function(){
   //TODO
   //load_selected_value($field,'$fill');
});
</script> ";
      }

      return $html;
   }

   function gen_toolbar_button($field,$field_array){
      if($field_array['dojoType']=='dijit.form.Button'){

         /*all paremeters will be inserted to the options string*/
         $inner   ='';
         //$inner   =$field_array['label'];
         $options ="jsId='toolbar__$field' ";
         $bypass=array('inner','section','style','label_pos','type','vid','filter','ref_table','order_by');
         foreach($field_array as $key => $value){
            if(!in_array($key,$bypass)){
               $options.=$key."='$value'\n";
            }
         }      

        $form_control=$this->form_controls[$field_array['dojoType']];
        return sprintf($form_control,$options,$inner);
      }
   }


   /**
    * Generate toolbar entries and add them to toolbar
    */
   function gen_toolbar(){
      foreach($GLOBALS['MODEL']['TOOLBAR'] as $field => $field_array){
         add_to_toolbar($this->gen_toolbar_entry($field,$field_array));
      }
   }

 
   function set_help_tips($help_array){
      $html="<style type='text/css'>.helptt{max-width:400px;text-align:justify;color:green;}</style>";
      foreach( $help_array as $key => $value){
         if($value == '')continue;
         //possible positions of tooltio: before,above,after,below
         $html.="<div dojoType='dijit.Tooltip' connectId='$key' position='after' >
         <!--b>HELP:</b-->
         <div class='helptt'>
            $value
         </div>
         </div>";
      }
      return $html;
   }

   /*
    $key_array: the key array to be visible in data grid
    json_file: data from the server
    return: data grid containing the key fields provided in $key_array
    */
   public function gen_data_grid($field_array,$key=null){
      d_r('dojox.data.CsvStore');
      //d_r('dojox.data.JsonRestStore');
      d_r('dojox.widget.PlaceholderMenuItem');
      d_r('dojox.grid.DataGrid');
      $html=""; 
      if(isset($GLOBALS['MODEL']['MAIN_RIGHT']) && isset($GLOBALS['MODEL']['MAIN_RIGHT']['GRID'])){
         $grid=$GLOBALS['MODEL']['MAIN_RIGHT']['GRID'];

         $html.="<span dojoType='dojox.data.CsvStore' clearOnClose='true' jsId='".$grid['store']."' url='".gen_url()."&form=".$grid['store']."&data=csv'></span>
      <div dojoType='dijit.Menu' jsid='".$grid['headerMenu']."' id='".$grid['headerMenu']."' style='display: none;'>
         <div dojoType='dojox.widget.PlaceholderMenuItem' label='GridColumns'></div>
      </div>";
         $html .="<table\n";

         /*Fields to bypass when creating forms*/
         $bypass=array('filter','columns','selector_id','ref_table','ref_key','event_key','sql');

         /*all paremeters will be inserted to the options string*/
         foreach($grid as $key => $value){
            if(!in_array($key,$bypass)){
               $html.=$key."='$value'\n";
            }
         }      

         $html .='><thead><tr>';

         /*Set labels for the table header if available in fileds array*/
         foreach($grid['columns'] as $key=>$array){
            $h_key         ='';
            $options       ='';
            $bypass        =array();

            //Sett cell type and editbility and other options
            if(is_array($array)){
               $h_key=$key;

               foreach($array as $key => $value){
                  if(!in_array($key,$bypass)){
                     $options.=$key."='".$value."' ";
                  }
               }
            }else{
               $h_key=$array;
            }


            $html.= "<th width='auto' field='$h_key' $options >
               ".(isset($GLOBALS['MODEL']['MAIN_LEFT'][$h_key]['label'])?$GLOBALS['MODEL']['MAIN_LEFT'][$h_key]['label']:$h_key)."
            </th>";
         }
         $html.= "</tr>
      </thead>
      </table>";
         if(!isset($grid['event_key'])){
            $grid['event_key']=$GLOBALS['MODEL']['KEYS']['PRIMARY_KEY'];
         }

         if(isset($grid['selector_id'])){
            $html.= "
            <script type='text/javascript'>
            function load_grid_item(e){
               var selectedValue = ".$grid['jsId'].".store.getValue(".$grid['jsId'].".getItem(e.rowIndex),'".$grid['event_key']."');
               load_selected_value(".$grid['selector_id'].",selectedValue);
               //alert('selected cell Value is '+selectedValue);
               //fill_form(selectedValue);
               //dijit.byId('".$GLOBALS['MODEL']['KEYS']['PRIMARY_KEY']."').setValue(selectedValue);
            }
            </script>";
         }
      }

      if(file_exists($this->view)){
         $GLOBALS['PREVIEW']['MAIN_RIGHT']=array(
            'GRID'=>$html,
         );
      }else{
         add_to_main_right($html); 
      }
   }


   /*
    key   : column of the table given for the class
    filter: choose filter table or the other table
    return: realtime updated selection box
    Note   : This will generate [key]_query_read_store.php to realtime provide the data to the selection box
    */
   public function gen_xhr_filtering_select($js_function,$key=null,$filter=null){
      $key     =$key==null?$this->self['key']:$key;
      $label   =$key==null?$GLOBALS['MODEL']['MAIN_LEFT'][$this->self['key']]['label']:$key;
      $form    =$filter==null?'&form=main':'&form=filter';
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
}

?>
