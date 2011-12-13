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
$GLOBALS["MOD_S_TABLES"]=array(
	"program"	=>"%sprogram",          
	"pay_for"	=>"%spay_for",
	"banks"		=>"%sbanks",          
	"employee"	=>"%semployee",
	"payment"	=>"%spayment",
   "filter"		=>"%sfilter"
);


/*add module name as a prefix to the table name (this is static)*/
add_table_prefix($GLOBALS["MOD_S_TABLES"],MODULE);


/*TODO: just a hack fix this*/
$GLOBALS["MOD_P_TABLES"]=$GLOBALS["MOD_S_TABLES"];
?>
