<?php
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();


/*
Generate payment sheet for the give course in given batch
*/
function gen_payment_sheet($do_clearing=null){
	$fields=array(
		"p.transaction_id"=>"transaction_id",
		"p.amount"=>"amount",
		"p.clearing_done"=>"clearing_done",
		"p.status"=>"status",
		"p.init_time"=>"init_time",
		"p.updated_time"=>"updated_time",
		"p.tp_ref_id"=>"tp_ref_id",
		/*
		"f.description"=>"description",
		"f.amount"=>"amount",
		"f.tax"=>"tax",
		"r.description"=>"description",
		"e.first_name"=>"first_name",
		"e.email"=>"email"
		*/
	);

	//$query="SELECT ".implode(',',array_keys($fields))." FROM payment_payment p, payment_pay_for f, payment_program r,  payment_employee e WHERE p.program_id=r.program_id AND p.pay_for_id=f.pay_for_id AND p.employee_id=e.employee_id AND f.pay_for_code='".$_SESSION[PAGE]['pay_for_code']."' AND r.program_code='".$_SESSION[PAGE]['program_code']."' AND status='ACCEPTED';";
	$query="SELECT ".implode(',',array_keys($fields))." FROM payment_payment p, payment_pay_for f, payment_program r,  payment_employee e WHERE p.program_id=r.program_id AND p.pay_for_id=f.pay_for_id AND p.employee_id=e.employee_id AND f.pay_for_code='".$_SESSION[PAGE]['pay_for_code']."' AND r.program_code='".$_SESSION[PAGE]['program_code']."';";

	/*
	$query="SELECT *      
		FROM payment_payment p, payment_pay_for f, payment_program r,  payment_employee e      
		WHERE p.program_id=r.program_id AND p.pay_for_id=f.pay_for_id AND p.employee_id=e.employee_id;";
	*/

	$arr=exec_query($query,Q_RET_ARRAY);
	
	//Exclude some columns from display
	unset($fields["p.clearing_done"]);
	unset($fields["p.init_time"]);
	echo "<table style='border:1px solid #C9D7F1;border-collapse:collapse;' border='1'>";
	echo "<tr><th colspan='3'>Attendance Sheet for Course ".$_SESSION[PAGE]['program_code']." <br\> Batch ".$_SESSION[PAGE]['pay_for_code']."</th></tr>";
	if($do_clearing == true){
		echo "<tr><th>";
		echo implode('</th><th>',array_values($fields));                                                                                                                   
		echo "</th><th>CLEAR</th></tr>";
	}else{
		echo "<tr><th>";
		echo implode('</th><th>',array_values($fields));
		echo "</th></tr>";
	}
	foreach($arr as $row){
		echo "<tr>";
		foreach($fields as $key => $value){
			echo "<td align='center'>".$row[$value]."</td>";
		}
		if($do_clearing == null){
			echo "<td>&nbsp;</td>";
		}else{
			if($row['clearing_done'] == true){
				echo "<td align='center'><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['transaction_id'].":A' checked='true' ></input></td>";
			}else{
				echo "<td align='center'><input dojoType='dijit.form.CheckBox' type='checkbox' name='".$row['transaction_id'].":A' ></input></td>";
			}
		}
		echo "</tr>";	
	}
	echo "</table>";
}

/*
Save payment for the students
*/
function save_payment(){
	$error=array();
	$arr=exec_query("SELECT transaction_id FROM ".$GLOBALS['MOD_S_TABLES']['payment']."  WHERE program_code='".$_SESSION[PAGE]['program_code']."' AND pay_for_code='".$_SESSION[PAGE]['pay_for_code']."'",Q_RET_ARRAY);

	foreach($arr as $c_arr){
		if(isset($_REQUEST[$c_arr['transaction_id'].":A"]) && $_REQUEST[$c_arr['transaction_id'].":A"] == 'on'){
		//Registering the courses
			exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['payment']." SET clear=true WHERE index_no='".$c_arr['index_no']."' AND exam_id='".$_SESSION[PAGE]['exam_id']."' AND course_id='".$_SESSION[PAGE]['course_id']."'",Q_RET_MYSQL_RES);
		}else{
			exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['course_reg']." SET payment=false WHERE index_no='".$c_arr['index_no']."' AND exam_id='".$_SESSION[PAGE]['exam_id']."' AND course_id='".$_SESSION[PAGE]['course_id']."'",Q_RET_MYSQL_RES);
		}

		if(!is_query_ok()){
			$error[]=get_sql_error();
		}

		if(isset($_REQUEST[$c_arr['index_no'].":M"]) && $_REQUEST[$c_arr['index_no'].":M"] == 'on'){
		//Registering the courses
			exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['course_reg']." SET medical=true WHERE index_no='".$c_arr['index_no']."' AND exam_id='".$_SESSION[PAGE]['exam_id']."' AND course_id='".$_SESSION[PAGE]['course_id']."'",Q_RET_MYSQL_RES);
		}else{
			exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['course_reg']." SET medical=false WHERE index_no='".$c_arr['index_no']."' AND exam_id='".$_SESSION[PAGE]['exam_id']."' AND course_id='".$_SESSION[PAGE]['course_id']."'",Q_RET_MYSQL_RES);
		}

		if(!is_query_ok()){
			$error[]=get_sql_error();
		}
	}
	//Return the registration status as json
	if(sizeof($error)>0){
		return_status_json('ERROR',implode(',',$error));
	}else{
		return_status_json('OK','Updated successfully!');
	}

}

//PDF generator function
function gen_pdf(){
   include A_CLASSES."/letterhead_pdf_class.php";
	$query="SELECT index_no FROM ".$GLOBALS['MOD_S_TABLES']['course_reg']." WHERE exam_id='".$_SESSION[PAGE]['exam_id']."' AND course_id='".$_SESSION[PAGE]['course_id']."'";
	$res=exec_query($query,Q_RET_MYSQL_RES);

   $letterhead=new Letterhead("A4","P");

	$content		="<table style='border-collapse:collapse' border='1'><tr><th>#</th><th>Index No</th><th>Attendance</th></tr>\n";
	$serial		=1;
	while($row = mysql_fetch_assoc($res)){
		$content.="<tr><td>".$serial++."</td><td>".$row['index_no']."</td><td>&nbsp;</td></tr>\n";
	}
	$content.='</table>';

   //insert the content to the pdf
   $letterhead->include_content($content);

   //Acquire pdf document
   $pdf=$letterhead->getPdf();

	//name of the pdf file
	$pdf_file=$_SESSION[PAGE]['course_id'].".pdf";

   //$pdf->Output('test_pdf.pdf', 'I');
   $pdf->Output($pdf_file, 'I');
   return;
	//print $content;
}

function gen_csv(){

	$fields=array(
		"p.transaction_id"=>"transaction_id",
		"p.amount"=>"amount",
		"p.clearing_done"=>"clearing_done",
		"p.status"=>"status",
		"p.init_time"=>"init_time",
		"p.updated_time"=>"updated_time",
		"p.tp_ref_id"=>"tp_ref_id"

		/*
		"f.description"=>"description",
		"f.amount"=>"amount",
		"f.tax"=>"tax",
		"r.description"=>"description",
		"e.first_name"=>"first_name",
		"e.email"=>"email"
		*/
	);

	$csv_file= $GLOBALS['MOD_S_TABLES']['payment'].".csv";
	$query="SELECT ".implode(',',array_keys($fields))." FROM payment_payment p, payment_pay_for f, payment_program r,  payment_employee e 
		WHERE p.program_id=r.program_id AND p.pay_for_id=f.pay_for_id AND p.employee_id=e.employee_id AND f.pay_for_code='".$_SESSION[PAGE]['pay_for_code']."' AND r.program_code='".$_SESSION[PAGE]['program_code']."';";

	db_to_csv_nr($query,$csv_file,null);
	exit();
}

//id table mapper array
$table_of_id=array(
	'program_code'=>$GLOBALS['MOD_S_TABLES']['program'],
	'pay_for_code'=>$GLOBALS['MOD_S_TABLES']['pay_for']
);

//Map filter for the given id
$filter_map=array(
	'pay_for_code'=>isset($_SESSION[PAGE]['program_id'])?"program_id='".$_SESSION[PAGE]['program_id']."'":null
);

//Request functoin switcher
if(isset($_REQUEST['form'])){
	switch($_REQUEST['form']){
   case 'main':
	if(isset($_REQUEST['action'])){
		switch($_REQUEST['action']){
		case 'modify':
			save_payment();
		break;
		case 'pdf':
			gen_pdf();
		break;
		case 'csv':
			if(isset($_SESSION[PAGE]['pay_for_code']) && isset($_SESSION[PAGE]['program_code'])){
				gen_csv();
			}
		break;
		case 'html':
		case 'reload':
			if(isset($_REQUEST['pay_for_code'])){
				$_SESSION[PAGE]['pay_for_code']=$_REQUEST['pay_for_code'];
			}
			gen_payment_sheet(true);
		break;
		case 'store':
			$filter=null;
			if(isset($filter_map[$_REQUEST['id']])){
				$filter=$filter_map[$_REQUEST['id']];
			}
			$xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter);
		break;
		case 'param':
			$_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
			//exceptional cases
			switch($_REQUEST['param']){
				case 'program_code':
					$admission_year=exec_query("SELECT program_id FROM ".$GLOBALS['MOD_P_TABLES']['program']." WHERE program_code='".$_SESSION[PAGE][$_REQUEST['param']]."'",Q_RET_ARRAY);
					$_SESSION[PAGE]['program_id']=$admission_year[0]['program_id'];
				break;
			}
			return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
		break;
		}
	}
	case 'filter':
	break;
	}
}else{
	echo "<div id='payment_report_frm' jsId='payment_report_frm' dojoType='dijit.form.Form' style='overflow:auto' >";
	if(isset($_SESSION[PAGE]['program_code'])&&isset($_SESSION[PAGE]['pay_for_code'])){
		gen_payment_sheet(true);
	}	
	echo "</div>";

	echo "<script type='text/javascript'>";
	//dojo javascript onload function starts
	echo "dojo.addOnLoad(function() {";

	//function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
	//
	$xhr_combobox->gen_xhr_combobox('program_code',"Program",$xhr_combobox->get_val('program_code'),80,20,null,null);
	$xhr_combobox->gen_xhr_combobox('pay_for_code',"Pay_for",$xhr_combobox->get_val('pay_for_code'),70,20,array('program_code','pay_for_code'),'payment_report_frm');

	//dojo javascript onload function ends
	echo "});";
	$xhr_combobox->param_setter();
	$xhr_combobox->html_requester(array('program_code','pay_for_code'),'payment_report_frm');
	$xhr_combobox->form_submitter('payment_report_frm');
	echo "</script>";
}

?>
