<?php
$page_array=array();
foreach ($GLOBALS['MODULES'] as $mod_key => $mod) {
   $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
   if(file_exists($module_menu_file)){
      include($module_menu_file);
      foreach($menu_array as $page_key => $page){
         $page_array[$page_key]=$mod_key."/".$page_key;
      }
   }
}

//--------------------------MODEL-------------------------------
$LOAD_DEFAULT_TOOLBAR=false;
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRI'	=>array('rid'),
      'UNI'	=>array(),
      'FOR'	=>array('role_id'),
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
   'FORM'=>array(
      "rid"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "type"	=>"hidden",
         "required"	=>"false",
         "label"	=>"Rid",
         "value"=>""
      ),
/*
      "role_id"=>array(
         "dojoType"     =>"dijit.form.FilteringSelect",
         "required"     =>"false",
         "label"        =>"Role",
         "searchAttr"   =>"label",
         "pageSize"     =>"10",
         "store"        =>"role_id_store",
         "onChange"     =>'set_param(this.id,this.value)',
         "filter"       =>get_filter(),
         "ref_table"    =>s_t('role'),
         "ref_key"      =>'group_name',
         "order_by"     =>'ORDER BY timestamp DESC',
         "vid"          =>array('group_name'),
      ),
      "program_id"=>array(
         "dojoType"     =>"dijit.form.FilteringSelect",
         "required"     =>"false",
         "label"        =>"Program",
         "searchAttr"   =>"label",
         "pageSize"     =>"10",
         "onChange"     =>'set_param(this.id,this.value)',
         "store"        =>"program_id_store",
         "filter"       =>get_filter(),
         "ref_table"    =>s_t('program'),
         "ref_key"      =>'short_name',
         "vid"          =>array('short_name'),
      ),
*/
      "module_id"=>array(
         "length"	   =>"200",
         "dojoType"     =>"dijit.form.ComboBox",
         "required"	=>"true",
         "required"	=>"true",
         "onChange"  =>'set_param(this.id,this.value)',
         "inner"     =>gen_select_inner(array_keys($GLOBALS['MODULES']),null,true),
         "pageSize"     =>"10",
         "label"	   =>"Module",
         "value"     =>""
      ),
      "page_id"=>array(
         "length"	   =>"250",
         "dojoType"  =>"dijit.form.Select",
         "required"	=>"true",
         "onChange"  =>'set_param(this.id,this.value)',
         "inner"     =>gen_select_inner($page_array,null,true),
         "pageSize"  =>"10",
         "label"	   =>"Page",
         "value"     =>""
      ),
      "doc"=>array(
         "length"	   =>"500",
         "style"	   =>"height:400px",
         "read_func" =>"htmlspecialchars_decode",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"false",
         "label"	   =>"Documentation",
         "value"     =>""
      ),
   ),
//---------------------GRID CONFIGURATION-----------------------
   'GRIDS'=>array(
      /*
      'GRID'=>array(
         'columns'      =>array('rid'=>array('hidden'=>'true'),'role_id'=>array('width'=>'50'),'program_id','module_id','page_id'),
         'filter'       =>get_filter(),
         'selector_id'  =>'toolbar__rid',
         'ref_table'    =>s_t(''),
         'event_key'    =>'rid',
         'order_by'     =>'ORDER BY timestamp DESC',
         'dojoType'     =>'dojox.grid.EnhancedGrid',
         'query'        =>'{ "rid": "*" }',
         'clientSort'   =>'true',
         'style'        =>'width:100%;height:400px',
         'onClick'      =>'load_grid_item',
         'columnReordering'=>'true',
         'headerMenu'   =>'gridMenu',
       ),
       */
    ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"       =>"100",
         "dojoType"     =>"dijit.form.FilteringSelect",
         "required"     =>"false",
         "label"        =>"Module/Page",
         "onChange"     =>'s_p_c_add("ok",fill_form,this.value);s_p_c_add("ok",reload_main_right,this.value);set_param(this.id,this.value)',
         "searchAttr"   =>"label",
         "pageSize"     =>"10",
         "store"        =>"rid_store",
         "ref_table"    =>s_t('user_doc'),
         "ref_key"      =>'rid',
         "vid"          =>array('module_id','page_id'),
      ),  
   "clear"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Clear form",
      "iconClass"=>get_icon_class('Clear'),
      "showLabbel"=>'true',
      "onClick"=>'clear_form("main")',
   ),

   "add"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Add",
      "iconClass"=>get_icon_class('NewPage'),
      "showLabbel"=>'true',
      "onClick"=>'submit_form("add")',
   ),  
   "save"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Save",
      "iconClass"=>get_icon_class('Save'),
      "showLabbel"=>'true',
      "onClick"=>'s_f_c_add("ok",reload_main_right,null);submit_form("modify")',
   ),  
   "remove"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Delete",
      "iconClass"=>get_icon_class('Delete'),
      "showLabbel"=>'true',
      "onClick"=>'submit_form("delete")',
   ),

      "reload_bttom"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Reload preview",
         "iconClass"=>get_icon_class('Undo'),
         "showLabbel"=>'true',
         "onClick"=>'reload_main_right()',
      ),
   ),
//--------------------CALLBACK FUNCTIONS------------------------
   'CALLBACKS'=>array(
      "add_record"=>array(
         "OK"     =>array(
            "func"   =>null,
            "vars"   =>array(),
            "status" =>false,
            "return" =>null
         ),  
         "ERROR"  =>array(),
      ),  
      "update_record"=>array(
         "OK"     =>array(),
         "ERROR"  =>array(),
      ),  
      "delete_record"=>array(
         "OK"     =>array(),  
         "ERROR"  =>array(),
      ),  
   ),  
);
?>
