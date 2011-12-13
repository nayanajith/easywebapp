<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}

include(MOD_CLASSES."/offline_voucher_class.php");

//Change offline payment status to PENDING
exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['enroll']." SET payment_status='PENDING', payment_method='OFFLINE'  WHERE id='".$_SESSION['enroll_id']."'",Q_RET_NON);

//Acquire payer information
//Get course, batch, enroll information
$course_arr=exec_query("SELECT c.title,c.fee,b.start_date,b.batch_id FROM ".$GLOBALS['MOD_P_TABLES']['course']." c,".$GLOBALS['MOD_P_TABLES']['batch']." b, ".$GLOBALS['MOD_P_TABLES']['enroll']." e WHERE e.id='".$_SESSION['enroll_id']."' AND e.batch_id=b.batch_id AND b.course_id=c.course_id",Q_RET_ARRAY);
$course_arr=$course_arr[0];

//Get student information
$student_arr=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['student']." WHERE registration_no='".$_SESSION['user_id']."'",Q_RET_ARRAY);
$student_arr=$student_arr[0];

//Build the array which is a parameter of the voucher generator
$payment_info=array(
   "RS ".sprintf("%.02f",$course_arr['fee']),
   strtoupper(number_to_text($course_arr['fee'])." rupees only"),
   $student_arr['first_name']." ".$student_arr['middle_names']." ".$student_arr['last_name'],
   $student_arr['registration_no'],
	$student_arr['NIC']
);

//Generic information of the convocation payment voucher
$payment_info=array(
   'acc_no'	      =>"086-1001-511-89665",
   'inv_title'	   =>"POSTGRADUATE APPLICATION 2011",
   'purpose'	   =>"REGISTRATION FEE - ".strtoupper($course_arr['title']),
   'amount_number'=>$course_arr['fee'],
	'amount_word'	=>strtoupper(number_to_text($course_arr['fee'])." rupees only"),
   'payer_name'	=>$student_arr['first_name']." ".$student_arr['middle_names']." ".$student_arr['last_name'],
   'payer_id'	   =>$student_arr['registration_no'],
   'payer_NIC'	   =>$student_arr['NIC'],
);
	
exec_query("INSERT INTO ".$GLOBALS['MOD_P_TABLES']['invoice']."(`".implode('`,`',array_keys($payment_info))."`)values('".implode("','",array_values($payment_info))."')",Q_RET_NON);

//Update invoice_id in invoice tabl
$arr=exec_query("SELECT id FROM ".$GLOBALS['MOD_P_TABLES']['invoice']." WHERE payer_id='".$student_arr['registration_no']."' ORDER BY  update_time DESC LIMIT 1",Q_RET_ARRAY);
$invoice_id=gen_reg_no($arr[0]['id']);
exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['invoice']."SET invoice_id='".$invoice_id."' WHERE id='".$arr[0]['id']."'",Q_RET_NON)

//Update transaction_id in enroll table
exec_query("UPDATE ".$GLOBALS['MOD_P_TABLES']['enroll']."SET transaction_id='".$invoice_id."' WHERE id='".$_SESSION['enroll_id']."'",Q_RET_NON)
	
//Generate the voucher 
//__construct($payer_info,$acc_no,$inv_title)
$voucher=new Voucher($invoice_id);

//Acquire pdf document
$pdf=$voucher->getPdf();

$pdf->Output('payment_voucher.pdf', 'I');
//$pdf->Output("/tmp/tt.pdf", 'F');
//return $pdf_file;

?>
