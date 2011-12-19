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
    *
    * Sample line of answers correspond to a student
    * 1223079;:;C;D;D;A;C;C;BLANK;D;A;A;E;C;B;C;C;A;C;(A,B,C);D;B
    */

   //Index number and answers will delimit with this
   protected $index_delimiter      =";:;";
   //Each question is delimited by this
   protected $question_delimiter   =";";
   //Multiple answers will delimited by this
   protected $answer_delimiter   ="[(,)]";
   //If this option is true first line is considered as the header and skipped
   protected $first_line_header  =true;
   //Blank answers will represented as this
   protected $blank               ="BLANK";
   protected $no_ans               ="NOA";

   //Delimiters for marking logic sheet
   //Question number delimiter
   protected $logic_question_delimiter   =";:;";
   //Option delimiter to seperate marks for each option A,B,C,D,E,BLANK
   protected $logic_option_delimiter   =";";
   //If this is true, first line of the file will consider as header and skipped
   protected $logic_first_line_header  =true;



   //All the information about the current paper
   protected $paper               =array();

   //Sections of the paper as in the form (0,30,60) in cluding first and last No.
   protected $sections            =array();

   //Current working section
   protected $cur_section         =1;
   protected $cur_section_size   =0;
   protected $cur_section_start   =0;
   protected $cur_section_end      =0;
   protected $no_of_students      =0;

   //Page will break at this no of rows in each table
   protected $page_break_at      =45;
   protected $division            =100;


   protected $options            =array("A","B","C","D","E");

   function __construct($paper_id) {
      $this->self['paper_id']   =$paper_id;

      //Load paper information to the array
      $query="SELECT * FROM mcq_paper WHERE paper_id='$paper_id'";
      $result=mysql_query($query,$GLOBALS['CONNECTION']);
      $this->paper = mysql_fetch_array($result);

      $this->sections=explode(",",$this->paper['sections']);


   }

   /*
    * Return number of sections in the paper
    */
   public function get_num_sections(){
      return sizeof($this->sections) -1;
   }

   public function get_course_id(){
      return $this->paper['course_id'];
   }
   public function get_exam_id(){
      return $this->paper['exam_id'];
   }

   /*
    * Read the file/database and return ansers lines
    */
   public function get_answer_lines(){
      $markFile=$GLOBALS['MARK_FILE_STORE']."/".$this->paper['course_id']."-".$this->paper['exam_id'].".csv";
      $lines    =file($markFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

      //Total No. of students sit the exam -> line count
      $this->no_of_students=sizeof($lines)-1;

      return $lines;
   }

   /*
    * Read the file/database and return ansers lines
    */
   public function get_logic_lines(){
      $markFile=$GLOBALS['MARK_FILE_STORE']."/".$this->paper['course_id']."-".$this->paper['exam_id']."-LOGIC.csv";
      $lines    =file($markFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

      //Total No. of students sit the exam -> line count
      $this->no_of_questions=sizeof($lines)-1;

      $start=$this->cur_section_start;
      $end   =$this->cur_section_end;

      $section_lines=array();

      foreach ($lines as $line_no => $line) {
         list($index_no,$answers)=explode($this->index_delimiter,$line);
         if($index_no>=$start && $index_no<=$end){
            $section_lines[]=$line;
         }
      }
      /*
      echo "<pre>";
      print_r($section_lines);
      echo "</pre>";
      */
      return $section_lines;
   }

   /*
    * Answer statistics generation for small number of students
    */
   public function gen_stat($section){
      //Set current section
      $this->cur_section=$section;

      //Validate sections
      if($section > $this->get_num_sections()){
         return -1;
      }
      //Read the marks from csv file
      $lines = $this->get_answer_lines();

      //Array to hold count of each option for a given paper
      $stat_array=array();

      //Section width of the given section
      $this->cur_section_size=($this->sections[$section]-$this->sections[$section-1]);
      $this->cur_section_start=$this->sections[$section-1]==0?$this->sections[$section-1]:$this->sections[$section-1]+1;
      $this->cur_section_end   =$this->sections[$section];

      //Initialize stat_array
      for($i=0;$i< $this->cur_section_size;$i++) {
         $stat_array[]=array(
            "A"         =>0,
            "B"         =>0,
            "C"         =>0,
            "D"         =>0,
            "E"         =>0,
         $this->blank=>0
         );
      }


      //Read line by line and count the options
      foreach ($lines as $line_no => $line) {
         //skip firstline (column headers) if requested
         if($this->first_line_header && $line_no == 0){
            continue;
         }

         //Split line in to index no and answer string
         list($index_no,$answers)=explode($this->index_delimiter,$line);

         //Split answers for each question and store in a array
         $tmp_array=explode($this->question_delimiter,$answers);
         $answers_array=array_slice($tmp_array,$this->sections[$section-1],$this->sections[$section]);

         //echo implode("__",$answers_array)."<br>";

         //Processing answer by unswer
         foreach ($answers_array as $key => $answer) {
            $answer=trim($answer," ,()");
            $answer=strtoupper($answer);
            if($answer != $this->blank){ //detect blank anwsers

               /*if(strlen($answer)>1 ){
                  //$multi_answer=split($this->answer_delimiter,$answer);
                  $multi_answer=preg_split($this->answer_delimiter,$answer);
                  }*/

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


   /*
    * Read marking logic
    */
   public function get_marking_logic($section=null){

      $no_of_questions   =$this->paper['no_of_questions'];
      //Start question No for the given section
      $section_start      =0;
      //End question No for the given section
      $section_end      =$no_of_questions;

      if($section != null){
         $this->cur_section_start=$this->sections[$section-1]==0?$this->sections[$section-1]:$this->sections[$section-1]+1;
         $this->cur_section_end   =$this->sections[$section];
         //Start question No for the given section
         $section_start   =$this->cur_section_start;
         //End question No for the given section
         $section_end   =$this->cur_section_end;
      }
      
      //Read logic lines from file/database
      $lines            =$this->get_logic_lines();
      $line_count         =sizeof($lines);
      echo $line_count;

      //If the first line of the file is header it will skipped and section will shifted on line down
      if($this->logic_first_line_header){
         $line_count      -=1;
         $section_start   +=1;
         $section_end   +=1;
      }

      /*
       if($line_count != $no_of_questions){
         return -1;
         }
         */

      $logic_array   =array();
      $multi_logic   =array();
      $multi_question      =0;
      $multi_logic_count   =0;
      $line_no=0;

      //for ($line_no=0;$line_no<=$this->cur_section_size+$sectio_extend;$line_no++) {
      foreach ($lines as $line) {
         //Split line in to index no and answer string
         list($question_no,$logic)=explode($this->logic_question_delimiter,$lines[$line_no]);
         @list($question_no_next,$logic_next)=explode($this->logic_question_delimiter,$lines[$line_no+1]);

         if($question_no == $question_no_next){
            $multi_question=$question_no;
            if($multi_logic_count==0){
               $multi_logic=array();
            }

            $tmp_array=explode($this->logic_option_delimiter,$logic);
            $multi_logic[]=array(
            "A"         =>   $tmp_array[0],
            "B"         =>   $tmp_array[1],
            "C"         =>   $tmp_array[2],
            "D"         =>   $tmp_array[3],
            "E"         =>   $tmp_array[4],
            $this->blank=>   $tmp_array[5],
            $this->no_ans=>$tmp_array[6]
            );
            $multi_logic_count++;
         }else{

            if($multi_logic_count!=0){
               //Split answers for each question and store in a array
               $tmp_array=explode($this->logic_option_delimiter,$logic);

               $multi_logic[]=array(
               "A"         =>   $tmp_array[0],
               "B"         =>   $tmp_array[1],
               "C"         =>   $tmp_array[2],
               "D"         =>   $tmp_array[3],
               "E"         =>   $tmp_array[4],
               $this->no_ans=>$tmp_array[5],
               $this->blank=> $tmp_array[6]
               );

               $logic_array[$question_no]=$multi_logic;
               $multi_logic_count=0;
            }else{

               //Split answers for each question and store in a array
               $tmp_array=explode($this->logic_option_delimiter,$logic);

               $logic_array[$question_no]=array(
               "A"         =>   $tmp_array[0],
               "B"         =>   $tmp_array[1],
               "C"         =>   $tmp_array[2],
               "D"         =>   $tmp_array[3],
               "E"         =>   $tmp_array[4],
               $this->no_ans=>$tmp_array[5],
               $this->blank=> $tmp_array[6]
               );
            }
         }
         $line_no++;
      }
      /*
       echo "<pre>";
       print_r($logic_array);
       echo "</pre>";
       */
      return $logic_array;
   }

   /*
    * Mark answers
    */

   public function mark_answers($section=null){
      //Set current section
      $this->cur_section=$section;

      //Validate sections
      if($section > $this->get_num_sections()){
         return -1;
      }
      //Save results here
      $result_path=$GLOBALS['MARK_FILE_STORE']."/".$this->paper['course_id']."-".$this->paper['exam_id']."-RESULT.csv";
      $result_sheet = fopen($result_path, 'w');
      fputcsv($result_sheet, array("Serial No.","Index No.","Mark"));


      //Read the marks from csv file
      $lines = $this->get_answer_lines();

      //Section width of the given section
      $this->cur_section_size=($this->sections[$section]-$this->sections[$section-1]);

      //Get marking logic array
      $marking_logic_array=array();

      if($section != null){
         $marking_logic_array=$this->get_marking_logic($section);
      }else{
         $marking_logic_array=$this->get_marking_logic();
      }

      /*
      echo "<pre>";
      print_r($marking_logic_array);
      echo "</pre>";
      */

      //Read line by line and mark the answers
      foreach ($lines as $line_no => $line) {
         //skip firstline (column headers) if requested
         if($this->first_line_header && $line_no == 0){
            continue;
         }

         //Students totla mark
         $mark=0;

         //Split line in to index no and answer string
         list($index_no,$answers)=explode($this->index_delimiter,$line);

         //Split answers for each question and store in a array
         $tmp_array=explode($this->question_delimiter,$answers);
         $answers_array=$tmp_array;

         if($section != null){
            $answers_array=array_slice($tmp_array,$this->sections[$section-1],$this->sections[$section]);
         }

         //Processing answer by unswer
         foreach ($answers_array as $key => $answer) {
            $answer=trim($answer," ,()");
            $answer=strtoupper($answer);

            //echo ($this->sections[$section-1]+$key+1).",";
            $marking_logic=$marking_logic_array[$this->sections[$section-1]+$key+1];

            //Multipe logic handle
            if(isset($marking_logic[0])&&is_array($marking_logic[0])){
             $single_mark=0;

             foreach ($marking_logic as $key2 => $logic) {
                $tmp_mark=0;
                if($logic[$this->no_ans]!=0){
                   $tmp_mark+=$logic[$this->no_ans];
                }else{
                   if($answer == $this->blank){
                      $tmp_mark=$logic[$this->blank];
                   }else {
                      foreach($this->options as $option){
                         if(strstr($answer,$option)==$option){
                            $tmp_mark+=$logic[$option];
                         }
                      }
                   }
                }

                //Choose the max score from multipe options
                if($single_mark<$tmp_mark || $key2==0){
                   $single_mark=$tmp_mark;
                }
             }
             $mark+=$single_mark;
          }else{
             if($marking_logic[$this->no_ans]!=0){
                $mark+=$marking_logic[$this->no_ans];
                //echo "<pre>";
                //print_r($marking_logic);
                //echo "</pre>";
             }else{
                if($answer == $this->blank){  //detect blank anwsers
                   $mark+=$marking_logic[$this->blank];
                }else{
                   foreach($this->options as $option){
                      if(strstr($answer,$option)==$option){
                         $mark+=$marking_logic[$option];
                      }
                   }
                }
             }
          }
         }
         //$mark=round($mark/$this->paper['no_of_questions'],0);
         //echo $mark."<br>";
         $mark=round($mark/$this->cur_section_size,0);
         //$query="UPDATE mcq_answers SET marks='$mark' WHERE paper_id='$paper_id' AND index_no='$index_no'";
         //$result=mysql_query($query,$GLOBALS['CONNECTION']);
         fputcsv($result_sheet, array($line_no,$index_no,$mark));
      }
      fclose($result_sheet);
      return $result_path;
   }

   public function print_marks($result_path){
      $row = 1;
      echo "<table class=clean border=1>";
      if (($handle = fopen($result_path, "r")) !== FALSE) {
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if($row==1){
               echo "<thead>";
            }
            echo "<tr>";
            for ($c=0; $c < sizeof($data); $c++) {
               echo "<td>".$data[$c]."</td>\n";
            }
            echo "</tr>";
            if($row==1){
               echo "</thead>";
            }
            $row++;
         }
         fclose($handle);
      }
      echo "</table>";
   }

   public function DB_to_csv(){
      /*
     $query_export="SELECT order_id,product_name,qty
     FROM orders
     INTO OUTFILE '$csv'
     FIELDS TERMINATED BY ','
     ENCLOSED BY '\"'
     LINES TERMINATED BY '\n'";
     $csv='test.csv';
     $table='';
     $query_import = "LOAD DATA INFILE '$csv'
     INTO TABLE $table
     FIELDS TERMINATED BY ':'
     LINES TERMINATED BY '\\r\\n' (examId,courseId,indexNo,answers)";

     */
   }

   public function csv_to_DB($result_path){

      $query_import = "LOAD DATA INFILE '$csv'
     INTO TABLE $table
     FIELDS TERMINATED BY ':'
     LINES TERMINATED BY '\\r\\n' (examId,courseId,indexNo,answers)";
         
      $row = 1;
      echo "<table class=clean border=1>";
      if (($handle = fopen($result_path, "r")) !== FALSE) {
         while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if($row==1){
               echo "<thead>";
            }
            echo "<tr>";
            for ($c=0; $c < sizeof($data); $c++) {
               echo "<td>".$data[$c]."</td>\n";
            }
            echo "</tr>";
            if($row==1){
               echo "</thead>";
            }
            $row++;
         }
         fclose($handle);
      }
      echo "</table>";
   }

}
?>


<?php
if(!($print||$csv)){
   if( isset($_GET['submit'])){
      ?>
<div align=right style='padding: 5px;'><a class='dataAction'
   href="page_gen.php?submit=<?php echo $_GET['submit'];?>&page=<?php echo $_GET['page'];?>&print=true&paper_id=<?php echo $_GET['paper_id'];?>&title=Item Analysis Report"
   target='_blank' title='Print Data'>print</a> <a class='dataAction'
   href="page_gen.php?submit=<?php echo $_GET['submit'];?>&page=<?php echo $_GET['page'];?>&csv=true&paper_id=<?php echo $_GET['paper_id'];?>&title=Item Analysis Report"
   target='_blank' title='Download Data'>csv</a></div>
      <?php }?>
<div style='padding: 7px; color: black' id=cont align=center><?php
}
if(empty($_GET['paper_id'])){
   ?>
<form name=mcq_exam_FRM action='page_gen.php' method=get><input
   type=hidden name='page' value='<?php echo $_GET['page'];?>'> <input
   type=hidden name='module' value='<?php echo $_GET['module'];?>'>
<table style='padding: 10px'>
   <tr>
      <td>Paper :</td>
      <td id='paper_id_td'><select name='paper_id' id='paper_id'>
      <?php
      openDB2("mcq_t");
      $SQL="SELECT * FROM mcq_paper";
      $RESULT=mysql_query($SQL,$GLOBALS['CONNECTION']);
      while( $ROW = mysql_fetch_array($RESULT) ) {
         echo "<option value='".$ROW['paper_id']."'>".$ROW['course_id']."&nbsp;@&nbsp;".$ROW['exam_id']."</option>";
      }
      closeDB();
      ?>
      </select></td>
   </tr>
</table>
<input type="submit" name="submit" value="Generate Report" > <input
   type="submit" name="submit" value="Mark Answers" ></form>
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

         if(isset($_GET['csv'])){
            $csv_file=$mcq_paper->mark_answers();
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="result.csv"');
            readfile($csv_file);
         }else{
            $mcq_paper->print_marks($mcq_paper->mark_answers());
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
if(!($csv||$print)){
   echo "</div>";
}
?>
