<?php
if(!isset($_SESSION['username'])){
	echo "Please login first.";
	return;
}

//Acquire payer information
//Get course, batch, enroll information
$course_arr=exec_query("SELECT c.title,c.fee,b.start_date,b.batch_id,c.online_payment_code FROM ".$GLOBALS['MOD_P_TABLES']['course']." c,".$GLOBALS['MOD_P_TABLES']['batch']." b, ".$GLOBALS['MOD_P_TABLES']['enroll']." e WHERE e.enroll_id='".$_SESSION['enroll_id']."' AND e.batch_id=b.batch_id AND b.course_id=c.course_id",Q_RET_ARRAY);
$course_arr=$course_arr[0];
//Get student information
$student_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['student']." WHERE registration_no='".$_SESSION['user_id']."'",Q_RET_ARRAY);
$student_arr=$student_arr[0];


function print_user_info($student_arr,$course_arr){
	echo"<h3>Online payment information</h3>";
 	echo"<h4>Registration Fee - ".$course_arr['title']."</h4>";
	echo"<table cellpadding='5'>
	<tr><td style='font-weight:bold'>Registration number</td><td>".$student_arr['registration_no']."</td></tr>
	<tr><td style='font-weight:bold'>NIC number</td><td>".$student_arr['NIC']."</td></tr>
	<tr><td style='font-weight:bold'>Name</td><td style='font-size:150%'>".$student_arr['first_name']." ".$student_arr['middle_names']." ".$student_arr['last_name']."</td></tr>
	<tr><td style='font-weight:bold'>Payment</td><td>Rs&nbsp;".sprintf("%.02f",$course_arr['fee'])."</td></tr>
	<tr><td style='font-weight:bold'>Convenience fee for online payment </td><td>Rs&nbsp;".sprintf("%.02f",(($course_arr['fee']/100)*$GLOBALS['TAX']))."</td></tr>
	<tr><td style='font-weight:bold'>Total payment</td><td>Rs&nbsp;".sprintf("%.02f",($course_arr['fee']+($course_arr['fee']/100)*$GLOBALS['TAX']))."</td></tr>
	</table>";

}

function print_instructions(){
   echo "<h4>Instructions</h4>
	<hr/>
	<ol>
	<li>When you press Proceed you will be directed to sampath bank's online payment system
	<li>The payment voucher will be mailed to your personal mail given in the application
	<li>Please use the transaction ID of the payment voucher for further queries on the online transaction
	</ol>
	";
}

function payment_rejected($student_arr,$payment_category){
	print_user_info($student_arr,$payment_category);
	echo "
	<br/>
	<br/>
	<br/>
	<h3>Online payment status</h3>
	<span style='color:red'>We are sorry that your payment was not successful!</span><br/>
	Please try again or try our <a href=\"javascript:open_page('ext_courses','pay_offline')\">offline payment procedure</a>.";

	//print_instructions();

	echo "<br/><br/><br/><br/><br/><div align='right' class='buttonBar'  >
	<button dojoType='dijit.form.Button' jsId='back_btn'onClick=\"open_page('ext_courses','payment')\">&laquo;&nbsp;Back</button>
	<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onClick=\"open_page('ext_courses','pay_online&retry=true')\" >Try again&nbsp;&raquo;</button>
	</div>
	";
}

function payment_accepted($student_arr,$course_arr){
	print_user_info($student_arr,$course_arr);

	echo "
	<br/>
	<br/>
	<br/>
	<h3>Online payment status</h3>
	<h4>
	<span style='color:green'>You have successfully completed the online payment!</span><br/>
	Please check your email for the payment voucher... <br/>
	Thank you!<h4>
	<!-- a href=''>Resend the voucher</a -->
	";

	/*
	print_instructions();

	echo "<br/><br/><br/><div align='right' class='buttonBar'  >
	<button dojoType='dijit.form.Button' jsId='back_btn'onClick=\"open_page('registration','payment');this.diable;\">&laquo;&nbsp;Back</button>
	<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onClick=\"iframe_open()\" >Proceed&nbsp;&raquo;</button>
	</div>
	";
	*/
}

function peyment_process($student_arr,$course_arr){
   //Change offline payment status to PENDING
   exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['enroll']." SET payment_status='PENDING', payment_method='ONLINE'  WHERE enroll_id='".$_SESSION['enroll_id']."'",Q_RET_NON);

	print_user_info($student_arr,$course_arr);

	print_instructions();

	include MOD_CLASSES."/crypt.php";
	$key='GIrR5G2U0o75Iwfg3O6mqd1LFz4WuSVBD5BdlANZN3M=';
	$tp_ref_id=$student_arr['registration_no'];
	$pay_for=$course_arr['online_payment_code'];
	$amount=$course_arr['fee'];
	$nic=$student_arr['NIC'];
	$email=$student_arr["email_1"];
	$full_name=$student_arr['first_name']." ".$student_arr['middle_names']." ".$student_arr['last_name'];


   $msg_crypt= new Message_crypt($key);
	$request=$msg_crypt->genRquest(
		$tp_ref_id,
		$pay_for,
		$amount,
		$nic,
	   $email,
		$full_name
	);

	//print_r($msg_crypt->genRquest($request));
	//$payment_gw_url="https://ucsc.lk/uis/?module=payment&page=tp_payment&data=true&program=M";
	$payment_gw_url="https://ucsc.lk/uis/?module=payment&page=tp_payment&data=true&program=S";
	
		
	//echo $request;
	echo "
	<script language='javascript'>
	function iframe_open(){
		//back_btn.setAttribute('disabled', true);
		//proceed_btn.setAttribute('label', 'Reload');
		proceed_btn.setAttribute('disabled', true);
		//document.getElementById('textFile').height='510';
		document.getElementById('m_form').submit();
	}
   </script>

   <!-- form method='POST' action='".$payment_gw_url."' id='m_form' target='textFile'-->
   <form method='POST' action='".$payment_gw_url."' id='m_form' >
		<input type='hidden' name='request' value='".$request."' />
		<!-- iframe id='textFile' name='textFile' style='border:0px;' width='100%'  height='10' align='center'>
		</iframe -->
		<br/><br/><br/>
		<div align='right' class='buttonBar'  >
			<button dojoType='dijit.form.Button' jsId='back_btn' onClick=\"open_page('ext_courses','payment');this.diable;\">&laquo;&nbsp;Back</button>
			<button dojoType='dijit.form.Button' jsId='proceed_btn' type='submit' onMouseUp=\"iframe_open()\" >Proceed&nbsp;&raquo;</button>
		</div>
	</form>";
}


switch($student_arr['status']){
case 'ACCEPTED':
	payment_accepted($student_arr,$course_arr);
break;
case 'REJECTED':
	if(isset($_REQUEST['retry']) && $_REQUEST['retry']=='true'){
		peyment_process($student_arr,$course_arr);
	}else{
		payment_rejected($student_arr,$course_arr);
	}
break;
default:
	peyment_process($student_arr,$course_arr);
break;
}

?>
