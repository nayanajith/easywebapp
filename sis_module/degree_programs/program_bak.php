<?php
include A_CLASSES."/data_entry_class.php";
$formgen = new Formgenerator('program','short_name');


if(isset($_REQUEST['add'])&&$_REQUEST['add']==true){
   if($formgen->add_record()){
      include A_CORE."/database_schema.php";   
      create_program_tables($_REQUEST['table_prefix']);
   }
   return;
}

if(isset($_REQUEST['modify'])&&$_REQUEST['modify']==true){
   $formgen->modify_record();
   return;
}



if(isset($_REQUEST['delete'])&&$_REQUEST['delete']==true){
   if($formgen->delete_record()){
      include A_CORE."/database_schema.php";   
      drop_program_tables($_REQUEST['table_prefix']);
   }
   return;
}

if($data==true){
   if(isset($_REQUEST['q'])){
      $formgen->xhr_form_filler_data($_REQUEST['q']);
   }else{
      $formgen->xhr_filtering_select_data('fill_form');
   }
}else{
?>
<script type='text/javascript'>
dojo.require("dojo.parser");

//form elements
dojo.require("dijit.form.FilteringSelect");
dojo.require("dijit.form.RadioButton");
dojo.require("dijit.form.CheckBox");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.Textarea");
dojo.require("dijit.form.DateTextBox");
dojo.require("dijit.form.NumberSpinner");
dojo.require("dijit.form.HorizontalSlider");
dojo.require("dijit.form.ValidationTextBox");
dojo.require("dijit.form.Form");
dojo.require("dijit.form.Button");

//data grid
dojo.require("dojox.grid.DataGrid");

//data stores
dojo.require("dojo.data.ItemFileWriteStore");
dojo.require("dojo.data.ItemFileReadStore");
</script>
<?php
   echo $formgen->gen_xhr_form_filler('fill_form');
   echo $formgen->gen_xhr_filtering_select('fill_form');
   echo $formgen->gen_form();
}

?>

