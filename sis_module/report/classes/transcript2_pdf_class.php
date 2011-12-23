<?php
set_time_limit(200);
require_once(A_LIB.'/tcpdf/config/lang/eng.php');
require_once(A_LIB.'/tcpdf/tcpdf.php');

/* Extend tcpdf to go for further customization of header and footer and more  */
class MYPDF extends TCPDF {

   /*Array to store header info*/
   protected $header_info=array();
   protected $AR_name   ='A.B.C. defghi';

   /*Set header infor*/
   public function set_header_info($transcpt_id){
      /*Header information of the mark book*/
      $this->header_info['logo']            = A_IMG."/".$GLOBALS['LOGO'];
      $this->header_info['year']            ="2010/2011";
      $this->header_info['transcpt_id']     =$transcpt_id;
   }

   /*Page header*/
   public function Header() {

      /*write images in pdf*/
      $this->Image(
         $file      =$this->header_info['logo'],
            $x         ='15',
            $y         ='3',
            $w         =20,
            $h         =0,
            $type      ='PNG',
            $link      ='',
            $align   ='R',
            $resize   =false,
            $dpi      =600,
            $palign   ='',
            $ismask   =false,
            $imgmask=false,
            $border   =0,
            $fitbox   =false,
            $hidden   =false,
            $fitonpage=false
      );

      /*Set font*/ 
      $this->SetFont('helvetica', 'B', 8);
      
      /*Custom long header  with html formatted*/ 
      $header ="
<h1>UNIVERSITY OF COLOMBO SCHOOL OF COMPUTING</h1>";

   /*Header position from the top*/
   $this->SetY(10);

   /*Write header to the file*/
   $this->writeHTML($header, true, false, false, false, 'C');
}
   /*Page footer*/
   public function Footer(){
      /*Position at 15 mm from bottom*/ 
      $this->SetY(-23);
      $this->SetX(-15);

      //Barcode style
		$style = array(
         'position' => '',
         'align' => 'C',
         'stretch' => false,
         'fitwidth' => true,
         'cellfitalign' => '',
         'border' => false,
         'hpadding' => 'auto',
         'vpadding' => 'auto',
         'fgcolor' => array(0,0,0),
         'bgcolor' => false, //array(255,255,255),
         'text' => true,
         'font' => 'helvetica',
         'fontsize' => 6,
         'stretchtext' => 4
		);

      $this->writeHTML('<hr>', true, false, false, false, 'C');
      $this->Ln(-4);
      //write1DBarcode($code, $type, $x='', $y='', $w='', $h='', $xres='', $style='', $align='')
		//$this->write1DBarcode($this->header_info["transcpt_id"], 'CODABAR', '165', '', '40', '6.5', 0.4, $style, 'N');
		$this->write1DBarcode($this->header_info["transcpt_id"], 'CODABAR', '10', '', '40', '6.5', 0.4, $style, 'N');

	   //Set font
      $this->SetFont('helvetica', '', 8);
      //TODO transcript id not aligning to right fix it
      $custom_footer='
      <br>
      <table>
         <tr>
            <td valign="bottom">
               <table>
                  <tr><td >Prepared by</td><td> ...........................................</td></tr>
                  <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                  <tr><td>Checked by</td><td> ...........................................</td></tr>
               </table>
            </td>
            <td align="right">
               <br>
               <table>
                  <tr><td align="center">........................................................</td></tr>
                  <tr><td align="center">'.$this->AR_name.'<br>Assistant Registrar/Examination <br> for Registrar</td></tr>
               </table>
            </td>
         </tr>
      </table>
      ';

      $this->writeHTML(str_replace("'","\"",$custom_footer), true, false, false, false, 'C');

      //Page number
      //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
      //$this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
   }

   public function transcript_config(){

      /*set document information*/ 
      $this->SetCreator(PDF_CREATOR);
      $this->SetAuthor('University of Colombo School of Computing');
      $this->SetTitle('Selection tesst admission');
       $this->SetSubject('Year 2011');
      $this->SetKeywords('UCSC');

      /*set default header data*/ 
      $PDF_HEADER_LOGO         =$this->header_info['logo'];
      $PDF_HEADER_LOGO_WIDTH   =20;
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
      $PDF_MARGIN_LEFT         =15;
      $PDF_MARGIN_TOP         =25; 
      $PDF_MARGIN_RIGHT         =10;
      $this->SetMargins($PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, $PDF_MARGIN_RIGHT);
      
      $PDF_MARGIN_HEADER      =5;
      $PDF_MARGIN_FOOTER      =5;
      $this->SetHeaderMargin($PDF_MARGIN_HEADER);
      $this->SetFooterMargin($PDF_MARGIN_FOOTER);
      
      /*set auto page breaks*/
      $PDF_MARGIN_BOTTOM      =10;
      $this->SetAutoPageBreak(TRUE, $PDF_MARGIN_BOTTOM);
      
    /*set image scale factor*/
      $PDF_IMAGE_SCALE_RATIO   =1.25;
      $this->setImageScale($PDF_IMAGE_SCALE_RATIO);
      
      /*set some language-dependent strings*/
      //$this->setLanguageArray($l);
      
      /* set font*/
      $this->SetFont('helvetica', '', 20);
     $this->AddPage();
   }
}


class Transcript{

   /*admission issued date by the AR/Exam*/
   protected $pdf;
   protected $index_no;
   protected $transcpt_id;
   protected $with_marks;
   protected $with_rank;
   protected $note;
   protected $awards;
   protected $papaer;

   /*
    * @param page_format      : A4,A5,B4
    * @param page_orientation   : P,L 
    */
   public function __construct($index_no,$transcpt_id,$with_marks,$with_rank=null,$note=null,$awards=null,$paper=null){
      $this->index_no   =$index_no;
      $this->transcpt_id=$transcpt_id;
      $this->with_marks =$with_marks;
      $this->with_rank  =$with_rank;
      $this->note       =$note;
      $this->awards     =$awards;
      $this->paper      =$paper;
   }

   /**
    * return the pdf of the transcript
    */
   public function getPDF(){

      /*Pdf generator page setup*/
      $PDF_PAGE_ORIENTATION='P';//(L,P)
      //$PDF_PAGE_FORMAT     ='LEGAL';
      $PDF_PAGE_FORMAT     =$this->paper;//(A4,LEGAL)
      $PDF_UNIT            ='mm';//mm,in,pt,cm
      $UNICODE             =true; 
      $ENCODING            ='UTF-8'; 
      $DISKCACHE           =false;//if TRUE reduce the RAM memory

      /*return pdf generation object*/
      $this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
      $this->pdf->set_header_info($this->transcpt_id);
      $this->pdf->transcript_config();
      $this->generate_transcript();

      return $this->pdf;   
   }

   /**
    * return the pdf of the transcript
    */
   public function getHTML(){
      $this->generate_transcript($thml=true);
   }



   /**
    * Generate the transcript in html or pdf
    */

   public function generate_transcript($html=false){
      include A_CLASSES."/student_class.php";
      $student       =new Student($this->index_no);
      $trancpt_detail=$student->getTranscript();
      $transcpt_id   =$this->transcpt_id;
      $index_no      =$this->index_no;
      $note          =$this->note;       
      $awards        =$this->awards;     




      //Following  variables are also requred to the fill the template
      $name          =$student->getName(2);
      $degree        ='Bachelor of Science in Computer Science Degree';
      $class         =$trancpt_detail['CLASS'];
      $issue_on      =date("M d,y");
      $reg_no        =$student->getRegNo();
      $rank_no       =$student->getRank();
      $DO_ADMIT      =$trancpt_detail['DOA'];
      $DO_AWARD      =$trancpt_detail['YOA'];
      $GPA           =$trancpt_detail['GPA'];

      $rank="";
      if($this->with_rank){
         $rank=" with <b>rank $rank_no</b> in the batch";
      } 

      $warding="This is to certify that <b>$name</b> with <b>Registration No: $reg_no</b> sat for the <b>$degree</b> examination held under <b>index No: $index_no</b> and reached the standard required for a <b>$class</b>".$rank;

      //dynamically change the font size of the course list
      $course_count=@sizeof($student->getYearMarks(1))+sizeof($student->getYearMarks(2))+sizeof($student->getYearMarks(3))+sizeof($student->getYearMarks(4));

      //Default font size
      $course_font_size="font-size:85%;";
      if($course_count >= 70){
         $diff =$course_count-70;

         //possible url param
         $zoom_factor=2.3;
         if(isset($_SESSION[PAGE]['zoom_factor'])){
            $zoom_factor=$_SESSION[PAGE]['zoom_factor'];
         }

         //give some exponential variation
         $diff =$diff-round($diff/$zoom_factor,0);
         $course_font_size=(85-$diff)."%";
      }

      //generate students scoring on each course in each year
      $year_title      ="<tr><td colspan='5' align='center' class='year'>YEAR %s</td></tr>";
      $course         ='';
      for($i=1;$i<=4;$i++){
         if($student->getYearCGPV($i)<=0)continue;
         $course.=sprintf($year_title,$i);
         foreach($student->getYearMarks($i) as $key => $course_arr ){
            $course.="<tr>";
            if($this->with_marks){
               $course.="<td>".$course_arr['course_id']."</td><td>".$course_arr['coursename']."</td><td>".$course_arr['credit']."</td><td>".$course_arr['mark']." - ".$course_arr['grade']."</td><td>".$course_arr['exam']."</td>";   
            }else{
               $course.="<td>".$course_arr['course_id']."</td><td>".$course_arr['coursename']."</td><td>".$course_arr['credit']."</td><td>".$course_arr['grade']."</td><td>".$course_arr['exam']."</td>";   

            }
            $course.="</tr>";
         }
      }

      if(!is_null($awards)){
         $awards="<br><br><div class='section_title'>The awards won by the student</div><br>$awards";
         
      }

      
      //TODO: this array should be global fix this
      $gradeGpv = array(
         "A+"=>4.25,"A"=>4.00,"A-"=>3.75,
         "B+"=>3.25,"B"=>3.00,"B-"=>2.75,
         "C+"=>2.25,"C"=>2.00,"C-"=>1.75,
         "D+"=>1.25,"D"=>1.00,"D-"=>0.75,
         "E"=>0.00,"F"=>0.00
      );


      $grades="<tr><th>GRADE</th><th align='center'>GPV</th></tr>";
      foreach($gradeGpv as $grade => $gpv){
         $grades.="<tr><td>$grade</td><td>$gpv</td></tr>";
      }

      //Other codes to be displayed
      $codes_arr=array(
         'MC'=>'Medical',
         'CM'=>'Completed',
         'NC'=>'Not Completed',
         'EO'=>'Exam Offence',
         'CN'=>'Cancelled'
      );

      $other_codes="<tr><th>CODE</th><th>DESCRIPTION</th></tr>";
      foreach($codes_arr as $code => $desc){
         $other_codes.="<tr><td>$code</td><td>$desc</td></tr>";
      }

      $classes_arr=getClassesArr();
      $classes="
            <tr><td width='35mm'>First class</td><td>3.5 and above</td></tr>
            <tr><td>Second class Upper</td><td>from 3.25 to 3.5</td></tr>
            <tr><td>Second class lower</td><td>from 3 to 3.25</td></tr>
            <tr><td>Pass</td><td>from 2 to 3</td></tr>
            <tr><td>Fail</td><td>below 2</td></tr>";

      $AR_name="A.B.C. def";
               
      $template=<<<EOS
<style type='text/css'>
*{
   font-size:100%;
   font-family:arial;
}

td{
   text-align:left;
}

.trans_title{

}
.section_title{
   text-decoration:underline;
   font-weight:bold;
}

.paragraph{
   text-align:justify;
}
.grade{
}

.grade th{
   background-color:silver;
}

.grade td{
   background-color:whitesmoke;
   text-align:center;
}

.trans_body{
   /*font-size:$course_font_size;*/
   font-size:85%;
}

.trans_body th{
   text-align:left;
   background-color:whitesmoke;
   font-size:85%;
   font-weight:bold;
}

.trans_body td{
   font-size:$course_font_size;
   text-align:left;
}

.year{
   background-color:silver;
}
.info{
   font-weight:bold;
   font-size:85%;
}

.classes{
   font-size:85%;
}

/*
A4
210mmx297mm
*/

</style>
<table align='center'  width='185mm' cellpadding='5'>
   <tr><td colspan='2' align='center' class='trans_title'><h3>BACHELOR OF SCIENCE IN COMPUTER SCIENCE</h3></td></tr>
   <tr><td colspan='2' align='justify' class='paragraph'>$warding</td></tr>
   <tr><td colspan='2' style='height:5mm;'>&nbsp;</td></tr>
   <tr>
      <td class='section_title' width='125mm'>
Subject Offered
      </td>
      <td class='section_title' width='60mm'>
Grade Point and Important Dates 
      </td>
   </tr>
   <tr>
      <td valign='top' width='125mm'>
         <table width='114mm' class='trans_body' cellpadding='0' cellspacing='0'>
            <thead>
               <tr><th width='13%'>COURSE</th><th width='65%'>COURSE TITLE</th><th width='10%'>CREDITS</th><th width='10%'>MARK/<br>GRADE</th><th width='7%'>EXAM<br>DATE</th></tr>
            </thead>
            <tbody>
               $course
            </tbody>
         </table>
         $awards
      </td>
      <td valign='top' width='60mm' >
         <table class='info'>
            <tr><td width='40mm'>YEAR OF ADMISSION</td><td>$DO_ADMIT</td></tr>
            <tr><td>DATE OF AWARD</td><td>$DO_AWARD</td></tr>
            <tr><td>GRADE POINT AVERAGE</td><td>$GPA</td></tr>
         </table>
         <br>
         <div class='section_title'>Key to Grades</div>
         <br>
         &nbsp;&nbsp;<table border='1' style='border-collapse:collapse;' class='grade info' width='30mm' >
            $grades
         </table>
         <br>
         <div class='section_title'>Key to Other Codes Used</div>
         <br>
         &nbsp;&nbsp;<table border='1' style='border-collapse:collapse;' class='grade info' width='50mm' >
            $other_codes
         </table>
         <br>
         <div class='section_title'>Key to Grade Point Averages</div>
         <br>
         <table class='classes'>
            $classes
         </table>
      </td>
   </tr>
</table>
EOS;


      $transcript_back=<<<EOS
<style>
h3{
   text-align:center;
}
td{
   text-align:justify;
}

th{
   font-weight:bold;
}

</style>
<table align='center'  width='185mm' cellpadding='5' border='1'>
   <tr>
      <td>
         <h3>COURSE CODES</h3>
         The following letter codes/symbols may be used to identify the type of course.
         <ul>
            <table>
               <tr><td width="10%">ENH</td><td width="90%">- Enhancement Courses</td></tr>
               <tr><td>*</td><td>- Non GPA contributing Courses</td></tr>
               <tr><td>SCS</td><td>- Computer Science</td></tr>
               <tr><td>ICT</td><td>- Information & Communication Technology</td></tr>
            </table>
         </ul>
      </td>
      <td >
         <h3>ACADEMIC CALENDER</h3>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<table >
            <tr>
               <th width="40%">&nbsp;</th><th  width="25%">Semester 1</th><th width="25%">Semester 2</th>
            </tr>
            <tr>
               <th>First half</th><td>10 weeks</td><td>5 weeks</td>
            </tr>
            <tr>
               <th>Semester break</th><td>1 week</td><td>1 week</td>
            </tr>
            <tr>
               <th>Second half</th><td>5 weeks</td><td>10 weeks</td>
            </tr>
            <tr>
               <th>Study leave</th><td>1 week</td><td>1 week</td>
            </tr>
            <tr>
               <th>Examination</th><td>5 weeks</td><td>5 weeks</td>
            </tr>
            <tr>
               <th>Vacation</th><td>2 weeks</td><td>4 weeks</td>
            </tr>
         </table>
      </td>
   </tr>
   <tr>
      <td>
         <h3>COURSE NUMBER SYSTEM</h3>
         <table>
            <tr><td>1001-1999</td><td>Year 1 Courses</td></tr>
            <tr><td>2001-2999</td><td>Year 2 Courses</td></tr>
            <tr><td>3001-3999</td><td>Year 3 Courses</td></tr>
            <tr><td>4001-4999</td><td>Year 4 Courses</td></tr>
         </table>
      </td>
      <td>
         <h3>MEDIUM OF INSTRUCTIONS</h3>
         All lectures and examinations of the course units are conducted in the English language
      </td>
   </tr>
   <tr>
      <td>
         <h3>GPA POLICY</h3>
         When calculating the Grade Point Average (GPA), all course units contributing to GPA are weighted according to their corresponding credit values. Grades of all registered course units in a study program are taken into account when calculating the GPA.
      </td>
      <td rowspan="2">
         <h3>AUTHENTICITY</h3>
         Authenticity of certificates can be verified by contacting the following officials
         <ul>
            Address:<br>
            Assistant Registrar UCSC<br>
            35, Reid Avenue,<br>
            Colombo 07, <br>
            Sri Lanka.<br>
            <br>
            FAX:0112587239
            TEL:0112581245/7
         </ul>
      </td>
   </tr>
   <tr>
      <td>
         <h3>DEGREE PROGRAM</h3>
         The University of Colombo School of Computing offers a 3-year Bachelor's Degree in Computer Science (BCSc), or a 3-year Bachelor's Degree in Information and Communication Technology (BICT), a 4-year Bachelor of Science Degree in Computer Science (BSc(Computer Science)) and a 4-year Bachelor of Science Degree in Information and Communication Technology (BSc (Information & Communication Technology)). The minimum number of credits  for a 3-year degree program is 90 and that for a 4-year program is 120.
      </td>
   </tr>
</table>
EOS;

      $transcript_back=<<<EOS
<style>
h3{
}

td{
   text-align:justify;
}

th{
   font-weight:bold;
}
p{
   text-align:justify;
}

</style>

<h3>COURSE CODES</h3>
<p>
The following letter codes/symbols may be used to identify the type of course.
   <ul>
      <table>
         <tr><td width="10%">ENH</td><td width="90%">- Enhancement Courses</td></tr>
         <tr><td>*</td><td>- Non GPA contributing Courses</td></tr>
         <tr><td>SCS</td><td>- Computer Science</td></tr>
         <tr><td>ICT</td><td>- Information & Communication Technology</td></tr>
      </table>
   </ul>
</p>

<h3>COURSE NUMBER SYSTEM</h3>
<p>
   <ul>
      <table width="200px">
         <tr><th>1001-1999</th><td>Year 1 Courses</td></tr>
         <tr><th>2001-2999</th><td>Year 2 Courses</td></tr>
         <tr><th>3001-3999</th><td>Year 3 Courses</td></tr>
         <tr><th>4001-4999</th><td>Year 4 Courses</td></tr>
      </table>
   </ul>
</p>


<h3>GPA POLICY</h3>
<p>
   When calculating the Grade Point Average (GPA), all course units contributing to GPA are weighted according to their corresponding credit values. Grades of all registered course units in a study program are taken into account when calculating the GPA.
</p>


<h3>DEGREE PROGRAM</h3>
<p>
   The University of Colombo School of Computing offers a 3-year Bachelor's Degree in Computer Science (BCSc), or a 3-year Bachelor's Degree in Information and Communication Technology (BICT), a 4-year Bachelor of Science Degree in Computer Science (BSc(Computer Science)) and a 4-year Bachelor of Science Degree in Information and Communication Technology (BSc (Information & Communication Technology)). The minimum number of credits  for a 3-year degree program is 90 and that for a 4-year program is 120.
</p>

<h3>ACADEMIC CALENDER</h3>
<p>
   <ul>
      <table width="300px">
         <tr>
            <th width="40%">&nbsp;</th><th  width="25%">Semester 1</th><th width="25%">Semester 2</th>
         </tr>
         <tr>
            <th>First half</th><td>10 weeks</td><td>5 weeks</td>
         </tr>
         <tr>
            <th>Semester break</th><td>1 week</td><td>1 week</td>
         </tr>
         <tr>
            <th>Second half</th><td>5 weeks</td><td>10 weeks</td>
         </tr>
         <tr>
            <th>Study leave</th><td>1 week</td><td>1 week</td>
         </tr>
         <tr>
            <th>Examination</th><td>5 weeks</td><td>5 weeks</td>
         </tr>
         <tr>
            <th>Vacation</th><td>2 weeks</td><td>4 weeks</td>
         </tr>
   </table>
   </ul>
</p>

<h3>MEDIUM OF INSTRUCTIONS</h3>
<p>
   All lectures and examinations of the course units are conducted in the English language
</p>


<h3>AUTHENTICITY</h3>
<p>
   Authenticity of certificates can be verified by contacting the following officials
   <ul>
      Address:<br>
      Assistant Registrar UCSC<br>
      35, Reid Avenue,<br>
      Colombo 07, <br>
      Sri Lanka.<br>
      <br>
      FAX:0112587239<br>
      TEL:0112581245/7
   </ul>
</p>

EOS;



      if($html){
         echo $template;
      }else{
         /*Add a page to the sheet*/
         $this->pdf->SetFont('helvetica', '', 9);
      
         //replace ' with " which does not support tcpdf
         $content=str_replace("'","\"",$template);
      
         /*write table to the sheet*/
         $this->pdf->writeHTML($content, true, false, false, false, 'L');

         //Transcript back 
			$this->pdf->AddPage();
         $content=str_replace("'","\"",$transcript_back);
         $this->pdf->setPrintHeader(false);
         $this->pdf->setPrintFooter(false);
         $this->pdf->writeHTML($content, true, false, false, false, 'L');

      }
   }
}
?>
