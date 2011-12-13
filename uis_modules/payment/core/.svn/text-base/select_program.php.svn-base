<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/

/*
Generate sub_programs array to be listed
*/
//$res=exec_query("SELECT short_name,table_prefix FROM ".$system_tables['program'],Q_RET_ARRAY);
$res=exec_query("SELECT short_name,table_prefix FROM ".$GLOBALS["S_TABLES"]['program'],Q_RET_ARRAY);
/*No sub_programs added yet*/
if(!is_array($res)){
	//exit();
}

$sub_programs=array();
foreach($res as $arr){
	$sub_programs[$arr['short_name']]=$arr['table_prefix'];
}
$GLOBALS['SUB_PROGRAMS']=$sub_programs;

function sub_program_select($sub_program){
	d_r('dijit.form.FilteringSelect');
	echo "
	<script>

	function change_sub_program(sub_program,desc){
		URL=\"".gen_url()."&sub_program=\"+desc;
		if(confirm('Press OK to confirm scheme change to '+desc)){
			open(URL,'_self');
		}	
	}
	</script>
	";


	echo "<select dojoType='dijit.form.FilteringSelect' 
	style='width:100px;'
	onChange='change_sub_program(this.get(\"value\"),this.get(\"displayedValue\"))'>\n";

	foreach($GLOBALS['SUB_PROGRAMS'] as $key => $value){
		if($sub_program == $key){
			echo "<option value='$value' selected=true>$key</option>\n";
		}else{
			echo "<option value='$value' >$key</option>\n";
		}
	}
	echo "</select>\n";
}


function gen_sub_program_tables($sub_program){
	foreach($GLOBALS['P_TABLES'] as $table => $p_table){
		if(isset($GLOBALS['SUB_PROGRAMS'][$sub_program])){
			$GLOBALS['P_TABLES'][$table]=$GLOBALS['SUB_PROGRAMS'][$sub_program].$p_table;
		}
	}
}

?>
