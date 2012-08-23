<?php
set_time_limit(200);
require_once(A_LIB.'/tcpdf/config/lang/eng.php');
require_once(A_LIB.'/tcpdf/tcpdf.php');

/* Extend tcpdf to go for further customization of header and footer and more  */
class MYPDF extends TCPDF {

   /*Array to store header info*/
   protected $header_info=array();

   /*Set header infor*/
   public function set_header_info(){
      /*Header information of the mark book*/
      $this->header_info['logo']            = $GLOBALS['A_LOGO'];
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
<h1>".$GLOBALS['INSTITUTE']."</h1>";

   /*Header position from the top*/
   $this->SetY(10);

   /*Write header to the file*/
   $this->writeHTML($header, true, false, false, false, 'C');
}
   /*Page footer*/
   public function Footer(){
      /*Position at 15 mm from bottom*/ 
      $this->SetY(-10);

      /*Set font*/ 
      $this->SetFont('helvetica', '', 8);

      /*Page number*/ 
      $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
      //$this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
   }

   public function letterhead_config(){

      /*set document information*/ 
      $this->SetCreator(PDF_CREATOR);
      $this->SetAuthor('University of Colombo School of Computing');
      $this->SetTitle('Selection tesst admission');
      $this->SetSubject('Year 2011');
      $this->SetKeywords('UCSC');

      /*set default header data*/ 
      $PDF_HEADER_LOGO        =$this->header_info['logo'];
      $PDF_HEADER_LOGO_WIDTH  =20;
      $PDF_HEADER_TITLE       ='Markbook\naa';
      $this->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE);

      /*set header and footer fonts*/ 
      $PDF_FONT_NAME_MAIN     ='helvetica';
      $PDF_FONT_SIZE_MAIN     =10;
      $PDF_FONT_NAME_DATA     ='helvetica';
      $PDF_FONT_SIZE_DATA     =8;
      $this->setHeaderFont(Array($PDF_FONT_NAME_MAIN, '', $PDF_FONT_SIZE_MAIN));
      $this->setFooterFont(Array($PDF_FONT_NAME_DATA, '', $PDF_FONT_SIZE_DATA));

      /*set default monospaced font*/ 
      $PDF_FONT_MONOSPACED      ='courier';
      $this->SetDefaultMonospacedFont($PDF_FONT_MONOSPACED);

      /*set margins*/
      $PDF_MARGIN_LEFT        =15;
      $PDF_MARGIN_TOP         =25; 
      $PDF_MARGIN_RIGHT       =10;
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


class Letterhead{

   /*date of the examination*/
   protected $date      ='25-04-2010';


   /*admission issued date by the AR/Exam*/
   protected $pdf;

   /*
    * @param page_format      : A4,A5,B4
    * @param page_orientation   : P,L 
    */
   public function __construct($page_format=null,$page_orientation=null){

      $this->date         =date('d-m-Y');

      /*Pdf generator page setup*/
      $PDF_PAGE_ORIENTATION   ='P';//(L,P)
      $PDF_PAGE_FORMAT        ='A4';
    
    if($page_format != null){
        $PDF_PAGE_FORMAT      =$page_format;
    }
    if( $page_orientation != null){
        $PDF_PAGE_ORIENTATION   = $page_orientation;
    }

      $PDF_UNIT               ='mm';//mm,in,pt,cm
      $UNICODE                =true; 
      $ENCODING               ='UTF-8'; 
      $DISKCACHE              =true;//if TRUE reduce the RAM memory



      /*return pdf generation object*/
      $this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
      $this->pdf->set_header_info();
      $this->pdf->letterhead_config();

   }

   public function getPdf(){
      return $this->pdf;   
   }


   public function include_content($content,$alignment='L'){
      /*Add a page to the sheet*/
      $this->pdf->SetFont('helvetica', '', 9);

      //replace ' with " which does not support tcpdf
      $content=str_replace("'","\"",$content);

      /*write table to the sheet*/
	   //public function writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='') {
      $this->pdf->writeHTML($content, true, false, false, false, $alignment);
   }
}
?>
