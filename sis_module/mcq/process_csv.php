<?php
include MOD_CLASSES."/process_csv_class.php";

if(!($print||$data)){
   if( isset($_GET['submit'])){
      ?>
<div align=right style='padding: 5px;'><a class='dataAction'
   href="<?php echo gen_url();?>&submit=<?php echo $_GET['submit'];?>&page=<?php echo $_GET['page'];?>&print=true&paper_id=<?php echo $_GET['paper_id'];?>&title=Item Analysis Report"
   target='_blank' title='Print Data'>print</a> <a class='dataAction'
   href="<?php echo gen_url();?>&submit=<?php echo $_GET['submit'];?>&page=<?php echo $_GET['page'];?>&data=true&paper_id=<?php echo $_GET['paper_id'];?>&title=Item Analysis Report"
   target='_blank' title='Download Data'>csv</a></div>
      <?php }?>
<div style='padding: 7px; color: black' id=cont align=center><?php
}
if(empty($_GET['paper_id'])){
   ?>
<form name=mcq_exam_FRM action='<?php echo gen_url(); ?>' method=get><input
   type=hidden name='page' value='<?php echo $_GET['page'];?>'> <input
   type=hidden name='module' value='<?php echo $_GET['module'];?>'>
<table style='padding: 10px'>
   <tr>
      <td>Paper :</td>
      <td id='paper_id_td'><select name='paper_id' id='paper_id'>
      <?php
      $SQL="SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['paper'];
      $RESULT=exec_query($SQL,Q_RET_MYSQL_RES);
      while( $ROW = mysql_fetch_assoc($RESULT) ) {
         echo "<option value='".$ROW['paper_id']."'>".$ROW['course_id']."&nbsp;@&nbsp;".$ROW['exam_id']."</option>";
      }
      ?>
      </select></td>
   </tr>
</table>


<input type="submit" name="submit" value="Generate Report" > 
<input type="submit" name="submit" value="Mark Answers" >
</form>
      <?php
}else{
   if(isset($_GET['submit'])){
      $submit=$_GET['submit'];
      openDB2('mcq_t');
      $mcq_paper= new MCQ_paper($_GET['paper_id']);
      if($submit=='Generate Report'){
         for($i=1;$i<$mcq_paper->get_num_sections()+1;$i++){

            //Page break after each page
            if($i>1){
               echo "<div class=break></div>";
            }
            //Page titles
            echo "<h2>".$mcq_paper->get_course_id()." ".$mcq_paper->get_exam_id()."</h2>";
            echo "<h3>Section $i</h3>";

            //Print satistics
            $mcq_paper->print_stat($mcq_paper->gen_stat($i));
         }
      }elseif($submit=='Mark Answers'){
         //$mcq_paper->get_marking_logic();

         if(isset($_GET['data'])){
            $csv_file=$mcq_paper->mark_answers(1);
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="result.csv"');
            readfile($csv_file);
         }else{
            $mcq_paper->print_marks($mcq_paper->mark_answers(1));
         }

      }
      closeDB();
   }

   /*
    openDB2("mcq_t");
    $SQL="SELECT * FROM mcq_marks";
    $RESULT=mysql_query($SQL,$GLOBALS['CONNECTION']);
    while( $ROW = mysql_fetch_array($RESULT) ) {
    $ans_array=explode(",", $ROW['answers']);
    foreach ($ans_array as $ans) {
      echo $ans;
      }
      echo trim($ans_array[0]);
      }
      closeDB();
      */
}
if(!($data||$print)){
   echo "</div>";
}
?>
