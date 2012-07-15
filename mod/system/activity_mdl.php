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
      "info"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"Info",
         "label_pos"	=>"top",
         "value"=>""
      ),
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
   'GRIDS'=>array(
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
          'ref_table'    =>s_t('log'),
          'event_key'    =>'rid',
          'dojoType'     =>'dojox.grid.EnhancedGrid',
          'query'        =>'{ "rid": "*" }',
          'clientsort'   =>'true',
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

         "onChange"=>'s_p_c_add("ok",fill_form,this.value);set_param(this.id,this.value)',
         "searchAttr"=>"label",
         "pagesize"=>"10",
         "store"=>"rid_store",

         "filter"=>isset($_SESSION[PAGE]['FILTER'])?" AND ".$_SESSION[PAGE]['FILTER']:null,
         "ref_table"=>s_t('log'),
         "ref_key"=>'rid',
         "order_by"=>'ORDER BY rid DESC',
         "vid"=>array('rid'),
      ),  
      "add"=>"false",  
      "save"=>"false",  
      "remove"=>"false",
   ),
   'WIDGETS'=>array(
   ),
);
?>
