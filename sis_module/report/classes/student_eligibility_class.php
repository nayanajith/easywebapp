<?php 
include(A_CLASSES."/student_class.php");

/*
eligibility_id   
eligibility_name 
G;PA1             
GPA2             
GPA3             
GPA4             
GPA              
course_year1     
course_year2     
course_year3     
course_year4     
attendance       
pre_eligibility  
deleted          
note             
*/

/**
Check the students elegibility for different criteria in a program

@param index_no index number of the student
@param elig eligibility name of the eligibility criteria

*/

class Eligibility extends Student{

   /*my variable store*/
   protected $self               =array();

   /*Eligibility array*/
   protected $criteria            =array();

   /*courses and marks should pass and get in each year*/
   protected $courses_for_year   =array();

   /**
   Constructor of the eligibility class

   @param index_no index number of the student
   @param elig eligibility name of the eligibility criteria
   */
   public function __construct($index_no,$eligibility_name){
      $this->self['index_no']   = $index_no;

      /*Comments on things like gpa not ok*/
      $this->self['comments']   = array();

      /*Array of courses to be sit to get eligible*/
      $this->self['to_sit']   = array();

      /*More info about aligibility/ineligibility*/
      $this->self['info']      = array();

      /*execute parent's(Student class's) constructor */
      parent::__construct($index_no);

      /*load eligibiligy information*/
      $this->select_eligibility($eligibility_name);
   }

   public function select_eligibility($eligibility_name){
      /*load elgibility criteria from the databse to the array*/
      $res=exec_query("SELECT * FROM ".$GLOBALS['P_TABLES']['eligibility']." WHERE eligibility_name='$eligibility_name'",Q_RET_ARRAY);
      if(!isset($res[0])){
         return_status_json('ERROR',"Eligibility $eligibility_name not found!");
         return;
      }
      $res_arr=$res[0];

      /*check availability of the eligibility criteria*/
      if(!$res_arr){
         log_msg('Eligibility.construct','eligibility not found!');   
         /*Exit in case if criteria not found*/
         exit();
      }else{
         $this->self['eligibility_name']=$eligibility_name;
      }

      /*Load courses for each year to be passed with the given conditions from the criteria*/
      for($i=1;$i<=4;$i++){
         $course_year='course_year'.$i;
         $this->courses_for_year[$i]=json_decode($res_arr[$course_year],true);
         unset($res_arr[$course_year]);
      }

      /*Load elegibility criteria from the array*/
      $this->criteria=$res_arr;
   }

   /**
   @return: Student state array 
   */
   public function eval_criteria(){
      /*Student state array */
      $student_state=array(
         'final'      =>'',   
         'to_sit'      =>array(),
         'comments'   =>array(),
         'info'      =>array()
      );

      if($this->compare_courses() && $this->compare_gpa() && $this->compare_attendance() && $this->compare_pre_eligibility()){
         /*Changing the state of the student inthe DB*/
         $update=exec_query("UPDATE ".$GLOBALS['P_TABLES']["student"]." SET status='".$this->self['eligibility_name']."' WHERE index_no='".$this->self['index_no']."'");

         /*If student pass the test final will be eligibility_name*/
         $student_state['final']=$this->self['eligibility_name'];
      }else{
         /*If the student fail the test final will be fail*/
         $student_state['final']      = 'fail';

         /*Further information will be stored in the array*/
         $student_state['to_sit']   = $this->self['to_sit'];
         $student_state['comments']   = $this->self['comments'];
         $student_state['info']      = $this->self['info'];

         /*
         if(isset($this->self['to_sit']) && sizeof($this->self['to_sit']) > 0){
            echo "To Sit ".implode(", To Sit:",$this->self['to_sit']);   
         }

         if(isset($this->self['comments']) && sizeof($this->self['comments']) > 0){
            echo "Comments: ".implode(',',$this->self['comments']);   
         }

         if(isset($this->self['info']) && sizeof($this->self['info']) > 0){
            echo "Info: ".implode(",",$this->self['info']);
         }
         */
      }
      return $student_state;
   }

   public function compare_gpa(){
      $satisfied=true;
      /*which variables to compare in this function*/
      $comp_array=array(
         'GPA1'=>1,
         'GPA2'=>2,
         'GPA3'=>3,
         'GPA4'=>4,
         'GPA'=>0
      );
      
      /*gpa validate recursively for every year if requested*/
      foreach($comp_array as $key => $year){

         /*gpa will validate only if it is requested i.e. > 0 and != '' */
         if($this->criteria[$key] != 0 &&  $this->criteria[$key] != ''){
            if($year == 0){
               if($this->getDGPA() < $this->criteria[$key]){ 
                  /*keep info for detaied info of the status*/
                  $this->self['info'][]="GPA: ".$this->getDGPA()." < ".$this->criteria[$key];
                  $satisfied=false;
               }
            }else{
               $year_credits   = $this->getYearCredits($year);
               $year_gpv      = $this->getYearDGPV($year);

               /*If the credits > 0 and gpv > 0 further check for less than required */
               if($year_gpv > 0 && $year_credits > 0){
                  $year_gpa=$year_gpv/$year_credits;
                  if($year_gpa < $this->criteria[$key]){ 
                     /*keep info for detaied info of the status*/
                     $this->self['info'][]="GPA$year: $year_gpa < ".$this->criteria[$key];
                     $satisfied=false;
                  }
               }else{ /*if gpv or credits =0  satisfied=false*/
                  /*keep info for detaied info of the status*/
                  $this->self['info'][]="GPA$year: year_gpv and/or year_credits < 0";
                  $satisfied=false;
               }
            }
         }   
      }
   return $satisfied;
   }

   /**

   */
   public function compare_attendance(){
      return true;   
   }

   public function compare_pre_eligibility(){
      return true;   
   }
   
   /**
   @return: the course criteria complienc of the studnet true/false
   */
   public function compare_courses(){
      $satisfied=true;

      /*compare the course criteria for each year*/
      foreach($this->courses_for_year as $year => $courses){
         if(is_array($courses)){

            /*compare each course in each year*/
            foreach($courses as $course => $grade){

               /*Get the maximum scored for the course*/
               $mark_arr=$this->getMark($course,$this->getRepeatMax($course));

               if($mark_arr[0] == -1){ /*no records found -> student should sit the subject*/

                  /*set comment for the course*/
                  $this->self['to_sit'][]=$course;
                  $satisfied=false;
               }elseif(array_sum($mark_arr) > getMinMarkC($grade)){
                  /*if the student have scored more than expected mark as passed*/
                  //echo $course." [".$grade."] ok<br>";
               }else{/*Student should sit the shubject*/

                  /*set comment for the course*/
                  $this->self['to_sit'][]=$course;
                  $satisfied=false;
               }
            }
         }
      }
      return  $satisfied;
   }
}/*End class*/
?>
