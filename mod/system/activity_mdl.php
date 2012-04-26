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
      "proto"=>array(
         "length"	=>"35",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Proto",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "timestamp"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Timestamp",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "user_id"=>array(
         "length"	=>"77",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "required"	=>"false",
         "label"	=>"User id",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "ip"=>array(
         "length"	=>"105",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Ip",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "module_id"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Module",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "page_id"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Page",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "cmid"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "required"	=>"false",
         "label"	=>"Cmid",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "action_"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Action",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "url"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Url",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "host"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Host",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "request"=>array(
         "length"	=>"700",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"false",
         "label"	=>"Request",
         "label_pos"	=>"top",
         "value"=>""
      ),
      /*
      "info"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Info",
         "label_pos"	=>"top",
         "value"=>""
      ),
       */
      "agent"=>array(
         "length"	=>"700",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Agent",
         "label_pos"	=>"top",
         "value"=>""
      ),
      /*
      "deleted"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.CheckBox",
         "required"	=>"false",
         "label"	=>"Deleted",
         "label_pos"	=>"right",
         "value"=>""
      ),
      "note"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Note",
         "label_pos"	=>"top",
         "value"=>""
      )
       */
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'MAIN_RIGHT'=>array(
       'GRID'=>array(
          'columns'      =>array(
             'rid'      =>array('hidden'=>'true'),
             'timestamp',
             'user_id',  
             'ip',       
             'module_id',   
             'page_id',     
             'cmid'     =>array('hidden'=>'true'),
             'action_',
             'url'      =>array('hidden'=>'true'),
             'host'     =>array('hidden'=>'true'),
             'request'  =>array('hidden'=>'true'),
             'agent'    =>array('hidden'=>'true')
          ),
          'filter'       =>isset($_SESSION[PAGE]['FILTER'])?$_SESSION[PAGE]['FILTER']:null,
          'selector_id'  =>'toolbar__rid',
          'ref_table'    =>$GLOBALS['S_TABLES']['log'],
          'event_key'    =>'rid',
          'dojoType'     =>'dojox.grid.DataGrid',
          'jsId'         =>'main_grid',
          'store'        =>'main_grid_store' ,
          'query'        =>'{ "rid": "*" }',
          'rowsperPage'  =>'40',
          'clientsort'   =>'true',
          'style'        =>'width:100%;height:400px',
          'onClick'      =>'load_grid_item',
          'rowselector'  =>'20px',
          'columnreordering'=>'true',
          'headerMenu'   =>'gridMenu',
       ),
    ),
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"=>"100",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"false",
         "label"=>"Label",
         "label_pos"=>"left",

         "onChange"=>'set_param(this.name,this.value);fill_form(this.value,"main")',
         "searchAttr"=>"label",
         "pagesize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset($_SESSION[PAGE]['FILTER'])?" AND ".$_SESSION[PAGE]['FILTER']:null,
         "ref_table"=>$GLOBALS['S_TABLES']['log'],
         "ref_key"=>'rid',
         "order_by"=>'ORDER BY rid DESC',
         "vid"=>array('rid'),
      ),  

      "clear"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Clear form",
         "iconclass"=>get_icon_class('Clear'),
         "showlabbel"=>'true',
         "onClick"=>'clear_form("main",toolbar__rid)',
         //"onClick"=>'load_selected_value(toolbar__rid,"NULL")',
      ),
/*
      "add"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Add",
         "iconclass"=>get_icon_class('NewPage'),
         "showlabbel"=>'true',
         "onClick"=>'submit_form("add");reload_grid(main_grid)',
      ),  
      "modify"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Modify",
         "iconclass"=>get_icon_class('Save'),
         "showlabbel"=>'true',
         "onClick"=>'submit_form("modify");reload_grid(main_grid)',
      ),  
      "remove"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Delete",
         "iconclass"=>get_icon_class('Delete'),
         "showlabbel"=>'true',
         "onClick"=>'submit_form("delete");reload_grid(main_grid)',
      ),
 */
     "add_filter"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Add fIlter",
         "iconclass"=>get_icon_class('Filter'),
         "showlabbel"=>'true',
         "onClick"=>'submit_form("add_filter");reload_grid(main_grid)',
      ),  
      "del_filter"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Delete filter",
         "iconclass"=>get_icon_class('Cancel'),
         "showlabbel"=>'true',
         "onClick"=>'submit_form("del_filter");reload_grid(main_grid)',
      ),
      "reload_grid"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Reload grid",
         "iconclass"=>get_icon_class('Undo'),
         "showlabbel"=>'true',
         "onClick"=>'reload_grid(main_grid)',
      ),
      "csv"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"CSV",
         "iconclass"=>get_icon_class('Database'),
         "showlabbel"=>'true',
         "onClick"=>'submit_form("csv")',
      ),
   ),
   'WIDGETS'=>array(
   ),
);
?>
