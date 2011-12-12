<?php

$menu_array   = array(
	"manage_config"				=>"Configure",
	"manage_courses"				=>"Courses",
	"manage_batches"				=>"Batches",
//	"registration"					=>array("PAGE"=>"Application","VISIBLE"=>"false"),
	"admission"						=>array("PAGE"=>"Admission Card","VISIBLE"=>"false"),
//	"payment"						=>array("PAGE"=>"Payment","VISIBLE"=>"false"),
	"login"							=>"Login"


//	"reports2" =>array("PAGE"=>"Reports2","VISIBLE"=>"false"),
//	"pay_online" =>array("PAGE"=>"Pay Online","VISIBLE"=>"false"),
//	"pay_offiline" =>array("PAGE"=>"Pay Offline","VISIBLE"=>"false"),
//	"offline_voucher" =>array("PAGE"=>"Generate voucher","VISIBLE"=>"false"),
//	"callback" =>array("PAGE"=>"callback","VISIBLE"=>"false")
);
//hide application and login link after login
if(isset($_SESSION['user_id'])){
	$menu_array["login"]=array("PAGE"=>"Login","VISIBLE"=>"false");
	$menu_array["admission"]=array("PAGE"=>"Admission Card","VISIBLE"=>"true");
}else{
	//Payment link only show after login
	$menu_array["registration"]=array("PAGE"=>"Application","VISIBLE"=>"false");
	$menu_array["payment"]=array("PAGE"=>"Payment","VISIBLE"=>"false");
}

//Visible application window after login if the users is a system user
if(isset($_SESSION['loged_module']) && $_SESSION['loged_module']=='home'){
	$menu_array["registration"]=array("PAGE"=>"Application","VISIBLE"=>"true");
	$menu_array["payment"]=array("PAGE"=>"Payment","VISIBLE"=>"false");
	$menu_array["login"]=array("PAGE"=>"Login","VISIBLE"=>"false");
	$admin_menu=array(
		"reports"						=>"Reports",
		"manage_hall"              =>"Manage Hall",
		"student_hall_allocation"  =>"Allocate Students",
		"generate_admissions"      =>"Generate Admissions",
		"generate_attendane_sheets"=>"Attendance Sheets",
		"generate_index_sheets"    =>"Index Sheets",
		"generate_index_stickers"  =>"Index Stickers",
		"generate_summery_sheets"  =>"Summery Sheets",
		"post_processing"          =>"Post Processing"
	);
	$menu_array=array_merge($menu_array,$admin_menu);
}

$toolbar	=array(
	"manage_config"		=>array(
		'Regenerate database'		=>array('icon'=>'Table','action'=>'submit_form("generate_db")')
	),
	"manage_courses"		=>array(
		'Add'		=>array('icon'=>'NewPage','action'=>'submit_form("add")'),
		'Save'	=>array('icon'=>'Save','action'=>'submit_form("modify")'),
		'Delete'	=>array('icon'=>'Delete','action'=>'submit_form("delete")'),
		'Grid'	=>array('icon'=>'Table','action'=>'grid()'),
		'Print'	=>array('icon'=>'Print')
	),
	"registration"		=>array(
		'Add'		=>array('icon'=>'NewPage','action'=>'submit_form("add")'),
		'Save'	=>array('icon'=>'Save','action'=>'submit_form("modify")'),
		'Delete'	=>array('icon'=>'Delete','action'=>'submit_form("delete")'),
		'Grid'	=>array('icon'=>'Table','action'=>'grid()'),
		'Print'	=>array('icon'=>'Print'),
		'CSV'		=>array('icon'=>'Database','action'=>'get_csv()'),
		'Add Filter'	=>array('icon'=>'Filter','action'=>'show_dialog()','label'=>'Add Filter')
	),
	"manage_hall"		=>array(
		'Add'		=>array('icon'=>'NewPage','action'=>'submit_form("add")'),
		'Save'	=>array('icon'=>'Save','action'=>'submit_form("modify")'),
		'Delete'	=>array('icon'=>'Delete','action'=>'submit_form("delete")'),
		'Search'	=>array('icon'=>'Search'),
		'CSV'		=>array('icon'=>'Database','action'=>'get_csv()'),
		'Grid'	=>array('icon'=>'Table','action'=>'grid()'),
		'Print'	=>array('icon'=>'Print'),
		'Add Filter'	=>array('icon'=>'Filter','action'=>'show_dialog()','dojoType'=>'dijit.form.Button','label'=>'Add Filter')
	),

	"post_processing"		=>array(
		'Add'		=>array('icon'=>'NewPage','action'=>'submit_form("add")'),
		'Save'	=>array('icon'=>'Save','action'=>'submit_form("modify")'),
		'Delete'	=>array('icon'=>'Delete','action'=>'submit_form("delete")'),
		'Search'	=>array('icon'=>'Search'),
		'CSV'		=>array('icon'=>'Database','action'=>'get_csv()'),
		'Grid'	=>array('icon'=>'Table','action'=>'grid()'),
		'Print'	=>array('icon'=>'Print'),
		'Add Filter'	=>array('icon'=>'Filter','action'=>'show_dialog()','dojoType'=>'dijit.form.Button','label'=>'Add Filter')
	),


	"student_hall_allocation"		=>array(
		'Start'	=>array('icon'=>'Function','action'=>'submit_form()','dojoType'=>'dijit.form.Button','label'=>'Start')
	),

	"generate_admissions"		=>array(
		'Start'	=>array('icon'=>'Function','action'=>'submit_form()','dojoType'=>'dijit.form.Button','label'=>'Start')
	),
	"generate_attendane_sheets"	=>array(
			"generate"=>array('icon'=>'Function','action'=>'submit_form()','label'=>'Generate Attendanc')
	),
	"generate_index_sheets"	=>array(
			"generate"=>array('icon'=>'Function','action'=>'submit_form()','label'=>'Generate Index List')
	),
	"generate_index_stickers"	=>array(
			"generate"=>array('icon'=>'Function','action'=>'submit_form()','label'=>'Generate Index Sticker List')
	),
	"generate_summery_sheets"	=>array(
			"generate"=>array('icon'=>'Function','action'=>'submit_form()','label'=>'Generate Summery Sheet')
	)

);


?>
