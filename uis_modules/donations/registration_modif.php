<?php
$title_inner="";
foreach(array("REV","PROF","DR","MR","MS","MRS") as $value){
	$title_inner.="<option value='$value'>$value</option>";
}

$captcha="
<img id='captcha_image' src='?module=donations&page=captcha&data=true' style='border:1px solid #C9D7F1' onClick='reload_captcha()' title='reload' ><br>
<p style='color:silver;'>Click on the image to load a new image</p>
Enter the characters you se in above image:<br>
<input type='text' dojoType='dijit.form.ValidationTextBox' name='captcha' required='true'>
<script type='text/javascript' >
	function reload_captcha(){
		document.getElementById('captcha_image').src='?module=donations&page=captcha&data=true&a='+Math.random();
	}
</script>
";

$fields=array(
"rec_id"=>array(
		"length"=>"77",
		"dojoType"=>"dijit.form.NumberTextBox",
		"type"=>"hidden",
		"required"=>"false",
		"label"=>"Reg id",
		"value"=>""),	
"title"=>array(
		"length"=>"50",
		"dojoType"=>"dijit.form.ComboBox",
		"section"=>"Please tell us a breaf about you",
		"required"=>"true",
		"label"=>"Title",
		"inner"=>$title_inner,
		"value"=>"MR"),	
"first_name"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"First name",
		"value"=>""),	
"middle_names"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Middle names",
		"value"=>""),	
"last_name"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Last name",
		"value"=>""),	
/*
"NIC"=>array(
		"length"=>"84",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Nic",
		"value"=>""),	
"passport"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Passport",
		"value"=>""),	
 */
"address1"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"section"=>"Let us know how to contact you",
		"label"=>"Address line 1",
		"value"=>""),	
"address2"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Address line 2",
		"value"=>""),	
"email"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Email",
		"value"=>""),	
"affiliation"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"section"=>"Further information about you",
		"label"=>"Affiliation",
		"value"=>""),	

"telephone"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Telephone",
		"value"=>""),	
/*
"fax"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Fax",
		"value"=>""),	
"city"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"City",
		"value"=>""),	
 */
"state"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"City/State",
		"value"=>""),	
"zip"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Zip",
		"value"=>""),	
"country"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Country",
		"value"=>""),	
"registration_type"=>array(
		"dojoType"=>"dijit.form.CheckBox",
		"jsId"=>"registration_type",
		"required"=>"false",
		"section"=>"If you like to register with us please check register and enter enter a password",
		//If the registration checked must fill the password field
		"onclick"=>"if(this.checked){password.required=true}else{password.required=false}",
		"label_pos"=>"left",
		"label"=>"Register&nbsp;",
		"value"=>"1"),	

"password"=>array(
		"length"=>"200",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"type"=>"password",
		"jsId"=>"password",
		"label"=>"Password",
		"value"=>""),	
/*
"captcha"=>array(
		"custom"=>"true",
		"disabled"=>"true",
		"section"=>"Read the text in the image below and enter in the given text box",
		"inner"=>$captcha,
		"label"=>"Captcha",
		),	

"verification_code"=>array(
		"hidden"=>"true",
		"custom"=>"true",
		"label"=>"",
		),
 */


/*
"functions"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.SimpleTextarea",
		"required"=>"false",
		"label"=>"Functions",
		"value"=>""),	
"last_login"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Last login",
		"value"=>""),	
"status"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Status",
		"value"=>""),	
"updated_time"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Updated time",
		"value"=>"")	
 */
);

if(isset($_SESSION['username'])){
	unset($fields['captcha']);
}
?>
