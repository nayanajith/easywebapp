<?php
$program_inner="";
$arr=exec_query('SELECT * FROM '.$GLOBALS['MOD_P_TABLES']['course'],Q_RET_ARRAY,null,'course_id');

foreach($arr as $course_id =>  $info){
   $program_inner.="<option value='$course_id'>".$info['title']."</option>";
}


/*Auto generated by form_gen.php*/
$fields=array(
	
"batch_id"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
      "type"=>"hidden",
		"label"=>"Batch id",
		"label_pos"=>"top",
		"value"=>""),	
"course_id"=>array(
  		"length"=>"140",
		"dojoType"=>"dijit.form.Select",
		"required"=>"true",
		"label"=>"Course",
		"label_pos"=>"top",
      "inner"=>$program_inner,
		"value"=>""),	


/*
"course_id"=>array(
      "onChange"=>'set_param("course_id",this.value)',
      "searchAttr"=>"course_id",
      "store"=>"course_id_store",
      "pageSize"=>"10",

		"length"=>"140",
		"dojoType"=>"dijit.form.FilteringSelect",
		"required"=>"true",
		"label"=>"Course code",
		"label_pos"=>"top",
		"value"=>""),	
 */
"description"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Description",
		"label_pos"=>"top",
		"value"=>""),	
"seats"=>array(
		"length"=>"50",
		"dojoType"=>"dijit.form.NumberTextBox",
		"required"=>"true",
		"label"=>"Seats",
		"label_pos"=>"top",
		"value"=>""),	
"start_date"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.DateTextBox",
		"required"=>"true",
		"label"=>"Start date",
		"label_pos"=>"top",
		"value"=>""),	
"end_date"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.DateTextBox",
		"required"=>"true",
		"label"=>"End date",
		"label_pos"=>"top",
		"value"=>""),	
/*
"start_time"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.TimeTextBox",
		"required"=>"true",
		"label"=>"Start time",
		"label_pos"=>"top",
		"value"=>""),	
"end_time"=>array(
		"length"=>"100",
		"dojoType"=>"dijit.form.TimeTextBox",
		"required"=>"true",
		"label"=>"End time",
		"label_pos"=>"top",
		"value"=>""),	
 */
"venue"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"true",
		"label"=>"Venue",
		"label_pos"=>"top",
		"value"=>""),	
"disabled"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.CheckBox",
		"required"=>"false",
		"label"=>"Disabled",
		"label_pos"=>"right",
		"value"=>""),	
/*
"deleted"=>array(
		"length"=>"70",
		"dojoType"=>"dijit.form.CheckBox",
		"required"=>"false",
		"label"=>"Deleted",
		"label_pos"=>"right",
		"value"=>""),	
 */
"note"=>array(
		"length"=>"350",
		"dojoType"=>"dijit.form.ValidationTextBox",
		"required"=>"false",
		"label"=>"Note",
		"label_pos"=>"top",
		"value"=>"")	
);
?>
