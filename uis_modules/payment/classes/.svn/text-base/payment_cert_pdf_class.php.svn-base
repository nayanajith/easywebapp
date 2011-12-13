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
		$this->header_info['logo']				=getcwd()."/img/ucsc-logo.png";
		$this->header_info['year']				="2010/2011";
	}

	/*Page header*/
	public function Header() {
		/*write images in pdf*/
		$this->Image(
			$file		=$this->header_info['logo'],
	 	  	$x			='100',
	 	  	$y			='5',
	 	  	$w			=20,
	 	  	$h			=0,
	 	  	$type		='PNG',
	 	  	$link		='',
	 	  	$align	='R',
	 	  	$resize	=false,
	 	  	$dpi		=600,
	 	  	$palign	='',
	 	  	$ismask	=false,
	 	  	$imgmask	=false,
	 	  	$border	=0,
	 	  	$fitbox	=false,
	 	  	$hidden	=false,
	 	  	$fitonpage=false
		);

		/*Set font*/ 
		$this->SetFont('helvetica', 'B', 8);

		/*Custom long header  with html formatted*/ 
		$header ="
<h3>University of Colombo School of Computing (UCSC), Sri Lanka</h3>
University Admissions Academic Year ".$this->header_info['year']." <br/>
Common Aptitude Test for Selection of Candidates<br/>
of the<br/>
Bachelor of Information and Communication Technology (BICT) at UCSC,Vavuniya Campus and the Rajarata University
<h3>EXAMINATION ADDMISSION CARD</h3>
<hr/>
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

	public function admission_config(){

		/*set document information*/ 
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('University of Colombo School of Computing');
		$this->SetTitle('Selection tesst admission');
      $this->SetSubject('Year 2011');
		$this->SetKeywords('UCSC, BICT, Admission');

		/*set default header data*/ 
		$PDF_HEADER_LOGO			=$this->header_info['logo'];
		$PDF_HEADER_LOGO_WIDTH 	=20;
      $PDF_HEADER_TITLE			='Markbook\naa';
		$this->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE);

      /*set header and footer fonts*/ 
		$PDF_FONT_NAME_MAIN		='helvetica';
		$PDF_FONT_SIZE_MAIN		=10;
      $PDF_FONT_NAME_DATA		='helvetica';
		$PDF_FONT_SIZE_DATA		=8;
		$this->setHeaderFont(Array($PDF_FONT_NAME_MAIN, '', $PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(Array($PDF_FONT_NAME_DATA, '', $PDF_FONT_SIZE_DATA));

		/*set default monospaced font*/ 
		$PDF_FONT_MONOSPACED		='courier';
		$this->SetDefaultMonospacedFont($PDF_FONT_MONOSPACED);

		/*set margins*/
		$PDF_MARGIN_LEFT			=25;
		$PDF_MARGIN_TOP			=23; 
		$PDF_MARGIN_RIGHT			=15;
		$this->SetMargins($PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, $PDF_MARGIN_RIGHT);
		
		$PDF_MARGIN_HEADER		=5;
		$PDF_MARGIN_FOOTER		=10;
		$this->SetHeaderMargin($PDF_MARGIN_HEADER);
		$this->SetFooterMargin($PDF_MARGIN_FOOTER);
		
		/*set auto page breaks*/
		$PDF_MARGIN_BOTTOM		=25;
		$this->SetAutoPageBreak(TRUE, $PDF_MARGIN_BOTTOM);
		
      /*set image scale factor*/
		$PDF_IMAGE_SCALE_RATIO	=1.25;
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


class Admission{
	/*date of the examination*/
	protected $date		='25-04-2010';

	/*duration and time of the examination*/
	protected $duration	='10.00-12.00';

	/*Studen should come before this ammount of minutes*/
	protected $releaf		='15';

	/*admission issued date by the AR/Exam*/
	protected $issued_on	='21-4-2010';

	/*Pdf generator object */
	protected $pdf;

	/*students index no */
	protected $index_no;

	public function __construct($index_no,$date,$duration,$releaf,$issued_on){

		$this->index_no	=$index_no;
		$this->date			=$date;
		$this->duration	=$duration;
		$this->releaf		=$releaf;
		$this->issued_on	=$issued_on;

		/*Pdf generator page setup*/
		$PDF_PAGE_ORIENTATION	='P';//(L,P)
		$PDF_UNIT					='mm';//mm,in,pt,cm
		$PDF_PAGE_FORMAT			='A4';
		$UNICODE						=true; 
		$ENCODING					='UTF-8'; 
		$DISKCACHE					=true;//if TRUE reduce the RAM memory

		/*return pdf generation object*/
		$this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
		$this->pdf->set_header_info();
		$this->pdf->admission_config();

	}

	/*	Return pointer to pdf document object 	*/
	public function getPdf(){
		return $this->pdf;	
	}


	/*
		This is where the student speficic information is added to the page	
	*/
	public function add_student_info(){

		//include "db.php";

		$query="SELECT * FROM ".$GLOBALS['P_TABLES']['user_info']." u, ".$GLOBALS['P_TABLES']['student_alloc']." s, ".$GLOBALS['P_TABLES']['exam_hall']." h WHERE u.index_no='".$this->index_no."' AND u.index_no=s.index_no AND s.center_id=h.center_id AND s.hall_id=h.hall_id ";
		$res=mysql_query($query,$GLOBALS['CONNECTION']);
		$row=mysql_fetch_assoc($res);

		$student_info=array(
/*Personal information of  the student*/
'personal'=>'
<table cellpadding="3">
	<tr><td width="35%">NAME WITH INITIALS</td><td>: '.$row['surname'].'</td></tr>
	<tr><td>INDEX NUMBER </td><td>: '.$row['exam_no'].'</td></tr>
	<tr><td>EXAMINATION MEDIUM </td><td>: ENGLISH</td></tr>
	<tr><td>NIC NO </td><td>: '.$row['nic'].'</td></tr>
	<tr><td>EXAMINATION CENTER</td><td>: '.$row['center_address'].'</td></tr>
</table>',

/*Signature of the student and the invigilator when thay attend the exam*/
'signature'=>'
<table border="1" cellpadding="2">
	<tr><td>Date & Time of Examination</td><td>Exam Hall</td><td>Candidate\'s Signature</td><td>Invigilator\'s Signature</td></tr>
	<tr><td>'.$this->date.'<br/>'.$this->duration.'</td><td>'.$row['hall'].'</td><td>&nbsp;</td><td>&nbsp;</td></tr>
</table>',

/*Conditions that students should be aware when the students sit to the exam */
'conditions'=>'
<h3>General Conditions</h3>
<ol>
<li>No candidate will be admitted to the Examination Hall without this card.</li>
<li>All specimen signatures must be clearly written in ink.</li>
<li>Candidate should adhere to the rules of examinations given in the attached document and in case the Supervisor is satisfied beyond reasonable doubt that a candidate has committed an examination offence he/she should furnish written statement on the offence committed when requested by the Supervisor.</li>
</ol>',

/*Further information student should aware when sitting the exam*/
'note'=>'
<table cellpadding="4">
	<tr><td colspan="2">You are required to be present at the examination centre at least '.$this->releaf.' minutes before the commencement of the examination.</td></tr>
	<tr><td colspan="2">Cellular phones and calculators cannot be brought in to the examination hall.</td></tr>
	<tr><td>Issued by: AR/Examinations UCSC</td><td>Date :'.$this->issued_on.'</td></tr>
</table>',

'attestation'=>'
<style>
.line{
	border-bottom:1px dashed black;
	height:40px;
	vertical-align:bottom;
}
.t_center{
	text-align:center;
}
</style>
<table>
	<h3>Attestation*</h3>
	<tr><td colspan="3" align="left">I certify that the above named candidate who is known to me personally placed his/her signature in my presence.</td></tr>
	<tr><td class="line"></td><td>&nbsp;</td><td class="line"></td></tr>
	<tr><td class="t_center">Signature of candidate</td><td width="200px"></td><td class="t_center">Signature of Attester</td></tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td height="100px" class="line"></td></tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td class="t_center">Official Seal of the Attester (with name)</td></tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td class="line" align="left">&nbsp;<br/>Date:</td></tr>
</table>',

'nb'=>'
<hr/>
<div style="text-align:justify">
	* (Head or Retired Head of a Government/Director Managed approved  school, Grama Niladhari of the Division, Justice of Peace, Commissioner of Oaths, Attorney at Law, Notary Public, Commissioned Officer of the armed forces, Staff Officer of Govt./Corporation, the Chief Incumbent of the Buddhist Vihara, A religious Dignitary of standing of any other religion)	
</div>'
		);

	/*Start writing from the given amount of depth from top*/
	$this->pdf->Write(25, '', '', 0, 'C', true, 0, false, false, 0);

	foreach($student_info as $key => $info){
		//$this->pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		$this->pdf->SetFont('Times', '', 9);
		$this->pdf->writeHTML($info, true, false, false, false, 'L');
	}

	}
}

?>
