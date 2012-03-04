
<h3>Rest password</h3>
<br>
<br>
<br>
<br>
<br>
<center>
<?php
if($GLOBALS['AUTH_MOD']=='LDAP'){
   echo "Your password should be reset through zimbra (LDAP) server";
   return;
}
$arr		=array();
$msg		="";
$no_reg	=true;
$password="";

if(isset($_REQUEST['email']) && $_REQUEST['email'] != '' ){
	$user_arr=exec_query("SELECT * FROM ".$GLOBALS['S_TABLES']['users']." WHERE email='".$_REQUEST['email']."'",Q_RET_ARRAY);
	$user_arr=$user_arr[0];
	if(get_num_rows() > 0 ){
		$password=rand(11111,22222);
		$password_md5=md5($password);
		$arr=exec_query("UPDATE ".$GLOBALS['S_TABLES']['users']." SET password='".$password_md5."' WHERE email='".$_REQUEST['email']."'",Q_RET_MYSQL_RES);
		//echo $password;

		/*TODO:send mail*/
		include_once A_CLASSES."/mail_class.php";
		include_once MOD_CLASSES."/mail_templates_class.php";
		$mail			=new Mail_native();
		$templates	=new Mail_templates();


		//send_mail($from,$to,$cc=null,$bcc=null,$subject,$message){
		if($mail->send_mail($GLOBALS['ADMIN_MAIL'],$user_arr['email'],null,null,"Password reset for UCSC",$templates->reset_password_template($password))){
			echo "<h4 style='color:green'>New password was sent to your mail</h4>";
		}

		$no_reg	=false;
	}else{
		$msg		="<h4 style='color:brown'>Email address you entered is not registered!</h4>";
	}
}

if($no_reg){
echo $msg;
d_r("dijit.form.Form");
d_r("dijit.form.Button");
d_r("dijit.form.ValidationTextBox");
?>
<div dojoType="dijit.form.Form" action="" style='background-color:whitesmoke;padding:30px;width:400px;vertical-align:center' align='center' class='bgCenter round'>
<input type="hidden" name="module" value="<?php echo MODULE; ?>" >
<input type="hidden" name="page" value="<?php echo PAGE; ?>"  >
<label for="email">My email address</label><input type='text' dojoType="dijit.form.ValidationTextBox" name="email" id="email" regExp	="\b[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b" invalidMessage	="Please enter a valid email address" trim="true" required="true" ><br><br>
<button dojoType="dijit.form.Button" type="submit">Email me a new password</button>
</div>
</center>

<?php
}
?>
