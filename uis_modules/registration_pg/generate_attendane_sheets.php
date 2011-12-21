<?php
if( isset($_REQUEST['generate']) && $_REQUEST['generate'] == 'true' ){
include(MOD_CLASSES.'/attendance_pdf_class.php');

$date			='10-09-2011';
$duration	='10.00am-12.00noon';

$attendance_sheets=new Attendance_sheets($date,$duration);
$query="SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['exam_hall']."";

if($_REQUEST['center'] != 'all' && $_REQUEST['center'] != '' ){
	$query="SELECT * FROM ".$GLOBALS['MOD_S_TABLES']['exam_hall']." WHERE center='".$_REQUEST['center']."'";
}

$res=exec_query($query,Q_RET_MYSQL_RES);

$attendance_sheets->generate_attendance_sheets($res);

/*Acquire pdf document*/
$pdf=$attendance_sheets->getPdf();

$pdf_file=A_RPT."/".$_SESSION['user_id']."-attendance-tmp.pdf"; 

/*Close and save PDF document*/
$pdf->Output($pdf_file, 'F');

echo W_RPT."/".$_SESSION['user_id']."-attendance-tmp.pdf";

}else{
?>

<div 	dojoType="dijit.form.Form" 
		name='frm_attendance' 
		id='frm_attendance' 
		jsId='frm_attendance'
		encType='multipart/form-data'
		action='<?php echo $GLOBALS['PAGE_GEN']; ?>';
		method='GET'>

	<script type='text/javascript' type="dojo/method" event="onSubmit">
	return true;
	</script>
<select name="center"  dojoType="dijit.form.ComboBox" > 
<option value='all'>all</option>
		<?php 
			$query="SELECT distinct center_id,center FROM ".$GLOBALS['MOD_S_TABLES']['exam_hall'];
			$arr=exec_query($query,Q_RET_ARRAY);
			foreach($arr as $row){
			echo "<option value='".$row['center']."'>".$row['center']."</option>";
			/*
			 echo '<input  name="'.$row['center'].'" dojoType="dijit.form.CheckBox" value="1" ></input>
				    <label for="mycheck">'.$row['center'].'</label><br>';
			 */
			}
		?>
</select>
</div>

<div dojoType="dijit.ProgressBar" style="width:300px" jsId="jsProgress"
       id="downloadProgress" maximum="100">
</div>
<div id='lnk'>

</div>

<script type='text/javascript' >
function submit_form(action){
	if(!confirm(dojo.toJson(dijit.byId('frm_attendance').getValues(), true))){
		return;	
	}

	jsProgress.update({maximum: 100, progress: 25 });
	if (dijit.byId('frm_attendance').validate()) {
		dojo.xhrGet({
		url			: '<?php echo gen_url(); ?>&generate=true&data=true', 
		handleAs		: 'text',
		form			: 'frm_attendance', 

		handle: function(response){
			jsProgress.update({maximum: 100, progress: 100 });
			var LNK=document.getElementById('lnk');
			LNK.innerHTML="<a href='"+response+"'>Download pdf</a>";
		},

		load: function(response) {
			jsProgress.update({maximum: 100, progress: 50 });
		}, 
		error: function() {
			alert('Error on submission');
		}
	});
		return false;
	}else{
		alert('Form contains invalid data.  Please correct first');
		return false;
	}
	return true;
}

</script>

<?php
}
?>
