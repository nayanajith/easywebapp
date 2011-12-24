<?php
//include(MOD_CLASSES.'/student_eligibility_class.php');
include(A_CLASSES.'/student_class.php');
require_once(A_LIB.'/tcpdf/config/lang/eng.php');
require_once(A_LIB.'/tcpdf/tcpdf.php');

/* Extend tcpdf to go for further customization of header and footer and more  */
class MYPDF extends TCPDF {

   /*Array to store header info*/
   protected $header_info=array();

   /*Set header infor*/
   public function set_header_info($program_short_name='BIT',$student_year=1,$academic_year='2006/2007',$batch=2000,$held_on='11/08/2007-12/08/2007'){

      /*Get program information*/
      $query="SELECT * FROM ".$GLOBALS['S_TABLES']['program']." WHERE short_name='$program_short_name'";
      $arr=exec_query($query,Q_RET_ARRAY);
      $program=$arr[0];

      /*Header information of the mark book*/
      $this->header_info['logo']            =$program['logo'];
      $this->header_info['program']         =$program['full_name'];
      $this->header_info['student_year']   =$student_year;
      $this->header_info['academic_year']      =$academic_year;
      $this->header_info['batch']         =$batch;
      $this->header_info['held_on']         =$held_on;
   }


   /*Page header*/
   public function Header() {
      /*Logo left(institute) and right(program)*/ 
      $ucsc_logo    = A_IMG."/".$GLOBALS['LOGO'];

      /*set logo for each program*/
      //$prog_logo    = A_ROOT."/".$this->header_info['logo'];
      $prog_logo    = A_IMG."/".$GLOBALS['LOGO'];

      /*write images in pdf*/
      $this->Image($ucsc_logo, 25, 10, 20, '', 'PNG', '', 'L', false, 300, '', false, false, 0, false, false, false);
      $this->Image($prog_logo, 10, 10, 20, '', 'PNG', '', 'R', false, 300, 'R', false, false, 0, false, false, false);

      /*Set font*/ 
      $this->SetFont('helvetica', '', 20);

      /*Custom long header  with html formatted*/ 
$header ='
<table>
   <tr>
      <td style="font-size:40px;font-weight:bold;">
         '.strtoupper($GLOBALS["INSTITUTE"]).'
      </td>
   </tr>
   <tr>
      <td style="font-size:35px;font-weight:bold;">
      '.$this->header_info['program'].'
      </td>
   </tr>
   <tr>
      <td style="font-size:25px;">
      Academic Year '.$this->header_info["academic_year"].' - '.number_to_text($this->header_info["student_year"]).' Year Examination - Semester '.($this->header_info["student_year"]).' & '.($this->header_info["student_year"]+1).'
      </td>
   </tr>
   <tr>
      <td style="font-size:25px">
      Examinations held on '.$this->header_info["held_on"].'
      </td>
   </tr>
   <tr>
      <td style="font-size:50px;font-weight:bold;">
      RESULTS - BATCH '.$this->header_info["batch"].'
      </td>
   </tr>
</table>';

   $this->SetY(10);
   $this->writeHTML($header, true, false, false, false, 'C');
   }

   /*Page footer*/
   public function Footer() {
      /*Position at 15 mm from bottom*/ 
      $this->SetY(-15);
      /*Set font*/ 
      $this->SetFont('helvetica', 'I', 8);
      /*Page number*/ 
      $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
   }

   // ---------------------------start mark book pdf config---------------------------------
   public function mark_book_pdf_config(){
      /*set document information*/ 
      $this->SetCreator(PDF_CREATOR);
      $this->SetAuthor('University of Colombo School of Computing');
      $this->SetTitle('Mark Book');
      $this->SetSubject('Year 2010');
      $this->SetKeywords('UCSC, BIT, mark book');
      
      /*set default header data*/ 
      $PDF_HEADER_LOGO         =A_IMG."/".$GLOBALS['LOGO'];
      $PDF_HEADER_LOGO_WIDTH    =20;
      $PDF_HEADER_TITLE         ='Markbook\naa';
      $this->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE);
      
      /*set header and footer fonts*/ 
      $PDF_FONT_NAME_MAIN      ='helvetica';
      $PDF_FONT_SIZE_MAIN      =10;
      $PDF_FONT_NAME_DATA      ='helvetica';
      $PDF_FONT_SIZE_DATA      =8;
      $this->setHeaderFont(Array($PDF_FONT_NAME_MAIN, '', $PDF_FONT_SIZE_MAIN));
      $this->setFooterFont(Array($PDF_FONT_NAME_DATA, '', $PDF_FONT_SIZE_DATA));
      
      /*set default monospaced font*/ 
      $PDF_FONT_MONOSPACED      ='courier';
      $this->SetDefaultMonospacedFont($PDF_FONT_MONOSPACED);
      
      /*set margins*/
      $PDF_MARGIN_LEFT         =25;
      $PDF_MARGIN_TOP         =35; 
      $PDF_MARGIN_RIGHT         =15;
      $this->SetMargins($PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, $PDF_MARGIN_RIGHT);
      
      $PDF_MARGIN_HEADER      =5;
      $PDF_MARGIN_FOOTER      =10;
      $this->SetHeaderMargin($PDF_MARGIN_HEADER);
      $this->SetFooterMargin($PDF_MARGIN_FOOTER);
      
      /*set auto page breaks*/
      $PDF_MARGIN_BOTTOM      =25;
      $this->SetAutoPageBreak(TRUE, $PDF_MARGIN_BOTTOM);
      
      /*set image scale factor*/
      $PDF_IMAGE_SCALE_RATIO   =1.25;
      $this->setImageScale($PDF_IMAGE_SCALE_RATIO);
      
      /*set some language-dependent strings*/
      //$this->setLanguageArray($l);
      
      /* set font*/
      $this->SetFont('helvetica', 'B', 20);
      
      /* add a page*/
      //$this->AddPage();
      //$this->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
      $this->SetFont('helvetica', '', 8);
   }
// --------------------------------end pdf generator configuretion---------------------------------------------
}
   
   

class Mark_book{
   /*
   Temporary html file to export as pdf    
   */
   protected $tmp_report_name      =null;

   /*program year to be print 1,2,3*/
   protected $student_year         =1;

   /*Batch of the students*/
   protected $batch               ='2000';
   
   /*academic year of the students*/
   protected $academic_year      ='2000/2001';
   
   /*width of the maks columns*/
   protected $mark_col_width      =55;

   /*size of the table header font*/
   protected $header_font         ='7pt';

   /*size of the table body font*/
   protected $body_font            ='8pt';

   /*Maximum number of columns can write in A4 sheat in landscape mode*/
   protected $max_rows_per_page   =32;
   protected $current_row_count   =32;

   
   /* name and width of columns   [column width, column name1, column name2, column name.. ]   */
   protected $cols_array=array(
      'No'            =>array('30'   ,'No.'),
      'Index_No'      =>array('50'   ,'Index No.','Reg No.'),
      'Name'         =>array('150'   ,'Name'),
      'Year'         =>array('30'   ,'Year'),
      'Course_list'   =>array(),
      'Class'         =>array('35'   ,'Class','Add','Pass'),
      'Result'         =>array('300'   ,'Result')
   );


   /*Pdf generator object */
   protected $pdf;

   /*Style the mark book table*/
   public function get_style(){
      return "
<style type=\"text/css\">
   td{
      text-align:center;
   }
   table{
      border-collapse:collapse;   
   }
   .top{
   }
   .year{
      font-size:".$this->body_font."px;   
   }
   .mark{
      font-size:".$this->body_font."px;
   }
   .tHeader{
      font-size:".$this->header_font."px;
      font-weight:bold;
      color:red;
   }
   .data{
      vertical-align:top;
      font-size:".$this->body_font."px;
   }
</style>";
   }

   /**
   Generate student record table header   
   */
   public function get_table_header(){
      /*Generate report header*/
      $table_header=$this->get_style();
      $table_header.='<table cellspacing="0" cellpadding="2" border="1"><tr>'."\n";
      /*adding column headers from array*/
      foreach($this->cols_array as $key => $sub_cols_arr){
         if($key == 'Course_list'){
            /*columns array of marks*/
            foreach($sub_cols_arr as $course => $semester){
               $table_header.='<td class="tHeader" width="'.$this->mark_col_width.'">'."\n";
               $table_header.=$course;
               $table_header.="</td>\n";
            }
         }else{
            $width=$sub_cols_arr[0];
            array_shift($sub_cols_arr);
            $table_header.='<td class="tHeader" width="'.$width.'">'."\n";
            $table_header.=implode("<br>",$sub_cols_arr);
            $table_header.="</td>\n";
         }
      }
      $table_header.="</tr>\n";
      return $table_header;
   }





   /*Return report html file path*/
   public function get_report_file(){
      return $this->tmp_report_name;
   }

   /**
   constructor of repoort generator
   
   @param student_year -> batch of the student -> 2000
   @param academic_year acacemic year -> 2006/2007
   @param exam_year exam year of the student -> 1st, 2nd
   @param held_on  dates which the exams were held -> '11/08/2007-12/08/2007' 
   */
   public function __construct($student_year=1,$batch='2000',$academic_year='2006/2007',$held_on='11/08/2007-12/08/2007'){
      //set print year 
      $this->student_year      =$student_year;
      $this->batch            =$batch;
      $this->academic_year      =$academic_year;

      $this->mark_col_width   =35;
      $this->header_font      ='8pt';
      $this->body_font         ='8pt';

      /*create temporary html file for the mark book*/
      $this->tmp_report_name   = sys_get_temp_dir().'/mark_book_page.html';
      $tmp_report_handler       = fopen($this->tmp_report_name, 'w');
      fwrite($tmp_report_handler,'');
      fclose($tmp_report_handler);

      /*Load courses of the corresponding year*/
      $this->load_courses();
      $this->load_eligibility();

      /*Insert relevenat year array in to columns array*/
      //$this->cols_array['Course_list']=$this->course_maping[$student_year];
      $this->cols_array['Course_list']=$this->year_courses;

      /*Pdf generator page setup*/
      $PDF_PAGE_ORIENTATION   ='L';
      $PDF_UNIT               ='mm';
      $PDF_PAGE_FORMAT         ='A4';

      /*create new PDF document*/ 
      $this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, true, 'UTF-8', false);

      /*set custom header infor for the mark book*/
      $this->pdf->set_header_info($student_year,$academic_year,$batch,$held_on);

      /*apply configuration for the mark book*/
      $this->pdf->mark_book_pdf_config();

   }

   /*load courses for the given year*/
   protected $year_courses=array();
   public function load_courses(){
      $res=exec_query("SELECT course_id,student_year,semester FROM ".$GLOBALS['P_TABLES']['course']." WHERE student_year='".$this->student_year."'",Q_RET_MYSQL_RES);   
      while($row =mysql_fetch_assoc($res)){
         $this->year_courses[$row['course_id']]=$row['semester'];
      }
   }

   /*all possible eligibility for the given year are checked */
   protected $year_eligibility=array();
   public function load_eligibility(){
      $res=exec_query("SELECT eligibility_name FROM ".$GLOBALS['P_TABLES']['eligibility']." WHERE eligibility_year='".$this->student_year."'",Q_RET_MYSQL_RES);   
      while($row = mysql_fetch_assoc($res)){
         $this->year_eligibility[]=$row['eligibility_name'];
      }
   }

   /*return pdf generation object*/
   public function getPdf(){
      return $this->pdf;   
   }

   /*
   Append string to the temporary html file
   @$string:any string to be inserted to the temporary html file
   */
   public function append_to_report($string,$rows){
      /*If No of rows is 1 make it 2 */
      $rows=$rows<=1?$rows=2:$rows;

      /*
      If the number of rows requested to append is larger than available
      pdf will be generated for previouse data and clean the page
      */
      if($this->current_row_count < $rows){
         /*If the page is filled, write it to the pdf*/

         /*Write end table tag to temp html file to complete the code*/
         $tmp_report_handler = fopen($this->tmp_report_name, 'a');
         fwrite($tmp_report_handler,'</table>');
         fclose($tmp_report_handler);

         /*Extract the content from the tmp html file*/
         $content = file_get_contents($this->tmp_report_name, false);

         /*Add a new page to the pdf file*/
         $this->pdf->AddPage();

         /*Write html content to the pdf object*/
         $this->pdf->writeHTML($content, true, false, true, false, 'J');

         /*Clean html page top prepaire for next page*/
         $tmp_report_handler = fopen($this->tmp_report_name, 'w');
         fwrite($tmp_report_handler,'');
         fclose($tmp_report_handler);
         
         /*Reset row count*/
         $this->current_row_count=$this->max_rows_per_page;
         return false;
      }

   

      /*open file handler to append data*/
      $tmp_report_handler = fopen($this->tmp_report_name, 'a');

      /*If the file has no lines included yet, write the header*/
      if($this->current_row_count == $this->max_rows_per_page){
         fwrite($tmp_report_handler,str_replace("'","\"",$this->get_table_header()));
         /*header will ocupy 3 lines*/
         $this->current_row_count-=3;
      }   
      
      /*tcpdf does not support single quoted(') strings so replace all single quotes (') with double quotes (")*/
      fwrite($tmp_report_handler,str_replace("'","\"",$string));
      fclose($tmp_report_handler);

      /*decrease the avaliable rows to be filled*/
      $this->current_row_count-=$rows;
      return true;
   }

      
   /*Student detail arry to be filled for each student before inserting to the report table*/
   protected $student=array(
      'No'         =>'6',
      'Index_No'   =>'0009202<br>R000920',
      'Name'      =>'Mr. M KATPAHARAJAH',
      'Marks'=>array(
         '2001'=>array(
            'C1'=>'31-D',
            'C2'=>'11-E',
            'C3'=>'16-E',
            'C4'=>'58-B',
            'C5'=>'15-E',
            'C6'=>'50-C',
            'C7'=>'27-E',
            'C8'=>'62-B'
         )
         ),
      'Class'      =>'1.88<br>1.88<br>1.88<br>',
      'Result'   =>'To Sit LMS Assessments: IT1203,IT1303.<br>Year 3. To Sit Modules: IT1203,IT1303.'
   );   

   /**
   return semester with custom prefixes ( eg IT,E,A,S )array of given 
   @param prefix    prefix of the course id IT,E,A,S
   @param sem       semester of the course 1 ~ 6
   @param sufix    revision no of the course 4,5,
   
   @return: array with the requested course ids
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
   
   /**
   @param a_year       academic(actual) year which exam is held
   @param s_year       students year of exam
   @param semester    semester of the exam
   @return       Unique id as exam_id
   eg:
   get_exam_id(2009,3,1);
   */
   
   public function get_exam_id($a_year,$s_year,$semester){
      if(strlen($a_year) == 4){
         $a_year=substr($a_year,-2,2);
      }
      return $a_year.$s_year.$semester;
   }

   /**
   BIT semester year mapping
   SEM -> YEAR
   */
   protected $bit_sem_year=array(
      1=>1,
      2=>1,
      3=>2,
      4=>2,
      5=>3,
      6=>3
   );
   
   /**
   @param course_id courseid of bit;
   @return array of breakdown of the courseid
   eg: break_course_id('IT1204');
   */
   public function break_course_id($course_id){
      $this->bit_sem_year;
      //$course_regexp="/^[MASEmase]|IT|it{1}[0-9]{4}$/";
      $course_regexp="/^[a-zA-Z]+[0-9]{4}$/";
   
      if(preg_match($course_regexp,$course_id)){
         $course_id=substr($course_id,-4,4);
         $semester=substr($course_id,0,1);
   
         $course_break=array(
            'year'      =>$this->bit_sem_year[$semester],   
            'semester'   =>$semester,   
            'id'         =>substr($course_id,0,2),   
            'revision'   =>substr($course_id,2,2)   
         );

         return $course_break;
      }else{
         return -1;
      }
   }

   /**
   @param exam_id examid of bit;
   @return array of breakdown of the exam_id
   eg: break_examid_id('0811');
   */
   public function break_exam_id($exam_id){
      return array(
         'a_year'      =>substr($exam_id,0,2),   
         's_year'      =>substr($exam_id,2,1),   
         'semester'   =>substr($exam_id,3,1)   
      );
   }
   
   
     /**
   Generate student marks and information array
   @param index_no index no of the student who you want to generate information (name,... etc) and marks
   @return 
   */
   protected $student_count=1;
   public function gen_student_array($index_no="0900621"){
      //$eligibility=new Eligibility($index_no,$this->year_eligibility[0]);
      $eligibility=new Student($index_no);

      $this->student['No']         =$this->student_count++;
      $this->student['Index_No']   =$index_no."<br>".$eligibility->getRegNo();
      $this->student['Name']      =$eligibility->getName(2);
      $this->student['Result']   ='';

      //$this->student['Class']   =$row['class'];

      $course_marsk=$eligibility->getCourses();
      $print_marks_array=array();
      foreach($this->year_courses as $course => $semester){
         if(isset($course_marsk[$course])){
            foreach($course_marsk[$course] as $exam => $mark){
               $print_marks_array[getExamYear($exam)][$course]=array_sum($mark).'-'.getGradeC(array_sum($mark));
            }
         }
      }

      $this->student['Marks']      =$print_marks_array;

      /*get gpa*/
      $this->student['Class']      =$eligibility->getDGPA();

      /*Get state of the student*/
      $student_state_prev=array();
      foreach($this->year_eligibility as $eligibility_name){
         $eligibility->select_eligibility($eligibility_name);
         $student_state=$eligibility->eval_criteria();
         $student_state_prev=array_unique(array_merge($student_state['to_sit'],$student_state_prev),SORT_STRING);

         if($student_state['final']==$eligibility){
            $this->student['Result']   .="$eligibility pass";
         }
      }

      $this->student['Result']   .="To Sit:".implode(', To Sit:',$student_state_prev);
   }



   /*generate student recourd(html) according to the student information array generated above and insert it to the temporary html file*/
   public function add_student_record(){
      $row_count=sizeof($this->student['Marks']);
      $result      ="<tr>";

      foreach($this->student as $key => $data){
         if($key=='Marks'){
            /*   print first row of marks*/
            $first_row=isset($data[key($data)])?$data[key($data)]:array();
            /*Print year of marks*/
            $result   .="<td width='".$this->cols_array['Year'][0]."'  class='top year'>".key($data)."</td>";
            /*print marks of the year according to mapping array blank will be printed as blank*/
            foreach($this->year_courses as $course => $semester ){
               if(isset($first_row[$course])){
                  $result.="<td class='top mark' >".$first_row[$course]."</td>";
               }else{
                  $result.="<td class='top mark' ></td>";
               }
            }
         }else{
            /*Print information other than marks*/
            if($key == 'Result' || $key == 'Name'){
               $result.="<td width='".$this->cols_array[$key][0]."' rowspan='".$row_count."' class='data' style='text-align:left'>$data</td>";
            }else{
               $result.="<td width='".$this->cols_array[$key][0]."' rowspan='".$row_count."' class='data'>$data</td>";
            }
         }
      }
      $result.="</tr>";

      /*Print other rows of marks*/
      $left_years=array_keys($this->student['Marks']);
      array_shift($left_years);

      foreach($left_years as $year){
         $result.="<tr>";
         $result.="<td class='mark'>$year</td>";

         foreach($this->course_maping[$this->student_year] as $id => $config ){
            if(isset($this->student['Marks'][$year][$id])){
               $result.="<td class='mark'>".$this->student['Marks'][$year][$id]."</td>";
            }else{
               $result.="<td class='mark'></td>";
            }
         }
         $result.="</tr>";
      }
      $this->append_to_report($result,$row_count);
   }
} 

?>
