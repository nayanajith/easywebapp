<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system or enter your detail as a one time user <a href='javascript:open_page(\"donations\",\"registration\")'>here</a>.";
	return;
}

//Keep the code for future use
if(isset($_REQUEST['code'])){
	$_SESSION['code']=$_REQUEST['code'];
}

//Keep the amount for future use
if(isset($_REQUEST['amount'])){
	$_SESSION['amount']=$_REQUEST['amount'];
}

//Redirect to relevent page for payment
if(isset($_REQUEST['pay_online'])){
	header('location:?module=donations&page=pay_online');
	return;
}elseif(isset($_REQUEST['pay_offline'])){
	header('location:?module=donations&page=pay_offline');
	return;
}

?>
<h3>Pleaes select a donation scheme to proceed</h3>
<?php
$table=$GLOBALS['MOD_S_TABLES']['program'];
d_r('dijit.form.Form');
d_r('dijit.form.NumberTextBox');
d_r('dijit.form.RadioButton');

$arr	=exec_query("SELECT * FROM $table WHERE disabled=0",Q_RET_ARRAY,null,'program_id');
echo "<form action='' id='donation_to_frm' dojoType='dijit.form.Form' jsId='donation_to_frm' onSubmit='return this.validate()'><table width='100%' cellpadding='5' cellspacing='20'>";
$first=true;
foreach($arr as $key => $row){
	echo "<tr>
		<td class='shadow round' style='background-color:whitesmoke;valign:center' >
		<label for='code_".$row['code']."'><div style='float:left;background-color:silver;width:50px;height:50px; border-bottom-right-radius: 100px 50px; border-top-right-radius: 100px 50px;'>";
	if(isset($_SESSION['code']) ){ 
		if($_SESSION['code'] == $row['code']){
		echo "<div dojoType='dijit.form.RadioButton' required='true' name='code' checked='true' value='".$row['code']."' style='margin-left:15px;margin-top:16px;' id='code_".$row['code']."' ></div>";
		}else{
			echo "<div dojoType='dijit.form.RadioButton' required='true' name='code' checked='false' value='".$row['code']."' style='margin-left:15px;margin-top:16px;' id='code_".$row['code']."' ></div>";
		}
	}else{
		echo "<div dojoType='dijit.form.RadioButton' required='true' name='code' checked='".($first?'true':'false')."' value='".$row['code']."' style='margin-left:15px;margin-top:16px;' id='code_".$row['code']."' ></div>";
	}
	echo "</div></label><label for='code_".$row['code']."'><div style='font-size:150%;padding:10px;float:left'>".$row['description']."</div></label></td></tr>";

	$first=false;
}

echo "<tr><td>Amount in SLR:&nbsp;<input type='text' dojoType='dijit.form.NumberTextBox' name='amount' required='true' value='".(isset($_SESSION['amount'])?$_SESSION['amount']:"")."' > </td></tr>";
echo "</table>";
echo "<input type='hidden' name='data' value='true' >
		<input type='hidden' name='module' value='donations' >
		<input type='hidden' name='page' value='donation_to' >";
echo "<br><br><br><div align='right' class='buttonBar'  >
		<button dojoType='dijit.form.Button' type='button' name='loginBtn' onClick=\"open_page('donations','registration')\">&laquo;&nbsp;Back</button>
		<button dojoType='dijit.form.Button' type='submit' name='pay_offline' >Pay offline to bank</button>
		<button dojoType='dijit.form.Button' type='submit' name='pay_online'>Credit/VISA card payment&nbsp;&raquo;</button>
		</div>";
echo "</form>";
?>
</script>
