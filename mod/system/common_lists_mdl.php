<?php
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRIMARY_KEY'	=>'rid',
      'UNIQUE_KEY'	=>array('list_name'),
      'MULTY_KEY'	=>array(''),
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'MAIN'=>array(
      "rid"=>array(
         "length"	=>"70",
         "dojoType"	=>"dijit.form.NumberTextBox",
         "type"	=>"hidden",
         "required"	=>"false",
         "label"	=>"Rid",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "list_name"=>array(
         "length"	=>"150",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"List name",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "list_title"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"List title",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "json"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"true",
         "label"	=>"Json",
         "label_pos"	=>"top",
         "value"=>""
      ),
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "rid"=>array(
         "length"	   =>"70",
         "dojoType"	=>"dijit.form.FilteringSelect",
         "required"	=>"false",
         "label"	   =>"List name",
         "label_pos"	=>"left",

         "onChange"=>'set_param(this.name,this.value);fill_form(this.value,"main")',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>null,
         "ref_table"=>$GLOBALS['S_TABLES']['common_lists'],
         "order_by"=>'ORDER BY list_name DESC',
         "vid"=>array('list_name'),
      ),

      "add"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Add",
         "label_pos"=>"left",
         "iconClass"=>get_icon_class('NewPage'),
         "showLabbel"=>'false',
         "onClick"=>'submit_form("add")',
      ),  
      "modify"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Modify",
         "label_pos"=>"left",
         "iconClass"=>get_icon_class('Save'),
         "showLabbel"=>'false',
         "onClick"=>'submit_form("modify")',
      ),  
      "remove"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Delete",
         "label_pos"=>"left",
         "iconClass"=>get_icon_class('Delete'),
         "showLabbel"=>'false',
         "onClick"=>'submit_form("delete")',
      ),
   ),
   'WIDGETS'=>array(
   ),
);
?>
