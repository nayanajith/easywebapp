<?php
//Generate the page array
$page_array=array();
foreach ($GLOBALS['MODULES'] as $mod_key => $mod) {
   $module_menu_file   =A_MODULES."/".$mod_key."/menu.php";
   if(file_exists($module_menu_file)){
      include($module_menu_file);
      foreach($menu_array as $page_key => $page){
         //Get the page name
         if(is_array($page)){
            $page=$page['label'];
         }
         
         //Get the module name
         $module=$GLOBALS['MODULES'][$mod_key];
         if(is_array($module)){
            $module=$module['MODULE'];
         }
         $page_array[$mod_key."/".$page_key]=$module."/".$page;
      }
   }
}

js("
function assign_module_page(value){
   var split = value.split('/');
   dijit.byId('module').set('value',split[0]);
   dijit.byId('page').set('value',split[1]);
}

function is_user_check(){
   var displayValue=dijit.byId('group_user_id').get('displayedValue');
   var search=/\[U\]/;
   if(search.test(displayValue)){
      dijit.byId('is_user').set('checked',true);
   }else{
      dijit.byId('is_user').set('checked',false);
   }
}
");

         
$custom_inner="<select  pageSize='20' id='module_page' dojoType='dijit.form.FilteringSelect' onChange='assign_module_page(this.value)' style='width:250px'>".gen_select_inner($page_array,null,false)."</select>";

$user_arr=exec_query("SELECT username,CONCAT(username,' [U]') label FROM ".s_t('users'),Q_RET_ARRAY,null,'username');
$group_arr=exec_query("SELECT group_name,CONCAT(group_name,' [G]') label FROM ".s_t('role'),Q_RET_ARRAY,null,'group_name');
$arr=array_merge($user_arr,$group_arr);
$group_user_id_inner=gen_select_inner($arr,'label',true);



//--------------------------MODEL-------------------------------
$LOAD_DEFAULT_TOOLBAR=true;
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRI'	=>array('rid'),
      'UNI'	=>array(
         'group_user_id'=>array('group_user_id','module','page'),
      ),
      'FOR'	=>array('module','page'),
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
      "group_user_id"=>array(
         "length"	      =>"200",
         "label"	      =>"Group/User id",
         "dojoType"     =>"dijit.form.Select",
         "onChange"     =>"is_user_check()",
         "inner"        =>$group_user_id_inner,
      ),
      "module_page"=>array(
         "label"	   =>"Module/Page",
         "inner"     =>$custom_inner,
         "custom"    =>"true"
      ),
      "module"=>array(
         "length"	=>"120",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	   =>"Module",
         "readonly"  =>"readonly",
         "value"=>""
      ),
      "page"=>array(
         "length"	=>"120",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Page",
         "readonly"  =>"readonly",
         "value"=>""
      ),

      "is_user"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.CheckBox",
         "required"	=>"false",
         "label"	=>"Is user",
         "value"=>""
      ),
      "access_right"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.Select",
         "inner"     =>gen_select_inner(array('DENIED','READ','WRITE'),null,true),
         "required"	=>"true",
         "label"	=>"Access right",
         "value"=>""
      )
   ),
//---------------------GRID CONFIGURATION-----------------------
   'GRIDS'=>array(
      'GRID'=>array(
         'columns'      =>array('rid'=>array('hidden'=>'true'),'group_user_id'=>array('width'=>'100'),'is_user'=>array('width'=>'30'),'module','page','access_right'=>array('width'=>'50')),
         'filter'       =>get_filter(),
         'selector_id'  =>'toolbar__rid',
         'ref_table'    =>s_t('permission'),
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
    ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"       =>"250",
         "dojoType"     =>"dijit.form.FilteringSelect",
         "required"     =>"false",
         "label"        =>"User/Module/Page",
         "onChange"     =>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"   =>"label",
         "pageSize"     =>"10",
         "store"        =>"rid_store",
         "filter"       =>get_filter(),
         "ref_table"    =>s_t('permission'),
         "ref_key"      =>'rid',
         "order_by"     =>'ORDER BY timestamp DESC',
         "vid"          =>array('group_user_id','module','page'),
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
);
?>
