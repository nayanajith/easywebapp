<?php
require_once(A_LIB.'/tcpdf/config/lang/eng.php');
require_once(A_LIB.'/tcpdf/tcpdf.php');
/* Extend tcpdf to go for further customization of header and footer and more  */
class MYPDF extends TCPDF {
   /*Array to store header info*/
   protected $header_info=array();

   /*Set header infor*/
   public function set_header_info(){
      /*Get program information*/
      /*Header information of the mark book*/
      $this->header_info['logo']            =getcwd()."/img/ucsc-logo.png";
      $this->header_info['year']            ="2010/2011";
   }

   /*Page header*/
   public function Header() {
      /*write images in pdf*/
      $this->Image(
         $file      =$this->header_info['logo'],
            $x         ='100',
            $y         ='5',
            $w         =20,
            $h         =0,
            $type      ='PNG',
            $link      ='',
            $align   ='R',
            $resize   =false,
            $dpi      =600,
            $palign   ='',
            $ismask   =false,
            $imgmask   =false,
            $border   =0,
            $fitbox   =false,
            $hidden   =false,
            $fitonpage=false
      );

      /*Set font*/ 
      $this->SetFont('helvetica', 'B', 8);

      /*Custom long header  with html formatted*/ 
      $header ="
<h3>University of Colombo School of Computing (UCSC), Sri Lanka</h3>
University Transcripts Academic Year ".$this->header_info['year']." <br>
Common Aptitude Test for Selection of Candidates<br>
of the<br>
Bachelor of Information and Communication Technology (BICT) at UCSC,Vavuniya Campus and the Rajarata University
<h3>EXAMINATION ADDMISSION CARD</h3>
<hr>
";
      /*Header location from the top*/
      $this->SetY(25);

      /*Write header to each page*/
      $this->writeHTML($header, true, false, false, false, 'C');
   }
   /*Page footer*/
   public function Footer(){
      /*Position at 15 mm from bottom*/ 
      $this->SetY(-15);

      /*Set font*/ 
      $this->SetFont('helvetica', '', 8);

      /*Page number*/ 
      //$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
      $this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
   }

   public function transcript_config(){

      /*set document information*/ 
      $this->SetCreator(PDF_CREATOR);
      $this->SetAuthor('University of Colombo School of Computing');
      $this->SetTitle('Selection tesst admission');
      $this->SetSubject('Year 2011');
      $this->SetKeywords('UCSC, BICT, Transcript');

      /*set default header data*/ 
      $PDF_HEADER_LOGO         =$this->header_info['logo'];
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
      $PDF_MARGIN_TOP         =23; 
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
      $this->AddPage();
      $this->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
      $this->SetFont('helvetica', '', 7);
   }
}


class Transcript{
   //Pdf generator object 
   protected $pdf;

   //students info array 
   protected $index_no;



   public function __construct($index_no){
      $this->index_no=$index_no;

      //Pdf generator page setup
      $PDF_PAGE_ORIENTATION   ='L';//(L,P)
      $PDF_UNIT               ='mm';//mm,in,pt,cm
      $PDF_PAGE_FORMAT         ='A4';
      $UNICODE                  =true; 
      $ENCODING               ='UTF-8'; 
      $DISKCACHE               =true;//if TRUE reduce the RAM memory

      //return pdf generation object
      $this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
      $this->pdf->set_header_info();
      $this->pdf->transcript_config();
      $this->add_student_info();

   }

   //Return pointer to pdf document object
   public function getPdf(){
      return $this->pdf;   
   }


   //This is where the student speficic information is added to the page   
   public function add_student_info(){

      include MOD_CLASSES."/student_class.php";
      $student = new Student($this->index_no);
      $course_score="";
      for($i=1;$i<=4;$i++){
      $course_score.="<h2>Year-".$i."</h2>";
      $course_score.=$student->getYearCGPV($i)/$student->getYearCredits($i);
      $course_score.="<table>";
      foreach($student->getYearMarks($i) as $key => $course ){
         $course_score.="<tr>";
         foreach($course as $key => $value){
            $course_score.="<td>$value</td>";   
         }
         $course_score.="</tr>";
      }
      $course_score.="</table>";
      }

      $trancpt_detail=$student->getTranscript();

      //total information other than subject breakdown to be printed in transcript
      $transcript=array(
         'index_no'   =>$this->index_no,
         'fullname'   =>$student->getName(2),
         'RegNo'      =>$student->getRegNo(),
         'DIssue'      =>date("Y-m-d"),
         'dgrad'      =>$trancpt_detail['YOA'],
         'dreg'      =>$trancpt_detail['DOA'],
               'DegreeName'=>$trancpt_detail['DEGREE'],
             'DClass'      =>$trancpt_detail['CLASS'],
             'GPA'         =>$trancpt_detail['GPA']
      );


      /*Start writing from the given amount of depth from top*/
      $this->pdf->Write(25, '', '', 0, 'C', true, 0, false, false, 0);

      $this->pdf->writeHTML($course_score, true, false, false, false, 'L');
      foreach($transcript as $key => $info){
         //$this->pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
         $this->pdf->SetFont('Times', '', 9);
         $this->pdf->writeHTML($info, true, false, false, false, 'L');
      }
   }
}

?>
