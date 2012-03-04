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
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"List name",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "list_title"=>array(
         "length"	=>"350",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"false",
         "label"	=>"List title",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "json"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.SimpleTextarea",
         "required"	=>"true",
         "section"=>"ll",
         "label"	=>"Json",
         "label_pos"	=>"top",
         "value"=>""
      ),
      "timestamp"=>array(
         "length"	=>"100",
         "dojoType"	=>"dijit.form.ValidationTextBox",
         "required"	=>"true",
         "label"	=>"Timestamp",
         "label_pos"	=>"top",
         "value"=>""
      )
   ),
//--------------FIELDS TO BE INCLUDED IN TOOLBAR----------------
   'TOOLBAR'=>array(
   ),
   'WIDGETS'=>array(
   ),
);
?>
