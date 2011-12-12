<?php
/*
This will include in to $GLOBALS["PAGE_GEN"]
*/
/*
Tables for the program
*/
/*
$GLOBALS["MOD_P_TABLES"]=array(
	"payment"			=>"%spayment",
   "filter"				=>"%sfilter"
);
     
array_walk($GLOBALS["MOD_P_TABLES"],'add_prefix',MODULE."_%s_");
 */
/*
Tables of the system
*/
$GLOBALS["MOD_P_TABLES"]=array(
	"course"	      =>"%scourse",          
	"batch"	      =>"%sbatch",          
	"student"	   =>"%sstudent",          
   "filter"		   =>"%sfilter",
	"registration" =>"%sregistration",
	"enroll"       =>"%senroll",
   /*
	"validation"   =>"%svalidation",
	"exam_hall"    =>"%sexam_hall",
	"student_alloc"=>"%sstudent_alloc",
	"post_processing"=>"%spost_processing"
    */
);


/*add module name as a prefix to the table name (this is static)*/

add_table_prefix($GLOBALS["MOD_P_TABLES"],MODULE);

/*TODO: just a hack fix this*/
//$GLOBALS["MOD_P_TABLES"]=$GLOBALS["MOD_P_TABLES"];
?>
