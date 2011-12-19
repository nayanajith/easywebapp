<div style='border: 1px solid silver; min-width: 50px; position: relative; background-color: white; z-index: 1'>
<div style='padding: 7px; color: gray'>
<center>
<?php 
$examId = $_POST['examId'];
//Acquiring the custom filename from FILES array
$filaname=null;
reset($_FILES);
$filaname=key($_FILES);
//If examid is empty request for examid
if(empty($_POST['examId']) && $filaname == null){
?>
<form name=mcq_exam_FRM action='' method=post>
   <table style='padding: 10px'>
      <tr>
         <td>ExamId:</td>
         <td id='examId_td'>
            <select name='examId' id='examId'>
<?php
openDB2("mcq_t");
$SQL="SELECT * FROM mcq_exam";
$RESULT=mysql_query($SQL,$GLOBALS['CONNECTION']);
while( $ROW = mysql_fetch_array($RESULT) ) {
   echo "<option value='".$ROW['examId']."'>".$ROW['examId']."</option>";
}
closeDB();
?>
            </select>
         </td>
      </tr>
   </table>
   <input type="submit" name="submit" value="Next&gt;" >
</form>

<?php
//If examid is not empty the filename is null then request for file
}elseif(!empty($_POST['examId'])&&$filaname==null){
?>
<form name=mcq_exam_FRM action='' method=post enctype="multipart/form-data">
   <table style='padding: 10px'>
      <tr>
         <td>Scanned answer file(csv):</td>
         <td>
            <input type="file" name="<?php echo $_POST['examId']; ?>" id="file" >
         </td>
      </tr>
   </table>
   <input type="submit" name="submit" value="Upload" ></form>
</form>
<?php
//If the filename is null prompt and return
if($filaname == null){
   return;
}else{
//Store files here
$store = "scanned_mark_sheets";
// Check for csv files

   if($_FILES[$filaname]["type"] != "text/csv"||$_FILES[$filename]["size"] < 20000){
      if($_FILES[$filename]["error"] > 0){
         drow_box("<br><br><center>Return Code: ".$_FILES[$filename]["error"]."</center><br><br>",'Error','yellow',250);
      }else{
         $dest=$store."/".$filaname.".csv";
         if(file_exists($dest)){
            echo $_FILES[$filaname]["name"] . " already exists. ";
            drow_box("<br><br><center>".$_FILES[$filaname]["name"]."already exists</center><br><br>",'Error','yellow',250);
         }else{
            move_uploaded_file($_FILES[$filaname]["tmp_name"],$dest);
            drow_box("<br><br><center>Stored in:".$dest."</center><br><br>",'Success','white',250);
         }
      }
   }else{
      drow_box('<br><br><center>Invalid File!</center><br><br>','Error','yellow',250);
   }
}
?>
</center>
</div>
</div>




