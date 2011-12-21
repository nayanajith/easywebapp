<?php
include A_CLASSES."/data_entry_class.php";
$super_table		='enroll';
$keys					=array('enroll_id');
$key1					='enroll_id';
$grid_array			=array('enroll_id','batch_id','registration_no','payment_status');
$grid_array_long	=array('enroll_id','batch_id','registration_no','payment_method','payment_status');

$table				=$GLOBALS['MOD_P_TABLES'][$super_table];
$formgen 			=null;

$file_name        ="manage_enrollment";

$formgen 		   = new Formgenerator($table,$keys,$file_name,null);

$help_file			=$file_name."_help.php";
$modif_file			=$file_name."_modif.php";
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
                $_REQUEST['batch_id']=$_REQUEST['course_id'].'-'.$_REQUEST['start_date'];
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
						$formgen->xhr_filtering_select_data();
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
						$formgen->xhr_filtering_select_data($GLOBALS['MOD_P_TABLES']['filter'],'filter_id',true);
					}

				}
			}
		break;
		case 'grid':
			echo $formgen->gen_data_grid($grid_array,null,$key1);
		break;
	}
}else{
echo "<table width=100%><tr><td style='vertical-align:top;valign:top'>";
	echo $formgen->gen_form(null,true);
	echo $formgen->gen_filter();
	echo "
		<script type='text/javascript' >
			function grid(){
            url='".gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:"")."&form=grid';
				open(url,'_self');
			}
		</script>
	";
echo "</td><td width=40% style='vertical-align:top;valign:top'>";
   echo $formgen->gen_data_grid($grid_array,null,$key1);
echo "</td></tr></table>";

$formgen->filter_selector();

//generate help tips 
include $help_file;
$formgen->set_help_tips($help_array);

}


?>
