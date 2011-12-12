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
		$this->header_info['logo']				=A_IMG."/ucsc-logo.png";
		$this->header_info['signature']		=A_IMG."/ar_signature.png";
		$this->header_info['year']				="SEPTEMBER 2011";
	}

	/*Page header*/
	public function Header() {
		/*write images in pdf*/
		/*
		$this->Image(
			$file		=$this->header_info['logo'],
	 	  	$x			=100,
	 	  	$y			=5,
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
		 */
		/*Set font*/ 
		//$this->SetFont('helvetica', 'B', 8);

		/*Custom long header  with html formatted*/ 
		/*
		$header ="
<h3>UNIVERSITY OF COLOMBO SCHOOL OF COMPUTING</h3>
UNIVERSITY OF COLOMBO<br/>
MASTER OF COMPUTER SCIENCE (MCS)<br/>
SELECTION TEST - SEPTEMBER ".$this->header_info['year']."<br/>
<h3>ADDMISSION CARD</h3>
<hr/>
";
		 */
		/*Header location from the top*/
		$this->SetY(0);

		/*Write header to each page*/
		//$this->writeHTML($header, true, false, false, false, 'C');
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
		//$PDF_HEADER_LOGO			=$this->header_info['logo'];
		//$PDF_HEADER_LOGO_WIDTH 	=20;
      //$PDF_HEADER_TITLE			='Markbook\naa';
		//$this->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE);

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
		$PDF_MARGIN_TOP			=10; 
		$PDF_MARGIN_RIGHT			=15;
		$this->SetMargins($PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, $PDF_MARGIN_RIGHT);
		
		$PDF_MARGIN_HEADER		=10;
		$PDF_MARGIN_FOOTER		=10;
		$this->SetHeaderMargin($PDF_MARGIN_HEADER);
		$this->SetFooterMargin($PDF_MARGIN_FOOTER);
		
		/*set auto page breaks*/
		$PDF_MARGIN_BOTTOM		=15;
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
		//$this->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		$this->SetFont('helvetica', '', 7);
	}
}


class Admission{
	/*date of the examination*/
	protected $date		='25-04-2010';

	/*duration and time of the examination*/
	protected $start	='10.00';

	protected $end		='12.00';

	/*Studen should come before this ammount of minutes*/
	protected $reliaf		='15';

	/*admission issued date by the AR/Exam*/
	protected $issued_on	='21-4-2010';

	/*Pdf generator object */
	protected $pdf;

	/*students index no */
	protected $registration_no;

	public function __construct($registration_no,$date,$start,$end,$reliaf,$issued_on){

		$this->registration_no	=$registration_no;
		$this->date			=$date;
		$this->start		=$start;
		$this->end			=$end;
		$this->reliaf		=$reliaf;
		$this->issued_on	=$issued_on;
		$this->duration	=$start.'-'.$end;

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
		This is where the student specific information is added to the page	
	*/
	public function add_student_info(){

		$query="SELECT * FROM ".$GLOBALS['MOD_P_TABLES']['registration']." u, ".$GLOBALS['MOD_P_TABLES']['student_alloc']." s, ".$GLOBALS['MOD_P_TABLES']['exam_hall']." h WHERE u.registration_no='".$this->registration_no."' AND u.registration_no=s.registration_no AND s.center_id=h.center_id AND s.hall_id=h.hall_id ";
		$res=exec_query($query,Q_RET_MYSQL_RES);
		$row=mysql_fetch_assoc($res);
		$day_arr=getdate(strtotime($this->date));

		$student_info=array(
'header' =>'
<table><tr>
<td width="15%" align="center"><img src="file://'.A_IMG.'/uoc-logo.png"></td>
<td align="center" width="70%">
<h3>UNIVERSITY OF COLOMBO SCHOOL OF COMPUTING</h3>
<b>UNIVERSITY OF COLOMBO</b><br/>
'.$GLOBALS['PROGRAMS'][$row['program']].' ('.$row['program'].')<br/>
SELECTION TEST - '.strtoupper($day_arr['month'].' '.$day_arr['year']).'
<h3>ADMISSION CARD</h3>
</td>
<td width="15%" align="center"><img src="file://'.A_IMG.'/ucsc-logo.png"></td>
</tr></table>
<hr/>
',

/*Personal information of  the student*/
'personal'=>'
<table cellpadding="3">
	<tr><td width="25%">NAME IN FULL</td><td width="75%">: '.strtoupper($row['first_name'].' '.$row['middle_names'].' '.$row['last_name']).'</td></tr>
	<tr><td>INDEX NUMBER </td><td>: '.$row['exam_no'].'</td></tr>
	<tr><td>EXAMINATION MEDIUM </td><td>: ENGLISH</td></tr>
	<tr><td>NIC/PASSPORT NO </td><td>: '.$row['NIC'].'</td></tr>
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
<h3>GENERAL CONDITIONS</h3>
<ol>
<li>No candidate will be admitted to the Examination Hall without this card.</li>
<li>All specimen signatures must be clearly written in ink.</li>
<li>Candidate should adhere to the rules of examinations and in case the Supervisor is satisfied beyond reasonable doubt that a candidate has committed an examination offence he/she should furnish written statement on the offence committed when requested by the Supervisor.</li>
<li>You are required to be present at the examination centre at least '.$this->reliaf.' minutes before the commencement of the examination.</li>
<li>Cellular phones and calculators cannot be brought in to the examination hall.</li>
</ol>',
/*Further information student should aware when sitting the exam*/
/*
'note'=>'
<style>
.line{
	border-bottom:1px dashed black;
	height:30px;
	vertical-align:bottom;
}
.t_center{
	text-align:center;
}
</style>

<table cellpadding="4">
	<tr><td class="line" align="center"><img width="60px" src="file://'.A_IMG.'/ar_signature.png"/></td><td>&nbsp;</td><td class="line">Date :'.$this->issued_on.'</td></tr>
	<tr><td>Assistant Registrar/Examination<br/><span style="text-decoration:line-through">/</span>Senior Assistant Registrar </td><td>&nbsp;</td><td>&nbsp;</td></tr>
</table>',
 */
'note'=>'
<br/>
<br/>
<table cellpadding="4">
<tr>
<td align="right" width="15%">Issued By:</td>
<td align="left" width="50%">Assistant Registrar / Examination<br/><span style="text-decoration:line-through">/</span>Senior Assistant Registrar</td>
<td align="right" width="10%">Date:</td>
<td align="left" width="25%">'.$this->issued_on.'</td>
</tr>
</table>
',

'attestation'=>'
<style>
.line{
	border-bottom:1px dashed black;
	height:30px;
	vertical-align:bottom;
}
.t_center{
	text-align:center;
}
</style>
<table>
	<tr><td colspan="3" >&nbsp;</td></tr>
	<tr><td colspan="3"><h3>ATTESTATION*</h3></td></tr>
	<tr><td colspan="3" align="left">I certify that I am the above named candidate bearing NIC/Passport number '.$row['NIC'].' and have placed my signature as indicated below.</td></tr>
	<tr><td class="line">Date:</td><td>&nbsp;</td><td class="line"></td></tr>
	<tr><td class="t_center">&nbsp;</td><td width="200px"></td><td class="t_center">Signature of candidate</td></tr>
	<tr><td colspan="3"><hr/></td></tr>
	<tr><td colspan="3" align="left">I certify that the above named candidate who is known to me personally placed his/her signature in my presence.</td></tr>
	<tr><td colspan="3" >&nbsp;</td></tr>
	<tr><td colspan="3" align="center"><h4>ATTESTER\'S DETAILS</h4></td></tr>
	<tr><td class="line"><p style="margin-top:100px">Name:</p></td><td colspan="2" class="line">&nbsp;</td></tr>
	<tr><td class="line">Address(Office):</td><td colspan="2" class="line">&nbsp;</td></tr>
	<tr><td class="line" align="left">Date:</td><td>&nbsp;</td><td class="line"></td></tr>
	<tr><td class="t_center"></td><td width="200px"></td><td class="t_center">Signature of Attester</td></tr>
	<tr><td>Official Seal of the Attester:</td><td>&nbsp;</td><td class="t_center"></td></tr>
</table>',

'nb'=>'
<br/>
<br/>
<br/>
<hr/>
<div style="text-align:justify;font-size:30px">
	* (Head or Retired Head of a Government/Director Managed approved  school, Grama Niladhari of the Division, Justice of Peace, Commissioner of Oaths, Attorney at Law, Notary Public, Commissioned Officer of the armed forces, Staff Officer of Govt./Corporation, the Chief Incumbent of the Buddhist Vihara, A religious Dignitary of standing of any other religion)	
</div>'
		);
//instructions to the page 2
$instructions='
	<h3>Important Instructions:</h3>
	<ol>
		<li>You are expected to bring the Admission Card to the Examination Centre duly signed and attested by the authorized person.*</li>

		<p>* (Head or Retired Head of a Government/Director Managed approved  school, Grama Niladhari of the Division, Justice of Peace, Commissioner of Oaths, Attorney at Law, Notary Public, Commissioned Officer of the armed forces, Staff Officer of Govt./Corporation, the Chief Incumbent of the Buddhist Vihara, A religious Dignitary of standing of any other religion)</p>
		
		<li>You must bring <b>one of the following identification documents</b> to the Examination Centre:</li>
		
		<ul>
			<li>National ID Card</li>
			<li>Passport</li>
			<li>Driving License</li>
			<li>Postal ID</li>
			<li>A Photograph in the absence of any of the above</li>
		</ul>

		<br/><b>(Please place your signature on the reverse of the photograph and your signature should be attested by an authorized person *)</b>
		
		<li>Your Index number for the Selection Test is given in the Admission Card.</li>
		<li>You are required to be at the assigned examination center at least '.$this->reliaf.' minutes before the commencement of the examination on the examination day.</li>
		<li>Examination will be starting at '.$this->start.' The paper is of two hours’ duration i.e. from '.$this->duration.'</li>
		<li>You must bring a good HB Pencil and an Eraser to the Examination Centre.</li>
		<li>Calculators and Cell phones will not be allowed within the Examination Centre.</li>
		<li>Passing the Selection Test alone does not guarantee you to be selected for the Master of Computer Science Programme.</li>
		<li>University of Colombo School of Computing (UCSC) will not provide you with travelling or any other expenses incurred for appearing for the Selection Test.</li>
		<li>Candidate should adhere to the rules of the Examination. In case the supervisor is satisfied beyond reasonable doubt that a candidate has committed an examination offence, he/she should furnish a written statement on the offence committed when requested by the supervisor.</li>
	</ol>
	
	';

		/*Start writing from the given amount of depth from top*/
		//$this->pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		//$this->pdf->SetY(0);
		$this->pdf->SetFont('Times', '', 10);
		foreach($student_info as $key => $info){
			//$this->pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
			$this->pdf->writeHTML($info, true, false, false, false, 'L');
		}

		$this->pdf->setPrintHeader(false);
		$this->pdf->AddPage();
		$this->pdf->Write(20, '', '', 0, 'C', true, 0, false, false, 0);
		$this->pdf->writeHTML($instructions, true, false, false, false, 'L');

	}
}

?>
