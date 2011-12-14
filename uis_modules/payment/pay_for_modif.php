<?php
$program_inner="";
$arr=exec_query('SELECT program_id,short_name FROM '.$GLOBALS['MOD_S_TABLES']['program'],Q_RET_ARRAY,null,'program_id');

foreach($arr as $program_id =>  $info){
   $program_inner.="<option value='$program_id'>".$info['short_name']."</option>";
}


$fields=array(
	
"pay_for_id"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.NumberTextBox",
		"type"=>"hidden",
		"required"=>"false",
		"label"=>"Pay for id",
		"value"=>""),	
"program_id"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.Select",
		"required"=>"true",
		"inner"=>$program_inner,
		"label"=>"Program short_name",
		"value"=>""),	
"short_name"=>array(
		"length"=>"140",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Short name",
		"value"=>""),	
"description"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Description",
		"value"=>""),	
"pay_for_code"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Payment Code",
		"value"=>""),	

/*
"amount"=>array(
		"length"=>"105",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Amount",
		"value"=>""),	
"tax"=>array(
		"length"=>"105",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Tax",
		"value"=>""),	
"registration"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Registration",
		"value"=>""),	
"coordinator"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Coordinator",
		"value"=>""),	
"deleted"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.NumberTextBox",
		"required"=>"false",
		"label"=>"Deleted",
		"value"=>""),	
*/
"note"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.SimpleTextarea",
		"required"=>"false",
		"label"=>"Note",
		"value"=>"")	
);
?>
