<?php
$status_inner=gen_select_inner($GLOBALS['STATUS'],null,true);
$base_class_inner=gen_select_inner(get_common_list('base_class',true),null,true);
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRI'	=>array('rid'),
      'UNI'	=>array('list_name'),
      'FOR'	=>array(),
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'FORM'=>array(
      "rid"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "type"	=>"hidden",
         "required"	=>"false",
         "label"	=>"Rid",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "base_class"=>array(
         "length"	=>"250",
         "dojoType"	=>"dijit.form.ComboBox",
         "required"	=>"true",
         "label"	=>"Base class",
         "store"=>"base_class_store",
         "searchAttr"=>"label",
         "pageSize"=>"20",
         "tooltip"=>"Select the group from existing group list or enter a new group name.",
         "default"=>false,

         "key_sql"=>"SELECT base_class FROM ".m_p_t('base_data')." WHERE base_class='%s'",

         /*
         "isolate"=>array(
            "add"=>"INSERT INTO ".m_p_t('base_data')."(base_class,base_key)value('EXT_GROUP','%s')",
            "update"=>null,
            "delete"=>null,
         ),
          */

         //"hasDownArrow"=> "false",
         "autoComplete"=>"false",

         "ref_table"=>m_p_t('base_data'),
         "ref_key"=>'base_class',
         "order_by"=>'ORDER BY base_class DESC',
         "vid"=>array('base_class'),

      ),
      "base_key"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Base key",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "base_value"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"true",
         "style"  =>"height:50px",
         "label"	=>"Base value",
         "label_pos"	=>"top",
         "value"=>""
      ),
      /*
      "status"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.Select",
         "required"	=>"true",
         "label"	=>"Status",
         "inner"  =>$status_inner,
         "label_pos"	=>"top",
         "value"=>""
      ),
       */
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'GRIDS'=>array(
       'GRID'=>array(
          'columns'      =>array('rid'=>array('hidden'=>'true'),'base_class'=>array('width'=>'60px'),'base_key'=>array('width'=>'100px'),'base_value'),
          'filter'       =>isset($_SESSION[PAGE]['FILTER'])?$_SESSION[PAGE]['FILTER']:null,
          'selector_id'  =>'toolbar__rid',
          'ref_table'    =>s_t('base_data'),
          'event_key'    =>'rid',
          'dojoType'     =>'dojox.grid.EnhancedGrid',
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
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"=>"170",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"false",
         "label"=>"Base Key",
         "label_pos"=>"left",

         "onChange"=>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset($_SESSION[PAGE]['FILTER'])?" AND ".$_SESSION[PAGE]['FILTER']:null,
         "ref_table"=>s_t('base_data'),
         "ref_key"=>'rid',
         "order_by"=>'ORDER BY rid DESC',
         "vid"=>array('base_key'),
      ),  
   ),
   'WIDGETS'=>array(
   ),
);
?>
