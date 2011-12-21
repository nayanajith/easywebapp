<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

//mysql_connect("localhost", "root", "letmein") or die(mysql_error());
//mysql_select_DB("bit") or die(mysql_error());

$gpa=-1;

/*
PUSHING LOGIC:

Year push : 10 max
Semester push : 5 max
note: consider only push 45-47 to 50 that is C- to  C  and only one subject can be pushed

SPECIAL:
All students will be pushed when subject average is low : 10 max
if( all_student_push 1 or 2 ){
   Semester push is 4 or 3;
   note: consider only push 45-47 to 50 that is C- to  C  and only one subject can be pushed
}else{
   Semester push is 0;
}


*/
/*
 * Executed:
 * update bit_all set M2204=50 where M2204=49;
 * update bit_all set M2204=50 where M2204=48;
 * update bit_all set E2204='C' where M2204=50;
 * update bit_all set Add1=6 where Reg='R' and y1='2009' AND A2104='2010' AND A2204='2010';
 */

/*
 $result = mysql_query("select *, totalgpc/totalcredits as gpa
 from
 (select Index_No, code, mm, grade, gpv, credits, gpc, category,@tc:=@tc+credits as totalcredits, @tgpc:=@tgpc+gpc as totalgpc
 from
 (select t1.indexno, t1.code, t1.mm, grades.grade, grades.gpv, courses.credits,grades.gpv * courses.credits as gpc, courses.category
 from
 courses, grades, (select indexno, code, max(marks) as mm from markbook where indexno='$indexnumber' group by indexno, code) t1 where t1.mm = grades.marks and t1.code=courses.code order by courses.category, gpc desc) t2, (select @tc:=0,@tgpc:=0) t3) t4;") or die(mysql_error());
 */
/*
 $result = mysql_query("
 SELECT b.Index_No indexno,b.E2104, ((g.gpv*4)+(c22.gpv*4)+(c23.gpv*4)+(c24.gpv*4)+(c11.gpv*4)+(c12.gpv*4)+(c13.gpv*4)+(c14.gpv*4))/32 gpa,c22.E2204,c23.E2304,c24.E2404,c11.E1104,c12.E1204,c13.E1304,c14.E1404  FROM bit_all b, grades g,
 (SELECT b.Index_No,g.gpv,b.E2204 FROM bit_all b, grades g WHERE b.M2204=g.marks) c22,
 (SELECT b.Index_No,g.gpv,b.E2304 FROM bit_all b, grades g WHERE b.M2304=g.marks) c23,
 (SELECT b.Index_No,g.gpv,b.E2404 FROM bit_all b, grades g WHERE b.M2404=g.marks) c24,


 (SELECT b.Index_No,g.gpv,b.E1104 FROM bit_all b, grades g WHERE b.M1104=g.marks) c11,
 (SELECT b.Index_No,g.gpv,b.E1204 FROM bit_all b, grades g WHERE b.M1204=g.marks) c12,
 (SELECT b.Index_No,g.gpv,b.E1304 FROM bit_all b, grades g WHERE b.M1304=g.marks) c13,
 (SELECT b.Index_No,g.gpv,b.E1404 FROM bit_all b, grades g WHERE b.M1404=g.marks) c14

 WHERE
 Reg='R' AND y1='2009'
 AND c22.Index_No=b.Index_No AND c23.Index_No=b.Index_No AND c24.Index_No=b.Index_No
 AND c11.Index_No=b.Index_No AND c12.Index_No=b.Index_No AND c13.Index_No=b.Index_No AND c14.Index_No=b.Index_No
 AND A2104='2010' AND A2204='2010' AND A2304='2010' AND A2404='2010'
 ORDER BY gpa DESC;
 ");
 */

/*
Get grades from 
*/
$mark_gpv=array();
$grade_gpv=array();
opendb();
mysql_select_DB("bit",$GLOBALS['CONNECTION']) or die(mysql_error());
$result1= mysql_query("SELECT marks,grade,gpv FROM grades");
while($row = mysql_fetch_array($result1)){
   $grade_gpv[$row['grade']]=$row['gpv'];
   $mark_gpv[$row['marks']]=$row['gpv'];
}
$grade_gpv['-AB-']=0;
$mark_gpv['-AB-']=0;



/*
 echo "<pre>";
 print_r($grade_gpv);
 echo "</pre>";
 */

/*
$prefix eg: IT,E,M
$sufux eg: 04,03,02
$sem: eg: 1,2,3,4,5

return: array of ids for the given semester
*/
function get_sem_array($prefix,$sem,$sufix){
   $ret_sem=array();
   if($sem==3){
      for($i=1;$i<=5;$i++){
         $ret_sem[]=$prefix.$sem.$i.$sufix;   
      }
   }else{
      for($i=1;$i<=4;$i++){
         $ret_sem[]=$prefix.$sem.$i.$sufix;   
      }
   }
   return $ret_sem;
}

$gpa_bit            =2;    //minimum GPA to acquire a pass to next year 
$gpa_year         =1.5; //minimum GPA to acquire a pass to next year 
$gpa_push_margin   =1.46;//minimum GPA which can be considered to be pushed
$yaer_push         =10;   //maximum push can be given to a student per year
$semester_push      =5;   //maxmimu push can be given to a student per semester
$course_push      =10;    //maximum overall push value which can be given to all students to fix the average

//this push is considered when pushing the particular subject
$all_course_push=array(
   "IT2104"=>6,
   "IT2204"=>0,
   "IT2304"=>6,
   "IT2404"=>2,
   "IT1104"=>0,
   "IT1204"=>0,
   "IT1304"=>0,
   "IT1404"=>0
);

/*
$prev_push: previouse amounts of push for the year
$course: the course eg: IT2104 which the push should be calculated

return: the amount of available push which could be used for the given course
*/
function get_push($prev_push,$course){
   global $semester_push;   
   global $course_push;
   global $all_course_push;
   $this_course_push=$course_push - $all_course_push[$course];
   $this_push=$semester_push - $prev_push;

   if($this_push<$this_course_push){
      return $this_push;
   }else{
      return $this_course_push;
   }
}

//generate grades and marks ides for the given year
$grades   =array_merge(get_sem_array('E','1','04'),get_sem_array('E','2','04'));
$marks   =array_merge(get_sem_array('M','1','04'),get_sem_array('M','2','04'));

//result for the registered student for year 1 in 2009
$year=$_REQUEST['year'];
$result2 = mysql_query("
   SELECT Index_No,".implode(",",$grades)." FROM bit_all 
   WHERE  
   Reg='R' AND y1='".$year."'
   AND A2104='2010' AND A2204='2010' AND A2304='2010' AND A2404='2010' 
   AND A1104='2009' AND A1204='2009' AND A1304='2009' AND A1404='2009' 
   ORDER BY Index_No
");

/*
$result2 = mysql_query("
   SELECT Index_No,".implode(",",$grades)." FROM bit_all 
   WHERE  
   Reg='R' AND y1='".$_REQUEST['year']."'
   AND A2104='2010' AND A2204='2010' AND A2304='2010' AND A2404='2010' 
   AND A1104='2009' AND A1204='2009' AND A1304='2009' AND A1404='2009' 
   ORDER BY Index_No
");
*/

//delete all records from bit gpa table  before filling back
mysql_query("DELETE FROM bit_gpa") or die(mysql_error());

//calculate gpa and c-munuses for each student and store in bit_gpa table
while($row = mysql_fetch_array($result2)){
   $grade_gpvt=0;
   $c_mine=0;

   foreach($grades as $grade){
      $g=strtoupper(trim($row[$grade]," \n"));

      //each subject have 4 credits 
      $grade_gpvt+=($grade_gpv[$g]*4);

      //c- are counted to check the elegibility
      if($row[$grade] == 'C-'){
         $c_mine++;
      }
   }

   //Total number of credit is easilly calculated by (4 x No. of grades)
   $gpa=$grade_gpvt/(4*sizeof($grades));

   mysql_query("INSERT INTO bit_gpa values('".$row['Index_No']."','$gpa','$c_mine')") or die(mysql_error());
   //mysql_query("REPLACE INTO bit_gpa values('".$row['Index_No']."','$gpa','$c_mine')") or die(mysql_error());
   //mysql_query("update bit_gpa set gpa='$gpa' where Index_No=".$row['Index_No']) or die(mysql_error());
}

//eligbility stat of the student
$status=array(
   "",
   "DIT"         =>"<span style='background:blue;'>DIT</span>", //1
   "DIT_PUSH"   =>"<span style='background:orange;font-weight:bold'>DIT_PUSH</span>", //2
   "Y2_PUSH"   =>"<span style='background:red'>2Y_PUSH</span>", //3
   "Y2"         =>"<span style='background:green;'>2Y</span>",//4
   "DIT_FAIL"   =>"<span style='background:green;'>DIT_FAIL</span>", //5
   "Y2_FAIL"   =>"<span style='background:green;'>2Y_FAIL</span>" //6
);

/*
elegibility of each student is calculated 
$row: array of students marks for the given courses   

return: eligibility stat of the given student
*/

function eligibility($row){
   global $marks;
   global $status;
   $eli=array(
      "certificate"=>-1,
      "next_year"=>-1
   );
   $count_fail=0;
   $c_minus   =0;
   $c_minus_sem_1=0;

   foreach($marks as $mark){
      if($row[$mark] < 50){
         $count_fail++;
      }
      if(trim($row[str_replace("M", "E", $mark)]," ") === 'C-'){
         $c_minus++;
         if(strpos($mark,"E1")!==FALSE){
            $c_minus_sem_1++;
         }
      }
   }


   //Eligibility for next year
   if($row['gpa'] >= 1.5){
      $eli["next_year"]='Y2';
   }elseif($row['gpa'] >= 1.46){
      $eli["next_year"]='Y2_PUSH';
   }else{
      $eli["next_year"]='Y2_FAIL';
   }

   //Eligibility for the certificate
   if($count_fail==0){
      $eli["certificate"]='DIT';
   //}elseif(($count_fail==1 && $c_minus==1)||($count_fail==2 && $c_minus==2)){
   }elseif(($count_fail==1 && $c_minus==1)){
      $eli["certificate"]='DIT_PUSH';
   }else{
      $eli["certificate"]='DIT_FAIL';
   }


/*
   if($count_fail==0){//elegible for DIT
      return 1;
   //}elseif($count_fail==1 && $c_minus==1 &&  $row['gpa'] >= 1.5){
   }elseif($count_fail==1 && $c_minus==1){ //consider pushing for DIT
      return 2;
   }elseif($count_fail==3 && $c_minus==1){//consider pushing for Year 2
      return 3;
   }elseif($count_fail<=2 && $row['gpa'] >= 1.5){ //elegible for year year 2
      return 4;
   }else{
      return 0;
   }
*/

   return $eli;
}

/*-------------------------------------------csv gen---------------------------------------*/
if(isset($_REQUEST['year_push'])&&$_REQUEST['year_push']=='on'){
   $filter[]="Y2_PUSH";
}
if(isset($_REQUEST['cert_push'])&&$_REQUEST['cert_push']=='on'){
   $filter[]="DIT_PUSH";
}

if(isset($_REQUEST['year_pass'])&&$_REQUEST['year_pass']=='on'){
   $filter[]="Y2";
}

if(isset($_REQUEST['cert_pass'])&&$_REQUEST['cert_pass']=='on'){
   $filter[]="DIT";
}



if($data){
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"bit_data.csv\"");

echo "'SEQ','Index No'";

foreach ($grades as $grade) {
   echo ",'".str_replace("E", "IT", $grade)."+".$all_course_push[str_replace("E", "IT", $grade)]."'";
}
echo ",'GPA','STATE','GPA NEW','PUSH'\n";

//order the records as requested
$order_by="ORDER BY g.Index_No";
if(isset($_GET['order_by'])&& $_GET['order_by']=='gpa'){
   $order_by="ORDER BY g.gpa DESC";
}

$result3 = mysql_query("
   SELECT m.Index_No,g.gpa,m.".implode(",m.",$grades).",m.".implode(",m.",$marks).", m.Add1 FROM bit_all m, bit_gpa g
   WHERE  m.Index_No=g.Index_No 
   AND ABS(g.gpa) >= $gpa_push_margin
   $order_by
");


$seq=1;
while($row = mysql_fetch_array($result3)){

   $eli=eligibility($row);
   //filter only records which should be pushed
   if (!(in_array($eli['next_year'],$filter) || in_array($eli["certificate"],$filter))) {
      continue;
   }

   echo "'".$seq++."','".$row['Index_No']."'";

   $avail=0;
   $prev_push=$row['Add1'];
   $push=0;
   $sem_1=false;
   $pushed_courses=array();

   //check for the grades which can be pushed 
   foreach(get_sem_array('E','1','04') as $grade){
      if(strpos(strtoupper($row[$grade]),"C-")!==FALSE){
         $avail=get_push($avail,str_replace("E", "IT", $grade));
         $push=50-$row[str_replace("E", "M", $grade)];

         if($avail >= $push){
            $pushed_courses[str_replace("E", "IT", $grade)]=$push;
            $prev_push=$push;
         }
         
         echo ",'".$row[$grade]."'";
      }elseif($row[str_replace("E", "M", $grade)]<50){
         echo ",'".$row[$grade]."'";
      }else{
         echo ",'".$row[$grade]."'";
      }
   }

   foreach(get_sem_array('E','2','04') as $grade){
      if(strpos(strtoupper($row[$grade]),"C-")!==FALSE){
         $avail=get_push($avail,str_replace("E", "IT", $grade));
         $push=50-$row[str_replace("E", "M", $grade)];

         if($avail >= $push){
            $pushed_courses[str_replace("E", "IT", $grade)]=$push;
            $prev_push=$push;
         }
         
         echo ",'".$row[$grade]."'";
      }elseif($row[str_replace("E", "M", $grade)]<50){
         echo ",'".$row[$grade]."'";
      }else{
         echo ",'".$row[$grade]."'";
      }
   }

      echo ",'".$row['gpa']."'";
      echo ",'".$eli['next_year']." / ".$eli["certificate"]."'";
      if(sizeof($pushed_courses)==0){
         echo ",,'cannot $push'\n";
      }else{
         echo ",'".($row['gpa']+(1/32))."'";
         echo ",'";
         $sep='';
         foreach($pushed_courses as $course => $push_val){
              echo $sep.$course."-".$row[str_replace("IT", "M", $course)]."+".$push_val;
            $sep='|';
         }
         echo "'\n";
      }
   }
   return;
}
/*-------------------------------------------csv gen end---------------------------------------*/

/*
echo "<pre>";
print_r($_REQUEST);
echo "</pre>";
*/
?>
<script type='text/javascript'>
dojo.require('dojo.parser');

//form elements
dojo.require('dijit.form.FilteringSelect');
dojo.require('dijit.form.RadioButton');
dojo.require('dijit.form.CheckBox');
dojo.require('dijit.form.TextBox');
dojo.require('dijit.form.Textarea');
dojo.require('dijit.form.DateTextBox');
dojo.require('dijit.form.NumberSpinner');
dojo.require('dijit.form.HorizontalSlider');
dojo.require('dijit.form.ValidationTextBox');
dojo.require('dijit.form.Form');
dojo.require('dijit.form.Button');

//data grid
dojo.require('dojox.grid.DataGrid');

//data stores
dojo.require('dojo.data.ItemFileWriteStore');
dojo.require('dojo.data.ItemFileReadStore');

</script>
<div dojoType='dijit.form.Form' id='bit_push_frm' jsId='bit_push_frm'
         encType='multipart/form-data'
         action='<?php echo W_ROOT; ?>/index.php';
         method='GET'>

<script type='text/javascript' type="dojo/method" event="onSubmit">
return true;
</script>

<input type=hidden value='<?php echo MODULE; ?>' name=module >
<input type=hidden value='<?php echo PAGE; ?>' name=page >
<input type=hidden value='<?php echo PROGRAM; ?>' name=program >
<table><tr>
<td>
Study Year:<select dojoType='dijit.form.FilteringSelect' name=acc_year>
<?php 
$opts=array('Y1'=>'Year 1','Y2'=>'Year 2','Y3'=>'Year 3');
   foreach($opts as $key => $value){
      if(isset($_REQUEST['acc_year'])&&$_REQUEST['acc_year']==$key){
         echo "<option value=".$key." selected=true>".$value."</option>";
      }else{
         echo "<option value=".$key.">".$value."</option>";
      }
   }
?>
</select>
Acc Year:<select dojoType='dijit.form.FilteringSelect' name=year>
<?php 
$res= mysql_query("select distinct(Y1) from bit_all order by abs(Y1);");
while($row=mysql_fetch_array($res)){
   if(isset($_REQUEST['year'])&&$_REQUEST['year']==$row['Y1']){
      echo "<option value=".$row['Y1']." selected=true>".$row['Y1']."</option>";
   }else{
      echo "<option value=".$row['Y1'].">".$row['Y1']."</option>";
   }
}
?>
</select>

</td></tr><tr><td>
<?php
$filter=array();
echo "<br>";
if(isset($_REQUEST['year_push'])&&$_REQUEST['year_push']=='on'){
   echo "<label for=year_push>Push to Next Year</label><input dojoType='dijit.form.CheckBox' id=year_push name=year_push checked >";
   $filter[]="Y2_PUSH";
}else{
   echo "<label for=year_push>Push to Next Year</label><input dojoType='dijit.form.CheckBox' id=year_push name=year_push >";
}
echo "<br>";

if(isset($_REQUEST['cert_push'])&&$_REQUEST['cert_push']=='on'){
   echo "<label for=cert_push>Push to Certificate</label><input dojoType='dijit.form.CheckBox' id=cert_push name=cert_push  checked >";
   $filter[]="DIT_PUSH";
}else{
   echo "<label for=cert_push>Push to Certificate</label><input dojoType='dijit.form.CheckBox' id=cert_push name=cert_push>";
}
echo "<br>";

if(isset($_REQUEST['year_pass'])&&$_REQUEST['year_pass']=='on'){
   echo "<label for=year_pass>Passed to Next Year</label><input dojoType='dijit.form.CheckBox' id=year_pass name=year_pass checked >";
   $filter[]="Y2";
}else{
   echo "<label for=year_pass>Passed to Next Year</label><input dojoType='dijit.form.CheckBox' id=year_pass name=year_pass >";
}
echo "<br>";

if(isset($_REQUEST['cert_pass'])&&$_REQUEST['cert_pass']=='on'){
   echo "<label for=cert_pass>Passed Certificate</label><input dojoType='dijit.form.CheckBox' id=cert_pass name=cert_pass  checked >";
   $filter[]="DIT";
}else{
   echo "<label for=cert_pass>Passed Certificate</label><input dojoType='dijit.form.CheckBox' id=cert_pass name=cert_pass>";
}
echo "<br>";

if(isset($_REQUEST['failed'])&&$_REQUEST['failed']=='on'){
   echo "<label for=failed>Failed</label><input dojoType='dijit.form.CheckBox' id=failed name=failed  checked >";
   $filter[]="DIT_FAIL";
   $filter[]="Y2_FAIL";
}else{
   echo "<label for=failed>Failed</label><input dojoType='dijit.form.CheckBox' id=failed name=failed>";
}
echo "<br>";


?>
</td>
</tr>
<tr>
<td>
<button dojoType='dijit.form.Button' type='submit' name='viewBtn' value='view'>
VIEW
</button>
<button dojoType='dijit.form.Button' type='submit' name='csv' value='true'>
CSV
</button>
</td>
</tr>
</table>
</div>
<br>


<?php
echo "<table border=1 style='border-collapse:collapse'>";
echo "<thead><tr><th>SEQ</th><th><a href='".gen_url(2)."&order_by=Index_No'>Index No</a></th>";

foreach ($grades as $grade) {
   echo "<th>".str_replace("E", "IT", $grade)."+".$all_course_push[str_replace("E", "IT", $grade)]."</th>";
}
echo "<th><a href='".gen_url(2)."&order_by=gpa'>GPA</a></th><th>STATE</th><th>GPA NEW</th><th>PUSH</th>";
echo "</tr></thead>\n";

//order the records as requested
$order_by="ORDER BY g.Index_No";
if(isset($_GET['order_by'])&& $_GET['order_by']=='gpa'){
   $order_by="ORDER BY g.gpa DESC";
}

/*
$result3 = mysql_query("
   SELECT m.Index_No,g.gpa,m.".implode(",m.",$grades).",m.".implode(",m.",$marks).", m.Add1 FROM bit_all m, bit_gpa g
   WHERE  m.Index_No=g.Index_No 
   AND ABS(g.gpa) >= 1.47
   $order_by
");
*/

$result3 = mysql_query("
   SELECT m.Index_No,g.gpa,m.".implode(",m.",$grades).",m.".implode(",m.",$marks).", m.Add1 FROM bit_all m, bit_gpa g
   WHERE  m.Index_No=g.Index_No 
   AND ABS(g.gpa) >= $gpa_push_margin
   $order_by
");


$seq=1;
while($row = mysql_fetch_array($result3)){

   $eli=eligibility($row);
   //filter only records which should be pushed
   //if (!($eli['next_year']=='Y2_PUSH' || $eli["certificate"]=='DIT_PUSH' )) {
   if (!(in_array($eli['next_year'],$filter) || in_array($eli["certificate"],$filter))) {
   //if (!( $eli['next_year']=='Y2_FAIL' )) {
      continue;
   }

   echo "<tr><td>".$seq++."</td><td>".$row['Index_No']."</td>";

   $avail=0;
   $prev_push=$row['Add1'];
   $push=0;
   $sem_1=false;
   $pushed_courses=array();

   foreach(get_sem_array('E','1','04') as $grade){
      if(strpos(strtoupper($row[$grade]),"C-")!==FALSE){
      //if(trim($row[$grade]," ")==='C-'){

         $avail=get_push($prev_push,str_replace("E", "IT", $grade));
         $push=50-$row[str_replace("E", "M", $grade)];

         if($avail >= $push){
            $pushed_courses[str_replace("E", "IT", $grade)]=$push;
            $prev_push=$push;
         }
         
         echo "<td align=right style='background:#5b92c8;font-weight:bold' title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }elseif($row[str_replace("E", "M", $grade)]<50){
         echo "<td align=right style='background:#C9D7F1;font-style:italic;' title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }else{
         echo "<td title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }
   }

   $prev_push=$row['Add1'];
   foreach(get_sem_array('E','2','04') as $grade){
      if(strpos(strtoupper($row[$grade]),"C-")!==FALSE){
      //if(trim($row[$grade]," ")==='C-'){

         $avail=get_push($prev_push,str_replace("E", "IT", $grade));
         $push=50-$row[str_replace("E", "M", $grade)];

         if($avail >= $push){
            $pushed_courses[str_replace("E", "IT", $grade)]=$push;
            $prev_push=$push;
         }
         
         echo "<td align=right style='background:#5b92c8;font-weight:bold' title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }elseif($row[str_replace("E", "M", $grade)]<50){
         echo "<td align=right style='background:#C9D7F1;font-style:italic;' title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }else{
         echo "<td title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }
   }

   echo "<td>".$row['gpa']."</td>";
   echo "<td>".$eli['next_year']." / ".$eli["certificate"]."</td>";

   //if ($eli["certificate"]=='DIT_PUSH' || $eli['next_year']=='Y2_PUSH'){
   //if (!(in_array($eli['next_year'],$filter) || in_array($eli["certificate"],$filter))) {
      if(sizeof($pushed_courses)==0){
         echo "<td></td><td>cannot $push</td>";
      }else{
         echo "<td>".($row['gpa']+(1/32))."</td>";
         echo "<td>";
         foreach($pushed_courses as $course => $push_val){
              echo $course."-".$row[str_replace("IT", "M", $course)]."+".$push_val."<br>";
         }
         echo "</td>";
      }
      /*
   }else{
      echo "<td></td></tr>";
   }
   */
}

echo " </tr>\n";
echo "</table>";
?>
