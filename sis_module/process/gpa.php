<?php
//include A_CLASSES."/data_entry_class.php";
include A_CLASSES."/xhr_combobox_class.php";
$xhr_combobox=new XHR_Combobox();


//id table mapper array
$table_of_id=array(
   'batch_id'=>$GLOBALS['P_TABLES']['batch'],
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
         case 'gen':
            gen_gpa();
         break;
         case 'html':
            $_SESSION[PAGE]['batch_id']=$_REQUEST['batch_id'];
            print_gpa();
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
   }
}else{
   echo "<div align='center'><div id='gpa_frm' jsId='gpa_frm' dojoType='dijit.form.Form' >";
   if(isset($_SESSION[PAGE]['batch_id'])){
      print_gpa();
   }   
   echo "</div></div>";

   echo "<script type='text/javascript'>";
   echo "dojo.addOnLoad(function() {";

   //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
   //$xhr_combobox->gen_xhr_combobox('student_year',"Student Year",$xhr_combobox->get_val('student_year'),30,20,null,null);
   $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,array('batch_id'),'gpa_frm');
   echo "
   var reload_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Reload',
      onClick:function(){request_html('gpa_frm',new Array('batch_id'),null);},
   });
   toolbar.addChild(reload_button);";

   $xhr_combobox->param_setter();$xhr_combobox->html_requester();
   echo "});";
   $xhr_combobox->form_submitter('gpa_frm');
   echo "</script>";
}

function gen_gpa(){
   global $classes;
   include A_CLASSES."/student_class.php";
   $arr=exec_query("SELECT index_no FROM ".$GLOBALS['P_TABLES']['student']." WHERE index_no LIKE '".$_SESSION[PAGE]['batch_id']."%'",Q_RET_ARRAY,null,'index_no');
   foreach(array_keys($arr) as $index_no){
      $student = new Student($index_no);
      //Students will has another row for degree gpa if he have done repeat courses and his gpa is less han pass gpa
      if($student->getCGPA()!=$student->getDGPA() && $student->getCGPA() < $classes['p']){
         $row=array(
            'index_no'=>$index_no,
            'degree_class'=>"D",
            'GPV1'=>round($student->getYearDGPV(1),4),
            'credits1'=>$student->getYearCredits(1),
            'GPA1'=>round($student->getYearDGPA(1),4),
            'GPV2'=>round($student->getYearDGPV(2),4),
            'credits2'=>$student->getYearCredits(2),
            'GPA2'=>round($student->getYearDGPA(2),4),
            'GPV3'=>round($student->getYearDGPV(3),4),
            'credits3'=>$student->getYearCredits(3),
            'GPA3'=>round($student->getYearDGPA(3),4),
            'GPV4'=>round($student->getYearDGPV(4),4),
            'credits4'=>$student->getYearCredits(4),
            'GPA4'=>round($student->getYearDGPA(4),4),
            'GPV'=>round($student->getDGPV(),4),
            'GPA'=>round($student->getDGPA(),4),
            'credits'=>$student->getTotalCredits(),
         );
         exec_query("REPLACE INTO ".$GLOBALS['P_TABLES']['gpa']."(".implode(array_keys($row),",").")values('".implode(array_values($row),"','")."')",Q_RET_NON);
      }
      $row=array(
         'index_no'=>$index_no,
         'degree_class'=>"C",
         'GPV1'=>round($student->getYearCGPV(1),4),
         'credits1'=>$student->getYearCredits(1),
         'GPA1'=>round($student->getYearCGPA(1),4),
         'GPV2'=>round($student->getYearCGPV(2),4),
         'credits2'=>$student->getYearCredits(2),
         'GPA2'=>round($student->getYearCGPA(2),4),
         'GPV3'=>round($student->getYearCGPV(3),4),
         'credits3'=>$student->getYearCredits(3),
         'GPA3'=>round($student->getYearCGPA(3),4),
         'GPV4'=>round($student->getYearCGPV(4),4),
         'credits4'=>$student->getYearCredits(4),
         'GPA4'=>round($student->getYearCGPA(4),4),
         'GPV'=>round($student->getCGPV(),4),
         'GPA'=>round($student->getCGPA(),4),
         'credits'=>$student->getTotalCredits(),
      );
      exec_query("REPLACE INTO ".$GLOBALS['P_TABLES']['gpa']."(".implode(array_keys($row),",").")values('".implode(array_values($row),"','")."')",Q_RET_NON);
   }
   return_status_json('OK','GPA calculated successfully for the batch-'.$_SESSION[PAGE]['batch_id']);
}

function print_gpa(){
   $row=array(
      'index_no',
      'degree_class',
      'GPV1',
      'credits1',
      'GPA1',
      'GPV2',
      'credits2',
      'GPA2',
      'GPV3',
      'credits3',
      'GPA3',
      'GPV4',
      'credits4',
      'GPA4',
      'GPV',
      'GPA',
      'credits',
   );
   $arr=exec_query("SELECT ".implode($row,",")." FROM ".$GLOBALS['P_TABLES']['gpa']." WHERE index_no LIKE '".$_SESSION[PAGE]['batch_id']."%'",Q_RET_ARRAY);
   $report= "<h3 class='coolh'>GPA of the students in batch ".$_SESSION[PAGE]['batch_id']."</h3>";
   $report.= "<table class='clean' border=1>";
   $report.= "<tr><th>".implode($row,"</th><th>")."</th></tr>";
   foreach($arr as $row){
      $report.= "<tr><td>".implode(array_values($row),"</td><td>")."</td></tr>";
   }
   $report.= "</table>";
   echo $report;
}

?>
