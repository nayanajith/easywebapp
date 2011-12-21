<?php
if( isset($_REQUEST['generate']) && $_REQUEST['generate'] == 'true' ){
/*this will help when generating the check digit*/
$modulus=5;
/*length of the exam No.*/
$exam_no_length=7;
/*composition of the examination no*/
/*
 1  2  3  4
[1][2][3][1]
1. center code 1~5
2. room No. 
3. sequence no for the center starting from given base
4. check digit
*/

$center_id_digits	=1;
$hall_id_digits	=2;
$student_id_digits=4;
$exam_no="%s%s%s%s";

/*Starting number of the indexes*/
$base_num=000;

/*exam cneters -> code -> no of students*/
$exam_centers=array();

/*generate the index numbers*/
function gen_exam_no($center_id,$hall_id,$student_id){
	global $center_id_digits;
	global $hall_id_digits;
	global $student_id_digits;
	global $modulus;

	$exam_no='';
	/*Fill zeros for center_id*/
	for($j=$center_id_digits;$j>strlen($center_id);$j--){
		$exam_no.='0';
	}
	$exam_no.=$center_id;
	/*Fill zeros for center_id*/
	for($j=$hall_id_digits;$j>strlen($hall_id);$j--){
		$exam_no.='0';
	}
	$exam_no.=$hall_id;
	/*Fill zeros for student id*/
	for($j=$student_id_digits;$j>strlen($student_id);$j--){
		$exam_no.='0';
	}
	$exam_no.=$student_id;

	$check=0;
	foreach(str_split($exam_no) as $digit){
		$check+=(int)$digit;
	}
	$check=($check%$modulus);
	return $exam_no.$check;
}

$query="SELECT DISTINCT(center),center_id FROM ".$GLOBALS['P_TABLES']['exam_hall'];
$hall_res=exec_query($query,Q_RET_MYSQL_RES);
while($hall_row=mysql_fetch_assoc($hall_res)){
	$exam_centers[$hall_row['center']]=$hall_row['center_id'];
}

$first_run			=true;
$query="SELECT sutdents_allocated FROM ".$GLOBALS['P_TABLES']['exam_hall']." WHERE sutdents_allocated != 0";
$first_run_res=exec_query($query,Q_RET_MYSQL_RES);
if(mysql_num_rows($first_run_res) >= 1){
	$first_run		=false;
}

	
foreach($exam_centers as $center => $center_id){
	echo "CENTER:".$center."</br>";
	$query="SELECT * FROM ".$GLOBALS['P_TABLES']['exam_hall']." WHERE center_id = '$center_id' ORDER BY ABS(hall_id)";
	$res=exec_query($query,Q_RET_MYSQL_RES);

/*
	*/
	$affected_students=0;
	$students_for_hall=0;
	while($row=mysql_fetch_assoc($res)){
		/*Total number can placed in a hall*/
		$students_for_hall	=$row['no_of_rooms']*$row['sutdents_per_room'];

		/*No of student already allocated*/
		$students_allocated	=$row['sutdents_allocated'];

		/*If no space left in the hall continue with next rows*/
		if($students_allocated >= $students_for_hall){
			echo "hall ".$row['hall']." filled!<br>";
			continue;	
		}


		$hall_id=$row['hall_id'];

		/*Romms starts with 1 not 0*/
		$room_no=1;

		echo "HALL:".$row['hall']."</br>";

		/*Select valid students in given range*/
		if($first_run){
			$query="SELECT * FROM ".$GLOBALS['P_TABLES']['user_info']." u, ".$GLOBALS['P_TABLES']['validation']." v WHERE u.index_no=v.index_no AND u.exam_center='$center' AND v.validation=1 ORDER BY ABS(u.surname_2),ABS(u.surname) LIMIT $affected_students,$students_for_hall";
		}else{
			$query="SELECT * FROM ".$GLOBALS['P_TABLES']['user_info']." u, ".$GLOBALS['P_TABLES']['validation']." v WHERE u.index_no=v.index_no AND u.exam_center='$center' AND v.validation=1 AND v.hall_allocated=0 ORDER BY ABS(u.surname_2),ABS(u.surname) LIMIT $affected_students,$students_for_hall";
		}

		$students_res=exec_query($query,Q_RET_MYSQL_RES);

		/*No of students return from the query*/
		$q_ret_students=mysql_num_rows($students_res);

		/*exam number / index number starts with 1 not 0*/
		$sequence=$students_allocated+1;

		/*Generat exam number for the student and allocate the student to a specific room*/
		while($student_row=mysql_fetch_assoc($students_res)){
			/*check whether hall already allocated and skip*/
			$query_hall_allocated="SELECT hall_allocated FROM ".$GLOBALS['P_TABLES']['validation']." WHERE index_no='".$student_row['index_no']."'";
			$res_hall_allocated=exec_query($query_hall_allocated,Q_RET_MYSQL_RES);
			$row_hall_allocated=mysql_fetch_assoc($res_hall_allocated);
			if($row_hall_allocated['hall_allocated'] == 1){
				echo "Already added!<br>";
				continue;	
			}
			/*Find room number*/
			for($k=1;$k<=$row['no_of_rooms'];$k++){
				if($sequence>($k-1)*$row['sutdents_per_room'] && $sequence<$k*$row['sutdents_per_room'] ){
					$room_no=$k;	
				}	
			}

			echo "ROOM:".$room_no."<br>";
			echo $student_row['index_no']."|".gen_exam_no($center_id,$hall_id,$sequence)."|".$student_row['surname']."<br>";	

			/*Generate exam_no and Alocate students*/
			$query_alloc="INSERT INTO ".$GLOBALS['P_TABLES']['student_alloc']."(index_no,exam_no,hall_id,room_no,center_id) values('".$student_row['index_no']."','".gen_exam_no($center_id,$hall_id,$sequence)."','".$hall_id."','".$room_no."','".$center_id."')";
			$student_alloc_res=exec_query($query_alloc,Q_RET_MYSQL_RES);

			/*Verify insertion of the data*/
			if(get_affected_rows()>=1){
				/*If insertion is ok make  hall_allocated=1 in bict_validation*/
				$query_hall_allocated="UPDATE ".$GLOBALS['P_TABLES']['validation']." SET hall_allocated=1 WHERE index_no='".$student_row['index_no']."'";
				$bla=exec_query($query_hall_allocated,Q_RET_MYSQL_RES);
				/*if insertion is ok setn new number of student allocated in each hall*/
				$set_allocated="UPDATE ".$GLOBALS['P_TABLES']['exam_hall']." SET sutdents_allocated='$sequence' WHERE hall_id='".$hall_id."' AND center_id='".$center_id."'";
				$bla=exec_query($set_allocated,Q_RET_MYSQL_RES);
				echo "added...<br>";	
			}
			$sequence++;
		}
		/*Increase the affected students and provide new starting point for LIMIT in query*/
		$affected_students+=$q_ret_students;
		echo "AFFECTED:".$affected_students."<br>";
	}
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

	<script type='text/javascript' type="dojo/method" event="onSubmit">
	return true;
	</script>
<select name="center"  dojoType="dijit.form.ComboBox" > 
<option value='all'>all</option>
		<?php 
			$query="SELECT distinct center_id,center FROM ".$GLOBALS['P_TABLES']['exam_hall'];
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
