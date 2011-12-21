<?php
/*
Tables for the program
*/
$system_tables=array(
   'program'         =>'program'
);

/*
Tables for the program
*/
$program_tables=array(
   'eligibility'         =>'%seligibility',
   'course'               =>'%scourse',
   'course_reg'         =>'%scourse_reg',
   'exam'               =>'%sexam',        
   'rubric'               =>'%srubric',        
   'paper'               =>'%spaper',        
   'push'               =>'%spush',        
   'gpa'                  =>'%sgpa',         
   'gpa2'                  =>'%sgpa2',         
   'log'                  =>'%slog',         
   'filter'               =>'%sfilter',         
   'marks'               =>'%smarks',       
   'student'            =>'%sstudent',
   'course_selection'   =>'%scourse_selection',
   'state'               =>'%sstate',
   'batch'               =>'%sbatch',
   'mcq_marking_logic'   =>'%smcq_marking_logic',
   'staff'               =>'%sstaff'
);     

$GLOBALS['P_TABLES']=$program_tables;
$GLOBALS['MOD_S_TABLES']=$system_tables;

/*add module name as a prefix to the table name (this is static)*/
add_table_prefix($GLOBALS["P_TABLES"],$GLOBALS['PROGRAMS'][PROGRAM]);

?>
