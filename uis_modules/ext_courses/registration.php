<?php
/*
if(!isset($_SESSION['username'])){
	echo "<h3>Postgraduate online application closed!</h3>";
	return;
}
 */
include A_CLASSES."/data_entry_class.php";
$super_table		='student';
$keys					=array('registration_no');
$key1					='registration_no';
$grid_array			=array('registration_no','NIC','email_1');
$grid_array_long	=array('registration_no','NIC','first_name','status');

$table				=$GLOBALS['MOD_P_TABLES'][$super_table];
$file_name        =PAGE;

$formgen 			=null;
if(isset($_SESSION['user_id']) ){
	$formgen 		= new Formgenerator($table,$keys,$file_name,$_SESSION['user_id']);
}else{
	$formgen 		= new Formgenerator($table,$keys,$file_name,null);
}
$help_file			=$file_name."_help.php";
$modif_file			=$file_name."_modif.php";
$filter_string		="";

/*Extract filter according to the filter_id in request string*/
if(isset($_REQUEST['filter_name']) && $_REQUEST['filter_name'] != ''){
	$filter_string=$formgen->ret_filter($_REQUEST['filter_name'],$GLOBALS['MOD_P_TABLES']['filter']);
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

if(isset($_REQUEST['form'])){
	switch($_REQUEST['form']){
      case 'main':
			if(isset($_REQUEST['action'])){
				switch($_REQUEST['action']){
				 case 'add':
					if($formgen->add_record()){
						$_SESSION['username']	=$_REQUEST['email_1'];
                  $_SESSION['password']	=$_REQUEST['NIC'];
                  $_SESSION['fullname']	=$_REQUEST['last_name'];
                  $_SESSION['user_id']		=$_REQUEST['rec_id'];
						$_SESSION['first_time'] =true;
						$_SESSION['downloaded'] =false;
						$_SESSION['course_id'] =$_REQUEST['program'];
					}
					return;
				 break;
				 case 'modify':
					return $formgen->modify_record();
				 break;
				 case 'delete':
					return $formgen->delete_record();
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
					return $formgen->add_filter($GLOBALS['MOD_P_TABLES']['filter']);
				 break;
				 case 'modify':
					return $formgen->modify_filter($GLOBALS['MOD_P_TABLES']['filter']);
				 break;
				 case 'delete':
					return $formgen->delete_filter($GLOBALS['MOD_P_TABLES']['filter']);
				 break;

				}	
			}else{
				if(isset($_REQUEST['data'])&&$_REQUEST['data']=='json'){
					if(isset($_REQUEST['id'])){
						$formgen->xhr_filter_filler_data($_REQUEST['id']);
					}else{
						$filter_string.="table_name='".$table."'";
						$formgen->xhr_filtering_select_data($GLOBALS['MOD_P_TABLES']['filter'],'filter_name',$filter_string);
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
			$formgen->xhr_filtering_select_data($GLOBALS['MOD_P_TABLES']['filter'],'filter_name',"table_name='".$table."'");
		break;
	}
}else{
	echo "<table width=100%><tr><td style='vertical-align:top;valign:top'>";
	if($GLOBALS['LAYOUT'] == 'pub'){
		echo "<h3>Short term course registration form</h3>";
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
   if(!isset($_SESSION['user_id'])){
		echo "</td><td width=40% style='vertical-align:top;valign:top;'>";
		echo "<img src='".IMG."/help_32.png'>";
      echo "<h4>Short term courses</h4>
<p>You can find more information about the available short term courses <a href='http://ucsc.lk/training'>here</a></p>
";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Registration procedure </h4>";
		echo "<ol>
			<li>Fill the application form and press <b>Next</b> button at the end of the form</li>
			<!--li>Download the generated application(pdf) and print it</li-->
			<li>Choose the preferred payment procedure online or offline and do the payment accordingly</li>
			<li>If you use online payment method, the payment invoice will be sent to your personal email.</li>
         <li>If the online payment is successful we will reserve a seat for you</li>
			<li>If you choose offline payment method, you have to download the voucher quadruples(pdf) and print it. Follow the procedure given in offline payment page. Finally you can scan and email, fax, post or handover the voucher stamped by the bank to the UCSC.</li>
         <li>When the payment voucher received to us we will reserve a seat for you. You can <a href=\"javascript:open_page('ext_courses','login')\">login</a> to the system and check for the status.</li>
			</ol>";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Postal address</h4>";
		echo "<pre style='font:inherit'><p>
Coordinator,
Computing Services Centre,
University of Colombo School of Computing,
No. 35, Reid Avenue,
Colomobo 07.</p></pre>";
		echo "<hr style='border:1px solid silver;'/>";
      echo "<h4>Further information</h4><p>
         For any queries regarding short term course registration please contact Computing Services Centre:
         <pre style='font:inherit'><p>
Tel: 0112158910 / 0112158911 / 0112581245

Fax: 0112587235

Email: <img  style='valign:bottom' src='".IMG."/csc_mail.png'></p>
         </pre></p>";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Technical assistance</h4><p>For technical assistance please write to <br/> <img src='".IMG."/uis_mail.png'></p>";
		echo "</td></tr></table>";


		echo "<br/><br/><br/><div align='right' class='buttonBar' style='border:0px;' >
		<button dojoType='dijit.form.Button' type='submit' name='loginBtn' onClick=\"submit_form('add','ext_courses','available_courses')\">Next&nbsp;&raquo;</button>
		</div>";
	}else{
		echo "</td></tr></table>";
		echo "<br/><br/><br/><div align='right' class='buttonBar'  >
		<button dojoType='dijit.form.Button' type='submit' name='loginBtn' onClick=\"submit_form('modify','ext_courses','registration')\">Modify</button>
		</div>";

	}
}

$formgen->filter_selector();

//generate help tips 
include $help_file;
$formgen->set_help_tips($help_array);
/*
echo "<script language='javascript'>";
echo $formgen->param_setter();
echo "</script>";
*/
}

?>
