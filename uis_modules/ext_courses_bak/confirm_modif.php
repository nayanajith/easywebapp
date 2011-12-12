<?php
$payment_inner="
<option value='ONLINE'>ONLINE</option>
<option value='OFFLINE'>OFFLINE</option>
";


if(isset($_REQUEST['sid'])){
$newsid = $_REQUEST['sid'];
}else{
$newsid = "";
}


$fields=array(
	
"reg_id"=>array(
		"length"=>"63",
		"dojoType"=>"dijit.form.NumberTextBox",
		"required"=>"false",
		"label"=>"Reg id",
		"value"=>""),	
"session_id"=>array(
		"length"=>"63",
		"dojoType"=>"dijit.form.NumberTextBox",
		"required"=>"false",
		"label"=>"Session id",
		"value"=>$newsid),	
"student_id"=>array(
		"length"=>"63",
		"dojoType"=>"dijit.form.NumberTextBox",
		"required"=>"false",
		"label"=>"Student id",
		"value"=>$_SESSION['user_id']),	
"status"=>array(
		"length"=>"140",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Status",
		"value"=>"RESERVED"),	
"payment_method"=>array(
		"length"=>"140",
		"dojoType"=>"dijit.form.ComboBox",
		"inner"=>$payment_inner,
		"label"=>"Payment method",
		"value"=>""),		
"payment_id"=>array(
		"length"=>"140",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Payment id",
		"value"=>""),	
"certificate_id"=>array(
		"length"=>"140",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"type"=>"hidden",
		"required"=>"false",
		"label"=>"Certificate id",
		"value"=>"")	
);

?>
