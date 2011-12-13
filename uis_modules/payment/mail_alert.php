<?php 
$client_ip	=$_SERVER['REMOTE_ADDR'];
/*Allow access to the script only form the local machin (cron job)*/
/*
if($client_ip != '127.0.0.1'){
	exit();
}
*/

include_once MOD_CLASSES."/mail_templates_class.php";
$templates	=new Mail_templates();

/*Get list of payments which mail_alert is not sent for each program*/
	$res=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." WHERE status='ACCEPTED' AND alert_sent=FALSE ORDER BY program_id,pay_for_id",Q_RET_MYSQL_RES);
//	$res=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." WHERE alert_sent=FALSE ORDER BY program_id,pay_for_id limit 10",Q_RET_MYSQL_RES);
	//$res=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['payment']." WHERE status='REJECTED'",Q_RET_MYSQL_RES);

	echo get_num_rows()." available for clear!"; 
	/*If the number of records are greater than 0 alert the clearing person*/
	$columns=array(
		'transaction_id'	=>'Transaction ID',
		'amount' 			=>'Amount',
		'tax' 				=>'Convenience Fee',
		'tp_ref_id'			=>'Reference ID',
		'init_time' 		=>'Payment Time',
		//'program_id' 		=>'Program',
		'pay_for_id' 		=>'Payment For'
	);

	if(get_num_rows() > 0){
		$program_arr=exec_query("SELECT tp_mail,program_id,short_name FROM ".$GLOBALS['MOD_S_TABLES']['program'],$type=Q_RET_ARRAY,$db=null,$array_key='program_id',$deleted=null,$no_connect=null);
		$pay_for_arr=exec_query("SELECT pay_for_id,short_name FROM ".$GLOBALS['MOD_S_TABLES']['pay_for'],$type=Q_RET_ARRAY,$db=null,$array_key='pay_for_id',$deleted=null,$no_connect=null);

		$program_payments=array();


		$clear_table_header="<th>".implode('</th><th>',array_values($columns))."</th>";
		while($row=mysql_fetch_assoc($res)){
			if(!isset($program_payments[$row['program_id']])){
				$program_payments[$row['program_id']]=$clear_table_header;
			}

			$program_payments[$row['program_id']].="<tr>";
			foreach($columns as $key => $value){
				switch($key){
/*
					case 'program_id':
						$program_payments[$row['program_id']].="<td>".$program_arr[$row[$key]]['short_name']."</td>";
					break;
*/
					case 'pay_for_id':
						$program_payments[$row['program_id']].="<td>".$pay_for_arr[$row[$key]]['short_name']."</td>";
					break;
					default:
						$program_payments[$row['program_id']].="<td>".$row[$key]."</td>";
					break;
				}
			}
			$program_payments[$row['program_id']].="</tr>";
		}
		$bursar_mail='';
		$clear_table="<table style='border-collapse:collapse' border=1>%s</table>";
		foreach($program_payments as $program_id => $tbody){
			$tp_list=sprintf($clear_table,$tbody);

			//Sending mail alert to the program coordinator
			$templates->tp_mail_alert($tp_list,$program_arr[$program_id]['tp_mail']);

			//All lists will be concatanated and send to bursar
			$bursar_mail.="<h4>".$program_arr[$program_id]['short_name'].'('.$program_arr[$program_id]['description'].")</h4>";
			$bursar_mail.=$tp_list;
		}

		//Sending the alert to bursar and if it is successful change the alert_stat to TRUE
		if($templates->bursar_mail_alert($bursar_mail)){
			$res=exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['payment']." SET alert_sent=TRUE WHERE status='ACCEPTED' AND alert_sent=FALSE",Q_RET_MYSQL_RES);
		}
	}

?>
