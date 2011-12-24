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
         case 'print':
         case 'html':
            transcript(true);
         break;
         case 'pdf':
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
   echo "<div><div id='transcpt_frm' jsId='transcpt_frm' dojoType='dijit.form.Form' >";
   if(isset($_SESSION[PAGE]['index_no'])){
      transcript($html=true);
   }   
   echo "</div></div>";

   echo "<script type='text/javascript'>";
   echo "dojo.addOnLoad(function() {";

   //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
   //$xhr_combobox->gen_xhr_combobox('student_year',"Student Year",$xhr_combobox->get_val('student_year'),30,20,null,null);
   //$item_array=array('FORMAT_L','FORMAT_P');
   //$xhr_combobox->gen_xhr_static_combo('transcpt_format','Format',$xhr_combobox->get_val('transcpt_format'),100,$item_array,null,null);

   $xhr_combobox->gen_xhr_combobox('index_no',"Index No",$xhr_combobox->get_val('index_no'),80,20,array('index_no'),'transcpt_frm');

   //If selected note will be printed
   $item_array=array('SUBJECT_TO_APPROVED_BY_THE_SENNATE','PENDING');
   $xhr_combobox->gen_xhr_static_combo('transcpt_note','Note',$xhr_combobox->get_val('transcpt_note'),210,$item_array,array('index_no','with_marks'),'transcpt_frm');

   $item_array=array('A4','LEGAL');
   $xhr_combobox->gen_xhr_static_combo('paper','Paper type',$xhr_combobox->get_val('paper','LEGAL'),60,$item_array,array('index_no','paper'),'transcpt_frm');

   $item_array=array(2,2.1,2.2,2.3,2.4,2.5,2.6,2.7,2.8,2.9,3);
   $xhr_combobox->gen_xhr_static_combo('zoom_factor','Zoom factor',$xhr_combobox->get_val('zoom_factor','2.3'),40,$item_array,array('index_no','with_marks','zoom_factor'),'transcpt_frm');

   //If selected print the report with marks
   $xhr_combobox->gen_checkbox('with_marks','With marks',$xhr_combobox->get_val('with_marks'),null,null);

   //If selected print the report with rank
   $xhr_combobox->gen_checkbox('with_rank','Show rank',$xhr_combobox->get_val('with_rank'),null,null);

   $xhr_combobox->param_setter();$xhr_combobox->html_requester();
   echo "});";
   $xhr_combobox->form_submitter('transcpt_frm');
   echo "</script>";
}



/*
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
 */


//Pdf version of transcript will be returend  in selected format
function transcript($html=false){
	//get next transaction for the day for the program
   $trans_id=1;
   $next=exec_query("SELECT MAX(rec_id)+1 next FROM ".$GLOBALS['P_TABLES']['transcript'],Q_RET_ARRAY);
   if($next){
	   $trans_id=$next[0]['next'];
   }

   $user_id_len=3;
   $user_id=$_SESSION['user_id'];
   for($j=$user_id_len;$j>=strlen($user_id);$j--){
      $user_id='0'.$user_id;
   }

   $transcpt_id=gen_reg_no($trans_id).'-'.$user_id.'-'.date('dm');


   if(isset($_SESSION[PAGE]['transcpt_format'])&&$_SESSION[PAGE]['transcpt_format']=='FORMAT_L'){
      include(MOD_CLASSES."/transcript1_pdf_class.php");
   }else{
      include(MOD_CLASSES."/transcript2_pdf_class.php");
   }

   //Generate the transcript
   $with_marks =false;
   $with_rank  =false;
   $note       =null;
   $awards     =null;
   $paper      ='LEGAL';

   if(isset($_SESSION[PAGE]['with_marks'])){
      $with_marks=$_SESSION[PAGE]['with_marks'];
   }

   if(isset($_SESSION[PAGE]['with_rank'])){
      $with_rank=style_text($_SESSION[PAGE]['with_rank']);
   }

   if(isset($_SESSION[PAGE]['transcpt_note'])){
      $note=style_text($_SESSION[PAGE]['transcpt_note']);
   }

   if(isset($_SESSION[PAGE]['note'])){
      $awards=style_text($_SESSION[PAGE]['note']);
   }

   if(isset($_SESSION[PAGE]['awards'])){
      $awards=style_text($_SESSION[PAGE]['awards']);
   }

   if(isset($_SESSION[PAGE]['paper'])){
      $paper=style_text($_SESSION[PAGE]['paper']);
   }

   $transcript=new Transcript($_SESSION[PAGE]['index_no'],$transcpt_id,$with_marks,$with_rank,$note,$awards,$paper);

   if($html){
      //Acquire html document
      echo "<div style='font-size:70%;'>";
      $transcript->getHTML();
      echo "</div>";
   }else{
      //Acquire pdf document
      $pdf=$transcript->getPDF();

      $pdf->Output($_SESSION[PAGE]['index_no'].'-transcript.pdf', 'I');
      //$pdf->Output(TMP."/".$_SESSION[PAGE]['index_no']."_transcript.pdf", 'F');
      //return $pdf_file;
   }
}

?>
