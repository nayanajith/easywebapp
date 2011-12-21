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

	public function voucher_config(){

		/*set document information*/ 
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('University of Colombo School of Computing');
		$this->SetTitle('Selection tesst admission');
		$this->SetSubject('Year 2011');
		$this->SetKeywords('UCSC');

		/*set default header data*/ 
		//$PDF_HEADER_LOGO			=$this->header_info['logo'];
		//$PDF_HEADER_LOGO_WIDTH=20;
		$PDF_HEADER_TITLE			='Markbook\naa';
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
		$PDF_MARGIN_LEFT			=5;
		$PDF_MARGIN_TOP			=5; 
		$PDF_MARGIN_RIGHT			=5;
		$this->SetMargins($PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, $PDF_MARGIN_RIGHT);
		
		$PDF_MARGIN_HEADER		=5;
		$PDF_MARGIN_FOOTER		=5;
		$this->SetHeaderMargin($PDF_MARGIN_HEADER);
		$this->SetFooterMargin($PDF_MARGIN_FOOTER);
		
		/*set auto page breaks*/
		$PDF_MARGIN_BOTTOM		=5;
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


class Voucher{

	/*date of the examination*/
	protected $date		='25-04-2010';

	/*admission issued date by the AR/Exam*/
	protected $pdf;

	//payer info array
	protected $payer_info;
	/*eg:
	$payment_info=array(
		"RS 2000.00",
		"SRI LANKAN RUPEES TOW THOUSAND ONLY",
		"Nayanajith mahendra laxaman",
		"08002002",
		"812940201v"
	);
	 */

	//Account number of the payee (eg: 086-1001-211-90316)
	protected $acc_no;

	//Title of the voucher (eg:BACHELOR OF INFORMATION AND COMMUNICATION TECHNOLOGY )
	protected $vou_title;

   //Purpose of the payment (eg: CONVOCATION REGISTRATION FEE)
	protected $purpose;

	/*
	 * @param page_format		: A4,A5,B4
	 * @param page_orientation	: P,L 
	 */
	public function __construct($payer_info,$acc_no,$vou_title,$purpose){

		$this->payer_info			=$payer_info;
		$this->acc_no				=$acc_no;
		$this->vou_title			=$vou_title;
		$this->purpose				=$purpose;
		$this->date					=date('d-m-Y');

		/*Pdf generator page setup*/
		$PDF_PAGE_ORIENTATION	='P';//(L,P)
		$PDF_PAGE_FORMAT			='A4';
		$PDF_UNIT					='mm';//mm,in,pt,cm
		$UNICODE						=true; 
		$ENCODING					='UTF-8'; 
		$DISKCACHE					=true;//if TRUE reduce the RAM memory

		/*return pdf generation object*/
		$this->pdf = new MYPDF($PDF_PAGE_ORIENTATION, $PDF_UNIT, $PDF_PAGE_FORMAT, $UNICODE, $ENCODING, $DISKCACHE);
		$this->pdf->voucher_config();
		$this->gen_voucher_quadruples();
	}

	//Return the pdf file
	public function getPdf(){
		return $this->pdf;	
	}


	//Generate quadruples of vouchers using the information feeded in constructor
	public function gen_voucher_quadruples(){
		$student_info=<<<EOD
		<style type='text/css'>
			.writing{
				font-family:times;
				font-style:italic;
				font-size:34px;
			}
			.fill_space{
				border-bottom:1px dotted black;
			}
			*{
				font-size:27px;
			}
		</style>
		<table border="0" cellpadding="5" style="border:1px solid black;">
			<tr>
				<td style="font-weight:bold" width="205">
					CASH-PAYING-IN-SLIP<br>
					(To be filled in quadruplicate)<br>
					%s
				</td>
				<td width="110">
					&nbsp;
				</td>
				<td colspan="2" style="font-weight:bold;font-size:32px;" width="400">
					%s<br>
					UNIVERSITY OF COLOMBO SCHOOL OF COMPUTING (UCSC)
				</td>
			</tr>	
			<tr>
				<td>
					<table style="border-collapse:collapse;" border="1" cellpadding="2" width="70%%">
						<tr><td rowspan="3" width="50">DEPOSIT<br>DATE</td><td>D</td><td>D</td><td>M</td><td>M</td><td>Y</td><td>Y</td><td>Y</td><td>Y</td></tr>
						<tr><td height="20">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>2</td><td>0</td><td>1</td><td>1</td></tr>
					</table>
				</td>
				<td colspan="3">
					<table style="border-collapse:collapse;" border="1" cellpadding="2" width="100%%">
						<tr><td colspan="2" align="center">Paid at People's Bank</td></tr>
						<tr><td height="20">Branch</td><td>Branch Code</td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="right">
					PURPOSE
				</td>	
				<td colspan="3" class="writing" >
					%s	
				</td>	
			</tr>
			<tr>
				<td align="right">
					PAID IN CREDIT OF
				</td>	
				<td colspan="3" class="writing">
					PEOPLES BANK,THIMBIRIGASYAYA - A/C No. <span style="font-weight:bold;font-size:38px;font-style:regular">%s</span>
				</td>	
			</tr>
			<tr>
				<td align="right">
					AMOUNT PAID
				</td>	
				<td colspan="3" class="writing">
					%s
				</td>	
			</tr>
			<tr>
				<td align="right">
					AMOUNT IN WORDS
				</td>	
				<td colspan="3" class="writing">
					%s
				</td>	
			</tr>
			<tr>
				<td colspan="1" align="center">
					<table style="border:1px solid black;" cellspacing="4" cellpadding="4" width="195">
						<tr><td class="fill_space">&nbsp;</td></tr>
						<tr><td align="center">CASH DEPOSITOR'S SIGNATURE</td></tr>
						<tr><td class="fill_space">&nbsp;</td></tr>
						<tr><td align="center">CASHIER'S SIGNATURE</td></tr>
					</table>
				</td>
				<td colspan="3" >
					<b>Instruction to Bank</b><br>
					Please do not accept unless the cage below is filled<br>
					<table style="border:1px solid black;" cellspacing="4" cellpadding="3" width="520">
						<tr><td width="125">APPLICANT'S FULL NAME</td><td width="360" class="writing fill_space">%s</td></tr>
						<tr><td>APPLICANT'S INDEX NO</td><td class="writing fill_space">%s</td></tr>
						<tr><td>APPLICANT'S NIC NO</td><td  class="writing fill_space">%s</td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="4" style="border-bottom:1px solid black;">
					DO NOT WRITE ANYTHING BELOW THIS LINE
				</td>
			</tr>
			<tr>
				<td height="100" colspan="4">
				</td>
			</tr>
		</table>
EOD;

		$quadruplicate=array(
			"UCSC COPY - 1",
			"CANDIDATES COPY - 2",
			"THIMBIRIGASYAYA BANK COPY - 3",
			"PAYING BANK COPY - 4"
		);

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
         'fontsize' => 8,
         'stretchtext' => 4
		);

		//replace ' with " which does not support tcpdf
		//$content=str_replace("'","\"",$content);

		//voucher position
		$x=5;
		$y=5;
		$vou_count=0;
		//Generate quadraples 
		foreach($quadruplicate as $quadruple){
			/*
			$s_1="UCSC COPY - 1";
			$s_2="BACHELOR OF INFORMATION AND COMMUNICATION TECHNOLOGY";
         $s_3="CONVOCATION REGISTRATION FEE";
         $s_4="086-1001-211-90316";
			$s_5="RS 2000.00";
			$s_6="SRI LANKAN RUPEES TOW THOUSAND ONLY";
			$s_7="Nayanajith mahendra laxaman";
         $s_8="08002002";
			$s_9="812940201v";
			*/

			$content=sprintf(
				$student_info,
				$quadruple,
            $this->vou_title,
            $this->purpose,
            $this->acc_no,
				$this->payer_info[0],
				$this->payer_info[1],
            $this->payer_info[2],
				$this->payer_info[3],
				$this->payer_info[4]
			);

			//Add a page to the sheet
			$this->pdf->SetFont('helvetica', '', 9);

			/*write table to the sheet*/
			//writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
			$this->pdf->setXY($x,$y);
			$this->pdf->writeHTML($content, true, false, false, false, 'L');

			//PRINT 1D BARCODES
			//write1DBarcode($code, $type, $x='', $y='', $w='', $h='', $xres='', $style='', $align='')
			$this->pdf->setXY($x+50,$y);
			$this->pdf->write1DBarcode($this->payer_info[3], 'I25', '', '', '80', 15, 0.4, $style, 'N');
			$y+=130;

			if($vou_count==1){
				$this->pdf->AddPage();
				$y=5;
			}

			$vou_count++;
		}
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
	
//__construct($payer_info,$acc_no,$vou_title)
$voucher=new Voucher($payment_info,$acc_no,$vou_title,$purpose);

//Acquire pdf document
$pdf=$voucher->getPdf();

$pdf->Output('test_pdf.pdf', 'I');
//$pdf->Output($pdf_file, 'F');
//return $pdf_file;
 */
?>
