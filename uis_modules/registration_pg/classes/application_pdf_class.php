<?php

require_once(A_LIB.'/tcpdf/config/lang/eng.php');
require_once(A_LIB.'/tcpdf/tcpdf.php');

/* Extend tcpdf to go for further customization of header and footer and more  */
class MYPDF extends TCPDF {

	/*Page footer*/
	
	public function Footer(){
		//Position at 15 mm from bottom
		//$this->SetY(-10);

		//Set font
		//$this->SetFont('helvetica', '', 8);

		//Page number
		//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		//$this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}

	/*Page header*/
	public function Header() {

		/*write images in pdf*/
		$this->Image(
			$file		=A_IMG."/".$GLOBALS['LOGO2'],
	 	  	$x			='20',
	 	  	$y			='7',
	 	  	$w			=15,
	 	  	$h			=0,
	 	  	$type		='PNG',
	 	  	$link		='',
	 	  	$align	='R',
	 	  	$resize	=false,
	 	  	$dpi		=650,
	 	  	$palign	='',
	 	  	$ismask	=false,
	 	  	$imgmask=false,
	 	  	$border	=0,
	 	  	$fitbox	=false,
	 	  	$hidden	=false,
	 	  	$fitonpage=false
		);

		/*write images in pdf*/
		$this->Image(
			$file		=A_IMG."/".$GLOBALS['LOGO'],
	 	  	$x			='175',
	 	  	$y			='7',
	 	  	$w			=15,
	 	  	$h			=0,
	 	  	$type		='PNG',
	 	  	$link		='',
	 	  	$align	='R',
	 	  	$resize	=false,
	 	  	$dpi		=650,
	 	  	$palign	='',
	 	  	$ismask	=false,
	 	  	$imgmask=false,
	 	  	$border	=0,
	 	  	$fitbox	=false,
	 	  	$hidden	=false,
	 	  	$fitonpage=false
		);


		//Set font
		$this->SetFont('helvetica', 'B', 8);
		
		//Custom long header  with html formatted
		$header ="<h1>University of Colombo School of Computing</h1>
	 <h3>University of Colombo – Sri Lanka</h3>";

		//Header position from the top
		$this->SetY(10);

		//Write header to the file
		$this->writeHTML($header, true, false, false, false, 'C');
	}

	public function application_config(){

		/*set document information*/ 
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('University of Colombo School of Computing');
		$this->SetTitle('PG Selection Test Admission');
		$this->SetSubject('Year 2011');
		$this->SetKeywords('UCSC');

		/*set default header data*/ 
		/*
		$PDF_HEADER_LOGO			=IMG."/".$GLOBALS['LOGO'];
		$PDF_HEADER_LOGO_WIDTH=20;
		$PDF_HEADER_TITLE			='Markbook\naa';
		$this->SetHeaderData($PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE);
		 */

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
		$PDF_MARGIN_LEFT			=15;
		$PDF_MARGIN_TOP			=25; 
		$PDF_MARGIN_RIGHT			=15;
		$this->SetMargins($PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, $PDF_MARGIN_RIGHT);
		
		$PDF_MARGIN_HEADER		=5;
		$PDF_MARGIN_FOOTER		=5;
		$this->SetHeaderMargin($PDF_MARGIN_HEADER);
		$this->SetFooterMargin($PDF_MARGIN_FOOTER);
		
		/*set auto page breaks*/
		$PDF_MARGIN_BOTTOM		=10;
		$this->SetAutoPageBreak(TRUE, $PDF_MARGIN_BOTTOM);
		
		/*set image scale factor*/
		$PDF_IMAGE_SCALE_RATIO	=1.25;
		$this->setImageScale($PDF_IMAGE_SCALE_RATIO);
		
		/*set some language-dependent strings*/
		//$this->setLanguageArray($l);
		
		/* set font*/
		$this->SetFont('helvetica', '', 20);
		$this->AddPage();
	}
}


class Application{

	/*date of the examination*/
	protected $date		='25-04-2010';

	/*admission issued date by the AR/Exam*/
	protected $pdf;

	//payer info array
	protected $applier_info;

	/*
	 * @param page_format		: A4,A5,B4
	 * @param page_orientation	: P,L 
	 */
	public function __construct($applier_info){
		$this->applier_info		=$applier_info;
		$this->year					=date('Y');

		/*Pdf generator page setup*/
		$PDF_PAGE_ORIENTATION	='P';//(L,P)
		$PDF_PAGE_FORMAT			='A4';
		$PDF_UNIT					='mm';//mm,in,pt,cm
		$UNICODE						=true; 
		$ENCODING					='UTF-8'; 
		$DISKCACHE					=true;//if TRUE reduce the RAM memory

		/*return pdf generation object*/
		$this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
		$this->pdf->application_config();
		$this->gen_application();
	}

	//Return the pdf file
	public function getPdf(){
		return $this->pdf;	
	}


	//Generate quadruples of vouchers using the information feeded in constructor
	public function gen_application(){
		$row=$this->applier_info;
		$content='
<style type='text/css'>
	th{
		text-align:left;	
		font-weight:bold;
	}
	td{
		font-size:8.5pt;	
	}
</style>
<table border="1" style="border-collapse:collapse;width:650" cellpadding="2">
<tr>
	<td align="center" colspan="2" width="650">
		<h3>'.$GLOBALS['PROGRAMS'][$row['program']].' – '.$this->year.'</h3>
		<h4>APPLICATION FORM</h4>
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<b>Name in Full:&nbsp;</b>'.$row['first_name'].' '.$row['middle_names'].' '.$row['last_name'].'
	</td>
</tr>
<tr>
	<td width="325">
	<table >
		<tr><th>Permanent Address:</th><td><br>'.$row['address1_1'].', '.$row['address2_1'].', '.$row['address3_1'].'.</td></tr>	
		<tr><th>Mobile Phone:</th><td>'.$row['mobile_1'].'</td></tr>	
		<tr><th>Fixed Phone:</th><td>'.$row['telephone_1'].'</td></tr>	
		<tr><th>E-mail:</th><td>'.$row['email_1'].'</td></tr>	
	</table>
	</td>
	<td width="325">
	<table >
		<tr><th>Office Address:</th><td><br>'.$row['address1_2'].', '.$row['address2_2'].', '.$row['address3_2'].'.</td></tr>	
		<tr><th>Mobile Phone:</th><td>'.$row['mobile_2'].'</td></tr>	
		<tr><th>Fixed Phone:</th><td>'.$row['telephone_2'].'</td></tr>	
		<tr><th>E-mail:</th><td>'.$row['email_2'].'</td></tr>	
	</table>
	</td>
</tr>
<tr>
	<td width="325">
		<b>Address for Correspondence :</b> '.strtolower($row['corresp_addr']).' <br> 
		   (Note :- All correspondence will be sent to this address.)
	</td>
	<td width="325">
		<table >
			<tr><th>National ID / Passport:</th><td>'.$row['NIC'].'</td></tr>	
			<tr><th>Date of Birth:</th><td>'.$row['DOB'].'</td></tr>	
			<tr><th>Gender:</th><td>'.$row['gender'].'</td></tr>	
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<b>Designation :</b>'.$row['designation'].'
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<b>Work Place :</b>'.$row['affiliation'].'
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<b>Job Description :</b>'.$row['job_description'].'
	</td>
</tr>
<tr>
	<td  width="325">
	<h4>University Education:</h4>	
		<table >
			<tr><td colspan="2">'.$row['university_1'].'</td></tr>	
			<tr><th>Degree Title:</th><td>'.$row['degree_title_1'].'</td></tr>	
			<tr><th>Year of Award:</th><td>'.$row['year_of_award_1'].'</td></tr>	
			<tr><th>Class/GPA:</th><td>'.$row['class_1'].'</td></tr>	
			<tr><th>Date Entered:</th><td>'.$row['date_entered_1'].'</td></tr>	
			<tr><th>Date Left:</th><td>'.$row['date_left_1'].'</td></tr>	
			<tr><th colspan="2">Degree Subjects:</th></tr>	
			<tr><td colspan="2">
					<ol>
						<li>'.$row['subject1_1'].'</li>
						<li>'.$row['subject2_1'].'</li>
						<li>'.$row['subject3_1'].'</li>
						<li>'.$row['subject4_1'].'</li>
					</ol>
			</td></tr>
		</table>
	</td>
	<td  width="325">
	<h4>University Education:</h4>	
		<table >
			<tr><td colspan="2">'.$row['university_2'].'</td></tr>	
			<tr><th>Degree Title:</th><td>'.$row['degree_title_2'].'</td></tr>	
			<tr><th>Year of Award:</th><td>'.$row['year_of_award_2'].'</td></tr>	
			<tr><th>Class/GPA:</th><td>'.$row['class_2'].'</td></tr>	
			<tr><th>Date Entered</th><td>'.$row['date_entered_2'].'</td></tr>	
			<tr><th>Date Left:</th><td>'.$row['date_left_2'].'</td></tr>	
			<tr><th colspan="2">Degree Subjects:</th></tr>	
			<tr><td colspan="2">
					<ol>
						<li>'.$row['subject1_2'].'</li>
						<li>'.$row['subject2_2'].'</li>
						<li>'.$row['subject3_2'].'</li>
						<li>'.$row['subject4_2'].'</li>
					</ol>
			</td></tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<h4>Other Educational / Professional Qualifications :</h4>
		<table style="font-size:80%">
			<tr><th></th><th>Qualification/Certificate</th><th>Institution/Organization</th><th>Date of Award</th><th>Duration</th></tr>
			<tr><td>1.</td><td>'.str_replace("#","</td><td>",$row['edu_prof_qual1']).'</td></tr>
			<tr><td>2.</td><td>'.str_replace("#","</td><td>",$row['edu_prof_qual2']).'</td></tr>
			<tr><td>3.</td><td>'.str_replace("#","</td><td>",$row['edu_prof_qual3']).'</td></tr>
			<tr><td>4.</td><td>'.str_replace("#","</td><td>",$row['edu_prof_qual4']).'</td></tr>
			<tr><td>5.</td><td>'.str_replace("#","</td><td>",$row['edu_prof_qual5']).'</td></tr>
			<tr><td>6.</td><td>'.str_replace("#","</td><td>",$row['edu_prof_qual6']).'</td></tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<h4>Employment Record :</h4>
		<table style="font-size:80%">
			<tr><th></th><th>Designation</th><th>Work Place/Employer</th><th>From Date</th><th>To Date</th></tr>
			<tr><td>1.</td><td>'.str_replace('#','</td><td>',$row['emp_rec1']).'</td></tr>
			<tr><td>2.</td><td>'.str_replace('#','</td><td>',$row['emp_rec2']).'</td></tr>
			<tr><td>3.</td><td>'.str_replace('#','</td><td>',$row['emp_rec3']).'</td></tr>
			<tr><td>4.</td><td>'.str_replace('#','</td><td>',$row['emp_rec4']).'</td></tr>
			<tr><td>5.</td><td>'.str_replace('#','</td><td>',$row['emp_rec5']).'</td></tr>
		</table>
	</td>
</tr>
<tr>
	<td  width="325">
	<h4>Referee (On University Education):</h4>	
		<table >
			<tr><th>Name:</th><td>'.$row['referee_name1'].'</td></tr>	
			<tr><th>Designation:</th><td>'.$row['referee_designation1'].'</td></tr>	
			<tr><th>Work Place:</th><td>'.$row['referee_work_place1'].'</td></tr>	
			<tr><th>Address:</th><td>'.$row['referee_address1'].'</td></tr>	
			<tr><th>Phone:</th><td>'.$row['referee_phone1'].'</td></tr>	
			<tr><th>E-mail:</th><td>'.$row['referee_email1'].'</td></tr>	
		</table>
	</td>
	<td  width="325">
	<h4>Referee (On Employment):</h4>	
		<table >
			<tr><th>Name:</th><td>'.$row['referee_name2'].'</td></tr>	
			<tr><th>Designation:</th><td>'.$row['referee_designation2'].'</td></tr>	
			<tr><th>Work Place:</th><td>'.$row['referee_work_place2'].'</td></tr>	
			<tr><th>Address:</th><td>'.$row['referee_address2'].'</td></tr>	
			<tr><th>Phone:</th><td>'.$row['referee_phone2'].'</td></tr>	
			<tr><th>E-mail:</th><td>'.$row['referee_email2'].'</td></tr>	
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<b>Note: </b>
		The above cages on referee details must be filled in. However you will be required to submit the above referee reports only when you are called for the Interview.
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		I declare that the above information is true and correct to the best of my knowledge. I agree to abide by the conditions stipulated by the University of Colombo School of Computing.<br>
		<table >
			<tr><th align="right">Date :</th><td style="border-bottom:1px dotted black;" ></td><th align="right">Signature :</th><td style="border-bottom:1px dotted black;"></td></tr>
		</table>
	</td>
</tr>


<tr>
	<td  align="center" colspan="2">
		<h4>For Office Use Only</h4>
	</td>
</tr>
<tr>
	<td  width="325">
		<table >
			<tr><td>Reference No :</td><td>&nbsp;</td></tr>  
			<tr><td>Attended Interview :</td><td>&nbsp;</td></tr>
			<tr><td>Aptitude Test: </td><td>Pass / Fail</td></tr>
		</table>
	</td>
	<td  width="325">
		<table >
			<tr><td>Called for Interview :</td><td>&nbsp;</td></tr>
			<tr><td>Selected For ( MIT/MCS/MSc.IS ):</td><td>&nbsp;</td></tr>
			<tr><td>Receipt No :</td><td>&nbsp;</td></tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan="2" width="650">
		<h4>Remarks :</h4>
		<br>
		<br>
	</td>
</tr>
</table>';

$notice='
<div align="center">
	<h3>User Instructions for the Masters Programmes – '.$this->year.'</h3>
	<h4>Note: Each applicant is allowed to submit only one online application form.</h4>
</div>
<div align="justify">
<p>
It is compulsory for candidates to complete the on-line application form available at the UCSC website http://www.ucsc.cmb.ac.lk. 

<p>
The application processing fee of Rs. 1,500/- can be paid by online or offline.

<ul>
<li>
If you follow online mode you should follow the payment Instruction given in the website and post only the printout of the application form after putting your signature.
</li>

<li>
If you follow offline mode you have to pay at any branch of the People’s Bank by downloading the cash paying slip and drawn in favour of MSc Programmes, University of Colombo School of Computing People’s Bank Account No. 086-1001-511-89665 (Thimbirigasyaya Branch). Please note that the UCSC copy of the cash-paying-in slip has to be sent along with the printout of the signed application form. Cheques/Money orders will not be accepted.  
</li>
</ul>
</p>

<p>
It is compulsory that the On-line Application should be submitted on or before Tuesday 30th August 2011. The printout of the on-line Application Form and the payment slip should be sent only under Registered Post to the Senior Assistant Registrar/Academic & Publications, UCSC, No: 35, Reid Avenue, Colombo 7 on or before 30th August 2011.
</p>

<p>
Please indicate on the top left hand corner of the envelope the title “Masters Programmes 2011”.
</p>

<p>
Those who have successfully completed the application process could obtained their Admission card by 02nd September 2011 from the UCSC website.
</p>

<p>
Inquiries regarding the Application Process and/or Aptitude Test should be made to Senior Assistant Registrar/Academic & Publications on 0112589123.  
</p>
			
<h4>Date of Aptitude: 10th September 2011</h4>
</div>
';
			$this->pdf->SetFont('helvetica', '', 9);


			//PRINT 1D BARCODES
			//write1DBarcode($code, $type, $x='', $y='', $w='', $h='', $xres='', $style='', $align='')
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
         'fontsize' => 8,
         'stretchtext' => 4
		);
			$this->pdf->setXY(15,24);
			$this->pdf->write1DBarcode($row['registration_no'], 'I25', '', '', '80', 15, 0.4, $style, 'N');

			/*write table to the sheet*/
			//writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
			$this->pdf->setXY(13,25);
			$this->pdf->writeHTML($content, true, false, false, false, 'L');

			$this->pdf->AddPage();
			$this->pdf->writeHTML($notice, true, false, false, false, 'L');
		}
}

/*
//EXAMPLE:
$acc_no="086-1001-211-90316";
$vou_title="BACHELOR OF INFORMATION AND COMMUNICATION TECHNOLOGY";
$purpose="CONVOCATION REGISTRATION FEE";

$payment_info=array(
   "RS 2000.00",
   "SRI LANKAN RUPEES TOW THOUSAND ONLY",
   "Nayanajith mahendra laxaman",
   "08002002",
	"812940201v"
);
	
//__construct($applier_info,$acc_no,$vou_title)
$voucher=new Application($payment_info,$acc_no,$vou_title,$purpose);

//Acquire pdf document
$pdf=$voucher->getPdf();

$pdf->Output('test_pdf.pdf', 'I');
//$pdf->Output($pdf_file, 'F');
//return $pdf_file;
 */
?>
