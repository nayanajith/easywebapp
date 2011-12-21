<?php
/*session_start();
include "config.php";
include "../../core/common.php";
include "login.php";
*/

if(isset($_SESSION['views'])){
   $_SESSION['views'] = $_SESSION['views']+ 1;
}else{
   $_SESSION['views'] = 1;
}

$_SESSION['host']=$_SERVER['REMOTE_ADDR'];
?>

<script type='text/javascript' 
type   ='text/javascript' 
src   ="<?php echo W_MODULES."/".$modules[$module]."/js/spreadsheet.js"; ?>"
</script>

<script type='text/javascript' 
type   ='text/javascript' 
src   ="<?php echo W_MODULES."/".$modules[$module]."/js/ajaxtt.js"; ?>"
</script>

<script type='text/javascript' 
type   ='text/javascript' 
src   ="<?php echo W_MODULES."/".$modules[$module]."/js/xml.js"; ?>"
</script>

<link 
rel   ="stylesheet" 
href   ="<?php echo W_MODULES."/".$modules[$module]."/css/spreadsheet.css"; ?>" 
type   ='text/css'
media   =screen />

<!-- display message -->
<div id=msg></div>

<?php
$examiner1="Mr GKA Dias";
opendb();
$query="
SELECT c.courseid,c.coursename,e.examiner1 
FROM courses AS c, examiners AS e 
WHERE c.courseid = e.id 
AND ( e.examiner1 = '$examiner1' OR e.examiner2 = '$examiner1')";

$result = mysql_query($query,$CONNECTION);
closedb();

$course_select1= "";
$course_select2= "";
$course_name1="";
$course_name2="";

while($row = mysql_fetch_array($result)) {
   if($row['examiner1']==$examiner1){
      $course_select1.="<option value='".$row['courseid']."'>".$row['courseid']."</option>";
      $course_name1.="case '".$row['courseid']."': return '".$row['coursename']."';break;";
   }else{
      $course_select2.="<option value='".$row['courseid']."'>".$row['courseid']."</option>";
      $course_name2.="case '".$row['courseid']."': return '".$row['coursename']."';break;";
   }
}

$input_array=array(
   "paper_name"   => array("hidden","Paper Name"),
   "examiner"      => array("hidden","Examiner"),
   "paper_code"   => array("select","Paper Code",),
   "examiner2"      => array("hidden","Second Examiner"),
   "num_of_stude"   => array("text","Number of Students"),
   "br"            => "</ul><ul class=form_ul>",
   "paper_rat"    => array("text","Paper"),
   "assig_rat"    => array("text","Assignment")
);


echo "
<script type='text/javascript'>

function course_name(course){
   switch(course){
   $course_name
   }
}

function examiner_change(ex){
   obj_pc=document.getElementById('paper_code');
   obj=document.getElementById(ex);
   if(obj.value==1){
      obj_pc.innerHTML=\"$course_select1\";
   }else{
      obj_pc.innerHTML=\"$course_select2\";
   }
}

</script>";
/*
 * Function to generate input fields
 */

function input_gen($id,$label,$type,$size,$_GET,$event,$val){
   $value=empty($_GET[$id]) ? $val : $_GET[$id];
   switch ($type) {
   case 'text':
      echo "<label for=$id >$label</label>";
      echo "<input type=text id='$id' name='$id' $event value='$value' size=$size>";
   break;
   case 'hidden':
      echo "<input type=hidden id='$id' name='$id' value='$value'>";
   break;
   case 'select':
      echo "<label for=$id >$label</label>";
      echo "<select id='$id' name='$id' $event size=$size>";
      echo $select;
      echo "</select>";
   break;
   case 'checkbox':
      echo "<label for=$id >$label</label>";
      echo "<input type=checkbox id='$id' name='$id' $event value='$value' size=$size>";
   break;
   default:
      echo "<br>";
   break;
   }
}
/*
 * Login/Logout form generator
   
if (isset($_SESSION['username'])){
    echo after_login();
}else{
    echo before_login();
    return;
}

*/

foreach ($input_array as $id => $label) {
      if($id=="br"){
         echo $label;   
      }else if($label[0]=="hidden"){
         echo "<input type=hidden name='$id' value='".$_GET[$id]."'>";
      }else if($id[1]=="paper_rat" or $id[1]=='assig_rat'){
         echo "<li>";
         if($id=='paper_rat'){
            input_gen($id,$label,$id[0],5,$_GET,"onchange='set_ratio()'","50");
         }else{
            input_gen($id,$label,$id[0],5,$_GET,"disabled=disabled","50");
         }
         input_gen("ab_$id","AB",'checkbox',5,$_GET,"onclick='set_ab(\"ab_$id\")'","");
         input_gen("nc_$id","NC",'checkbox',5,$_GET,"onclick='set_nc(\"nc_$id\")'","");
         echo "</li>";
      }elseif($id=="paper_code"){
         echo "<li>";
         input_gen($id,$label,'select',1,$_GET,"onchange='course_select(\"$id\")'",$course_select1);
         echo "</li></form>";
      }elseif ($id=="break"){
         echo $label;
      }elseif ($id=="examiner"){
         echo "<li>";
         $opt="<option value=1>1</option><option value=2>2</option>";
         input_gen($id,$label,'select',1,$_GET,"onchange='examiner_change(\"$id\");'",$opt);
         echo "</li>";
      }elseif ($id=="num_of_stude"){
         echo "<li>";
         input_gen($id,$label,'text',5,$_GET,"onchange='set_rows(\"$id\")'","");
         echo "</li>";
      }else{
         if(empty($_GET[$id])){
            echo "<li>";
            input_gen($id,$label,'text',5,$_GET,"disabled=disabled style='border:0px; background:transparent;'","");
            echo "</li>";
         }else{
            echo "<li>";
            input_gen($id,$label,'text',5,$_GET,"","");
            echo "</li>";
         }
      }
   }
?>

</ul>

<ul class=menu>
<?php
$menu_array=array(
   "save_changes()"    => "Save draft",
   "fill_data()"       => "Fill from paste",
   "calculate_all()" => "Calculate all",
   "upload_marks()"    => "Submit the final",
   "revert_marks()"    => "Revert",
   "clean_content()" => "Clean"
);
foreach ($menu_array as $func => $name) {
   echo "<li><input type=button onclick='$func' value='$name'></li>";
}
?>
</ul>

<?php
echo "<div id=spreadsheet>";
include 'spreadsheet.php';
echo "</div>";
?>

<br />
<br />
<div id=status_bar><!-- Status bar --></div>
<script type='text/javascript' >
   init_element();
   calculate_all();
   sync.start();
</script>
</body>
</html>
