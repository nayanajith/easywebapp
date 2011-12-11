<?php
include A_CLASSES."/data_entry_class.php";
$super_table		='st_reg';
$keys					=array('student_id');
$key1					='student_id';
$grid_array			=array('student_id','email','NIC','first_name','last_name');

$table				=$GLOBALS['MOD_P_TABLES']['student'];

$formgen 			=null;
if(isset($_SESSION['user_id'])){
	$formgen 		= new Formgenerator($table,$keys,$super_table,$_SESSION['user_id']);
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

if(isset($_REQUEST['form'])){
	switch($_REQUEST['form']){
      case 'main':
			if(isset($_REQUEST['action'])){
				switch($_REQUEST['action']){
				 case 'add':
					if($formgen->add_record()){
						$_SESSION['username']	=$_REQUEST['email'];
                  $_SESSION['password']	=$_REQUEST['NIC'];
                  $_SESSION['fullname']	=$_REQUEST['last_name'];
                  
                  $table = "student";
                  $query = "SELECT * FROM ".$table." WHERE NIC = '". $_SESSION['password']."'" ;
                  $res = exec_query($query,Q_RET_MYSQL_RES);
                  $student = mysql_fetch_array($res);

                  $_SESSION['user_id']		=$student['student_id'];
						$_SESSION['first_time'] =true;
					}
					
				$ind = $_SESSION['user_id'];
				while(strlen($ind) < 5){
				$ind = '0'.$ind;
				}
				$ind = $ind.date('y');
				$ind = $ind.($ind%5);
				
                  $table = "student";
                  $query = "UPDATE ".$table." SET index_no = '".$ind."' WHERE NIC = '". $_SESSION['password']."'" ;
                  $res = exec_query($query,Q_RET_MYSQL_RES);				

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
		echo "<h3>Student Registration form</h3>";
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
	echo "</td><td width=50% style='vertical-align:top;valign:top'>";
	echo $formgen->gen_data_grid($grid_array,null,$key1);
	echo "</td></tr></table>";
}else{
		echo "</td><td width=40% style='vertical-align:top;valign:top;'>";
		echo "<img src='".IMG."/help_32.png'>";
		echo "<h4>Registration procedure </h4>";
		echo "<ol>
			<li>Fill the Registration form and press <b>Next</b> button at the end of the form</li>
			<li>Once you have been registered into the system you will be able to apply for the available courses</li>
			</ol>";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Further information</h4>For any queries regarding student registration please contact <br/>Academic & Publications branch UCSC<br />
			   <b>TP: 0112589123</b><br/><br/>";
		echo "<hr style='border:1px solid silver;'/>";
		echo "<h4>Technical assistance</h4>For technical assistance please write to <br/> <img src='".IMG."/uis_mail.png'>";
		echo "</td></tr></table>";


	if(isset($_SESSION['user_id'])){
		echo "<br/><br/><br/><div align='right' class='buttonBar'  >
		<button dojoType='dijit.form.Button' type='submit' name='loginBtn' onClick=\"submit_form('modify','".MODULE."','personal')\">Next&nbsp;&raquo;</button>
		</div>";
	}else{
		echo "<br/><br/><br/><div align='right' class='buttonBar'  >
		<button dojoType='dijit.form.Button' type='submit' name='loginBtn' onClick=\"submit_form('add','".MODULE."','personal')\">Next&nbsp;&raquo;</button>
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
