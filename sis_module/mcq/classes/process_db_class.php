<?php
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
   protected $blank_answer         ="BLANK";
   protected $no_answer            ="NOA";

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
   protected $cur_section_range   =array();
   protected $no_of_students      =0;

   //Page will break at this no of rows in each table
   protected $page_break_at      =45;
   protected $division            =100;


   protected $options            =array("A","B","C","D","E");

   protected $paper_state         =array(
                                    'INIT'               =>'INIT',   
                                    'ANSWER_UPLOADED'      =>'ANSWER_UPLOADED',   
                                    'LOGIC_UPLOADED'      =>'LOGIC_UPLOADED',   
                                    'BOTH_UPLOADED'      =>'BOTH_UPLOADED',   
                                    'ANSWERS_EXTRACTED'   =>'ANSWERS_EXTRACTED',   
                                    'LOGIC_EXTRACTED'      =>'LOGIC_EXTRACTED',   
                                    'BOTH_EXTRACTED'      =>'BOTH_EXTRACTED',   
                                    'SECTION_MARKED'      =>'SECTION_%s_MARKED',   
                                    'ALL_MARKED'         =>'ALL_MARKED',   
                                    'ERROR'               =>'ERROR_%s'   
                                 );

   function __construct($paper_id) {
      $this->self['paper_id']   =$paper_id;

      //Load paper information to the array
      $res=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['paper']." WHERE paper_id='$paper_id'",Q_RET_MYSQL_RES);
      $row=mysql_fetch_assoc($res);
      $this->paper = $row;
      
      $this->sections                  =explode(",",$row['sections']);
      $this->index_delimiter            =$row['index_delimiter'];         
      $this->question_delimiter         =$row['question_delimiter'];      
      $this->answer_delimiter            =$row['answer_delimiter'];        
      $this->first_line_header         =$row['first_line_header'];       
      $this->blank_answer               =$row['blank_answer'];            
      $this->no_answer                  =$row['no_answer'];               
      $this->logic_question_delimiter   =$row['logic_question_delimiter'];
      $this->logic_option_delimiter      =$row['logic_option_delimiter'];  
      $this->logic_first_line_header   =$row['logic_first_line_header']; 

      $row=exec_query("SELECT count(*) FROM ".$GLOBALS['MOD_P_TABLES']['answers']." WHERE paper_id='".$this->self['paper_id']."'",Q_RET_ARRAY);
      $this->no_of_students=$row[0]['count(*)'];

   }

   /*Set/Change paper processing state*/
   public function set_state($state,$detail){
      $query="UPDATE ".$GLOBALS['MOD_P_TABLES']['paper']." SET state='".sprintf($this->paper_state[$state],$detail)."' WHERE paper_id='".$this->self['paper_id']."'";
      $res=exec_query($query,Q_RET_MYSQL_RES);
      return $res;
   }

   /*
    * Return number of sections in the paper
    */
   public function get_num_sections(){
      return sizeof($this->sections);
   }

   /*Return an array('start'=>#, 'end'=>#) with section range 0 will return full length*/
   public function get_section_range($section){
      /*Array of start and end of the section*/
      $section_range=array(
         'start'   =>0,
         'end'      =>0
      );
      switch($section){
         case 0:
            /*all sections*/
            $section_range['start']   =1;
            $section_range['end']   =array_sum($this->sections);
         break;
         case 1:
            /*first section*/
            $section_range['start']   =1;
            $section_range['end']   =$this->sections[0];
         break;
         default:
            /*sections other tan first section*/
            for($i=1;$i<$section;$i++){
               $section_range['start']+=$this->sections[$i-1];
            }
            $section_range['end']=$section_range['start']+$this->sections[$section-1];
            $section_range['start']+=1;
         break;
      }
   
      return $section_range;
   }

   /*Return course id*/
   public function get_course_id(){
      return $this->paper['course_id'];
   }

   /*Returnn course id*/
   public function get_exam_id(){
      return $this->paper['exam_id'];
   }

   /**
   This function will extract the student answers csv file and inser the data to the database
   @param 
   @return 
   */
   public function extract_answers(){
      $answer_file=MOD_A_CSV."/answer_file_".$this->self['paper_id'].".csv";
      if(file_exists($answer_file)){
         /*Temporary table to extract answers*/
         $temp_table="temp_answers_".$_SESSION['user_id'];

         /*Drop temporary table if exists before uploading marks*/
         exec_query("DROP TABLE IF EXISTS $temp_table",Q_RET_MYSQL_RES);

         /*Temp table creationn sql*/
         $create_table="
             CREATE TABLE `$temp_table` (
                `index_no`  varchar(40) NOT NULL,
                `answers`   text NOT NULL,
               `paper_id`  int,
               `state`     varchar(50),
                PRIMARY KEY  (`index_no`)
           );
         ";

         /*Create temp table and if success continue with extraction*/
         if(exec_query($create_table,Q_RET_MYSQL_RES)){
            $table      =$temp_table;
            $delimiter   =$this->index_delimiter;
            $encloser   ='';
            $terminator   ='\n';
            $field_array=array('index_no','answers');
            $first_line_header=$this->first_line_header;

            /*Extract data from csv to temp table*/
            csv_to_db2($answer_file,$table,$field_array,$delimiter,$encloser,$terminator,$first_line_header,$db=null);

            /*Set other parameters (paper_id, state) in temp table */
            exec_query("UPDATE $temp_table SET paper_id='".$this->self['paper_id']."', state='EXTRACTED'",Q_RET_MYSQL_RES);

            /*Delete previouse uploads for the same paper*/
            exec_query("DELETE FROM ".$GLOBALS['MOD_P_TABLES']['answers']." WHERE paper_id='".$this->self['paper_id']."'",Q_RET_MYSQL_RES);

            /*insert data in to the actual answers table from temp table*/
            if(exec_query("REPLACE INTO ".$GLOBALS['MOD_P_TABLES']['answers']."(index_no,answers,paper_id,state) SELECT index_no,answers,paper_id,state FROM  $temp_table",Q_RET_MYSQL_RES)){
               return true;
            }else{
               log_msg("ERROR","Answers extraction failed!");   
               return false;
            }

            /*Delete temporary table*/
            exec_query("DROP TABLE IF EXISTS $temp_table",Q_RET_MYSQL_RES);
         }else{
            log_msg("ERROR","Error creating temp tablei!");   
            return false;
         }
      }else{
         log_msg("ERROR","Answer file doees not exists!");   
         return false;
      }
   }

   /**
   This function will extract the logic csv file and inser the data to the database
   @param 
   @return 
   */
   public function extract_marking_logic(){
      $logic_file=MOD_A_CSV."/mark_logic_file_".$this->self['paper_id'].".csv";
      if(file_exists($logic_file)){
         /*Temporary table to extract answers*/
         $temp_table="temp_logic_".$_SESSION['user_id'];

         /*Drop temporary table if exists before uploading marks*/
         exec_query("DROP TABLE IF EXISTS $temp_table",Q_RET_MYSQL_RES);

         /*Temp table creationn sql*/
         $create_table="
             CREATE TABLE `$temp_table` (
                `question`  int NOT NULL,
                `option_id` int NOT NULL DEFAULT 1,
               `multiple_choice` boolean NOT NULL DEFAULT 1,
               `mark_for_wrong_sns` int NULL,
               `mark_for_correct_sns` int NULL,
               `A`         int NULL,
               `B`         int NULL,
               `C`         int NULL,
               `D`         int NULL,
               `E`         int NULL,
               `NOA`       int NULL,
               `BLANK`     int NULL,
               `paper_id`  varchar(50),
               PRIMARY KEY (`question`,`option_id`)
           );
         ";

         /*Create temp table and if success continue with extraction*/
         if(exec_query($create_table,Q_RET_MYSQL_RES)){

            /*parameters for csv extraction function*/
            $table      =$temp_table;
            $delimiter   =$this->logic_question_delimiter;
            $encloser   ='';
            $terminator   ='\n';
            $field_array=array('question','option_id','multiple_choice','mark_for_wrong_sns','mark_for_correct_sns','A','B','C','D','E','NOA','BLANK');
            $first_line_header=$this->logic_first_line_header;

            /*TODO: header verification and column mapping*/

            /*Extract data from csv to temp table*/
            csv_to_db2($logic_file,$table,$field_array,$delimiter,$encloser,$terminator,$first_line_header,$db=null);

            /*Set other parameters (paper_id, state) in temp table */
            exec_query("UPDATE $temp_table SET paper_id='".$this->self['paper_id']."'",Q_RET_MYSQL_RES);

            /*Clean up previouse deta before replacing*/
            exec_query("DELETE FROM ".$GLOBALS['MOD_P_TABLES']['marking_logic']." WHERE paper_id='".$this->self['paper_id']."'",Q_RET_MYSQL_RES);

            /*insert data in to the actual answers table from temp table*/
            if(exec_query("REPLACE INTO ".$GLOBALS['MOD_P_TABLES']['marking_logic']."(".implode(',',$field_array).",paper_id) SELECT ".implode(',',$field_array).",paper_id FROM  $temp_table",Q_RET_MYSQL_RES)){
               return true;
            }else{
               log_msg("ERROR","Logic extraction failed!");   
               return false;
            }

            /*Delete temporary table*/
            exec_query("DROP TABLE IF EXISTS $temp_table",Q_RET_MYSQL_RES);
         }else{
            log_msg("ERROR","Error creating temp table!");   
            return false;
         }
      }else{
         log_msg("ERROR","Logic file doees not exists!");   
         return false;
      }

   }

   /*
    * Read the file/database and return ansers lines
    */
   public function get_answer_lines($section_range){
      //$res=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['answers']." WHERE ",Q_RET_MYSQL_RES);
      $res=exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['answers']." WHERE paper_id='".$this->self['paper_id']."'",Q_RET_MYSQL_RES);
      return $res;
   }

   /*
    * Read the file/database and return ansers lines
    */
   public function get_logic_lines($section_range){

      $arr = exec_query("SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['marking_logic']." WHERE paper_id='".$this->self['paper_id']."' AND question >= ".$section_range['start']." AND question <= ".$section_range['end']." ",Q_RET_ARRAY);

      $logic_array=array();
      foreach($arr as $l_arr){
         $logic_array[$l_arr['question']][$l_arr['option_id']]=$l_arr;
      }

      return $logic_array;
   }

   /*
    * Answer statistics generation for small number of students TODO: port the function to work with DB not CSV
    */
   public function gen_stat($section){
      //Set current section
      $this->cur_section=$section;

      /*Get section range */
      $section_range=$this->get_section_range($section);
      
      /*section range used in stat printing function*/
      $this->cur_section_range=$section_range;
      
      /*Size of the section*/
      $this->cur_section_size=($section_range['end']-$section_range['start']+1);
      
      //Validate sections
      if($section > $this->get_num_sections()){
         return -1;
      }

      //Read the marks from csv file
      $answer_lines_res = $this->get_answer_lines($section_range);

      //$lines = $this->get_answer_lines($section_range);

      //Array to hold count of each option for a given paper
      $stat_array=array();

      //Initialize stat_array
      for($i=0;$i< $this->cur_section_size;$i++) {
         $stat_array[]=array(
            "A"         =>0,
            "B"         =>0,
            "C"         =>0,
            "D"         =>0,
            "E"         =>0,
         $this->blank_answer=>0
         );
      }

      //Read line by line and count the options
      //foreach ($lines as $line_no => $line) {
      while($row=mysql_fetch_assoc($answer_lines_res)){
            
         //Split line in to index no and answer string
         //list($index_no,$answers)=explode($this->index_delimiter,$line);
         $index_no=$row['index_no'];
         $answers   =$row['answers'];
         //Split answers for each question and store in a array
         $tmp_array=explode($this->question_delimiter,$answers);
         $answers_array=array_slice($tmp_array,$section_range['start']-1,$section_range['end']);

         //echo implode("__",$answers_array)."<br>";

         //Processing answer by unswer
         foreach ($answers_array as $key => $answer) {
            $answer=trim($answer," ,()");
            $answer=strtoupper($answer);
            if($answer != $this->blank_answer){ //detect blank_answer anwsers

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
               //count blank_answer answers
               $stat_array[$key][$this->blank_answer]+=1;
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
               <span class='emp left' >".$val1."</span>
        &nbsp;
               <span class='emp right' >".$val2."</span>
            </td>";
      }else{
         $td.="<td>
            <span class='left'>".$val1."</span>
        &nbsp;
            <span class='right'>".$val2."</span>
         </td>";
      }
      return $td;
   }

   public function print_stat($stat_array) {
    $table="<style type="text/css">*{font-size:92%;} .left{ float:left;} .right{float:right;text-align:right;} .emp{text-decoration:underline;font-weight:bold;} </style>";
    $table.="<div align='center'><h2>Item analysis report for course ".$this->paper['course_id']." of exam ".$this->paper['exam_id']." </h2>";
    $table.="<h3>No. of questions :".$this->paper['no_of_questions'].", No. of students :".$this->no_of_students." </h2></div>";
      $table.="<table cellpadding='3' border='1' style='border-collapse:collapse;' id='itemAnalysis' width='100%'>";

      //generate the HEADER -- header will repeat in each page when printed
      $table.= "
      <tr style='text-align:center'>
      <th>QUESTION</th>";

      //generate rest of the options
      foreach ($this->options as $option) {
         $table.="<th>".$option."</th>";
      }
      $table.= "<th>ATTEMPTS</th><th>BLANKS</th></tr>";

      //printing data from stat_array
      foreach ($stat_array as $quest_number => $opt_array) {
         //Add page break for print at given line number or end of the table
         if($quest_number == $this->page_break_at || ($quest_number+1)==$this->cur_section_size){
            $table.="<tr ><th style=''>".($quest_number+1)."</th>";
         }else{
            $table.= "<tr><th style=''>".($this->cur_section_range['start']-1+$quest_number+1)."</th>";
         }

         $non_blank_answer=0; //To count total non blank_answer answers for the question

         //Sort to obtain the maximum count to be bold in print
         arsort($opt_array);

         //Printing individual cont
         foreach ($this->options as $option) {
            //count for each option
            $opt_count=$opt_array[$option];

            //count percentage for each option
            $opt_percent=round((($opt_count/$this->no_of_students)*100),2);

            //Non blank_answer accumulation
            $non_blank_answer+=$opt_count;

            //emphesize and print max count and percentage
            if(key($opt_array)==$option){
               $table.=$this->two_val_td($opt_count,$opt_percent."%",true);
            }else{
               $table.=$this->two_val_td($opt_count, $opt_percent."%",false);
            }
         }

         //non blank_answer total percentage
         $non_blank_answer_percent=round((($non_blank_answer/$this->no_of_students)*100),2);

         //print non blank_answer total
         $table.=$this->two_val_td($non_blank_answer, $non_blank_answer_percent."%",false);

         //blank_answer count
         $blank_answers=$opt_array["BLANK"];

         //blank_answer percentage
         $blank_answer_percent=round((($blank_answers/$this->no_of_students)*100),2);

         //emphesize and print blank_answer count and percentage
         if(key($opt_array)=="BLANK"){
            $table.=$this->two_val_td($blank_answers,$blank_answer_percent."%",true);
         }else{
            $table.=$this->two_val_td($blank_answers, $blank_answer_percent."%",false);
         }
         $table.="</tr>";
      }
      $table.="</table>";
    //return $table;
    $table=str_replace("'","\"",$table);

    echo $table;
    /*Returning as a pdf*/
   /*

    include A_CLASSES."/letterhead_pdf_class.php";
    $letterhead=new Letterhead();

    //insert the content to the pdf
    $letterhead->include_content($table);

    //Acquire pdf document
    $pdf=$letterhead->getPdf();

    //Return pdf document to the output stream
    $pdf->Output('test_pdf.pdf', 'I');
*/

   }


   /*
    * Read marking logic
    */
   public function get_marking_logic($section_range){

      //Read logic lines from file/database TODO
      $lines            =$this->get_logic_lines($section_range);
      
      $line_count         =get_num_rows();

      return  $lines;

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
            $this->blank_answer=>   $tmp_array[5],
            $this->no_answer=>$tmp_array[6]
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
               $this->no_answer=>$tmp_array[5],
               $this->blank_answer=> $tmp_array[6]
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
               $this->no_answer=>$tmp_array[5],
               $this->blank_answer=> $tmp_array[6]
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

   /** 
    * Mark answers
    *@param section: section number 
    */
   public function mark_answers($section){
      /*Set current section*/
      $this->cur_section=$section;

      //enable/disable munus carryforward
      $minus_carry_forward=false;

      /*Get section range */
      $section_range=$this->get_section_range($section);

      /*Validate sections*/
      if($section > $this->get_num_sections()){
         return -1;
      }

      /*Save results here in this csv also inserted in to the database*/
      $result_path=MOD_A_CSV."/result_file_".$this->self['paper_id'].".csv";
      $result_sheet = fopen($result_path, 'w');
      fputcsv($result_sheet, array("Serial No.","Index No.","Mark"));


      /*Read the answers as MYSQL RESOURCE from db*/
      $answer_lines_res = $this->get_answer_lines($section_range);

      /*Get marking logic array*/
      $marking_logic_array=$this->get_marking_logic($section_range);

      /*Serial number to include in csv*/
      $serial_no=1;
      
      /*Clearn before processing*/
      $query="DELETE FROM ".$GLOBALS['MOD_P_TABLES']['marks']." WHERE paper_id='".$this->self['paper_id']."' AND section='$section'";
      $res=exec_query($query,Q_RET_MYSQL_RES);

      /*Read line by line and mark the answers $row reprecent a student*/
      while($row=mysql_fetch_assoc($answer_lines_res)) {

         /*Students mark will be accumulated to this variable*/
         $mark=0;

         /*Split line in to index no and answer string*/
         $index_no=$row['index_no'];
         $answers   =$row['answers'];

         /*Split answers for each question and store in a array*/
         $tmp_array=explode($this->question_delimiter,$answers);
         $answers_array=$tmp_array;

         /*Slice array and get the range for te requested range*/
         $answers_array=array_slice($tmp_array,$section_range['start']-1,$this->sections[$section-1]);

         /*Processing answer by unswer*/
         foreach ($answers_array as $key => $answer) {

            /*Purify the answer string*/
            $answer=trim($answer," ,()");

            /*Case convert to upper*/
            $answer=strtoupper($answer);

            $logic_no=$section_range['start']+$key;

            /*Logic for the given question*/
            $marking_logic=$marking_logic_array[$logic_no];
            

            /*Multipe logic handle*/
            if(isset($marking_logic)&&sizeof($marking_logic)>1){

                $multi_mark      = 0;
               $blank_answer   = false;
               $first_option   = true;

               /*Mark answers using multiple logics*/
                foreach ($marking_logic as $key2 => $logic) {
                   $tmp_mark=0;
                   if($logic[$this->no_answer]!=0){ /*Mark no answer (if marks provided for no answer questions)*/
                      $tmp_mark+=$logic[$this->no_answer];
                   }else{

                      if($answer == $this->blank_answer){ /*Mark Blanks (if marks provided for blank answers)*/ 
                         $tmp_mark+=$logic[$this->blank_answer];
                        $blank_answer   = true;
                      }else {
                         foreach($this->options as $option){ /*Mark other options(A,B,C,D,E)*/
                            if(strpos($answer,$option)!==false){
                               $tmp_mark+=$logic[$option];
                            }
                         }
                      }
                   }
         
                   /*Choose the max score from multipe options*/
                   if($first_option || $multi_mark<$tmp_mark){
                     $first_option=false;
                      $multi_mark=$tmp_mark;
                   }

                  /*For single choice questions only three marking states counted not answered/wrong_answer/correct_answer 0/<wront>/<correct>*/
                  if(!$blank_answer && ($logic['multiple_choice']==0 && $multi_mark < $logic['mark_for_correct_sns'])){
                     $multi_mark=$logic['mark_for_wrong_sns'];
                  }
                }

               /*Final mark of multiple logics */

               if(!$minus_carry_forward && $multi_mark < 0){
                  $multi_mark=0;
               }
                $mark+=$multi_mark;

            }else{ /*No multiple answers*/
                $single_mark   = 0;
               $blank_answer   = false;

               /*Marking logic*/
               $logic=$marking_logic[1];

                if($logic[$this->no_answer]!=0){ /*Mark no answer (if marks provided for no answer questions)*/
                   $single_mark+=$logic[$this->no_answer];
                }else{
                   if($answer == $this->blank_answer){  /*Mark Blanks (if marks provided for blank answers)*/
                      $single_mark+=$logic[$this->blank_answer];
                     $blank_answer   = true;
                   }else{
                      foreach($this->options as $option){
                         if(strpos($answer,$option)!==false){/*Mark other options (A,B,C,D,E)*/
                            $single_mark+=$logic[$option];
                         }
                      }
                   }
                }
               /*Handle multipel chouse and single choice questions*/
            
               if(!$blank_answer && ($logic['multiple_choice']==0 && $single_mark < $logic['mark_for_correct_sns'])){
                  $single_mark=$logic['mark_for_wrong_sns'];
               }

               if(!$minus_carry_forward && $single_mark < 0){
                  $single_mark=0;
               }

               $mark+=$single_mark;
            }
         }


         
         /*To add manual markes*/
         /*
         $manual_mark=exec_query("SELECT manual_mark FROM ".$GLOBALS['MOD_P_TABLES']['marks']." WHERE index_no='$index_no' AND section='$section' ",Q_RET_ARRAY);
         $mark=round(($mark+$manual_mark[0]['manual_mark'])/$this->sections[$section-1],0);
         */

         $mark=round(($mark/$this->sections[$section-1])*(100/60),0);

         /*Add mark record to database*/
         $query="REPLACE INTO ".$GLOBALS['MOD_P_TABLES']['marks']."(index_no,paper_id,section,mark) values('$index_no','".$this->self['paper_id']."',$section,$mark)";
         $res=exec_query($query,Q_RET_MYSQL_RES);

         /*Reset mark variable*/
         $mark=0;

         /*Add mark record to csv*/
         fputcsv($result_sheet, array($serial_no++,$index_no,$mark));
      }

      /*Close csv*/
      fclose($result_sheet);
      
      /*Return path to csv*/
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
