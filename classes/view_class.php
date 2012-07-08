<?php
/**
 *
 */

class View{
   //The view file
   protected $view   ="_viw%s.php";

   //The model file
   protected $model  ="_mdl%s.php";

   //all the sub arrays form the model
   protected $keys   =array();
   protected $form   =array();
   protected $grids  =array();
   protected $toolbar=array();
   protected $widgets=array();

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
        if(isset($_SESSION['role_id'])){
            $arr=exec_query("SELECT file_prefix FROM ".$GLOBALS['S_TABLES']['role']." WHERE group_name='".$_SESSION['role_id']."'",Q_RET_ARRAY);
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

         if(isset($GLOBALS['MODEL'])){
           $this->keys    =get_mdl_property('KEYS');
           $this->form    =get_mdl_property('FORM');
           $this->grids   =get_mdl_property('GRIDS');
           $this->toolbar =get_mdl_property('TOOLBAR');
           $this->widgets =get_mdl_property('WIDGETS');
         }
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
         if($GLOBALS['LAYOUT']=='app2'){
            add_to_toolbar(
               "\n<span dojoType='dojox.data.QueryReadStore' 
               url='".gen_url()."data=json&action=combo&form=main&field=".$field_id."'
               jsId='".$store_id."'
               requestMethod='post'
               >
               </span>\n"
            );
         }else{
            add_to_main_top(
               "\n<span dojoType='dojox.data.QueryReadStore' 
               url='".gen_url()."data=json&action=combo&form=main&field=".$field_id."'
               jsId='".$store_id."'
               requestMethod='post'
               >
               </span>\n"
            );
         }
         $this->stores[]=$store_id;
      }
   }

   //Mapping interlel html chuncks for each dojo type
   //
   protected $form_controls=array(
       "dijit.form.FilteringSelect"   =>"<select %s>%s</select>",
       "dijit.form.ComboBox"          =>"<select %s>%s</select>",
       "dijit.form.Select"            =>"<select %s>%s</select>",
       "dojox.form.CheckedMultiSelect"=>"<select %s>%s</select>",
       "dijit.form.MultiSelect"       =>"<select %s>%s</select>",
       "dijit.form.SimpleTextarea"    =>"<textarea %s>%s</textarea>",
       "dijit.form.NumberTextBox"     =>"<input %s>",
       "dijit.form.TextBox"           =>"<input %s>",
       "dijit.form.NumberSpinner"     =>"<input %s>",
       "dijit.form.ValidationTextBox" =>"<input %s>",
       "dijit.form.DateTextBox"       =>"<input %s constraints=\"{datePattern:'yyyy-MM-dd'}\" promptMessage='yyyy-MM-dd' invalidMessage='Invalid date. Please use yyyy-MM-dd format.' >",
       "dijit.form.TimeTextBox"       =>"<input %s constraints=\"{'timePattern':'hh:mm:ss'}\" promptMessage='hh:mm:ss' invalidMessage='Invalid time. Please use hh:mm:ss format.' >",
       "dijit.form.CheckBox"          =>"<input %s>%s",
       "dijit.form.RadioButton"       =>"<input %s>%s",
       "dijit.InlineEditBox"          =>"<span %s></span>",
       "dijit.form.Button"            =>"<button %s>%s</button>",
       "dijit.form.DropDownButton"    =>"<div %s>%s</div>",
   );



   /*
    Generate entry for the given table field
    select-> data for select box/combo box
    data-> data for text area
   */
   public function gen_field_entry($field,$field_array){
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

		/*if tooltip is set add tooltip */
      $tooltip      ="";
      if(isset($field_array['tooltip']) && $field_array['tooltip'] != ""){
         $tooltip="<div dojoType='dijit.Tooltip' id='tooltip_".$field."' connectId='$field'><div style='max-width:400px;text-align:justify'>".$field_array['tooltip']."</div></div>";
      }

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
         $custom_arr['label']="<label for='$field' >".$field_array['label']."$required</label>";
         $custom_arr['field']=$html.$inner;
      }else{
         d_r($field_array['dojoType']);
         $form_control   =$this->form_controls[$field_array['dojoType']];
         $options        =" jsId='$field' id='$field' name='$field' ";

         /*Fields to bypass when creating forms*/
         $bypass=array('default','isolate','inner','iconClass','label','section','style','label_pos','type','vid','filter','ref_table','ref_key','order_by','tooltip');

         //If tooltip is set then bypass title
         if(isset($field_array['tooltip'])){
            $bypass[]='title';
         }

         /*all paremeters will be inserted to the options string*/
         foreach($field_array as $key => $value){
            if(!in_array($key,$bypass)){
               $options.=$key."='$value'\n";
            }
         }

         //hidden fields make not visible
         if(isset($field_array['type']) && $field_array['type'] == "hidden"){
            $options .="style='width:0px;border:0px;height:0px;overflow:hidden;display:non;'\n";
            $custom_arr['field']=sprintf($form_control,$options,$inner);
            $custom_arr['label']='';
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

            $custom_arr['label']="<label for='$field' >".$field_array['label']."$required</label>";
            $custom_arr['field']=$html.$tooltip;
         }
      }
      return $custom_arr;
   }

   public  function finish_view(){
      if(file_exists($this->view)){
         include $this->view;
      }
   }

   /**
    * Layout of the form is in flow format 
    * <label>
    * <field>
    */
   public function form_flow_layout(){
      $form_preview=get_pviw_property('FORM');
      d_r('dijit.form.Form');
      $html= "<div><div dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' style='padding:10px'>";
      $html.="Required fields marked as <font color='red'>*</font>";
      /*Set html table background and padding/spacing*/
      foreach($form_preview as $key => $arr){
         $html.=$arr['label']."<br>";
         $html.=$arr['field']."<br><br>";
      }

      $html.="</div></div>";

      //add generated form to MAIN_LEFT 
      add_to_main_left($html);
   }

   /**
    * Layout of the form in table format
    * <label><field>
    */
   public function form_table_layout(){
      $form_preview=get_pviw_property('FORM');
      d_r('dijit.form.Form');
      $html= "<div><div dojoType='dijit.form.Form' id='main' jsId='main' encType='multipart/form-data' method='POST' style='padding:10px;'>";
      $html.="Required fields marked as <font color='red'>*</font><table>";
      /*Set html table background and padding/spacing*/
      foreach($form_preview as $key => $arr){
         $html.="<tr><td>".$arr['label']."</td>";
         $html.="<td>".$arr['field']."</td></tr>";
      }

      $html.="</table></div></div>";

      //add generated form to MAIN_LEFT 
      add_to_main_left($html);
   }



   /*
   Generating form for using ghe fields array which was generated in model-class 
   $layout: table,flow
   */
   public function gen_form($captchar=null,$filter_selector=null,$layout='table'){
      $table=$this->table;

      //Load data for the given key in to data array
      if($this->data_load_key != null){
         $this->get_data();
      }

      //Fill the preview array with the field/labels
      foreach($this->form as $field => $field_array){
          set_pviw_property(array('FORM',$field),$this->gen_field_entry($field,$field_array,true));
      }

      //layout the fields and labels according to the requested layout
      if(!file_exists($this->view)){
         switch($layout){
         case 'table':
            $this->form_table_layout();
         break;
         case 'flow':
         default:
            $this->form_flow_layout();
         break;
         }
      }
   }

   public function csv_field_selector($checked_fields=null){
      d_r('dijit.TooltipDialog');
      d_r('dijit.form.CheckBox');
      d_r('dijit.form.DropDownButton');
      d_r('dojo.query');
		//NOTE: related javascript function available in js/common.js

      $csv_inner="
<span>CSV</span>
<div dojoType='dijit.TooltipDialog' align='center'>
<b>Select the fields you want to include in the CSV</b>
<table  id='csv__table'>";
      $cols=3;
      $td=0;
      foreach($this->form as $id => $arr){
         if($td == 0){
            $csv_inner.="<tr>";
         }
         //Set default checked fields if the parameter is not null else all will be checked
         $checked="";
         if(!is_null($checked_fields)){
            if(in_array($id,$checked_fields)){
               $checked="checked='true'";
            }
         }else{
            $checked="checked='false'";
         }
         
         $label=$id;
         if(isset($arr['label'])){
            $label=$arr['label'];
         }

         $csv_inner.="<td>
            <input type='checkbox' dojoType='dijit.form.CheckBox' value='$id' id='csv__".$id."' jsId='csv__".$id."' $checked >
            </td>
            <td>
            <label for='csv__".$id."'>".$label."</label>
            </td>";
         $td++;
         if($td==$cols || !next($this->form) || is_null(next($this->form))){
            $csv_inner.="</tr>";
            $td=0;
         }
      }
      $csv_inner.='</table>
   <button dojoType="dijit.form.Button" type="submit" onClick="get_csv()">Get CSV</button></div>';
      return $csv_inner;
   }

   /*
    * generate entry for toolbar
   */
   public function gen_toolbar_entry($field,$field_array){

      //if the entry/control type is a button the execute gen_toolbar_button instead
      if(!(isset($field_array['custom']) && $field_array['custom'] == 'true') && $field_array['dojoType']=='dijit.form.Button' ){
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

      //for csv overrides the inner by this function
      if($field_=='csv'){
         if(isset($field_array['checked_fields'])){
            $inner=$this->csv_field_selector($field_array['checked_fields']);
         }else{
            $inner=$this->csv_field_selector();
         }
      }

      //DropDownButtons should have the inner span to show its text
      if($inner == "" && $field_array['dojoType'] == 'dijit.form.DropDownButton'){
         d_r('dijit.TooltipDialog');
         $inner="<span>".$field_array['label']."</span><div dojoType='dijit.TooltipDialog' align='center'></div>";
      }


      //If the field require a stor add a store
      if(isset($field_array['store'])){
         $this->add_store($field,$field_array['store']);
      }

      /*Handl custom form input method or generic one*/
      if(isset($field_array['custom']) && $field_array['custom'] == 'true' ){
         $html.=$inner;
      }else{
         d_r($field_array['dojoType']);
         $form_control  =$this->form_controls[$field_array['dojoType']];
			
         //hidden fields make not visible
         if(isset($field_array['type']) && $field_array['type'] == "hidden"){
            $options .="style='width:0px;border:0px;height:0px;overflow:hidden;display:non;'\n";
            $html .=sprintf($form_control,$options,$inner);
         }else{
            //dojo data-dojo-props (to put placeholder and more)
				$data_dojo_props	="";
				if(isset($field_array['data-dojo-props'])){
					$data_dojo_props	=$field_array['data-dojo-props'];
				}elseif(isset($field_array['label'])){
					$data_dojo_props	="data-dojo-props=\"placeHolder:'".$field_array['label']."'\"";
				}elseif(isset($field_array['title'])){
					$data_dojo_props	="data-dojo-props=\"placeHolder:'".$field_array['title']."'\"";
				}
         
            $options ="\njsId='$field'\nid='$field'\n$data_dojo_props\n";
         
            /*Fields to bypass when creating forms*/
            $bypass=array('default','isolate','inner','icon','label','section','style','label_pos','type','vid','filter','ref_table','ref_key','order_by','placeHolder','checked_fields','tooltip');
         
            //If tooltip is set bypass title
            if(isset($field_array['tooltip'])){
               $bypass[]='title';
            }elseif(!isset($field_array['tooltip']) && isset($field_array['label'])){
               $field_array['title']=$field_array['label'];
            }
         
            //all paremeters will be inserted to the options string other than thos to be bypassed
            foreach($field_array as $key => $value){
               if(!in_array($key,$bypass)){
                  $options.=$key."='$value'\n";
               }
            }

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

            //Add label to the endo of the checkboxes  and radio buttons
            if(in_array($field_array['dojoType'] ,array('dijit.form.CheckBox','dijit.form.RadioButton'))){
               $html .=sprintf($form_control,$options,"<label for='$field' style='padding-left:0px'>".$field_array['label']."</label>");
            }else{
               //combining the dojo type mapping in above array with the generated content
               $html .=sprintf($form_control,$options,$inner);
            }

            /*if tooltip is set add tooltip */
            $tooltip      ="";
            if(isset($field_array['tooltip']) && $field_array['tooltip'] != ""){
               $tooltip="<div dojoType='dijit.Tooltip' id='tooltip_".$field."' connectId='$field'>\n<div style='max-width:400px;text-align:justify'>\n".$field_array['tooltip']."\n</div>\n</div>\n";
            }

            $html .=$tooltip;
         }
      }
      if(
         isset($field_array['store']) && 
         (
            $field_array['dojoType'] == 'dijit.form.FilteringSelect' || 
            $field_array['dojoType'] == 'dijit.form.Select' || 
            $field_array['dojoType'] == 'dijit.form.ComboBox'
         )
      ){
      $html.="
<script>
//Set the previouse value in drop down box
dojo.ready(function(){
   load_selected_value($field,'$fill');
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
         $bypass=array('inner','section','style','label_pos','type','vid','filter','ref_table','order_by','placeHolder','checked_fields');
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
         if(is_array($field_array)){
            add_to_toolbar("\n".$this->gen_toolbar_entry($field,$field_array)."\n");
         }
      }
   }

 
   /*
    $key_array: the key array to be visible in data grid
    json_file: data from the server
    return: data grid containing the key fields provided in $key_array
    */
   public function gen_data_grid($field_array,$key=null){
      d_r('dojox.widget.PlaceholderMenuItem');
      d_r('dojox.grid.EnhancedGrid');
      d_r('dojox.grid.enhanced.plugins.Pagination');
      //d_r('dojox.grid.enhanced.plugins.Filter');
      //d_r("dojox.grid.enhanced.plugins.DnD");
      //d_r("dojox.grid.enhanced.plugins.Menu");
      d_r("dojox.grid.enhanced.plugins.NestedSorting");
      //d_r("dojox.grid.enhanced.plugins.IndirectSelection");
      //d_r("dojox.grid.enhanced.plugins.Search");
      d_r("dojox.grid.enhanced.plugins.Printer");
      d_r("dojox.grid.enhanced.plugins.exporter.CSVWriter");
      d_r("dojox.grid.enhanced.plugins.exporter.TableWriter");

      $html=""; 
      foreach($this->grids as $grid_key => $grid){
         //If jsId is not set explicitly set the key with grid__ prefix as jsId
         if(!isset($grid['jsId']))$grid['jsId']="grid__".$grid_key;

         //If store is not set explicitly set the grid_key formatted into grid__$grid_key_store 
         if(!isset($grid['store']))$grid['store']="grid__".$grid_key."_store";

         //Page stepper only enable for csvstore
         $page_stepper='true';

         //Switch between csv store and qreadstore
         if(isset($grid['store_type'])){
            switch($grid['store_type']){
            case 'csv':
               d_r('dojox.data.CsvStore');
               $html.="<span dojoType='dojox.data.CsvStore' clearOnClose='true' jsId='".$grid['store']."' url='".gen_url()."&form=".$grid['store']."&data=csv'></span>";
            break;
            case 'query':
               d_r('dojox.data.JsonRestStore');
               $html.="<span dojoType='dojox.data.QueryReadStore' requestMethod='post' clearOnClose='true' jsId='".$grid['store']."' url='".gen_url()."form=".$grid['store']."&data=json'></span>";
            break;
            }
         }else{
            d_r('dojox.data.JsonRestStore');
            $html.="<span dojoType='dojox.data.QueryReadStore' requestMethod='post' clearOnClose='true' jsId='".$grid['store']."' url='".gen_url()."form=".$grid['store']."&data=json'></span>";
            //d_r('dojo.data.ItemFileWriteStore');
            //$html.="<span dojoType='dojo.data.ItemFileWriteStore' requestMethod='post' clearOnClose='true' jsId='".$grid['store']."' url='".gen_url()."form=".$grid['store']."&data=json'></span>";
         }

         //Menu for the right click on grid
         $html.="<div dojoType='dijit.Menu' jsid='".$grid['headerMenu']."' id='".$grid['headerMenu']."' style='display: none;'>
         <div dojoType='dojox.widget.PlaceholderMenuItem' label='GridColumns'></div>
         </div>";

         /*
         $html.="<div dojoType='dijit.Menu' id='cellMenu'  style='display: none;'>
         <div dojoType='dijit.MenuItem' onclick='grid_to_table(".$grid['jsId'].",\"".style_text($grid['ref_table'])."\")'>Print Preview</div>
         <div dojoType='dijit.MenuItem' onclick='grid_print(".$grid['jsId'].",\"".style_text($grid['ref_table'])."\")'>Print</div>
         <div dojoType='dijit.MenuItem' onclick='grid_to_csv(".$grid['jsId'].")'>CSV</div>
         </div>";
          */

         /*
         <div dojoType='dijit.Menu' id='rowMenu'  style='display: none;'>
            <div dojoType='dijit.MenuItem'>Row Menu Item 1</div>
            <div dojoType='dijit.MenuItem'>Row Menu Item 2</div>
         </div>
         <div dojoType='dijit.Menu' id='headerMenu'  style='display: none;'>
            <div dojoType='dijit.MenuItem'>Cell Menu Item 1</div>
            <div dojoType='dijit.MenuItem'>Cell Menu Item 2</div>
         </div>
         <div dojoType='dijit.Menu' id='selectedRegionMenu'  style='display: none;'>
            <div dojoType='dijit.MenuItem'>Action 1 for Selected Region</div>
            <div dojoType='dijit.MenuItem'>Action 2 for Selected Region</div>
         </div>
         */

         //EnhancedGrid table with plugins
         $html .='<table
autoHeight="false"
rowsPerPage="25"
errorMessage="No records to display!"
selectable="true"
plugins=\'{
    pagination: {
        pageSizes: ["20", "50", "100", "200","all"],
        defaultPageSize:20,
        description: true,
        sizeSwitch: true,
        pageStepper: '.$page_stepper.',
        gotoButton: true,
        maxPageStep: 4,
        position: "bottom"
    },
   printer:true,
   exporter: true,
   nestedSorting: true,
}\'';

         /*
         //Sample grid table with several options
         $html .='<table
autoHeight="false"
rowsPerPage="25"
errorMessage="No records to display!"
selectable="true"
plugins=\'{
    pagination: {
        pageSizes: ["20", "50", "100", "200","all"],
        defaultPageSize:20,
        description: true,
        sizeSwitch: true,
        pageStepper: '.$page_stepper.',
        gotoButton: true,
        maxPageStep: 4,
        position: "bottom"
    },
   filter:true,
   printer:true,
   exporter: true,
   nestedSorting: true,
   search:true,
   dnd: true,
   indirectSelection: true,
   menus:{
      headerMenu:"headerMenu", 
      rowMenu:"rowMenu", 
      cellMenu:"cellMenu",
      selectedRegionMenu:"selectedRegionMenu"
   }
}\'
';
          */

         /*Fields to bypass when creating forms*/
         $bypass=array('filter','rowSelector','columns','selector_id','ref_table','ref_key','event_key','sql','onClick');

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
            $label         ='';
            $style         ='';
            $bypass        =array('label');

            

            //Sett cell type and editbility and other options
            if(is_array($array)){

               //If width is not set set it to auto
               if(!isset($array['width'])){
                  $options =' width="auto" ';
               }

               $h_key=$key;

               //Set all options for each column header which are not in bypass array
               foreach($array as $key => $value){
                  if(!in_array($key,$bypass)){
                     $options.=$key."='".$value."' ";
                  }
               }

               //if label is set internally then set it as label
               if(isset($array['label'])){
                  $label   =$array['label'];
               }

               //if label is set internally then set it as label
               if(isset($array['style'])){
                  $style   =$array['style'];
               }
               $style.=';word-wrap:break-word;';

            }else{
               $options =' width="auto" ';
               $h_key   =$array;
               $style   ='word-wrap:break-word;';
            }

            //If the lable is not internally set then check for the lable from FORM else set label as column name
            if($label == ''){
               if(isset($this->form[$h_key]['label'])){
                  $label   =$this->form[$h_key]['label'];
               }else{
                  $label   =style_text($h_key);
               }
            }

            $html.= "<th field='$h_key' $options title='$label' style='$style' >
               ".$label."
            </th>";
         }
         $html.= "</tr>
      </thead>
      <script type='dojo/on' data-dojo-event='click' data-dojo-args='evt'>
        load_grid_item(".$grid['jsId'].",'".$grid['event_key']."',".$grid['selector_id'].",evt);
      </script>
      </table>";
         if(!isset($grid['event_key'])){
            $grid['event_key']=get_pri_keys();
         }

         /*
         if(isset($grid['selector_id'])){
            $html.= "
            <script type='text/javascript'>
            function load_grid_item(e){
               var selectedValue = ".$grid['jsId'].".store.getValue(".$grid['jsId'].".getItem(e.rowIndex),'".$grid['event_key']."');
               load_selected_value(".$grid['selector_id'].",selectedValue);
            }
            </script>";
         }
          */
         set_pviw_property(array('GRIDS',$grid_key),$html);
         if(!file_exists($this->view)){
            add_to_main_right($html); 
         }
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
      url='".gen_url().'?'.$filter_name."&data=json$form'
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
