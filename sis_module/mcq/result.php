<?php
include 'mcq/config.php';
/*
 * MCQPaper Class to hold all the functionalities which required to process MCQ
 * Papers
 */
class MCQ_paper {
   /*Array to hold index and batch*/
   protected $self               = array();

   /*
    * Properties of the paper
    */
   protected $index_delimiter      =";:;";
   protected $question_delimiter   =";";
   protected $answer_delemiter   ="[(,)]";
   protected $first_line_header  =true;
   
   protected $sections            =array(0,30,60);
   protected $cur_section         =1;
   protected $cur_section_size   =0;
   
   protected $no_of_students      =0;
   
   protected $page_break_at      =45;

   protected $blank               ="BLANK";
   protected $options            =array("A","B","C","D","E");

   function __construct($course_id,$exam_id) {
      $this->self['course_id']   =$course_id;
      $this->self['exam_id']      =$exam_id;
   }

   /*
    * Answer statistics generation for small number of students
    */
   public function gen_stat($section){
      //Set current section
      $this->cur_section=$section;
      
      //Read the marks from csv file
      $markFile=$GLOBALS['MARK_FILE_STORE']."/".$this->self['exam_id'].".csv";
      $lines    =file($markFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      
      //Total No. of students sit the exam -> line count
      $this->no_of_students=sizeof($lines)-1;
      
      //Array to hold count of each option for a given paper
      $stat_array=array();

      //Section width of the given section
      $this->cur_section_size=($this->sections[$section]-$this->sections[$section-1]);
      
      //Initialize stat_array
      for($i=0;$i< $this->cur_section_size;$i++) {
         $stat_array[]=array(
            "A"=>0,
            "B"=>0,
            "C"=>0,
            "D"=>0,
            "E"=>0,
         $blank=>0
         );
      }


      //Read line by line and count the options
      foreach ($lines as $line_no => $line) {
         //skip firstline (column headers) if requested
         if($first_line_header && $line_no == 0){
            continue;
         }

         //Split line in to index no and answer string
         list($index_no,$answers)=explode($this->index_delimiter,$line);
         
         //Split answers for each question and store in a array
         $tmp_array=explode($this->question_delimiter,$answers);
         $answers_array=array_slice($tmp_array,$this->sections[$section-1],$this->sections[$section]);

         //Processing answer by unswer
         foreach ($answers_array as $key => $answer) {
            //Filter multiple answers
            if($answer != $this->blank){ //detect blank anwsers
               if(strlen($answer)>1 ){
                  $multi_answer=split($this->answer_delemiter,$answer);
               }
               foreach($this->options as $option){
                  if(strstr($answer,$option)==$option){
                     //Count option answers
                     $stat_array[$key][$option]+=1;
                  }
               }
            }else{
               //count blank answers
               $stat_array[$key][$this->blank]+=1;
            }
         }
      }
      return $stat_array;
   }

   /*
    * Echo/return two value td
    */
   public function two_val_td($val1,$val2,$emp){
      $td="";
      //If emp is true Emphasize the record
      if($emp){
         $td.="<td>
               <div style='font-weight:bold;' class=left>".$val1."</div>
               &nbsp;&nbsp;
               <div style='font-weight:bold;' class=right>".$val2."</div>
            </td>";
      }else{
         $td.="<td>
            <div class=left>".$val1."</div>
            &nbsp;&nbsp;
            <div class=right>".$val2."</div>
         </td>";
      }
      echo $td;
   }

   public function print_stat($stat_array) {
      //Statistics table
      echo "<table cellpadding=3 border=1 style='border-collapse:collapse;' id=itemAnalysis width=100%>";
      
      //generate the HEADER -- header will repeat in each page when printed
      echo "<thead>
      <tr style='margin:0px;padding:0px;'>
         <td colspan=10 style='margin:0px;padding:0px;'></td>
      </tr>
      <tr style='text-align:center;font-size:10px;'>
      <th>QUESTION</th>";
      
      //generate rest of the options
      foreach ($this->options as $option) {
         echo "<th>".$option."</th>";
      }
      echo "<th>ATTEMPTS</th><th>BLANKS</th></tr></thead>";
      
      //printing data from stat_array
      foreach ($stat_array as $quest_number => $opt_array) {
         //Add page break for print at given line number or end of the table
         if($quest_number == $this->page_break_at || ($quest_number+1)==$this->cur_section_size){
            echo "<tr ><th style='font-size:10px;'>".($quest_number+1)."</th>";
         }else{
            echo "<tr><th style='font-size:10px;'>".($this->sections[$this->cur_section-1]+$quest_number+1)."</th>";
         }
         
         $non_blank=0; //To count total non blank answers for the question
         
         //Sort to obtain the maximum count to be bold in print
         arsort($opt_array);
         
         //Printing individual cont
         foreach ($this->options as $option) {
            //count for each option
            $opt_count=$opt_array[$option];
            
            //count percentage for each option
            $opt_percent=round((($opt_count/$this->no_of_students)*100),2);
            
            //Non blank accumulation
            $non_blank+=$opt_count;
            
            //emphesize and print max count and percentage
            if(key($opt_array)==$option){
               $this->two_val_td($opt_count,$opt_percent."%",true);
            }else{
               $this->two_val_td($opt_count, $opt_percent."%",false);
            }
         }
         
         //non blank total percentage
         $non_blank_percent=round((($non_blank/$this->no_of_students)*100),2);
         
         //print non blank total
         $this->two_val_td($non_blank, $non_blank_percent."%",false);

         //blank count
         $blanks=$opt_array["BLANK"];
         
         //blank percentage
         $blank_percent=round((($blanks/$this->no_of_students)*100),2);
         
         //emphesize and print blank count and percentage
         if(key($opt_array)=="BLANK"){
            $this->two_val_td($blanks,$blank_percent."%",true);
         }else{
            $this->two_val_td($blanks, $blank_percent."%",false);
         }
         echo "</tr>";
      }
      echo "</table>";
   }
}
?>


<?php 
if(!($print||$csv)){
?>
<div align=right style='padding: 5px;'>
<a 
   class   ='dataAction'
   href   ="page_gen.php?page=<?php echo $_GET['page'];?>&print=true&exam_id=<?php echo $_GET['exam_id'];?>&title=<?php echo $_GET['exam_id'];?> Item Analysis Report"
   target='_blank' 
   title   ='Print Data'
>print</a> 
<a 
   class   ='dataAction'
   href   ="" 
   title   ='Download Data'
>csv</a>
</div>
<?php }?>
<div style='padding: 7px; color: gray' id=cont align=center>
<?php
if(empty($_GET['exam_id'])){
?>
<form name=mcq_exam_FRM action='page_gen.php' method=get><input
   type=hidden name='page' value='<?php echo $_GET['page'];?>'> <input
   type=hidden name='module' value='<?php echo $_GET['module'];?>'>
<table style='padding: 10px'>
   <tr>
      <td>ExamId:</td>
      <td id='exam_id_td'><select name='exam_id' id='exam_id'>
      <?php
      openDB2("mcq_t");
      $SQL="SELECT * FROM mcq_exam";
      $RESULT=mysql_query($SQL,$GLOBALS['CONNECTION']);
      while( $ROW = mysql_fetch_array($RESULT) ) {
         echo "<option value='".$ROW['examId']."'>".$ROW['examId']."</option>";
      }
      closeDB();
      ?>
      </select></td>
   </tr>
</table>
<input type="submit" name="submit" value="Next&gt;" ></form>
<?php 
}else{
$mcq_paper= new MCQ_paper($_GET['course_id'], $_GET['exam_id']);
$mcq_paper->print_stat($mcq_paper->gen_stat(1));
$mcq_paper->print_stat($mcq_paper->gen_stat(2));
   
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
}
?>
</div>
