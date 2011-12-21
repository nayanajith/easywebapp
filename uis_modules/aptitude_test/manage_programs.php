<?php
include A_CLASSES."/data_entry_class.php";
include MOD_CORE."/database_schema.php";	
$formgen = new Formgenerator('program','short_name');

if(isset($_REQUEST['form'])){
	switch($_REQUEST['form']){
      case 'main':
			if(isset($_REQUEST['action'])){
				switch($_REQUEST['action']){
				 case 'add':
					if($formgen->add_record()){
						create_aptitude_test_tables($_REQUEST['table_prefix']);
					}
				 break;
				 case 'modify':
					return $formgen->modify_record();
				 break;
				 case 'delete':
					if($formgen->delete_record()){
						//drop_aptitude_test_tables($_REQUEST['table_prefix']);
					}
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
						$formgen->xhr_filtering_select_data($GLOBALS['P_TABLES']['filter'],'filter_id',true);
					}

				}
			}
		break;
		case 'grid':
			$json_url=$formgen->gen_json(array('short_name','full_name','degree'),"  ",false);
			echo $formgen->gen_data_grid(array('short_name','full_name','degree'),$json_url);
		break;
	}
}else{
	echo $formgen->gen_form();
	echo $formgen->gen_filter();
	echo "
		<script type='text/javascript' >
			function grid(){
				url='".gen_url()."&form=grid';
				open(url,'_self');
			}
		</script>
	";
}


?>
