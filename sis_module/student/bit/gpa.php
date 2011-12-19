<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<link rel="stylesheet"
   href="<?php echo CSS; ?>/common_css.php" type="text/css" >
</head>
<body>
<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

//mysql_connect("localhost", "root", "letmein") or die(mysql_error());
//mysql_select_DB("bit") or die(mysql_error());

$gpa=-1;

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

$grades   =array('E2104','E2204','E2304','E2404','E1104','E1204','E1304','E1404');
$marks   =array('M2104','M2204','M2304','M2404','M1104','M1204','M1304','M1404');

$result2 = mysql_query("
   SELECT Index_No,".implode(",",$grades)." FROM bit_all 
   WHERE  
   Reg='R' AND y1='2009'
   AND A2104='2010' AND A2204='2010' AND A2304='2010' AND A2404='2010' 
   AND A1104='2009' AND A1204='2009' AND A1304='2009' AND A1404='2009' 
   ORDER BY Index_No
");



mysql_query("DELETE FROM bit_gpa") or die(mysql_error());

while($row = mysql_fetch_array($result2)){
   $grade_gpvt=0;
   $c_mine=0;

   foreach($grades as $grade){
      $g=strtoupper(trim($row[$grade]," \n"));
      $grade_gpvt+=($grade_gpv[$g]*4);

      if($row[$grade] == 'C-'){
         $c_mine++;
      }
   }
   $gpa=$grade_gpvt/(4*sizeof($grades));

   mysql_query("INSERT INTO bit_gpa values('".$row['Index_No']."','$gpa','$c_mine')") or die(mysql_error());
   //mysql_query("REPLACE INTO bit_gpa values('".$row['Index_No']."','$gpa','$c_mine')") or die(mysql_error());
   //mysql_query("update bit_gpa set gpa='$gpa' where Index_No=".$row['Index_No']) or die(mysql_error());
}

$status=array(
   "",
   "<span style='background:blue;'>DIT</span>",
   "<span style='background:orange;font-weight:bold'>DIT_PUSH</span>",
   "<span style='background:red'>3Y_PUSH</span>",
   "<span style='background:green;'>3Y</span>"
);

function eligiblity($row){
   global $marks;
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

   if($count_fail==0){
      return 1;
   }elseif($count_fail==1 && $c_minus==1 &&  $row['gpa'] >= 1.5){
      return 2;
   }elseif($count_fail==3 && $c_minus==1 ){
      return 3;
   }elseif($count_fail<=2 && $row['gpa'] >= 1.5){
      return 4;
   }else{
      return 0;
   }
}
$order_by="ORDER BY g.Index_No";

if(isset($_GET['order_by'])&& $_GET['order_by']=='gpa'){
   $order_by="ORDER BY g.gpa DESC";
}


$result3 = mysql_query("
   SELECT m.Index_No,g.gpa,m.".implode(",m.",$grades).",m.".implode(",m.",$marks).", m.Add1 FROM bit_all m, bit_gpa g
   WHERE  m.Index_No=g.Index_No AND ABS(g.gpa) > 1.47
   $order_by
");

echo "<table border=1 style='border-collapse:collapse'>";
echo "<thead><tr><th><a href='?order_by=Index_No'>Index No</a></th>";

foreach ($grades as $grade) {
   echo "<th>".str_replace("E", "IT", $grade)."</th>";
}
echo "<th><a href='?order_by=gpa'>GPA</a></th><th>STATE</th><th>Add1</th><th>PUSH</th>";
echo "</tr></thead>";

while($row = mysql_fetch_array($result3)){
   $ele=eligiblity($row);

   if (!($ele==2 || $ele==3)) {
      continue;
   }

   $avail=0;
   $push=0;
   $sem_1=false;
   echo "<tr><td>".$row['Index_No']."</td>";
   foreach($grades as $grade){
      if(trim($row[$grade]," ")==='C-'){
         $avail=10-$row['Add1'];
         $push=50-$row[str_replace("E", "M", $grade)];

         if(strpos($grade,"E1")!==FALSE){
            $sem_1=true;
         }else{
            $sem_1=false;
         }
         
         echo "<td align=right style='background:#5b92c8;font-weight:bold' title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }elseif($row[str_replace("E", "M", $grade)]<50){
         echo "<td align=right style='background:#C9D7F1;font-style:italic;' title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }else{
         echo "<td title='".$row[str_replace("E", "M", $grade)]."'>".$row[$grade]."</td>";
      }
   }
   echo "<td>".$row['gpa']."</td>";
   echo "<td>".$status[$ele]."</td>";
   echo "<td>".$row['Add1']."</td>";

   if ($ele==2 || $ele==3){
      if($avail >= $push){
         echo "<td title='".($row['gpa']+(1/32))."'>".$push."</td>";
      }else{
         echo "<td>cannot $push</td>";
      }
   }else{
      echo "<td></td>";
   }
}

echo " </tr>\n";
echo "</table>";
/*
 echo "<br >";

 echo "<table border=\"1\" cellspacing=\"2\" cellpadding=\"2\">";
 echo "<tr>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Code</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Marks</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Grade</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">GPV</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Credits</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">GPC</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Category</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Total Credits</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">Total GPC</font></th>";
 echo "<th><font face=\"Arial, Helvetica, sans-serif\">GPA</font></th>";
 echo "</tr>";
 echo "<tr>";
 while($row = mysql_fetch_array($result)){
 if ($row['category']=="C")
 $color='compulsory';
 if ($row['category']=='O')
 $color='optional';

 if($gpa==-1 && $row['totalcredits']>=50)
 $gpa=$row['gpa'];
 echo "<td class=\"$color\">".$row['code']."</td>";
 echo "<td>".$row['mm']."</td>";
 echo "<td>".$row['grade']."</td>";
 echo "<td>".$row['gpv']."</td>";
 echo "<td>".$row['credits']."</td>";
 echo "<td>".$row['gpc']."</td>";
 echo "<td>".$row['category']."</td>";
 echo "<td>".$row['totalcredits']."</td>";
 echo "<td>".$row['totalgpc']."</td>";
 echo "<td>".$row['gpa']."</td>";
 echo "<tr>";
 }
 echo "</table>";

 echo "<h2> GPA= ".round($gpa,2)."</h2>";

 }
 else
 {
 echo "<form enctype=\"multipart/form-data\" action=\"gpa.php\" method=\"POST\">";
 echo "Index Number: <input name=\"indexnumber\" type=\"text\" ><br >";
 echo "<input type=\"submit\" value=\"Submit\" >";
 echo "</form>";
 }

 */

?>

</body>
</html>
