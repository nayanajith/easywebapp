<?php
include A_CLASSES."/xhr_combobox_class.php";
include_once(A_CLASSES."/student_class.php");
$xhr_combobox=new XHR_Combobox();

/*
   Student registraton form generator
*/
function gen_push_list(){
   //Batch id must set to function further
   if(isset($_SESSION[PAGE]['batch_id'])){
   }else{
      return;
   }

   //Some index numbers are already tried and faild to push thay are removed from processing
   $index_filter='';   
   if(isset($_SESSION[PAGE]['tried_indexs']) && sizeof($_SESSION[PAGE]['tried_indexs']) > 0){
      $index_filter="AND index_no NOT IN('".implode($_SESSION[PAGE]['tried_indexs'],"','")."')";   
   }

   //The renge which is feasible to push  1.97 -> 2 likewise
   $range         =0.04;


   //Classes array is sed from student_class.php file
   //$classes=array("P"=>2,"2L"=>3,"2U"=>3.25,"1"=>3.5);
   global $classes;
   $report         = "<tr><th>Index No</th><th>GPA-D</th><th>GPA-C</th><th>Push to</th><th>Try to push</th></tr>";

   //Valid index numbers should be extracted to generate gpa with
   $arr            =exec_query("SELECT index_no FROM ".$GLOBALS['P_TABLES']['student']." WHERE batch_id='".$_SESSION[PAGE]['batch_id']."' AND status NOT IN('TRANSFERED','CANCELED','GRADUATED','BANNED') $index_filter",Q_RET_ARRAY,null,'index_no');

   //Try to push the gpa-d if the student is close to pass and try to push gpa-c if the student is close to a class 
   //gpa-d > gpa-c 
   //total scores of repeated courses is considered when pushing a degree and 10 marks will be provided totally 
   //only c grades will be considered when calculating for a class push and only provide 5 marks to push

   foreach(array_keys($arr) as $index_no){
      $student   =new Student($index_no);
      $gpad      =round($student->getDGPA(),2);
      $gpac      =round($student->getCGPA(),2);
      $push_to   =null;

      if( $classes['P']-$range < $gpad && $gpad < $classes['P']){
         $push_to   ='PASS';
      }elseif( $classes['2L']-$range < $gpac && $gpac < $classes['2L']){
         $push_to   ='2nd LOWER';
      }elseif( $classes['2U']-$range < $gpac && $gpac < $classes['2U']){
         $push_to   ='2nd UPPER';
      }elseif( $classes['1']-$range < $gpac && $gpac < $classes['1']){
         $push_to   ='FIRST CLASS';
      }

      if(!is_null($push_to)){
         $report.= '<tr><td>'.$index_no.'</td><td>'.$gpad.'</td><td>'.$gpac.'</td><td>'.$push_to.'</td><td align="center"><input type="radio" name="index_no" value="'.$index_no.'" ></td></tr>';
      }
   }

   echo "<h3>List of students which are in feasible push range</h3>";
   echo "<p>Select one of the student to be pushed and press 'Try to push' button.</p>";
   echo "<table border='1' style='border-collapse:collapse'>";
   echo $report;
   echo "</table>";
}

//Try to push the given index number
function try_to_push(){
   if(!isset($_REQUEST['index_no'])){
      echo "Index number not set!";
      return;
   }
   include_once(MOD_CLASSES."/student_push_class.php");
   $student_push = new Student_push($_REQUEST['index_no']);
   $push_arr=$student_push->push();
   if(!is_null($push_arr)){
      //Array ( [solution] => Array ( [0] => SCS3005;2011-01-01:3:1;3;1.5;57;60;C+;B- [1] => SCS3006;2011-01-01:3:1;1;0.75;69;70;B;B+ ) [gpv_push] => 279.75;282 [gpa_push] => 2.98;3 [class_push] => P;2L )
      echo "<h3>Student total score card with pushing information</h3>";
      echo "<p>The possible pushing criteria is given below and all the score information is listed further down. Please check the validity and press 'Save' button to save the changes to the student.</p>";
      echo "<h3>Student ".$_REQUEST['index_no']." Pushing criteria</h3>";
      echo "<table border='1' style='border-collapse:collapse'>";
      echo "<tr><th>Course id</th><th>Exam Id</th><th>Credits</th><th>GPV</th><th>Mark</th><th>Mark</th><th>Grade</th><th>Grade</th></tr>";
      foreach($push_arr['solution'] as $value){
         $criteria=explode(';',$value);
         echo "<tr>
            <td>".$criteria[0]."<input type='hidden' name='course_id' value='".$criteria[0]."'></td>
            <td>".$criteria[1]."<input type='hidden' name='".$criteria[0].":exam_id' value='".$criteria[1]."'></td>
            <td>".$criteria[2]."</td>
            <td>".$criteria[3]."</td>
            <td>".$criteria[4]."</td>
            <td>".$criteria[5]."<input type='hidden' name='".$criteria[0].":push' value='".($criteria[5]-$criteria[4])."'></td>
            <td>".$criteria[6]."</td>
            <td>".$criteria[7]."</td>
         </tr>";
      //echo "<tr><td>".implode($criteria,'</td><td>' )."</td></tr>";
      }
      echo "</table>";
      echo "<br>";
      echo "<table border='1' style='border-collapse:collapse'>
      <tr><th>KEY</th><th>Previous</th><th>Now</th></tr>
      <tr><td>GPV</td><td>".str_replace(';','</td><td>',$push_arr['gpv_push'])."</td></tr>
      <tr><td>GPA</td><td>".str_replace(';','</td><td>',$push_arr['gpa_push'])."</td></tr>
      <tr><td>DEGREE/CLASS</td><td>".str_replace(';','</td><td>',$push_arr['class_push'])."</td></tr>
      </table>";

      echo "<h3>Complete score card of the student</h3>";
      echo "<table><tr>";
      for($i=1;$i<=4;$i++){
         $year_marks=$student_push->getYearMarks($i);

         //Dont print marks for null arrays
         if(sizeof($year_marks) <= 0)continue;

         echo "<td valign='top'>";
         echo "Year-".$i;
         echo "<table border='1' style='border-collapse:collapse'>";
         echo "<tr><th>course_id</th><th>credit</th><th>grade</th><th>mark</th><th>exam</th><th>GPV</th></tr>";
         foreach($year_marks as $course){
            //Array ( [course_id] => SCS2001 [coursename] => Operating Systems [credit] => 4 [grade] => A- [mark] => 76 [exam] => 2009-01-01
            $style="";
            if($student_push->isRepeatCourse($course['course_id'])){
               $style="style='font-weight:bold'";
            }
            unset($course['coursename']);
            $course['gpv']=$course['credit']*getGradeGpv($course['grade']);
            echo "<tr ><td $style>".implode(array_values($course),'</td><td>')."</td></tr>";
         }
         echo "<tr><td>SUMMERY</td><td>".$student_push->getYearCredits($i)."</td><td>GPV-D</td><td>".$student_push->getYearDGPV($i)."</td><td>GPV-C</td><td>".$student_push->getYearCGPV($i)."</td></tr>";
         echo "</table>";
         echo "</td>";
      }
      echo "</tr></table>";
   $gradeGpv = array(
      "A+"=>4.25,"A"=>4.00,"A-"=>3.75,
      "B+"=>3.25,"B"=>3.00,"B-"=>2.75,
      "C+"=>2.25,"C"=>2.00,"C-"=>1.75,
      "D+"=>1.25,"D"=>1.00,"D-"=>0.75,
      "E"=>0.00,"F"=>0.00
   );
      echo "<table border='1' cellpadding='2' style='border-collapse:collapse'><tr><td><b>GPV</b></td>";
      foreach($gradeGpv as $grade => $gpv){
         echo "<td>$grade : $gpv</td>"; 
      }
      echo "</tr></table>";

   }else{

      //Bypass the index numbers which is checked before and fails to push
      if(!isset($_SESSION[PAGE]['tried_indexs'])){
         $_SESSION[PAGE]['tried_indexs']=array();
      }
      $_SESSION[PAGE]['tried_indexs'][]=$_REQUEST['index_no'];

      echo "Can not push ".$_REQUEST['index_no']." sorry!";
      gen_push_list();
   }
}

function apply_push(){
   print_r($_REQUEST);
}


//id table mapper array
$table_of_id=array(
   'batch_id'=>$GLOBALS['P_TABLES']['batch'],
   'index_no'=>$GLOBALS['P_TABLES']['student'],
);

//Map filter for the given id for returning json data
$filter_map=array(
   'index_no'=>isset($_SESSION[PAGE]['batch_id'])?" batch_id='".$_SESSION[PAGE]['batch_id']."'":null,
);

//order the given id for returning json data
$order_by_map=array(
   'batch_id'=>" ORDER BY batch_id DESC"
);


//Switch the functionality according to the request
if(isset($_REQUEST['action'])){
   switch($_REQUEST['action']){
      case 'save':
         apply_push();
      break;
      case 'push':
         try_to_push();
      break;
      case 'store':
         $filter   =null;
         $order_by=null;
         if(isset($filter_map[$_REQUEST['id']])){
            $filter=$filter_map[$_REQUEST['id']];
         }
         if(isset($bordr_by_map[$_REQUEST['id']])){
            $order_by=$bordr_by_map[$_REQUEST['id']];
         }
         $xhr_combobox->json_data($table_of_id[$_REQUEST['id']],$_REQUEST['id'],$filter,$order_by);
      break;
      case 'html':
      case 'csv':
      case 'print':
         gen_push_list();
      break;
      case 'param':
         $_SESSION[PAGE][$_REQUEST['param']]=$_REQUEST[$_REQUEST['param']];

         //exceptional cases
         switch($_REQUEST['param']){
            case 'batch_id':
            break;
         }
         return_status_json('OK',$_REQUEST['param'].'='.$_REQUEST[$_REQUEST['param']]);
      break;
   }
}else{
      //Print html when requested
      echo "<div align='center'><div dojoType='dijit.form.Form' id='push_to_frm' name='push_to_frm' jsId='push_to_frm' >";
         gen_push_list();
      echo "</div></div>";

      //Index number selector in toolbar
      echo "<script type='text/javascript'>";
      echo "dojo.addOnLoad(function() {";

      //function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null);
      $xhr_combobox->gen_xhr_combobox('batch_id',"Batch",$xhr_combobox->get_val('batch_id'),80,20,array('batch_id'),'push_to_frm');
//      $xhr_combobox->gen_xhr_static_combo('push_to','Push to',$xhr_combobox->get_val('push_to'),110,array_keys(),array('batch_id','push_to'),'push_to_frm');

      echo "
   var reload_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Reload list',
      onClick:function(){request_html('push_to_frm',new Array('batch_id'),null);},
   });
   toolbar.addChild(reload_button);

   var load_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconFunction',
      label: 'Try to push',
      onClick:function(){request_html('push_to_frm',null,'push');},
   });
   toolbar.addChild(load_button);

   var apply_button=new dijit.form.Button({
      iconClass:'dijitIcon dijitIconSave',
      label: 'Save',
      onClick:function(){request_html('push_to_frm',null,'save');},
   });
   toolbar.addChild(apply_button);

";


      $xhr_combobox->param_setter();$xhr_combobox->html_requester();
      echo "});";
      $xhr_combobox->form_submitter('push_to_frm');
      echo "</script>";

   }
?>
