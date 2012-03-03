<?php
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
      'PRIMARY_KEY'=>'rid',
      'UNIQUE_KEY'=>array('group_name')	
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'MAIN'=>array(
      "rid"=>array(
      		"length"=>"70",
				"dojoType"=>"dijit.form.NumberTextBox",
      		"type"=>"hidden",
				"required"=>"false",
				"label"=>"Rid",
				"label_pos"=>"top",
      		"value"=>""),	
      "group_name"=>array(
      		"length"=>"350",
      		"dojoType"=>"dijit.form.ValidationTextBox",
      		"required"=>"true",
      		"label"=>"Group name",
      		"label_pos"=>"top",
      		"value"=>""),	
      "file_prefix"=>array(
      		"length"=>"70",
      		"dojoType"=>"dijit.form.ValidationTextBox",
      		"required"=>"true",
      		"label"=>"File prefix",
				"label_pos"=>"top",
				"value"=>""),	
      "description"=>array(
      		"length"=>"350",
      		"dojoType"=>"dijit.form.ValidationTextBox",
      		"required"=>"true",
      		"label"=>"Description",
      		"label_pos"=>"top",
      		"value"=>""),	
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
      "rid"=>array(
			"length"=>"100",
         "dojoType"=>"dijit.form.FilteringSelect",
			"required"=>"true",
			"label"=>"Group",
			"label_pos"=>"left",

         "onChange"=>'set_param(this.name,this.value);fill_form(this.value,"main")',
         "searchAttr"=>"label",
         "pageSize"=>"10",
         "store"=>"rid_store",

         "filter"=>null,
         "ref_table"=>$GLOBALS['S_TABLES']['groups'],
         "order_by"=>'ORDER BY group_name DESC',
         "vid"=>array('group_name'),
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
);?>
