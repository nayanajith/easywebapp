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
   'eligibility'     =>'%seligibility',
   'course'          =>'%scourse',
   'exam'            =>'%sexam',        
   'rubric'          =>'%srubric',        
   'paper'           =>'%spaper',        
   'gpa'             =>'%sgpa',         
   'log'             =>'%slog',         
   'filter'          =>'%sfilter',         
   'marks'           =>'%smarks',       
   'marks_stat'      =>'%smarks_stat',       
   'student'         =>'%sstudent',
   'student_state'   =>'%sstudent_state',
   'batch'           =>'%sbatch',
   'grades'          =>'%sgrades'
);     

$GLOBALS['P_TABLES']=$program_tables;
$GLOBALS['MOD_S_TABLES']=$system_tables;

/*add module name as a prefix to the table name (this is static)*/
add_table_prefix($GLOBALS["P_TABLES"],$GLOBALS['PROGRAMS'][PROGRAM]);

?>
