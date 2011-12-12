
<?php
//echo 'use session id .. not reg id or payment type... :P sid ';
//echo $_SESSION['sid']. " user ". $_SESSION['user_id'];
/*
$table = "reg";
$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."' AND session_id = '".$_REQUEST['sid']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$reg = mysql_fetch_array($res);
*/
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){

echo "Please login to the system <a href='javascript:open_page(\"courses\",\"login\")'>HERE</a>";
}else{



$table = $GLOBALS['MOD_P_TABLES']["reg"];
$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."' AND session_id = '".$_SESSION['sid']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);

// check if reg has been made;
if(($reg = mysql_fetch_array($res)) == false){

$query = "INSERT INTO ".$table."(session_id,student_id,status) VALUES('".$_SESSION['sid']."','". $_SESSION['user_id']."','PENDING')" ;
$res = exec_query($query,Q_RET_MYSQL_RES);


$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."' AND session_id = '".$_SESSION['sid']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$reg = mysql_fetch_array($res);
}
//echo $reg['reg_id'];
 

 

/*
if(isset($_SESSION['reg_id']) &&  $_SESSION['reg_id'] != ""){

$table = "reg";
$query = "SELECT * FROM ".$table." WHERE reg_id = '".$_SESSION['reg_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$reg = mysql_fetch_array($res);
}*/


$table = $GLOBALS['MOD_P_TABLES']["student"];
$query = "SELECT * FROM ".$table." WHERE student_id = '". $_SESSION['user_id']."'" ;
$res = exec_query($query,Q_RET_MYSQL_RES);
$student = mysql_fetch_array($res);

$table = $GLOBALS['MOD_P_TABLES']["schedule"];
$query = "SELECT * FROM ".$table." WHERE session_id = '". $_SESSION['sid']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$session = mysql_fetch_array($res2);
   
$table = $GLOBALS['MOD_P_TABLES']["course"];
$query = "SELECT * FROM ".$table." WHERE course_id = '". $session['course_id']."'" ;
$res2 = exec_query($query,Q_RET_MYSQL_RES);
$course = mysql_fetch_array($res2);  

// update payment type
$table = $GLOBALS['MOD_P_TABLES']["reg"];
$query = "UPDATE ".$table." SET payment_method = '".$_REQUEST['paymeth']."' WHERE reg_id = '".$reg['reg_id']."'" ;
$res3 = exec_query($query,Q_RET_MYSQL_RES);	
    
if($_REQUEST['paymeth'] == 'ONLINE'){
   //echo $reg['payment_method'];
  
   echo"<h1>Online payment information</h1>";
   echo "<hr style='border:1px solid silver;'/>";
 	echo '<table> <tr><td width = 70% valign = "top" >';
 	echo '<h4 style = "font-style:oblique">Personal Details</h4>';
	echo"<table cellpadding='5'>
	<tr><td style='font-weight:bold'>NIC number</td><td>".$student['NIC']."</td></tr>
	<tr><td style='font-weight:bold'>Name</td><td style='font-size:150%'>".$student['first_name']." ".$student['last_name']."</td></tr>
	<tr><td style='font-weight:bold'>Email Address </td><td>".$student['email']."</td></tr>
	</table>";
	echo "<hr style='border:1px solid silver;'/>";	
   echo '<h4 style = "font-style:oblique">Course Details</h4>'; 
 	// 	<tr><td style='font-weight:bold'>Registration number</td><td>".$reg['reg_id']."</td></tr> 

	
	echo"<table cellpadding='5'>
	<tr><td style='font-weight:bold'>Course Code</td><td>".$course['short_name']."</td></tr>
	<tr><td style='font-weight:bold'>Course Name</td><td>".$course['long_name']."</td></tr>
	<tr><td style='font-weight:bold'>Session Name</td><td>".$session['session_name']."</td></tr>
	<tr><td style='font-weight:bold'>Start Date</td><td>".$session['start_date']."</td></tr>
	<tr><td style='font-weight:bold'>End Date</td><td>".$session['end_date']."</td></tr>
	</table>";

	echo "<hr style='border:1px solid silver;'/>";	
   echo '<h4 style = "font-style:oblique">Payment Details</h4>'; 
 	// 	<tr><td style='font-weight:bold'>Registration number</td><td>".$reg['reg_id']."</td></tr> 

	
	echo"<table cellpadding='5'>	
	<tr><td style='font-weight:bold'>Payment</td><td>Rs&nbsp;".$course['course_fee']."</td></tr>
	<tr><td style='font-weight:bold'>Convenience fee for online payment </td><td>Rs&nbsp;".$course['course_fee']*(3.093/100)."</td></tr>
	<tr><td style='font-weight:bold'>Total payment</td><td>Rs&nbsp;".$course['course_fee']*(103.093/100)."</td></tr>
	</table>";
	echo "<hr style='border:1px solid silver;'/>";	   
   
   include MOD_CLASSES."/crypt.php";
   //include "mod/courses/classes/crypt.php";
	$key='amEMiw9fp7YnSWO/ea4DU1HX8QgPzn05B38jBSqxc60=';
	$tp_ref_id=$reg['reg_id']+ 10000000;
	$pay_for="A";
	$amount=$course['course_fee'];
	$nic=$student['NIC'];
	$email=$student["email"];
	$full_name=$student['first_name']." ".$student['last_name'];


  $msg_crypt=  new Message_crypt($key);
	$request=$msg_crypt->genRquest(
		$tp_ref_id,
		$pay_for,
		$amount,
		$nic,
	   $email,
		$full_name
	);
   
   echo "<div align = 'right' ><form method = 'POST'>
   <input type = 'hidden' name = 'request' value = ".$request." />
   <input type = 'hidden' name = 'page' value = 'tp_payment' />
   <input type = 'hidden' name = 'module' value = 'payment' />
   <input type = 'hidden' name = 'data' value = 'true' />   
   <input type = 'hidden' name = 'program' value = 'P' />
   <button dojoType='dijit.form.Button' type='submit'  >Make Payment&nbsp;&raquo;</button>
   </form></div>";
   
   

   echo "<form method = 'GET'>
   <input type = 'hidden' name = 'request' value = ".$request." />
   <input type = 'hidden' name = 'page' value = 'check' />
   <input type = 'hidden' name = 'module' value = 'courses' />
   <input type = 'hidden' name = 'datap' value = 'true' />   
   <input type = 'hidden' name = 'program' value = 'P' />
   <input type='submit' onclick = payonline() value = 'Check' />
   </form>";
   
   echo "</td><td valign = 'top' style = 'border-left:1px solid silver'>";
   echo '<h4>Online Payment procedure</h4>';
   echo "<ol>
		<li>Please check your details</li>
		<li>If these details are correct and you wish to proceed with your payment, click make payment</li>
		<li>You will then be directed to the Sampath Bank secure payment gateway where you can make the payment</li>
		<li>If your details are not correct please go back and correct them</li>
		</ol>";
echo "</td></tr></table>";
   
   
   
   
   
}else{

$_SESSION['voucher_reg_id'] = $reg['reg_id'];
//echo $_SESSION['sid']; 
echo '<h1>Paying Offline to the bank</h1>';
echo "<hr style='border:1px solid silver;'/>";
echo '<h4>Instructions</h4>';
echo '<ol>';
echo "<li>Please download the <a href='?module=".MODULE."&page=offline_pdf&data=true'><b>PDF</b></a> file of the payment voucher.";
echo '<li>There are four copies as given below,';
echo "<ol type='I'>";
echo '<li>UCSC copy 1 ( Post this to us)';
echo '<li>Candidate copy (Keep this with you)';
echo '<li>Thimbirigasyaya bank copy(Bank will keep this)';
echo '<li>Bank copy ( Bank will keep this)
</ol>';
echo '<li>You need to sign on each voucher stating the date of payment and handover to any branch of Peoples Bank with the required payment.';
echo '<li>The UCSC copy must be sent to UCSC, and please note that Handing over the copy of voucher is compulsory to process your application.';
echo "<pre style='font:inherit'>
<b>Postal address:</b>
Senior Assistant Registrar/Academic and Publications,
UCSC,
No: 35 Reid Avenue,
Colombo 07.
</pre>
</ol>";


}



}
?>
