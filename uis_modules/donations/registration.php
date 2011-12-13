<?php
include A_CLASSES."/data_entry_class.php";
$super_table		='registration';
$keys					=array('email');
$key1					='email';
$grid_array			=array('reg_id','email');
$grid_array_long	=array('reg_id','NIC','email','first_name','status');

$table				=$GLOBALS['MOD_S_TABLES'][$super_table];
$formgen 			=null;
if(isset($_SESSION['email'])){
	$formgen 		= new Formgenerator($table,$keys,$super_table,$_SESSION['email']);
}else{
	$formgen 		= new Formgenerator($table,$keys,$super_table,null);
}

$help_file			=$super_table."_help.php";
$modif_file			=$super_table."_modif.php";
$filter_string		="";

/*Extract filter according to the filter_id in request string*/
if(isset($_REQUEST['filter_name']) && $_REQUEST['filter_name'] != ''){
	$filter_string=$formgen->ret_filter($_REQUEST['filter_name'],$GLOBALS['MOD_S_TABLES']['filter']);
	log_msg('filter',$filter_string);
}

/*generate csv with column headers*/
if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
	$filter_str=$filter_string!=""?" WHERE ".$filter_string:"";
	$query="";
	if(isset($_REQUEST['form']) && $_REQUEST['form']=='grid'){
		$fields=implode(",",$grid_array);
		$query="SELECT $fields FROM ".$table.$filter_str;
	}else{
      include $modif_file;
		$columns=array();
		foreach($fields as $k => $v){
			if(isset($v['custom']) && $v['custom']=='true'){
			}else{
				$columns[]=$k;
			}
		}
		$comma="";


		$fields=implode(",",$columns);
		$query="SELECT $fields FROM ".$table.$filter_str;
	}
	
	$csv_file= $table.".csv";
	db_to_csv_nr($query,$csv_file);
	return;
}

//id table mapper array
$table_of_id=array(
	'short_name'=>$GLOBALS['MOD_S_TABLES']['program']
);

//Map filter for the given id
$filter_map=array(
//	'program'=>isset($_SESSION[PAGE]['student_year'])?"student_year='".$_SESSION[PAGE]['student_year']."'":null,
);

if(isset($_REQUEST['form'])){
	switch($_REQUEST['form']){
      case 'main':
			if(isset($_REQUEST['action'])){
				switch($_REQUEST['action']){
				 case 'add':
					 //verify captcha befor adding record to the database
					if(verify_captcha('captcha')){
						//adding the record to the database
						$formgen->add_record();
						$arr	=exec_query("SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['registration']." WHERE email='".$_REQUEST['email']."'",Q_RET_ARRAY);
						$arr	=$arr[0];

						//setting registration number fot he user
						exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['registration']." set registration_no='".gen_reg_no($arr['rec_id'])."' WHERE rec_id='".strtoupper($arr['rec_id'])."'",Q_RET_NON);
							
						//If the user want to register drop him/her to the email verification procedure
						if(isset($_REQUEST['registration_type']) && $_REQUEST['registration_type']=1){
							//Generating secure random number with 32 bit length 
							include A_CLASSES."/common_crypt_class.php";
							$crypt=new Common_crypt();
							$verification_code=$crypt->secure_rand(32);
							$_REQUEST['verification_code']=$verification_code;

							//Sending email verification mail with random number and record id
							include_once MOD_CLASSES."/mail_templates_class.php";
							$templates	=new Mail_templates();
							if($templates->email_verification($arr['rec_id'],$verification_code)){
								return_status_json('OK',"Email verification sent!");
							}
							return;
						}else{
							//If the user is one time user let him/her login instantly
							exec_query("UPDATE ".$GLOBALS['MOD_S_TABLES']['registration']." SET status='TEMP' WHERE rec_id='".$arr['rec_id']."'",Q_RET_NON);
							$_SESSION['username']	=$_REQUEST['email'];
                     $_SESSION['user_id']		=$arr['rec_id'];
                     $_SESSION['email']		=$_REQUEST['email'];
							$_SESSION['downloaded'] =false;
						}
					}else{
						return_status_json('ERROR',"Invalid captchar!");
					}
					return;
				 break;
				 case 'modify':
					return $formgen->modify_record();
				 break;
				 case 'delete':
					return $formgen->delete_record();
				 break;
				 case 'combo':
					$filter_str=null;
					if(isset($filter_map[$_REQUEST['id']])){
						$filter_str=$filter_map[$_REQUEST['id']];
					}
					$formgen->xhr_filtering_select_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter_str);
				 break;
				 case 'param':
					$_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
					//exceptional cases
					/*
					switch($_REQUEST['param']){
						case 'exam_id':	
							$admission_year=exec_query("SELECT student_year FROM ".$GLOBALS['P_TABLES']['exam']." WHERE exam_id='".$_SESSION[PAGE][$_REQUEST['param']]."'",Q_RET_ARRAY);
							$_SESSION[PAGE]['student_year']=$admission_year[0]['student_year'];
						break;
					}
					 */
					return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
				 break;
				}	
			}else{
				if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
					if(isset($_REQUEST['id'])){
						$formgen->xhr_form_filler_data($_REQUEST['id']);
					}else{
						$formgen->xhr_filtering_select_data(null,null,$filter_string);
					}
				}
			}
		break;
		case 'filter':
			if(isset($_REQUEST['action'])){
				switch($_REQUEST['action']){
				 case 'add':
					return $formgen->add_filter($GLOBALS['MOD_S_TABLES']['filter']);
				 break;
				 case 'modify':
					return $formgen->modify_filter($GLOBALS['MOD_S_TABLES']['filter']);
				 break;
				 case 'delete':
					return $formgen->delete_filter($GLOBALS['MOD_S_TABLES']['filter']);
				 break;

				}	
			}else{
				if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
					if(isset($_REQUEST['id'])){
						$formgen->xhr_filter_filler_data($_REQUEST['id']);
					}else{
						$filter_string.="table_name='".$table."'";
						$formgen->xhr_filtering_select_data($GLOBALS['MOD_S_TABLES']['filter'],'filter_name',$filter_string);
					}

				}
			}
		break;
		case 'grid':
			//$json_url=$formgen->gen_json($grid_array_long,$filter_string,false);
			echo $formgen->gen_data_grid($grid_array_long,null,$key1);
			$formgen->filter_selector();
		break;
		case 'select_filter':
			$formgen->xhr_filtering_select_data($GLOBALS['MOD_S_TABLES']['filter'],'filter_name',"table_name='".$table."'");
		break;
	}
}else{
	echo "<table width=100%><tr><td style='vertical-align:top;valign:top'>";
	if($GLOBALS['LAYOUT'] == 'pub'){
		echo "<h3>Donor/Funder registration form</h3>";
		echo $formgen->gen_form(false,false);
	}else{
		echo $formgen->gen_form(true,true);
		echo $formgen->gen_filter();
	}
	echo "
		<script language='javascript'>
			function grid(){
				url='".gen_url()."&form=grid';
				open(url,'_self');
			}
		</script>
	";
	//$json_url=$formgen->gen_json($grid_array,$filter_string,false,null);
if($GLOBALS['LAYOUT'] != 'pub'){
	echo "</td><td width=40% style='vertical-align:top;valign:top'>";
	echo $formgen->gen_data_grid($grid_array,null,$key1);
	echo "</td></tr></table>";
}else{
		echo "</td><td width=40% style='vertical-align:top;valign:top;'>";
		echo "<img src='".IMG."/help_32.png'>";
		echo "<h3>Guidelines for the Donors/Funder </h3>";
		echo "
			<h4>Register with us</h4>
<p>			If you are willing to do further donations/fund rising in future, please register with us so that it is possible to maintain a history of your donations/fundings as well as we can maintain a closer relationship with you. </p>
			<hr style='border:1px solid silver;'/>

			<h4>Pay as a one time user</h4>
<p>			If you are paying as a onetime user you will only have to fill a few field of the form and straightaway you will be able to do the donation. Even though you are not registered with us you will receive an invoice for the payment and if you want to do more donation/funding in future, there is no restriction.</p>
			<hr style='border:1px solid silver;'/>

			<h4>Pay online</h4>
<p>			You can py online using your credit/debit card using our online payment system. After completing the procedure you will redirected to the banks payment gateway. There you can enter your credit card details and complete the transaction.</p>
			<hr style='border:1px solid silver;'/>

			<h4>Pay offline</h4>
		<p>It is also possible to pay offilien using our offline payment voucher. When your are following the offline payment method you will have to download a pdf of the payment voucher and follow the instructions given in relevant location in the middle of the procedure. </p>
			";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Technical assistance</h4>For technical assistance please write to <br/> <img src='".IMG."/uis_mail.png'>";
		echo "</td></tr></table>";
		echo "<br/><br/><br/><div align='right' class='buttonBar' id='buttonBar'  >";
		if(isset($_SESSION['user_id'])){
			echo "<button dojoType='dijit.form.Button' type='submit' name='loginBtn' onClick=\"this_submit_form('modify')\">Next&nbsp;&raquo;</button>";
		}else{
			echo "<button dojoType='dijit.form.Button' type='submit' name='loginBtn' onClick=\"this_submit_form('add')\">Next&nbsp;&raquo;</button>";
		}
		echo "</div>";
}
$formgen->filter_selector();

//generate help tips 
include $help_file;
$formgen->set_help_tips($help_array);
echo "<script language='javascript'>";
echo $formgen->param_setter();
echo "</script>";
}

?>

<script language='javascript'>
//If the user want to register with us direct him to email_verification page else direct him to donation type selection page
function this_submit_form(action){
	if(registration_type.checked){
		submit_form(action,'donations','email_verification');
	}else{
		submit_form(action,'donations','donation_to');
	}
}

</script>
