<?php
class Mail_templates{
	protected $mail;
	function __construct($mail=null){
		if($mail != null){
			$this->mail=$mail;
		}
	}

	function reset_password_template($password){
		return "
		Your new password: $password
		";
	}

	/*Generate the invoice pdf*/
	function gen_pdf($content,$pdf_file){
	   include A_CLASSES."/letterhead_pdf_class.php";
		//$letterhead=new Letterhead("A6","L");
		$letterhead=new Letterhead("A4","P");

		//insert the content to the pdf
		$letterhead->include_content($content);

		//Acquire pdf document
		$pdf=$letterhead->getPdf();

		//$pdf->Output('test_pdf.pdf', 'I');
		$pdf->Output($pdf_file, 'F');
		return $pdf_file;
	}


	//HTML output of the invoice
	function payment_invoice_html($user_info,$program_info,$pay_for_info){
	return "
		<style> td{border-bottom:1px dotted whitesmoke;}</style>
		<table style='border:1px solid silver;'>
			<tr><td colspan='2' align='center'><h3>PAYMENT RECEIPT</h3></td></tr>
			<tr><td>Telephone</td><td>+94-11-2581245</td></tr> 
			<tr><td>Fax Number</td><td>+94-11-2587239</td></tr> 
			<tr><td>Email to Contact</td><td>info@ucsc.cmb.ac.lk</td></tr> 
			<tr><td>UCSC Website</td><td>www.ucsc.cmb.ac.lk</td></tr> 
			<tr><td colspan='2'><hr/></td></tr> 
			<tr><td >Payment Reference ID</td><td >".$user_info['transaction_id']."</td></tr> 
			<tr><td >Payer Name</td><td>".$user_info['first_name']." ".$user_info['middle_names']." ".$user_info['last_name']."</td></tr> 
			<tr><td >Payment for</td><td>".$program_info['description']." ".$pay_for_info['description']."</td></tr> 
			<tr><td >Payment Amount</td><td align='right'>".sprintf("%.02f",$user_info['amount'])."</td></tr> 
			<tr><td >Convenience fee (".$pay_for_info['tax']."% of Payment)</td><td align='right'>".sprintf("%.02f",$user_info['tax'])."</td></tr> 
			<tr><td >Total fee</td><td align='right'><u>".sprintf("%.02f",number_format($user_info['tax']+$user_info['amount'],2))."</u></td></tr> 
		</table>
		";
	}





	function payment_invoice($user_info,$program_info,$pay_for_info){
		$body=$this->payment_invoice_html($user_info,$program_info,$pay_for_info);

		$pdf_file=INVOICE_DIR."/".$user_info['transaction_id'].".pdf"; 

		/*Generate the pdf of the invoice*/
		$body=str_replace("'","\"",$body);
		$this->gen_pdf($body,$pdf_file);

		include_once A_CLASSES."/mail_class.php";
		$mail			=new Mail_native();
		return $mail->mail_attachment($user_info['transaction_id'].".pdf", INVOICE_DIR, $user_info['email'], $GLOBALS['PAYMENT_ADMIN_MAIL'], "UCSC payment gateway", $GLOBALS['PAYMENT_ADMIN_MAIL'], 'UCSC payment status', $body);
	}






	function mail_alert($mesg){
		$body="<h4>There are payments to be cleared.</h4>
INFO:<br/>\n
$mesg	

--<br/>\n
Auto generated on ".date('d-m-Y')."<br/>\n
UCSC Payment Gateway<br/>\n
";	
	include_once A_CLASSES."/mail_class.php";
	$mail			=new Mail_native();
	$mail->send_mail($GLOBALS['PAYMENT_ADMIN_MAIL'],$GLOBALS['PAYMENT_CLAER_MAIL'],null,null,"UCSC online payment status",$body);
	return $mail->send_mail($GLOBALS['PAYMENT_ADMIN_MAIL'],'nml@ucsc.cmb.ac.lk',null,null,"UCSC online payment status",$body);
	}





	function email_verification($rec_id,$varification_code){
		$url="https://".$_SERVER['HTTP_HOST']."?module=donations&page=email_verification&rec_id=$rec_id&code=".urlencode($varification_code);
		$body="<h4>Email varification</h4>
Please click on the link <a href='$url'>$url</a> or copy paste in browser address bar  to complete the registration procedure.<br/>\n

--<br/>\n
Auto generated on ".date('d-m-Y')."<br/>\n
UCSC funding/donation program<br/>\n
";	
		include_once A_CLASSES."/mail_class.php";
		$mail			=new Mail_native();
		$mail->send_mail($GLOBALS['NOREPLY_MAIL'],$this->mail,null,null,"UCSC Funder/Donor Rgistration",$body);
	}
}

?>
