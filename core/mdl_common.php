<?php
//-----------------KEY FIELDS OF THE MODEL----------------------
$KEYS=array(
);
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
$FORM=array(
);

$GRIDS=array(
);

//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
$TOOLBAR=array(
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
      "onClick"=>'s_f_c_add("ok",reload_grid,grid__GRID);submit_form("add")',
   ),  
   "save"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Save",
      "iconClass"=>get_icon_class('Save'),
      "showLabbel"=>'true',
      "onClick"=>'s_f_c_add("ok",reload_grid,grid__GRID);submit_form("modify")',
   ),  
   "remove"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Delete",
      "iconClass"=>get_icon_class('Delete'),
      "showLabbel"=>'true',
      "onClick"=>'s_f_c_add("ok",reload_grid,grid__GRID);submit_form("delete")',
   ),
  "add_filter"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Add fIlter",
      "iconClass"=>get_icon_class('Filter'),
      "showLabbel"=>'true',
      "onClick"=>'s_f_c_add("ok",reload_grid,grid__GRID);submit_form("add_filter")',
   ),  
   "del_filter"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Delete filter",
      "iconClass"=>get_icon_class('Cancel'),
      "showLabbel"=>'true',
      "onClick"=>'s_f_c_add("ok",reload_grid,grid__GRID);submit_form("del_filter")',
   ),
   "reload_grid"=>array(
      "dojoType"=>"dijit.form.Button",
      "label"=>"Reload grid",
      "iconClass"=>get_icon_class('Undo'),
      "showLabbel"=>'true',
      "onClick"=>'reload_grid(grid__GRID)',
   ),
   "csv"=>array(
      "dojoType"=>"dijit.form.DropDownButton",
      "label"=>"CSV",
      "iconClass"=>get_icon_class('Table'),
      "showLabbel"=>'true',
   ),
);
$CALLBACKS=array(
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
);

//If the toolbar items not set for the given fields default will be added to the model.toolbar
foreach($TOOLBAR as $key => $arr){
   if(isset($GLOBALS['MODEL']['TOOLBAR'])){
      if(!isset($GLOBALS['MODEL']['TOOLBAR'][$key])){
         $GLOBALS['MODEL']['TOOLBAR'][$key]=$arr;
      }
   }else{
      $GLOBALS['MODEL']['TOOLBAR']=array();
   }
}

?>
