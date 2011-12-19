<?php
include "login.php";

//Required login  to access this area
if ($GLOBALS['LAYOUT'] != 'app' && !isset($_SESSION['username'])){
	return;
}

//include A_CLASSES."/data_entry_pub_class.php";
include A_CLASSES."/data_entry_class.php";
$super_table		='convocation_reg';
$keys					=array('reg_no');
$key1					='reg_no';
$grid_array			=array('reg_no','index_no');
$grid_array_long	=array('reg_no','pay_offline_status','pay_online_status');
$table				=$GLOBALS['MOD_S_TABLES'][$super_table];
$formgen				=null;

if($GLOBALS['LAYOUT'] == 'pub'){
	$formgen 			= new Formgenerator($table,$keys,$super_table,strtoupper($_SESSION['username']));
}else{
	$formgen 			= new Formgenerator($table,$keys,$super_table,null);
}


$help_file			=$super_table."_help.php";
$modif_file			=$super_table."_modif.php";
$filter_string		="";


/*Extract filter according to the filter_id in request string*/
if(isset($_REQUEST['filter_name']) && $_REQUEST['filter_name'] != ''){
	$filter_string=$formgen->ret_filter($_REQUEST['filter_name'],$GLOBALS['MOD_S_TABLES']['filter']);
}

/*generate csv with column headers*/
if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
	$filter_str=$filter_string!=""?" WHERE ".$filter_string:"";
   include $modif_file;
	$columns=array_keys($fields);
	$comma="";


	$fields=implode(",",$columns);
	$query="SELECT $fields FROM ".$table.$filter_str;
	
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
					return $formgen->add_record();
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
					return $formgen->add_filter();
				 break;
				 case 'modify':
					return $formgen->modify_filter();
				 break;
				 case 'delete':
					return $formgen->delete_filter();
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
	if($GLOBALS['LAYOUT'] == 'pub' ){
		echo "<h3>Convocation registration form</h3>";
		echo $formgen->gen_form(true,false);
	}else{
		echo $formgen->gen_form(true,true);
	}
	echo $formgen->gen_filter();
	echo "
		<script type="text/javascript" >
			function grid(){
				url='".gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:"")."&form=grid';
				open(url,'_self');
			}
		</script>
	";
echo "</td><td width=40% style='vertical-align:top;valign:top;border-left:1px dotted silver;padding:5px;'>";
	if($GLOBALS['LAYOUT'] == 'pub' ){
		/*
		echo "<h4>Declaration form</h4>You can download Delcration form [<a href=''>pdf</a>]";
		echo "<h4>Convocation information</h4>You can download convocation information letter [<a href=''>pdf</a>]";
		echo "<hr style='border:1px solid silver;'>";
		 */
		echo "<img src='".IMG."/help_32.png'>";
		echo "<h4>Help on typing sinhala and tamil</h4>You can get assistance to enter your name in Sinhala and Tamil from our <a href='http://ucsc.lk/ltrl/services/feconverter/' target='_blank' >font converter</a> or <a href='http://translate.google.com/#en|ta|' >google translator</a>";
		/*
		echo "<h4>Enable sinhala in windows</h4>Download and install sinhala packa from ltrl [<a href='http://www.ucsc.cmb.ac.lk/ltrl/?page=downloads' target='_blank'>link</a>]";
		echo "<h4>Enable sinhala in linux</h4>A guide on how to enable sinhala in linux [<a href='http://sinhala.sourceforge.net/' target='_blank'>link</a>]";
		*/
		echo "<hr style='border:1px solid silver;'>";
		echo "<h4>Further information</h4>For any queries regarding convocation registration please contact the examination division of the UCSC<br >
			   <b>TP: 0112588996/97</b><br><br>";
		echo "<hr style='border:1px solid silver;'>";
		echo "<h4>Technical assistance</h4>For technical assistance please write to <br> <img src='".IMG."/uis_mail.png'>";
		echo "</td></tr></table>";

		echo "<br><br><br><div align='right' class='buttonBar'  >
		<button dojoType='dijit.form.Button' type='submit' onClick=\"submit_form('modify','registration','payment')\">Next&nbsp;&raquo;</button>
		</div>";

	}else{
		echo $formgen->gen_data_grid($grid_array,null,$key1);
		echo "</td></tr></table>";
	}


$formgen->filter_selector();

//generate help tips 
include $help_file;
$formgen->set_help_tips($help_array);
/*
echo "<script type="text/javascript" >";
echo $formgen->param_setter();
echo "</script>";
*/
}
?>
