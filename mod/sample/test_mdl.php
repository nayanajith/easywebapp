<?php
//--------------------------MODEL-------------------------------
$LOAD_DEFAULT_TOOLBAR=true;
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRI'	=>array('rid'),
      'UNI'	=>array(),
      'FOR'	=>array(),
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
      "name"=>array(
         "length"	=>"140",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Name",
         "value"=>""
      ),
      "note"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Note",
         "value"=>""
      )
   ),
//---------------------GRID CONFIGURATION-----------------------
   'GRIDS'=>array(
      'GRID'=>array(
         'columns'      =>array('rid'=>array('hidden'=>'true'),'name'=>array('width'=>'50'),'note'=>array('width'=>'50')),
         'filter'       =>get_filter(),
         'selector_id'  =>'toolbar__rid',
         'ref_table'    =>m_p_t('test'),
         'event_key'    =>'rid',
         'order_by'     =>'ORDER BY name DESC',
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
         "length"       =>"100",
         "dojoType"     =>"dijit.form.FilteringSelect",
         "required"     =>"false",
         "label"        =>"Label",
         "onChange"     =>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"   =>"label",
         "pageSize"     =>"10",
         "store"        =>"rid_store",
         "filter"       =>get_filter(),
         "ref_table"    =>m_p_t('test'),
         "ref_key"      =>'rid',
         "order_by"     =>'ORDER BY name DESC',
         "vid"          =>array('name'),
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
