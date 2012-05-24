<?php
$GLOBALS['MODEL']=array(
//-----------------KEY FIELDS OF THE MODEL----------------------
   'KEYS'=>array(
   ),
//--------------FIELDS TO BE INCLUDED IN FORM-------------------
//---------------THIS ALSO REFLECT THE TABLE--------------------
   'FORM'=>array(
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'GRIDS'=>array(
   ),
   'TOOLBAR'=>array(
      "create"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Create/Re-create",
         "iconClass"=>get_icon_class('Process'),
         "showLabbel"=>'true',
         "onClick"=>'xhr_generic("create_tables","create","json")',
      ),
      "migrate"=>array(
         "dojoType"=>"dijit.form.Button",
         "label"=>"Migrate",
         "iconClass"=>get_icon_class('Process'),
         "showLabbel"=>'true',
         "onClick"=>'xhr_generic("db_migrate","migrate","json")',
      ),
   ),
   'WIDGETS'=>array(
   ),
);

//add_to_model('true','TOOLBAR','migrate','disabled');
include "db_admin_mdl.inc.php";
?>
