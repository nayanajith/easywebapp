<?php
include A_CLASSES."/data_entry_class.php";
$super_table		='banks';
$keys					=array('bank_name');
$key1					='bank_name';
$grid_array			=array('bank_id','bank_name');
$grid_array_long	=array('bank_id','bank_name');

$table				=$GLOBALS['MOD_S_TABLES'][$super_table];
$formgen 			= new Formgenerator($table,$keys,$super_table,null);
$help_file			=$super_table."_help.php";
$modif_file			=$super_table."_modif.php";
$filter_string		="";



/*Extract filter according to the filter_id in request string*/
if(isset($_REQUEST['filter_name']) && $_REQUEST['filter_name'] != ''){
	$filter_string=$formgen->ret_filter($_REQUEST['filter_name']);
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
	echo $formgen->gen_form(false,true);
	echo $formgen->gen_filter();
	echo "
		<script type="text/javascript" >
			function grid(){
				url='".gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:"")."&form=grid';
				open(url,'_self');
			}
		</script>
	";
echo "</td><td width=40% style='vertical-align:top;valign:top'>";
	//$json_url=$formgen->gen_json($grid_array,$filter_string,false,null);
	echo $formgen->gen_data_grid($grid_array,null,$key1);
echo "</td></tr></table>";
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

