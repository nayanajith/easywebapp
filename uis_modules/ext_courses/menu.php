<?php

$menu_array   = array(
	"login"					=>"Login",
	"registration"			=>"Registration",
	"available_courses"	=>array("PAGE"=>"Available Courses","VISIBLE"=>"false"),
	"payment"				=>array("PAGE"=>"Payments","VISIBLE"=>"false"),
	"pay_online"         =>array("PAGE"=>"Pay Online","VISIBLE"=>"false"),
	"pay_offiline"       =>array("PAGE"=>"Pay Offline","VISIBLE"=>"false"),
	"offline_voucher"    =>array("PAGE"=>"Generate voucher","VISIBLE"=>"false"),
	"callback"           =>array("PAGE"=>"callback","VISIBLE"=>"false")
);
//hide application and login link after login
if(isset($_SESSION['user_id'])){
	$menu_array["login"]=array("PAGE"=>"Login","VISIBLE"=>"false");
	$menu_array["available_courses"]	=array("PAGE"=>"Available Courses","VISIBLE"=>"true");
   //Disapear registration link after login
	$menu_array["registration"]="My Profile";
}

//Visible application window after login if the users is a system user
if(isset($_SESSION['loged_module']) && $_SESSION['loged_module']=='home'){
	$menu_array["registration"]=array("PAGE"=>"Students","VISIBLE"=>"true");
	$menu_array["payment"]=array("PAGE"=>"Payment","VISIBLE"=>"false");
	$menu_array["login"]=array("PAGE"=>"Login","VISIBLE"=>"false");
	$admin_menu=array(
	   "manage_courses"				=>"Courses",
	   "manage_batches"				=>"Batches",
		"manage_enrollment"			=>"Enrollments",
		"reports"						=>"Reports",
	   "manage_config"				=>"Configure",
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
	"manage_batches"		=>array(
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
	
);


?>
