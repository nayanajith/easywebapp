<?php
/*
Print course summery
*/
function get_course_summery($exam_hid){
   $courses_arr=exec_query("SELECT distinct course_id FROM ".$GLOBALS['P_TABLES']['marks']." WHERE exam_hid='$exam_hid' ORDER BY course_id DESC",Q_RET_ARRAY,null,'course_id');
   if($courses_arr){
      echo "<table class='report_table'>";
      foreach($courses_arr as $course_id => $info_arr){
         echo "<tr><td>$course_id</td></tr>";
      }
      echo "</table>";
   }else{
      echo "No any courses found";   
   }
}

if(isset($_REQUEST['data']) && isset($_REQUEST['exam_hid'])){
   get_course_summery($_REQUEST['exam_hid']);
}else{
?>
<style type='text/css'>
.report_table{
}

.report_table th{
   background-color:#C9D7F1;   
}

.report_table td{
   border-bottom:1px solid #C9D7F1;
}

</style>
<div id='content_body' align='center'>
<?php
//Print exam summery
$colomns=array('exam_date','exam_hid','semester','student_year','exam_time','venue');
$exams_arr=exec_query("SELECT ".implode($colomns,',')." FROM ".$GLOBALS['P_TABLES']['exam']." ORDER BY exam_hid DESC",Q_RET_ARRAY);
echo "<h4 class='coolh'>Examination summery</h4>";
echo "<table class='report_table'>";
echo "<tr>";

foreach($colomns as $key){
   echo "<th >".style_text($key)."</th>";
}
echo "<th>Uploaded</th></tr>";

foreach($exams_arr as $exam_hid => $info_arr){
   echo '<tr><td style="vertical-align:top;valign:top"><a href="javascript:request_data(\''.$info_arr['exam_hid'].'\')">';
   echo implode(array_values($info_arr),'<a/></td><td style="vertical-align:top;valign:top" ><a href="javascript:request_data(\''.$info_arr['exam_hid'].'\')">');
   echo "</a></td><td id='".$info_arr['exam_hid']."'></td></tr>";
}
echo "</table>";


?>
<div align='center' id='xhr_content'>
</div>
</div>
<script type='text/javascript' >
//xhr request to get data from backend
function request_data(exam_hid) {

   //var xhr_content_obj=document.getElementById('xhr_content');
   var xhr_content_obj=document.getElementById(exam_hid);
   if(xhr_content_obj.innerHTML != ''){
      xhr_content_obj.innerHTML ='';
      return;
   }
   //If index number is blank return 
   dojo.xhrGet({
      url       : '<?php echo gen_url(); ?>&data=json&exam_hid='+exam_hid,
      handleAs :'text',
      load       : function(response, ioArgs) {        
           update_status_bar('OK','Done');
         xhr_content_obj.innerHTML=response;
         //dojo.parser.parse(content_obj);
      },
      error : function(response, ioArgs) {
           update_status_bar('ERROR',response);
      }
   });
}

function submit_form(){
   //Elements to get to data to be printed
   var content_obj=document.getElementById('content_body');
   var styles=document.getElementsByTagName("style");

    var consoleRef=window.open('','myconsole',
  'width=800,height=600'
   +',menubar=0'
   +',toolbar=0'
   +',location=0'
   +',status=0'
   +',scrollbars=1'
   +',resizable=1');
   
   //print Styles
   var print_style="body { background-colour:#EFEFEF; margin:0px auto !important; padding:0px auto !important;} #A4 {background-color:#FFFFFF; left:200px; right:190px; height:297mm !important; width:210mm !important; margin:1px solid #FFFFFF; }";
   //Write style from current page to printing page
   consoleRef.document.writeln("<style type='text/css'>");
   for(var i=0;i<styles.length;i++){
      consoleRef.document.writeln(styles.item(i).innerHTML);
   }
   consoleRef.document.writeln(print_style);
   consoleRef.document.writeln("</style>");
   consoleRef.document.writeln("<div id='A4'align='center'>");
   consoleRef.document.writeln(content_obj.innerHTML);
   consoleRef.document.writeln("</div>");
   consoleRef.print();
   consoleRef.document.close();
}
</script>
<?php
}
?>
