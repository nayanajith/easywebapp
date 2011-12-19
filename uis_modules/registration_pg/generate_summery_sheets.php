<?php
if( isset($_REQUEST['generate']) && $_REQUEST['generate'] == 'true' ){
include(MOD_CLASSES.'/summery_list_pdf_class.php');

$date			='20-02-2011';
$duration	='10.00am-12.00noon';

$attendance_sheets=new Summery_list($date,$duration);
$query="SELECT * FROM ".$GLOBALS['P_TABLES']['exam_hall']."";

if($_REQUEST['center'] != 'all' && $_REQUEST['center'] != '' ){
	$query="SELECT * FROM ".$GLOBALS['P_TABLES']['exam_hall']." WHERE center='".$_REQUEST['center']."'";
}

$res=exec_query($query,Q_RET_MYSQL_RES);

$attendance_sheets->generate_summery_sheets($res);

/*Acquire pdf document*/
$pdf=$attendance_sheets->getPdf();

$pdf_file=A_RPT."/".$_SESSION['user_id']."-summery_list.pdf"; 

/*Close and save PDF document*/
$pdf->Output($pdf_file, 'F');

echo W_RPT."/".$_SESSION['user_id']."-summery_list.pdf";

}else{
?>

<div 	dojoType="dijit.form.Form" 
		name='frm_attendance' 
		id='frm_attendance' 
		jsId='frm_attendance'
		encType='multipart/form-data'
		action='<?php echo $GLOBALS['PAGE_GEN']; ?>';
		method='GET'>

	<script type="text/javascript" type="dojo/method" event="onSubmit">
	return true;
	</script>
Select Center<select name="center"  dojoType="dijit.form.ComboBox" > 
<option value='all'>all</option>
		<?php 
			$query="SELECT distinct center_id,center FROM ".$GLOBALS['P_TABLES']['exam_hall'];
			$arr=exec_query($query,Q_RET_ARRAY);
			foreach($arr as $row){
				echo "<option value='".$row['center']."'>".$row['center']."</option>";
			}
		?>
</select>
</div>
<div dojoType="dijit.ProgressBar" style="width:300px" jsId="jsProgress"
       id="downloadProgress" maximum="10">
</div>
<div id='lnk'>

</div>

<script type="text/javascript" >
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
