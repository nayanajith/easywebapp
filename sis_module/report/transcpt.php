<?php
//include A_CLASSES."/data_entry_class.php";
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();


//id table mapper array
$table_of_id=array(
   'index_no'=>$GLOBALS['P_TABLES']['student'],
);

//Map filter for the given id
$filter_map=array(
);



//Request functoin switcher
if(isset($_REQUEST['form'])){
   switch($_REQUEST['form']){
      case 'main':
      if(isset($_REQUEST['action'])){
         switch($_REQUEST['action']){
         case 'transcript':
            transcript();
         break;
         case 'pdf':
            transcript_pdf();
         break;
         case 'cert':
            gen_certificate();
         break;
         case 'print':
            transcript();
         break;
         case 'html':
            $_SESSION[PAGE]['index_no']=$_REQUEST['index_no'];
            transcript();
         break;
         case 'store':
            $filter=null;
            if(isset($filter_map[$_REQUEST['id']])){
               $filter=$filter_map[$_REQUEST['id']];
            }
            $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter);
         break;
         case 'param':
            $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];
            return_status_json('OK',"Set ".$_REQUEST['param']."=".$_REQUEST[$_REQUEST['param']]);
         break;
         }
      }
      case 'filter':
      break;
   }
}else{
   echo "<div><div id='attendance_frm' jsId='attendance_frm' dojoType='dijit.form.Form' >";
   if(isset($_SESSION[PAGE]['index_no'])){
      transcript();
   }   
   echo "</div></div>";

   echo "<script type='text/javascript'>";
   echo "dojo.addOnLoad(function() {";

   //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
   //$xhr_combobox->gen_xhr_combobox('student_year',"Student Year",$xhr_combobox->get_val('student_year'),30,20,null,null);
   $item_array=array('FORMAT_L','FORMAT_P');
   $xhr_combobox->gen_xhr_static_combo('transcpt_format','Format',$xhr_combobox->get_val('transcpt_format'),100,$item_array,null,null);

   $xhr_combobox->gen_xhr_combobox('index_no',"Index No",$xhr_combobox->get_val('index_no'),80,20,array('index_no'),'attendance_frm');
   //Different report types to be select
   $item_array=array('WITH_MARKS','WITHOUT_MARKS');
   $xhr_combobox->gen_xhr_static_combo('transcpt_marks','Marks',$xhr_combobox->get_val('transcpt_marks'),120,$item_array,array('index_no','transcpt_marks'),'attendance_frm');

   $item_array=array('SUBJECT_TO_APPROVED_BY_THE_SENNATE','PENDING');
   $xhr_combobox->gen_xhr_static_combo('transcpt_note','Note',$xhr_combobox->get_val('transcpt_note'),210,$item_array,array('index_no','transcpt_marks'),'attendance_frm');

   $xhr_combobox->param_setter();$xhr_combobox->html_requester();
   echo "});";
   $xhr_combobox->form_submitter('attendance_frm');
   echo "</script>";
}



function transcript(){
   include A_CLASSES."/student_class.php";
   $student = new Student($_SESSION[PAGE]['index_no']);
   $trancpt_detail=$student->getTranscript();

   //total information other than subject breakdown to be printed in transcript
   $transcript=array(
      'index_no'   =>$_SESSION[PAGE]['index_no'],
      'fullname'   =>$student->getName(2),
      'RegNo'      =>$student->getRegNo(),
      'DIssue'     =>date("Y-m-d"),
      'dgrad'      =>$trancpt_detail['YOA'],
      'dreg'       =>$trancpt_detail['DOA'],
      'DegreeName' =>$trancpt_detail['DEGREE'],
      'DClass'     =>$trancpt_detail['CLASS'],
      'GPA'        =>$trancpt_detail['GPA']
   );

   //print students personal information
   echo "<table>";
   foreach( $transcript as $key => $value){
      echo "<tr><th align=left>$key</th><td>$value</td></tr>";   
   }
   echo "</table>";

   //Print studetns socoring on each course in each year
   for($i=1;$i<=4;$i++){
      if($student->getYearCGPV($i)<=0)continue;
      echo "<h2>Year-".$i."</h2>";
      echo "Year GPA: ".round($student->getYearCGPV($i)/$student->getYearCredits($i),2);
      echo "<table>";
      foreach($student->getYearMarks($i) as $key => $course ){
         echo "<tr>";
         foreach($course as $key => $value){
            echo "<td>$value</td>";   
         }
         echo "</tr>";
      }
      echo "</table>";
   }
}

function transcript_id(){
	//get next transaction for the day for the program
	$next_arr=exec_query("SELECT count(*)+1 next FROM ".$GLOBALS['P_TABLES']['transcript']." WHERE DATE(init_time)=CURRENT_DATE()",Q_RET_ARRAY);
	$trans_id=$next_arr[0]['next'];

	/*current date in 25032011 format*/
	$date				=date("dmy");
	
	$composite_no='';

	/*Fill zeros for program_id*/
	/*
	for($j=$this->program_id_digits;$j>strlen($program_id);$j--){
		$composite_no.='0';
	}
	$composite_no.=$program_id;
	*/

	/*Fill zeros for transaction_id*/
   /*
	for($j=$this->trans_id_digits;$j>strlen($trans_id);$j--){
		$composite_no.='0';
	}
	$composite_no.=$trans_id;
	$composite_no.=$date;
    */

   /*
	$check=0;
	foreach(str_split($composite_no) as $digit){
		$check+=(int)$digit;
	}
	$check=($check%$this->modulus);
    */
	/*Complete transaction id*/
	//return $program_code.$this->code_seperator.$pay_for_code.$this->code_seperator.$tp_ref_id.$this->code_seperator.$composite_no.$check;
	//return $program_code.$this->code_seperator.$pay_for_code.$this->code_seperator.$tp_ref_id.$this->code_seperator.$composite_no;

}

//Pdf version of transcript will be returend  in selected format
function transcript_pdf(){
   if(isset($_SESSION[PAGE]['transcpt_format'])&&$_SESSION[PAGE]['transcpt_format']=='FORMAT_L'){
      include(MOD_CLASSES."/transcript1_pdf_class.php");
   }else{
      include(MOD_CLASSES."/transcript2_pdf_class.php");
   }

   //Generate the transcript
   $with_marks =false;
   $note       ="";
   if(isset($_SESSION[PAGE]['transcpt_marks']) && $_SESSION[PAGE]['transcpt_marks'] == 'WITH_MARKS'){
      $with_marks=true;
   }

   if(isset($_SESSION[PAGE]['transcpt_note'])){
      $note=$_SESSION[PAGE]['transcpt_note'];
   }

   $transcript=new Transcript($_SESSION[PAGE]['index_no'],$with_marks,style_text($note));

   //Acquire pdf document
   $pdf=$transcript->getPdf();

   $pdf->Output($_SESSION[PAGE]['index_no'].'-transcript.pdf', 'I');
   //$pdf->Output(TMP."/".$_SESSION[PAGE]['index_no']."_transcript.pdf", 'F');
   //return $pdf_file;
}

function gen_certificate(){
   echo "cert";
}
?>
