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
   'eligibility'        =>'%seligibility',
   'course'             =>'%scourse',
   'exam'               =>'%sexam',        
   'rubric'             =>'%srubric',        
   'paper'              =>'%spaper',        
   'push'               =>'%spush',        
   'gpa'                =>'%sgpa',         
   'gpa2'               =>'%sgpa2',         
   'log'                =>'%slog',         
   'filter'             =>'%sfilter',         
   'marks'              =>'%smarks',       
   'marks_stat'         =>'%smarks_stat',       
   'transcript'         =>'%stranscript',       
   'student'            =>'%sstudent',
   'course_selection'   =>'%scourse_selection',
   'state'              =>'%sstate',
   'batch'              =>'%sbatch',
   'staff'              =>'%sstaff',
   'mcq_paper'          =>'%smcq_paper',
   'mcq_answers'        =>'%smcq_answers',
   'mcq_marking_logic'  =>'%smcq_marking_logic',
   'mcq_marks'          =>'%smcq_marks',
   'grades'             =>'%sgrades'
);     

$GLOBALS['P_TABLES']=$program_tables;
$GLOBALS['MOD_S_TABLES']=$system_tables;

/*add module name as a prefix to the table name (this is static)*/
add_table_prefix($GLOBALS["P_TABLES"],PROGRAM);


?>
