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
   public function set_header_info(){
      /*Header information of the mark book*/
      $this->header_info['logo']            = A_IMG."/".$GLOBALS['LOGO'];
      $this->header_info['year']            ="2010/2011";
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
      $this->SetY(-35);
      $this->SetX(-15);

      /*Set font*/ 
      $this->SetFont('helvetica', '', 8);
      $custom_footer="
      <hr>
      <br>
      <br>
      <table>
         <tr>
            <td valign='bottom'>
               <table>
                  <tr><td >Prepared by</td><td> ...........................................</td></tr>
                  <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                  <tr><td>Checked by</td><td> ...........................................</td></tr>
               </table>
            </td>
            <td align='right'>
               <br>
               <table>
                  <tr><td align='center'>........................................................</td></tr>
                  <tr><td align='center'>".$this->AR_name."<br>Assistant Registrar/Examination <br> for Registrar</td></tr>
               </table>
            </td>
         </tr>
      </table>
      ";

      $this->writeHTML(str_replace("'","\"",$custom_footer), true, false, false, false, 'C');

      /*Page number*/ 
      $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
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

   /*
    * @param page_format      : A4,A5,B4
    * @param page_orientation   : P,L 
    */
   public function __construct($index_no,$with_marks){

      /*Pdf generator page setup*/
      $PDF_PAGE_ORIENTATION='P';//(L,P)
      $PDF_PAGE_FORMAT      ='A4';
      $PDF_UNIT            ='mm';//mm,in,pt,cm
      $UNICODE               =true; 
      $ENCODING            ='UTF-8'; 
      $DISKCACHE            =false;//if TRUE reduce the RAM memory

      /*return pdf generation object*/
      $this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
      $this->pdf->set_header_info();
      $this->pdf->transcript_config();
      $this->generate_transcript($index_no,$with_marks);
   }

   public function getPdf(){
      return $this->pdf;   
   }


   public function generate_transcript($index_no,$with_marks){
      include A_CLASSES."/student_class.php";
      $student=new Student($index_no);
      $trancpt_detail=$student->getTranscript();
   
      //generate studetns socoring on each course in each year
      $year_title      ="<tr><td colspan='5' align='center' class='year'>YEAR %s</td></tr>";
      $course         ='';
      for($i=1;$i<=4;$i++){
         if($student->getYearCGPV($i)<=0)continue;
         $course.=sprintf($year_title,$i);
         foreach($student->getYearMarks($i) as $key => $course_arr ){
            $course.="<tr>";
            if($with_marks){
               $course.="<td>".$course_arr['course_id']."</td><td>".$course_arr['coursename']."</td><td>".$course_arr['credit']."</td><td>".$course_arr['mark']." - ".$course_arr['grade']."</td><td>".$course_arr['exam']."</td>";   
            }else{
               $course.="<td>".$course_arr['course_id']."</td><td>".$course_arr['coursename']."</td><td>".$course_arr['credit']."</td><td>".$course_arr['grade']."</td><td>".$course_arr['exam']."</td>";   

            }
            $course.="</tr>";
         }
      }

      //Following  variables are used to the fill the template
      $name            =$student->getName(2);
      $degree         ='Bachelor of Science in Computer Science Degree';
      $class         =$trancpt_detail['CLASS'];
      $issue_on      =date("M d,y");
      $reg_no         =$student->getRegNo();
      $DO_ADMIT      =$trancpt_detail['DOA'];
      $DO_AWARD      =$trancpt_detail['YOA'];
      $GPA            =$trancpt_detail['GPA'];


      //TODO: this array should be global fix this
      $gradeGpv = getGradeGPVArr();

      $grades='';
      foreach($gradeGpv as $grade => $gpv){
         $grades.="<tr><td>$grade</td><td>$gpv</td></tr>";
      }


      $classes_arr=getClassesArr();
      $classes="
            <tr><td width='35mm'>First class</td><td>3.5 and above</td></tr>
            <tr><td>Second class Upper</td><td>from 3.25 to 3.5</td></tr>
            <tr><td>Second class lower</td><td>from 3 to 3.25</td></tr>
            <tr><td>Pass</td><td>from 2 to 3</td></tr>";

      $AR_name="A.B.C. def";
               
      $template=<<<EOS
<style type="text/css">
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
   font-size:80%;
}

.trans_body th{
   text-align:left;
   background-color:whitesmoke;
   font-size:85%;
   font-weight:bold;
}

.trans_body td{
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
   <tr><td colspan='2' align='justify' class='paragraph'>This is to certify that <b>$name</b> sat for the <b>$degree</b> examination held under <b>index No: $index_no</b> and reached the standard required for a <b>$class</b></td></tr>
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
               <tr><th width='13%'>COURSE</th><th width='60%'>COURSE TITLE</th><th width='10%'>CREDITS</th><th width='10%'>MARK/<br>GRADE</th><th width='14%'>EXAM<br>DATE</th></tr>
            </thead>
            <tbody>
               $course
            </tbody>
         </table>
      </td>
      <td valign='top' width='60mm' >
         <table class='info'>
            <tr><td width='40mm'>YEAR OF ADMISSION</td><td>$DO_ADMIT</td></tr>
            <tr><td>DATE OF AWARD</td><td>$DO_AWARD</td></tr>
            <tr><td>GRADE POINT AVARAGE</td><td>$GPA</td></tr>
         </table>
         <br>
         <div class='section_title'>Key to Grades</div>
         <br>
         <br>
         &nbsp;&nbsp;<table border='1' style='border-collapse:collapse;' class='grade info' width='30mm' >
            <tr><th>GRADE</th><th align='center'>GP</th></tr>
            $grades
         </table>
         <br>
         <div class='section_title'>Key to Grade points</div>
         <br>
         <br>
         <table class='classes'>
            $classes
         </table>
      </td>
   </tr>
</table>
EOS;
      /*Add a page to the sheet*/
      $this->pdf->SetFont('helvetica', '', 9);

      //replace ' with " which does not support tcpdf
      $content=str_replace("'","\"",$template);

      /*write table to the sheet*/
      $this->pdf->writeHTML($content, true, false, false, false, 'L');
   }
}
?>
