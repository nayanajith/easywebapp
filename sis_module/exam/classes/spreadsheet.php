<?php
/*
session_start();  
include_once 'config.php';
*/

if(isset($_SESSION['views']))
    $_SESSION['views'] = $_SESSION['views']+ 1;
else
    $_SESSION['views'] = 1;
    
$_SESSION['host']=$_SERVER['REMOTE_ADDR'];
?>

<?php
$rows=$_GET['rows'];

if ($rows <= 1){
   $rows=10;
}


$cols=array("id"=>"","INDEX_NO"=>"","PAPER"=>"","ASSIGNMENT"=>"","PUSH"=>"");
$xml=null;
$index_no   ="";
$paper       ="";
$assignment   ="";
$push      ="";

/*Load xml database if exists*/
if (file_exists($xml_marks) && !isset($_GET['expand'])){
   $xml = simplexml_load_file($xml_marks);   
   /*Number of rows from xml*/
   $rows=sizeof($xml->student);
}

echo "<input type=hidden value='$rows' id=num_rows>";

$paste_ta="<textarea cols=4 rows=2 id=row_data name=raw_data style='border:0px;overflow:hidden;' onclick='this.innerHTML=\"\";'>Paste Here</textarea>";
$paste_ta2="<textarea cols=4 rows=2 style='border:0px;overflow:hidden;' onclick='this.innerHTML=\"\";'>Paste Here</textarea>";

$fixed_th="<table border=1 align=center cellpadding=0 cellspacing=0 id=table_header '>\n
<tr>
<th width=50px>$paste_ta</th>
<th width=50px>INDEX NO</th>
<th width=50px>PAPER</th>
<th width=50px>ASSIGNMENT</th>
<th width=50px>FINAL</th>
<th width=50px>GRADE</th>
<th width=50px>GPV</th>
<th width=50px>SUGGESTION<br>(mark\t|\tgrade\t|\tgpv)</th>
<th width=50px>PUSH</th>
</tr>
</table>
";
echo "<div  id=table_scroll>";
//echo $fixed_th;
echo "<table border=1 align=center cellpadding=0 cellspacing=0  style='border-collapse:collapse;border:1px solid black;font-size:11px;'>\n";
echo "<tr style='visibility:vissible;overflow;hidden;height:0px;'>
<th width=50px>$paste_ta</th>
<th width=50px>INDEX NO</th>
<th width=50px>PAPER</th>
<th width=50px>ASSIGNMENT</th>
<th width=50px>FINAL</th>
<th width=50px>GRADE</th>
<th width=50px>GPV</th>
<th width=50px>SUGGESTION<br>(mark\t|\tgrade\t|\tgpv)</th>
<th width=50px>PUSH</th>
</tr>";
if($rows){
   for ($i=1; $i<$rows; $i++){
      
      /*Reading data from xml*/
      if($xml && !isset($_GET['expand'])){
         foreach($xml->student[$i-1]->attributes() as $attribute => $value) {
            $cols[$attribute]=$value;
         }
      }
      
      echo "<tr>
      <td id='0:$i' class='serial_no' title='0:$i'>
      $i
      </td>
      <td>
         <input size=7 type=text id='1:$i' name='1:$i' class='index_no' title='1:$i' value='".$cols['INDEX_NO']."' >
      </td>
      <td>
         <input size=3: type=text id='2:$i' name='2:$i' class='paper' title='2:$i' value='".$cols['PAPER']."'  onchange='calculate_marks($i)'>
      </td>
      <td>
         <input size=3 type=text id='3:$i' name='3:$i' class='assignment'  title='3:$i'  value='".$cols['ASSIGNMENT']."' onchange='calculate_marks($i)'>
      </td>
      <td class='final'>
         <div id='4:$i'  title='4:$i' ></div>
      </td>
      <td class='grade'>
         <div id='5:$i'  title='5:$i' ></div>
      </td>
      <td class='gpv'>
         <div id='6:$i'  title='6:$i' ></div>
      </td>
      <td class='suggestion'>
         <div id='7:$i' title='7:$i' ></div>
      </td>
      <td>
         <input size=2 type=text id='8:$i'  title='8:$i' class='push' value='".$cols['PUSH']."' onchange='calculate_marks($i)'>
      </td>
   </tr>";
   }
}else{
   echo "<tr>
   <td><input type=text></td>
   <td><input type=text></td>
   <td><input type=text></td>
   <td><input type=text></td>
   <td><input type=text></td>
</tr>";
}
echo "</table></div>";
?>
