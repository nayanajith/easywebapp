<?php
if( isset($_REQUEST['generate']) && $_REQUEST['generate'] == 'true' ){
include(MOD_CLASSES.'/admission_pdf_class.php');

$date			='10th September 2011';
$start		='10.00am';
$end			='12.00noon';
$releaf		='30';
$issued_on	='2nd September 2011';
$admission_dir	= MOD_A_ROOT."/admissions";
$append_pdfs	= MOD_A_ROOT."/pdf/rules.pdf ".MOD_A_ROOT."/pdf/instructions.pdf ".MOD_A_ROOT."/pdf/sample.pdf";

/*MySQL connection*/
//include "db.php";

/*Get all index numbers form bict_validation table*/
$query="SELECT registration_no FROM ".$GLOBALS['MOD_S_TABLES']['validation']." WHERE validation=1 AND admission_generated=0";
$res=exec_query($query,Q_RET_MYSQL_RES);

while($row=mysql_fetch_assoc($res)){
	echo "Generating admission for ".$row['registration_no']." ...<br>\n";
	$tmp_pdf		="/tmp/".$row['registration_no']."-tmp.pdf";
	$final_pdf	=$admission_dir."/".$row['registration_no'].".pdf";

	/*start new admissionn card to the student*/
	$admission=new Admission($row['registration_no'],$date,$start,$end,$releaf,$issued_on);

	/*insert student info into the pdf*/
	$admission->add_student_info();

	/*Acquire pdf document*/
	$pdf=$admission->getPdf();

	/*Close and save PDF document*/
	$pdf->Output($final_pdf, 'F');
	log_msg('generated',$row['registration_no']);
	//$pdf->Output($tmp_pdf, 'F');
	/*
	$cmd="/usr/bin/gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=$final_pdf $tmp_pdf $append_pdfs";
	$out=exec($cmd);
	unlink($tmp_pdf);
	*/

	/*Set admission generated true*/
	$query="UPDATE ".$GLOBALS['MOD_S_TABLES']['validation']." SET admission_generated=1 WHERE registration_no='".$row['registration_no']."'";
	$bla=exec_query($query,Q_RET_MYSQL_RES);
	//exit();
}
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
