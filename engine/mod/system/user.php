<?php
opendb();
include A_CLASSES."/data_entry_class.php";
$formgen = new Formgenerator('user','staff_id');

if(isset($_REQUEST['add_modify'])&&$_REQUEST['add_modify']==true){
   return $formgen->add_modify_record();
}

if(isset($_REQUEST['delete'])&&$_REQUEST['delete']==true){
   return $formgen->delete_record();
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

