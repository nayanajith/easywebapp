<?php
/*
This will include in to $GLOBALS['PAGE_GEN']
*/
/*
Tables for the program
*/
//include A_CORE."/database.php";

$program_tables=array(
	'course'					=>'%scourse',
	'reg' 					=>'%sreg',
	'student'				=>'%sstudent',			
	'schedule'           =>'%sschedule'
	
);     

/*
Tables of the system
*/
$GLOBALS['MOD_P_TABLES']=$program_tables;
add_table_prefix($GLOBALS["MOD_P_TABLES"],MODULE);

?>
