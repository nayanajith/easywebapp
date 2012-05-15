<?php
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRIMARY_KEY'	=>'rid',
      'UNIQUE_KEY'	=>array(''),
      'MULTY_KEY'	=>array(''),
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'MAIN_LEFT'=>array(
      "rid"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "type"	=>"hidden",
         "required"	=>"false",
         "label"	=>"Rid",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "title"=>array(
         "length"	=>"300",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Title",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "content"=>array(
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"true",
         "type"   =>"hidden",
         "label"	   =>"Content",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "display_from"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.DateTextBox",
         "required"	=>"true",
         "label"	=>"Display from",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "display_to"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.DateTextBox",
         "required"	=>"false",
         "label"	=>"Display to",
         "label_pos"	=>"top",
         "value"=>""
      ),

   ),
//---------------------GRID CONFIGURATION-----------------------
   'MAIN_RIGHT'=>array(
       'GRID'=>array(
          'columns'      =>array('rid'=>array('hidden'=>'true'),'title','display_from','display_to'),
          'filter'       =>isset($_SESSION[PAGE]['FILTER'])?$_SESSION[PAGE]['FILTER']:null,
          'selector_id'  =>'toolbar__rid',
          'ref_table'    =>s_t('news'),
          'event_key'    =>'rid',
          'dojoType'     =>'dojox.grid.DataGrid',
          'jsId'         =>'main_grid',
          'store'        =>'main_grid_store' ,
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
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"=>"100",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"false",
         "label"=>"Label",
         "label_pos"=>"left",

         "onChange"=>'set_param(this.name,this.value);fill_form(this.value,"main")',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset($_SESSION[PAGE]['FILTER'])?" AND ".$_SESSION[PAGE]['FILTER']:null,
         "ref_table"=>s_t('news'),
         "ref_key"=>'rid',
         "order_by"=>'ORDER BY rid DESC',
         "vid"=>array('title'),
      ),  

      "clear"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Clear form",
         "iconClass"=>get_icon_class('Clear'),
         "showLabbel"=>'true',
         "onClick"=>'clear_form("main")',
         //"onClick"=>'load_selected_value(toolbar__rid,"NULL")',
      ),

      "add"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Add",
         "iconClass"=>get_icon_class('NewPage'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("add");reload_grid(main_grid)',
      ),  
      "save"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Save",
         "iconClass"=>get_icon_class('Save'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("modify");reload_grid(main_grid)',
      ),  
      "remove"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Delete",
         "iconClass"=>get_icon_class('Delete'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("delete");reload_grid(main_grid)',
      ),
     "add_filter"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Add fIlter",
         "iconClass"=>get_icon_class('Filter'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("add_filter");reload_grid(main_grid)',
      ),  
      "del_filter"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Delete filter",
         "iconClass"=>get_icon_class('Cancel'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("del_filter");reload_grid(main_grid)',
      ),
      "reload_grid"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Reload grid",
         "iconClass"=>get_icon_class('Undo'),
         "showLabbel"=>'true',
         "onClick"=>'reload_grid(main_grid)',
      ),
      "csv"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"CSV",
         "iconClass"=>get_icon_class('Table'),
         "showLabbel"=>'true',
         "onClick"=>'submit_form("csv");',
       ),
   ),
   'WIDGETS'=>array(
   ),
);
?>
