<?php 
include A_CLASSES."/data_entry_class.php";

//add_table_prefix($program_table_schemas,"");
//echo create_tables($program_table_schemas);

$table				=$GLOBALS['MOD_P_TABLES']['reg'];
$file					='manage_confirmations';
$key1					='reg_id';

$grid_array			=array('reg_id','session_id','student_id','status','payment_id');
$grid_array_long	=array('reg_id','session_id','student_id','status');

$formgen 			= new Formgenerator($table,$key1,$file);
$filter_string		="";

if(isset($_REQUEST['data']) && $_REQUEST['data']=='csv'){
	$filter_str=$filter_string!=""?" WHERE ".$filter_string:"";
   include $file."_modif.php";
	$columns=array_keys($fields);
	
	$fields=implode(",",$columns);
	//$query="SELECT $headers FROM ".$table." UNION SELECT $fields FROM ".$table." ".$filter_str;
	$query="SELECT $fields FROM ".$table.$filter_str;
	
	$csv_file= $table.".csv";
	db_to_csv_nr($query,$csv_file);
	return;
}

	if($_REQUEST['form'] == 'main'){
 //     case 'main':
			if(isset($_REQUEST['action'])){
				switch($_REQUEST['action']){
				 case 'add':
					return $formgen->add_record();
				 break;
				 case 'modify':
					return $formgen->modify_record();
				 break;
				 case 'delete':
					return $formgen->delete_record(true);
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
//		break;
      

	
}else{
//echo "<table width=100%><tr><td style='vertical-align:top'>";
   echo "<table height = 100%  style = 'vertical-align:top '> <tr><td valign = 'top' >" ;
	echo $formgen->gen_form(false,true);
	echo "</td><td width = 100% valign = 'top'>";
   //$json_url=$formgen->gen_json($grid_array,'',true,'course');
   //echo  $json_url;
   echo $formgen->gen_data_grid($grid_array,null,$key1);
	echo "</td></tr></table>";

	//$url = gen_json($grid_array,null,false);
   //echo gen_data_grid($grid_array,$url,$key=null);
}
?>
