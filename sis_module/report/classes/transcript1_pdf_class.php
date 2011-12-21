<?php
set_time_limit(200);
require_once(A_LIB.'/tcpdf/config/lang/eng.php');
require_once(A_LIB.'/tcpdf/tcpdf.php');

/* Extend tcpdf to go for further customization of header and footer and more  */
class MYPDF extends TCPDF {

   /*Array to store header info*/
   protected $header_info   =array();
   protected $AR_name      ='A.B.C Mangalie';

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
      $this->SetY(-25);

      /*Set font*/ 
      $this->SetFont('helvetica', '', 8);
      $custom_footer="
         <table>
            <tr>
               <td>&nbsp;</td>
               <td align='center'>
                  ...........................................................<br>
                  ".$this->AR_name."<br>
                  Assistant Registrar/Examinations<br>
                  for Registrat
               </td>
            </tr>
         </table>";
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
      $PDF_HEADER_LOGO_WIDTH=20;
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
   public function __construct($index_no,$with_marks,$note){
      /*Pdf generator page setup*/
      $PDF_PAGE_ORIENTATION='L';//(L,P)
      $PDF_PAGE_FORMAT      ='A4';
      $PDF_UNIT            ='mm';//mm,in,pt,cm
      $UNICODE               =true; 
      $ENCODING            ='UTF-8'; 
      $DISKCACHE            =true;//if TRUE reduce the RAM memory

      /*return pdf generation object*/
      $this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
      $this->pdf->set_header_info();
      $this->pdf->transcript_config();
      $this->generate_transcript($index_no,$with_marks,$note);

   }

   public function getPdf(){
      return $this->pdf;   
   }


   public function generate_transcript($index_no,$with_marks,$note){

      //Note to display in transcript
      $note="<tr><td>NOTE</td><td>: $note</td></tr>";

      include A_CLASSES."/student_class.php";
      $student=new Student($index_no);
      $trancpt_detail=$student->getTranscript();

   
      //generate studetns socoring on each course in each year
      $year_title         ="<tr><td colspan='5'align='center'>YEAR %s</td></tr>";
      $course_year_arr   =array(1=>"",2=>"",3=>"",4=>"");
      
      for($i=1;$i<=4;$i++){
         if($student->getYearCGPV($i)<=0)continue;
         $course_year_arr[$i].=sprintf($year_title,$i);
         foreach($student->getYearMarks($i) as $key => $course_arr ){
            $course_year_arr[$i].="<tr>";
            if($with_marks){
               $course_year_arr[$i].="<td>".$course_arr['course_id']."</td><td>".$course_arr['coursename']."</td><td>".$course_arr['credit']."</td><td>".$course_arr['mark']." - ".$course_arr['grade']."</td><td>".$course_arr['exam']."</td>";   
            }else{
               $course_year_arr[$i].="<td>".$course_arr['course_id']."</td><td>".$course_arr['coursename']."</td><td>".$course_arr['credit']."</td><td>".$course_arr['grade']."</td><td>".$course_arr['exam']."</td>";   

            }
            $course_year_arr[$i].="</tr>";
         }
      }
      $course_year1=isset($course_year_arr[1])?$course_year_arr[1]:'';
      $course_year2=isset($course_year_arr[2])?$course_year_arr[2]:'';
      $course_year3=isset($course_year_arr[3])?$course_year_arr[3]:'';
      $course_year4=isset($course_year_arr[4])?$course_year_arr[4]:'';

      //Following  variables are used to the fill the template
      $name            =$student->getName(2);
      $degree         ='Bachelor of Science in Computer Science Degree';
      $class         =$trancpt_detail['CLASS'];
      $issue_on      =date("M d, Y");
      $reg_no         =$student->getRegNo();
      $d_o_admit      =$trancpt_detail['DOA'];
      $d_o_award      =$trancpt_detail['YOA'];
      $GPA            =$trancpt_detail['GPA'];


      $template=<<<EOS
<style type='text/css'>
*{
   font-size:94%;
   font-family:arial;
}
.title{
   border-bottom:1px solid black;
   font-weight:bold;
}

.name{
   font-weight:bold;
}

.index_no{
   font-weight:bold;
}

.trans_body{
}

.trans_body th{
   border-bottom:1px solid black;
   text-align:left;
   font-size:85%;
   font-weight:bold;
}

.trans_body td{
}

.trans_body_2{
}

.trans_body_2 th{
   text-align:left;
   font-size:85%;
   font-weight:bold;
}

.year{
   text-align:center;
   font-size:90%;
}
.info{
   font-size:90%;
   font-weight:bold;
}

</style>
<table  cellpadding='2' cellspacing='0' >
   <tr><td  class='title'>ACADEMIC TRANSCRIPT</td><td align='right' class='title'>DATE ISSUED : $issue_on</td></tr>
   <tr><td class='name' colspan='2' align='left'>NAME : $name</td></tr>
   <tr><td class='index_no'>INDEX NUMBER : $index_no</td><td align='right' class='index_no'>REGISTRATION NUMBER : $reg_no</td></tr>
   <tr><td class='name' colspan='2'>&nbsp;</td></tr>
   <tr>
      <td  valign='top' >
         <table width='100%' class='trans_body' cellpadding='0' cellspacing='0'>
            <tr><th width='10%'>COURSE</th><th width='60%'>COURSE TITLE</th><th width='9%'>CREDITS</th><th width='10%'>MARK/<br>GRADE</th><th width='14%'>EXAM<br>DATE</th></tr>
            $course_year1
            </table>
      <table width='100%' class='trans_body_2' cellpadding='0' cellspacing='0'>
            <tr><th width='10%'>&nbsp;</th><th width='60%'>&nbsp;</th><th width='9%'>&nbsp;</th><th width='10%'>&nbsp;</th><th width='14%'>&nbsp;</th></tr>
            $course_year2
            </table>
      </td>
      <td  valign='top' >
         <table width='100%' class='trans_body' cellpadding='0' cellspacing='0'>
            <tr><th width='10%'>COURSE</th><th width='60%'>COURSE TITLE</th><th width='9%'>CREDITS</th><th width='10%'>MARK/<br>GRADE</th><th width='14%'>EXAM<br>DATE</th></tr>
            $course_year3
         </table>
         <table width='100%' class='trans_body_2' cellpadding='0' cellspacing='0'>
            <tr><th width='10%'>&nbsp;</th><th width='60%'>&nbsp;</th><th width='9%'>&nbsp;</th><th width='10%'>&nbsp;</th><th width='14%'>&nbsp;</th></tr>
            $course_year4
         </table>
      </td>
   </tr>
</table>
EOS;
      $info=<<<EOS
<style type='text/css'>
   .info{
      font-size:90%;
      font-weight:bold;
   }
</style>
<table>
   <tr>
      <td width='60%'>
         <table class='info'>
            <tr><td width='25%'>DEGREE</td><td>: $degree</td></tr>
            <tr><td>YEAR OF ADMISSION</td><td>: $d_o_admit</td></tr>
            <tr><td>DATE OF AWARD</td><td>: $d_o_award</td></tr>
            <tr><td>GRADE POINT AVARAGE</td><td>: $GPA</td></tr>
            <tr><td>CLASS OBTAINED</td><td>: $class</td></tr>
            $note
         </table>
      </td>
      <td>&nbsp;</td>
   </tr>
</table>
EOS;


      /*Add a page to the sheet*/
      $this->pdf->SetFont('helvetica', '', 9);

      //replace ' with " which does not support tcpdf
      $content=str_replace("'","\"",$template);

      /*write table to the sheet*/
      $this->pdf->writeHTML($content, true, false, false, false, 'L');

      $this->pdf->SetY(-40);
      $this->pdf->writeHTML(str_replace("'","\"",$info), true, false, false, false, 'L');
   }
}
?>
