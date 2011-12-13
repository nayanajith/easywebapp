<?php 
$client_ip	=$_SERVER['REMOTE_ADDR'];
/*Allow access to the script only form the local machin (cron job)*/
/*
if($client_ip != '127.0.0.1'){
	exit();
}
*/

/*Get list of payments which mail_alert is not sent for each program*/
	$res=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." WHERE status='ACCEPTED' AND alert_sent=FALSE",Q_RET_MYSQL_RES);
	//$res=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." WHERE status='REJECTED'",Q_RET_MYSQL_RES);

	echo get_num_rows()." available for clear!"; 
	/*If the number of records are greater than 0 alert the clearing person*/
	$columns=array(
		'transaction_id'	=>'Transaction ID',
		'amount' 			=>'Amount',
		'tax' 				=>'Convenience Fee',
		'tp_ref_id'			=>'Reference ID',
		'init_time' 		=>'Payment Time',
		'program_id' 		=>'Program',
		'pay_for_id' 		=>'Payment For'
	);

	if(get_num_rows() > 0){
		$program_arr=exec_query("SELECT program_id,short_name FROM ".$GLOBALS['MOD_S_TABLES']['program'],$type=Q_RET_ARRAY,$db=null,$array_key='program_id',$deleted=null,$no_connect=null);
		$pay_for_arr=exec_query("SELECT pay_for_id,short_name FROM ".$GLOBALS['MOD_S_TABLES']['pay_for'],$type=Q_RET_ARRAY,$db=null,$array_key='pay_for_id',$deleted=null,$no_connect=null);


		$clear_table="<table style='border-collapse:collapse' border=1>";
		$clear_table.="<th>".implode('</th><th>',array_values($columns))."</th>";
		while($row=mysql_fetch_assoc($res)){
			$clear_table.="<tr>";
			foreach($columns as $key => $value){
				switch($key){
					case 'program_id':
						$clear_table.="<td>".$program_arr[$row[$key]]['short_name']."</td>";
					break;
					case 'pay_for_id':
						$clear_table.="<td>".$pay_for_arr[$row[$key]]['short_name']."</td>";
					break;
					default:
						$clear_table.="<td>".$row[$key]."</td>";
					break;
				}
			}
			$clear_table.="</tr>";
		}
		$clear_table.="</table>";

		/*Sending the alert and if it is successful change the alert_stat to TRUE*/
		if(send_alert("Payments to be cleared!<br/>\n".$clear_table)){
			$res=exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['payment']." SET alert_sent=TRUE WHERE status='ACCEPTED' AND alert_sent=FALSE",Q_RET_MYSQL_RES);
		}
	}

/*
 * Sending mail alert to the clearing person
 *
 * @param program : payment program
 * @param count	: No. of records which alert required
 */
function send_alert($mesg){
	include_once MOD_CLASSES."/mail_templates_class.php";
	$templates	=new Mail_templates();
	return $templates->mail_alert($mesg);
}
?>
