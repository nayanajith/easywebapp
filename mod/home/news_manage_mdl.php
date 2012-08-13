<?php
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRIMARY_KEY'	=>'rid',
      'UNIQUE_KEY'	=>array(''),
      'MULTY_KEY'	   =>array(''),
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
      "title"=>array(
         "length"	=>"300",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Title",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "role_id"=>array(
          "length"=>"200",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"true",
         "label"=>"Role",

         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"role_id_store",

         "ref_table"=>s_t('role'),
         "ref_key"=>'group_name',
         "order_by"=>'ORDER BY group_name DESC',
         "vid"=>array('group_name')
      ),
      "content"=>array(
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"true",
         "type"      =>"hidden",
         "label"	   =>"Content",
         "label_pos"	=>"top",
         "read_func" =>"htmlspecialchars_decode",
         "write_func"=>"",
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
      "display_until"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.DateTextBox",
         "required"	=>"false",
         "label"	=>"Display until",
         "label_pos"	=>"top",
         "value"=>""
      ),

   ),
//---------------------GRID CONFIGURATION-----------------------
   'GRIDS'=>array(
       'GRID'=>array(
          'columns'      =>array('rid'=>array('hidden'=>'true'),'role_id','title','display_from','display_until','content'=>array('hidden'=>'true')),
          'filter'       =>isset($_SESSION[PAGE]['FILTER'])?$_SESSION[PAGE]['FILTER']:null,
          'selector_id'  =>'toolbar__rid',
          'ref_table'    =>s_t('news'),
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
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"=>"200",
         "dojoType"=>"dijit.form.FilteringSelect",
         "required"=>"false",
         "label"=>"Select News",
         "label_pos"=>"left",

         "onChange"=>'f_f_c_add("ok",write_editor);s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset($_SESSION[PAGE]['FILTER'])?" AND ".$_SESSION[PAGE]['FILTER']:null,
         "ref_table"=>s_t('news'),
         "ref_key"=>'rid',
         "order_by"=>'ORDER BY rid DESC',
         "vid"=>array('title'),
      ),  

   ),
   'WIDGETS'=>array(
   ),
);
?>
