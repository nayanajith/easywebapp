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

//Get the content of the help effective file 
$mod_page=get_param('page_id');
$mod_page=explode('/',$mod_page);
$doc_file=get_doc_file($mod_page[0],$mod_page[1]);

$content="";
if(file_exists($doc_file) && filesize($doc_file) > 0){
   $fh=fopen($doc_file,'r');
   $content=fread($fh,filesize($doc_file));
   fclose($fh);
}

//--------------------------MODEL-------------------------------
$LOAD_DEFAULT_TOOLBAR=false;
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
   'FORM'=>array(
      "doc"=>array(
         "length"	   =>"500",
         "style"	   =>"height:400px",
         "read_func" =>"htmlspecialchars_decode",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"false",
         "label"	   =>"Documentation",
         "inner"     =>$content
      ),
   ),
//---------------------GRID CONFIGURATION-----------------------
   'GRIDS'=>array(
    ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "page_id"=>array(
         "length"	   =>"250",
         "dojoType"  =>"dijit.form.FilteringSelect",
         "required"	=>"true",
         "onChange"  =>'set_param(this.id,this.value)',
         "onChange"  =>'s_p_c_add("ok",reload_main,this.value);set_param(this.id,this.value)',
         "inner"     =>gen_select_inner($page_array,null,true),
         "pageSize"  =>"10",
         "label"	   =>"Page",
         "value"     =>""
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
