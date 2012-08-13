<?php
//Descriptive program
function getDescProgram(){
	$arr=exec_query("SELECT * FROM ".s_t('program')." WHERE short_name='".PROGRAM."'",Q_RET_ARRAY);
	return strtoupper($arr[0]['full_name']);
}

//Descriptive degree array
function getDescDegreeArr(){
   $descDegree=array(
      "Bachelor of Computer Science with Honours",          //0
      "Bachelor of Computer Science",                       //1
      "Bachelor of Science (Computer Science) with Honours",//2
      "Bachelor of Science (Computer Science)",             //3
      "Not Completed"                                       //4
   );
   return  $descDegree;
}

//Descriptive Class array
function getDescClassArr(){
   $descClass=array(
      "First Class(Honours)",          //0
      "First Class",                   //1
      "Second Class(Upper Division)",  //2
      "Second Class(Lower Division)",  //3
      "Pass",                          //4
      "Pending"                        //5
   );
   return $descClass;
}
 
//Class array
function getClassesArr(){
   $classes=array(
      "P"   =>2,
      "2L"  =>3,
      "2U"  =>3.25,
      "1"   =>3.5
   );
   return $classes;
}

function getGradeGPVArr(){
   $gradeGpv = array(
      "A+"=>4.25,"A"=>4.00,"A-"=>3.75,
      "B+"=>3.25,"B"=>3.00,"B-"=>2.75,
      "C+"=>2.25,"C"=>2.00,"C-"=>1.75,
      "D+"=>1.25,"D"=>1.00,"D-"=>0.75,
      "E"=>0.00,"F"=>0.00,"AB"=>0.00,
      "NR"=>0.00,"MC"=>0.00,"NA"=>0.00,
      "NC"=>0.00,"0"=>0.00
   );
   return  $gradeGpv;
}

function getGradeGpv($grade){
   $gradeGpv=getGradeGPVArr();
   if(!empty($gradeGpv[strtoupper(trim($grade))])){
      return $gradeGpv[strtoupper(trim($grade))];
   }else{
      return 0;
   }
}

/*
 * Array to hold minimum marks to obtain a particular Grade
 */
function getMinMarkCArr(){
   $minGradeMark=array(
      'D-'=>20,'D'=>30,'D+'=>40,
      'C-'=>45,'C'=>50,'C+'=>55,
      'B-'=>60,'B'=>65,'B+'=>70,
      'A-'=>75,'A'=>80,'A+'=>90
   );
   return  $minGradeMark;
}

function getMinMarkC($grade){
   $minGradeMark=getMinMarkCArr();
   return $minGradeMark[strtoupper(trim($grade))];
}


/*
 * Return grade for mark
 */
function getGradeC($mark,$course_id){
   $grade=null;
   if(isNonGrade($course_id) || !is_numeric($mark)){
      return $mark;
   }else{
      if ($mark < 20) {
         $grade = "E";
      } elseif ($mark < 30) {
         $grade = "D-";
      } elseif ($mark < 40) {
         $grade = "D";
      }elseif ($mark < 45) {
         $grade = "D+";
      }elseif ($mark < 50) {
         $grade = "C-";
      }elseif ($mark < 55) {
         $grade = "C";
      }elseif ($mark < 60) {
         $grade = "C+";
      }elseif ($mark < 65) {
         $grade = "B-";
      }elseif ($mark < 70) {
         $grade = "B";
      }elseif ($mark < 75) {
         $grade = "B+";
      }elseif ($mark < 80) {
         $grade = "A-";
      }elseif ($mark < 90) {
         $grade = "A";
      }elseif ($mark < 101) {
         $grade = "A+";
      }
   }
   return $grade;
}

$GLOBALS['course_arr']=null;
function getCourseArr(){
   //global $GLOBALS['course_arr'];
   if(is_null($GLOBALS['course_arr'])){
      $GLOBALS['course_arr']  = exec_query("SELECT * FROM ".p_t('course'),Q_RET_ARRAY,null,'rid');
   }
}

/**
Return the rid of course_id 
*/
$GLOBALS['course_id_rid']=null;
function rid_of_course_id($course_id){
	getCourseArr();
   if(is_null($GLOBALS['course_id_rid'])){
		foreach($GLOBALS['course_arr'] as $key => $arr){
			$GLOBALS['course_id_rid'][$arr['course_id']]=$key;
		}
	}
	return $GLOBALS['course_id_rid'][$course_id];
}

/*Course excpetions*/
/*
$courseNonGrade   = array('ENH1001','ENH1002');
$courseNonCredit   = array('SCS3026','ICT3015','ICT3016');
*/


/*
 * Check for non grade courses
 */
function isNonGrade($courseid){
   getCourseArr();
   if($GLOBALS['course_arr'][$courseid]['non_grade']==true){
      return true;
   }else{
      return false;
   }
}

/*
 * Check for non credit courses
 */
function isNonCredit($courseid){
   getCourseArr();
   if($GLOBALS['course_arr'][$courseid]['non_credit']==true){
      return true;
   }else{
      return false;
   }
}


/*
 * Return year of the course
 */
function courseYear($courseid){
   getCourseArr();
   if(isset($GLOBALS['course_arr'][$courseid]['student_year'])){
      return $GLOBALS['course_arr'][$courseid]['student_year'];
   }else{
      return null;
   }
}

/*
 * Return Credits of the given course unit
 */
function getCredits($courseid){
   getCourseArr();

   //if(!isNonGrade($courseid) && !isNonCredit($courseid)){
   if(!isNonCredit($courseid)){
      return $GLOBALS['course_arr'][$courseid]['lecture_credits']+$GLOBALS['course_arr'][$courseid]['practical_credits'];
   }else{
      return 0;
   }
}

function getCourseName($course_id){
   getCourseArr();
   return $GLOBALS['course_arr'][$course_id]['course_name'];
}

/**
 * Returen course code
 */
function getCourseCode($course_id){
   getCourseArr();
   return $GLOBALS['course_arr'][$course_id]['course_id'];
}


/*
 *Return alternate course IDS as an array 
 * */
function getAltCourses($course_id){
   getCourseArr();
   if(!is_null($GLOBALS['course_arr'][$course_id]['alt_course_id'])){
      $alt_courses=explode(',',$GLOBALS['course_arr'][$course_id]['alt_course_id']); 
      array_unshift($alt_courses,$course_id);
      return $alt_courses; 
   }else{
      return array($course_id);
   }
}


/*
 * Return array of exam ids for a given batch
 */
 /*
function get_exam_ids($batch){

   //grab last tow chars of the batch
   $reg    =substr($batch,-2,2);

   //special case: 2002/2003(A)
   if(!is_numeric($reg)){
      $reg  = "3";
   }else{
      $reg  = (int)substr($batch,-2,2);
   }
}
*/

/*
 * Return array of exam ids for a given batch
 */
$course_exam_arr=array();

/*
 * Verify the course according to the index no
 */
function isCS($indexno){
   preg_match('/\d+020\d+/', $indexno, $matches);
   if(empty($matches[0])){
      return true;
   }else{
      return false;
   }
}


/*
This class accomplishes many functions related to the student (student centric -> index no)

1. 
2. 
3. 

*/

class Student{

   /*Array to hold index and batch*/
   protected $self      = array();

   /*
    * Print debug messages on screen (depricated)
    */
   protected $DEBUG=false; //Turn ON and OFF debug messages

   public function debug($msg,$id,$color){
      if($this->DEBUG){
         echo "<span style='color:".$color."'>[".$id."]</span>".$msg."<br>";
      }
   }

   /*Array to hold all the course data*/

   /*
    courses= array(
    -----'scs1001'=> array(
    -----------'0911'=> array(
    -----------------'marks1'=>55,
    -----------------'marks2'=>55,
    -----------------'marks3'=>55,
    -----------------'final' =>c
    ------------------),
    ---------- '0811'=> array(
    ------------------),
    ------------),
    -----),
    );
    */

   protected $courses= array();
   protected $regInfo= array();
   protected $gpaInfo= array();

   //Recalculate the gpa if TRUE
   protected $RECALCULATE=true;

   public function __construct($index_no=null,$recalc=null) {

      $this->self['program']  = PROGRAM;
      $this->self['index_no'] = $index_no;

      /*Related tables*/
      $this->self['marks']    = p_t('marks');
      $this->self['student']  = p_t('student');
      $this->self['gpa']      = p_t('gpa');
      //$this->self['grades'] = p_t("grades"];
      $this->self['course']   = p_t('course');

      $this->loadRegData();
      $this->loadCourses();
      $this->loadGPAData();

      //If marks have uploaded after 
      if(is_null($recalc)){
         $this->RECALCULATE      = $this->is_marks_course_updated_after_gpa();
      }else{
         $this->RECALCULATE      = $recalc;
      }
   }

   public function getRegNo(){
      return $this->regInfo['registration_no'];
   }

   public function getIndex(){
      return $this->self['index_no'];
   }

   public function getBatch(){
      return $this->self['batch'];
   }

   public function getRank(){
      $rank_query="SELECT rank FROM(SELECT index_no,degree_gpa,@rownum:=@rownum+1 rank FROM ".$this->self['gpa'].",(SELECT @rownum:=0) r WHERE year='3' AND index_no LIKE CONCAT(LEFT('".$this->self['index_no']."',2),'%') ORDER BY degree_gpa DESC) AS r WHERE index_no='".$this->self['index_no']."'";
      $rank_array=exec_query($rank_query,Q_RET_ARRAY);
      return $rank_array[0]['rank'];
   }

   /*
    * Return students studentration date
    */
   public function getRegDate(){
      return $this->regInfo['date_of_regist'];
   }

   /*
    * Return full name of the user
    */
   public function getName($cat){
      switch($cat){
         case 1:
            return $this->regInfo['last_name']." ".$this->regInfo['initials'];
            break;
         case 2:
            return $this->regInfo['full_name'];
            break;
         case 3:
            return $this->regInfo['title']." ".$this->regInfo['last_name']." ".$this->regInfo['initials'];
            break;
         case 4:
            return $this->regInfo['title']." ".$this->regInfo['full_name'];
            break;
         default:
            return $this->regInfo['full_name'];
      }
   }

   /*
    * Return the query to set studentration detail
    */

   public function getRegQuery($set){
      $query   ="UPDATE ".$this->self['student']." SET ";
      $query   .=" ".$set." ";
      $query   .="WHERE index_no =".$this->getIndex();
      return $query;
   }

   /*
    * Return studentered year
    */
   public function getDegYear(){
      return $this->regInfo['date_of_graduation'];
   }

   /*
    * Return studentered year
    */
   public function getTitle(){
      return $this->regInfo['title'];
   }

   /*
    * Load students studentration detail to the regInfo array to be used
    */
   protected $validIndex=false;
   public function loadRegData(){
   /*TODO: both tables should be merged*/
      $query   ="SELECT * FROM ".$this->self['student']." WHERE index_no ='".$this->self['index_no']."'";

      /*Student info from student extended table*/
      $rep = exec_query($query,Q_RET_ARRAY);

      if($rep){
         $this->regInfo = $rep[0];
         foreach($rep[0] as $key => $value){
            $this->regInfo[$key] = $value;
         }
      }else{
         log_msg($this->self['index_no'],'load reg data failed');   
      }

      if($this->regInfo['index_no']==''){
         return false;
      }else{
         $this->validIndex=true;
      }
   }

   /*
    * Check validity of the index no
    */
   public function isValidIndex(){
      return $this->validIndex;
   }

/*
    Return Descriptive detailes to print the transcript
    DEGREE :Bachelor of Science (Computer Science) with Honours
      YEAR OF ADMISSION :
      DATE OF AWARD :
      GRADE POINT AVERAGE :0
      CLASS OBTAINED :First Class(Honours)
    */
public function getTranscript(){
      //Descriptive degree array
      $descDegree=getDescDegreeArr();

      //Descriptive Class array
      $descClass=getDescClassArr();
      
      $transcript=array();
      $myGPA=$this->getCGPA();
      $myClass=$this->getClass($myGPA);

      $transcript['DOA']=$this->getRegDate(); //Date of Admission
      $transcript['YOA']=$this->getDegYear(); //Year of Award
      $transcript['GPA']=round($myGPA,2);       //Grade Point Avarage
      //Selecting Descriptive degree and Descriptive class
      switch($this->getDegree().":".$myClass){
         //four year
         case '4:2L':
            $transcript['DEGREE']=$descDegree[0];  //Degree
            $transcript['CLASS']=$descClass[3];      //Class
            break;
         case '4:2U':
            $transcript['DEGREE']=$descDegree[0];
            $transcript['CLASS']=$descClass[2];
            break;
         case '4:1':
            $transcript['DEGREE']=$descDegree[0];
            $transcript['CLASS']=$descClass[0];
            break;
         case '4:P':
            $transcript['DEGREE']=$descDegree[1];
            $transcript['CLASS']=$descClass[4];
            break;
            //Three year
         case '3:2L':
            $transcript['DEGREE']=$descDegree[2];
            $transcript['CLASS']=$descClass[3];
            break;
         case '3:2U':
            $transcript['DEGREE']=$descDegree[2];
            $transcript['CLASS']=$descClass[2];
            break;
         case '3:1':
            $transcript['DEGREE']=$descDegree[2];
            $transcript['CLASS']=$descClass[1];
            break;
         case '3:P':
            $transcript['DEGREE']=$descDegree[3];
            $transcript['CLASS']=$descClass[4];
            break;
         default:
            $transcript['DEGREE']=$descDegree[4];
            $transcript['CLASS']=$descClass[5];
            break;
      }
      return $transcript;
   }



      /*
    * Return Years of the degree (3,4) or the degree name if $descriptive is true
    */
   public function getDegree(){
      $degreeYear=3;
      foreach($this->courses as $course_id => $course){
         if(courseYear($course_id)==4){
            $degreeYear=4;
            break;
         }
      }
      return $degreeYear;
   }



   /*
    * Classes for GPA
    */

   public function getClass($GPA){
      $classes=getClassesArr();
      $gpa=$GPA;

      if(!$gpa){
         $mycgpa=$this->getCGPA();
         $mydgpa=$this->getDGPA();

         $gpa=$mydgpa;
         //Choose from degree gpa and class gpa
         if($mydgpa >=$classes['P'] && $mycgpa < $mydgpa){
            $gpa=$mycgpa;
         }
      }
      $this->debug($gpa, $this->getIndex(), 'red');
      if($gpa <2){
         return -1;
      }elseif($gpa <3){
         return array_search(2,$classes);
      }elseif($gpa <3.25){
         return array_search(3,$classes);
      }elseif($gpa <3.5){
         return array_search(3.25,$classes);
      }else{
         return array_search(3.5,$classes);
      }
   }

   /**
    * Check wether students marks updated after gpa calculation if so recalculate gpa
    */

   public function is_marks_course_updated_after_gpa(){
      $arr=exec_query("SELECT MAX(m.timestamp)<g.timestamp OR MAX(c.timestamp)>MAX(m.timestamp) updated FROM ".p_t('marks')." m,".p_t('gpa')." g,".p_t('course')." c where m.index_no='".$this->self['index_no']."' AND m.index_no=g.index_no",Q_RET_ARRAY);

      return $arr[0]['updated'];
   }


   /*
    * Return Overall Grade Point Value
    * +------+---------+-----------+-----------+------------+------------+
    * | year | credits | class_gpv | class_gpa | degree_gpa | degree_gpv |
    * +------+---------+-----------+-----------+------------+------------+
    * | 1    |      31 |     94.25 |   3.04032 |    3.04032 |      94.25 |
    * | 2    |      30 |       103 |   3.43333 |    3.43333 |        103 |
    * | 3    |      30 |       103 |   3.43333 |    3.43333 |        103 |
    * | 4    |      30 |     97.25 |   3.24167 |    3.24167 |      97.25 |
    * | 4T   |     121 |     397.5 |   3.28512 |    3.28512 |      397.5 |
    * +------+---------+-----------+-----------+------------+------------+
    *
    */

   public function select_year($year=null){
      if(!is_null($year)){
         return $year;
      }elseif(isset($this->gpaInfo['4T'])){
         return '4T';
      }elseif(isset($this->gpaInfo['3T'])){
         return '3T';
      }
   }


   public function getDGPV($year=null){
      return round($this->gpaInfo[$this->select_year($year)]['degree_gpv'],2);
   }

   /*
    * Return Overall Credits 
    */
   public function getTotalCredits($year=null){
      return $this->gpaInfo[$this->select_year($year)]['credits'];
   }



   /*
    * Return Overall Grade Point Average
    */
   public function getDGPA($year=null){
      return round($this->gpaInfo[$this->select_year($year)]['degree_gpa'],2);
   }

   /*
    * Return Overall Grade Point Value
    */
   public function getCGPV($year=null){
      return round($this->gpaInfo[$this->select_year($year)]['class_gpv'],2);
   }

   /*
    * Return Overall Grade Point Average
    */
   public function getCGPA($year=null){
      return round($this->gpaInfo[$this->select_year($year)]['class_gpa'],2);
   }


   /*
    * Return Total credits for the given year
    */
   public function getYearCredits($year){
      return $this->gpaInfo[$this->select_year($year)]['credits'];
   }

   /*
    * Return Degree GPV for a given year
    */
   public function getYearDGPV($year){
      return $this->gpaInfo[$this->select_year($year)]['credits'];
   }

   /*
    * Return Class GPV for a given year
    */
   public function getYearCGPV($year){
      $gpv=0;
      foreach($this->courses as $course_id => $course){
         if(courseYear($course_id)==$year && !isNonGrade($course_id) && !isNonCredit($course_id)){
            if($this->isRepeatCourse($course_id)){
               //log_msg("repeat", $this->getIndex()."|".$course_id."|".$this->getGrade($this->getRepeatMax($course_id)));
            }
            $gpv+=getGradeGpv($this->getGrade($this->getRepeatMax($course_id)))*getCredits($course_id);
         }
      }
      return $gpv;
   }

   /*
    * Return Degree GPA of the given year for a given year
    */
   public function getYearDGPA($year){
      if($this->getYearCredits($year)>0){
         return $this->getYearDGPV($year)/$this->getYearCredits($year);
      }else{
         return 0;
      }
   }

   /*
    * Return Class GPA of the given year for a given year
    */
   public function getYearCGPA($year){
      if($this->getYearCredits($year)>0){
         return $this->getYearCGPV($year)/$this->getYearCredits($year);
      }else{
         return 0;
      }
   }

   /*
    * Return Grade of a given subject
    */
   public function getDGrade($course_exam_arr){
      $course_id  =$course_exam_arr['course_id'];
      $exam_id   =$course_exam_arr['exam_id'];

      if(!isset($this->courses[$course_id]) || !isset($this->courses[$course_id][$exam_id]) || $this->courses[$course_id][$exam_id]['state']!='PR'){
         //Return when requesting for unavailable courses
         return null;
      }
      $marks=$this->courses[$course_id][$exam_id];
      $total_mark=is_numeric($marks['final_mark'])?$marks['final_mark']+$marks['push']:$marks['final_mark'];
      return getGradeC($total_mark,$course_id);
   }


   /*
    * Chech whether the course id repeating course and
    * return suitable Degraded Grade for repeted subjects
    */
   public function getGrade($course_exam_arr){
      $course_id  =$course_exam_arr['course_id'];
      $exam_id   =$course_exam_arr['exam_id'];
      //Grades to be degraded
      $dgrades=array('A+','A','A-','B+','B','B-','C+');
      $grade=$this->getDGrade($course_exam_arr);
      if(!is_null($grade)){
         if( $this->isRepeatCourse($course_id)==true){
            if(in_array($grade, $dgrades)){
               return "C";
            }else{
               return $grade;
            }
         }else{
            return $grade;
         }
      }else{
         return null;
      }
   }

   /*
    * Return Mark of a given subject
    */
   public function getMark($course_id,$exam_id){
      if(isset($this->courses[$course_id])){
         $course=$this->courses[$course_id];
         if(!isset($course[$exam_id])){
            return null; 
         }
         $marks=$course[$exam_id];
         return array($marks['final_mark'],$marks['push']);
      }else{
         return null;   
      }
   }



   /*
    * Check whether the given course is repeating course
    */
   public function isRepeatCourse($course_id){
      if(!isset($this->courses[$course_id])){
         //Return when requesting for unavailable courses
         $this->debug("not found", $course_id, "red");
         return null;
      }
      $course=$this->courses[$course_id];

      //get the initial atempt to top
      asort($course);
      //reset the key to first
      reset($course);
      $marks=$course[key($course)];
      if(sizeof($course)==1){
         return false;
         //IF THE STUDENT HAVE REPEATED THE FIRST ABSENT ATTEMPT WITH SUBMITTING MEDICAL IT IS NOT REPEAT
      }elseif(sizeof($course)==2 && strtoupper($marks['final_mark']) == 'MC'){ /////////////fix
         log_msg('not repeat',$course_id."|".$marks['final_mark'], 'red'); /////////////////fix
         return false;
      }else{
         return true;
      }
   }

   /*
    * Return Max Repeat mark for a given course
    */
   public function getRepeatMax($course_id){
      //global $gradeExp;//from common
      /*
      $gradeExp = array(
         "AB"=>0.00,"NC"=>0.00,
         "NR"=>0.00,"MC"=>0.00,
         "NA"=>0.00,"F"=>0.00,
         "0"=>0.00
      );
       */
      if(!isset($this->courses[$course_id])){
         //Return when requesting for unavailable courses
         return null;
      }

      //Finding all the alternative courses
      $alt_course_ids=getAltCourses($course_id);

      //Exceptional marks 
      $gradeExp = array(
         "NC"=>0.00,"CM"=>0.00
      );

      
      //Iterate all the alternative courses
      foreach($alt_course_ids as $alt_course_id){
         //If the course does not exists in students course array continue loop
         if(!isset($this->courses[$alt_course_id]))continue;

         $course_id  =$alt_course_id;
         $course     =$this->courses[$course_id];
         $mark       =0;
         $eid        =null;
         //If the student have repeated the subject find the maximum he earned
         if(sizeof($course) >1){
            foreach($course as $exam_id => $marks){
               if(key_exists(strtoupper($marks['grand_final_mark']), $gradeExp)  && $mark == 0){
                  $eid=$exam_id;
               }elseif($marks['grand_final_mark']>$mark){
                  $mark=$marks['grand_final_mark'];
                  $eid=$exam_id;
               }
            }
         }else{
            $eid=key($course);
         }
      }
      return array('course_id'=>$course_id,'exam_id'=>$eid);
   }

   /*
    * Print Marks obtained for the subjects in each year
    */
   public function getYearMarks($year){
      $marks=array();
      foreach($this->courses as $course_id => $course){
         if(courseYear($course_id)==$year){
            foreach($course as $key => $exam){
               $mark=array(
                  'course_id' =>getCourseCode($course_id),
                  'coursename'=>getCourseName($course_id),
                  'credit'    =>getCredits($course_id),
                  //'grade'     =>getGradeC($exam['grand_final_mark'],$course_id),
                  'grade'     =>$exam['class_grade'],
                  'mark'      =>$exam['grand_final_mark'],
                  'exam'      =>$this->getExamYear($key),
               );
               $marks[]=$mark;
            }
         }
      }
      return $marks;
   }

   /**
    * Return year of examination
    */
   protected $exams=null;
   public function getExamYear($exam_id){
      //grab first tow chars of the batch
      //exam_id -> 2011-01-01:4:1
      //$reg    =substr($exam_id,0,10);
      if(is_null($this->exams)){
         $this->exams=exec_query("SELECT * FROM ".p_t('exam'),Q_RET_ARRAY,null,'rid');
         return substr($this->exams[$exam_id]['exam_date'],0,4);
      }else{
         return substr($this->exams[$exam_id]['exam_date'],0,4);
      }
   }


   /*
    * Load ALL marks obtained in all exams for the student in to an array
    */
   public function loadCourses(){
      $course     = null;
      $query   =" SELECT course_id,exam_id,state,final_mark,push,grand_final_mark,degree_grade,degree_gpv,class_grade,class_gpv FROM ".$this->self['marks']." WHERE index_no ='".$this->self['index_no']."' AND can_release=true ORDER BY course_id";

      $result  = exec_query($query,Q_RET_MYSQL_RES);
      while($row = mysql_fetch_assoc($result)){
         $marks=array(
            'final_mark'      =>$row['final_mark'],
            'push'            =>$row['push'],
            'state'           =>$row['state'],
            'grand_final_mark'=>$row['grand_final_mark'],
            'class_grade'     =>$row['class_grade'],
            'degree_grade'    =>$row['degree_grade'],
            'class_gpv'       =>$row['class_gpv'],
            'degree_gpv'      =>$row['degree_gpv']
         );
         if(!empty($this->courses[$row['course_id']]))
         {
            $course=$this->courses[$row['course_id']];
            $course[$row['exam_id']]  = $marks;
            $this->courses[$row['course_id']]=$course;
         }else{
            $course=array();
            $course[$row['exam_id']]  = $marks;
            $this->courses[$row['course_id']]=$course;
         }
      }
   }

   /*Return course array*/
   public function getCourses(){
      return $this->courses;   
   }


   /** 
    * loading gpa information of the student
    */
   public function loadGPAData(){
      $this->gpaInfo=exec_query("SELECT year,credits,class_gpv,class_gpa,degree_gpa,degree_gpv FROM ".$this->self['gpa']." WHERE index_no ='".$this->self['index_no']."'",Q_RET_ARRAY,null,'year');
   
   }

}

?>
