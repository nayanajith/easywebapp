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
		/*Get program information*/

		/*Header information of the mark book*/
		$this->header_info['logo']				= A_IMG."/".$GLOBALS['LOGO'];
		$this->header_info['year']				="2010/2011";
	}

	/*Page header*/
	public function Header() {

		/*write images in pdf*/
		$this->Image(
			$file		=$this->header_info['logo'],
	 	  	$x			='30',
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
<h3>UNIVERSITY OF COLOMBO SCHOOL OF COMPUTING (UCSC)</h3>
BACHLOR OF INFORMATION & COMMUNICATION TECHNOLOGY (BICT)<br>					
APITITUDE TEST - ".$this->header_info['year'];

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
		//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}

	public function admission_config(){

		/*set document information*/ 
		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('University of Colombo School of Computing');
		$this->SetTitle('Selection tesst admission');
      $this->SetSubject('Year 2011');
		$this->SetKeywords('UCSC, BICT');

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
		$PDF_MARGIN_LEFT			=15;
		$PDF_MARGIN_TOP			=25; 
		$PDF_MARGIN_RIGHT			=10;
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
		$this->SetFont('helvetica', 'B', 20);
		
		/* add a page*/
		/*
		$this->AddPage();
		$this->Write(0, '', '', 0, 'C', true, 0, false, false, 0);
		$this->SetFont('helvetica', '', 7);
		*/
	}
}


class Attendance_sheets{

	/*date of the examination*/
	protected $date		='25-04-2010';

	/*duration and time of the examination*/
	protected $duration	='10.00am-12.00noon';

	/*admission issued date by the AR/Exam*/
	protected $pdf;

	public function __construct($date,$duration){

		$this->date			=$date;
		$this->duration	=$duration;

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

	public function getPdf(){
		return $this->pdf;	
	}


	public function generate_attendance_sheets($res){
	/*
		$query="SELECT * FROM ".$GLOBALS['P_TABLES']['exam_hall']." WHERE center_id!=1";
		$res=exec_query($query,Q_RET_MYSQL_RES);
	*/
		while($row=mysql_fetch_assoc($res)){
      	$center_id	=$row['center_id'];
			$hall_id		=$row['hall_id'];
      	$num_rooms	=$row['no_of_rooms'];

			$hall			=$row['hall'];
			$room_no		=1;
			$center_addr=$row['center_address'];
			$center		=$row['center'];
			$subject		="Bachelor of Information and Communication Technology (BICT)";

			/*Maximum rows per page*/
			$max_rows	=30;

			for($room_no=1;$room_no <= $num_rooms; $room_no++){
				/*Variable to store Attendance sheet html*/
				$sheet_style="
<style type="text/css">
th{
	background-color:silver;
	font-weight:bold;
	text-align:center;
}


</style>";

      		$sheet_head='
<table style="font-size:11pt;">
<tr><td><b>Center:</b> '.$center.'</td><td align="right"><b>Date:</b> '.$this->date.'</td></tr>
<tr><td><b>Hall:</b> '.$hall.'</td><td align="right"><b>Time:</b> '.$this->duration.'</td></tr>
<tr><td><b>Room:</b> '.$room_no.'</td><td align="right"></td></tr>
<tr><td align="center" colspan="2"><h3>ATTENDANCE LIST</h3></td></tr>
</table>';

				$sheet_note='
<div style="text-align:justify;font-size:9pt">
Supervisors are kindly requested to mark absentees clearly <b>-absent-</b> & those present. One copy is to be returned					
under the separate cover to the Assistant Register (Examination) , & one to be enclosed in relevant packet of answer					
scripts. When answer scripts are packeted separately for each part of paper, it is necessary to enclose a copy of the					
attendance list in each packet.
</div>
<br>';

				$sheet_table='<table border="1" style="border-collapse:collapse" cellpadding="2">';
				$sheet_thead='<tr><th width="40px">Serial</th><th width="65px">Index No</th><th width="340px">Name</th><th width="75px">ID Number</th><th width="120px">Signature</th></tr>';

				$sheet_footer='
<style type="text/css">
.fill{
	border-bottom:1px dashed black;
	width:155px;
	height:30px;
}
</style>
<table >
<tr><td align="right" width="140px;">&nbsp;<br><b>Date:</b></td><td class="fill" ></td><td width="140px;"></td><td></td></tr>
<tr><td align="right">&nbsp;<br><b>Signature of Invigilator:</b></td><td class="fill" ></td><td align="right">&nbsp;<br><b>Signature of  Supervisor:</b></td><td class="fill" ></td></tr>
</table>';

				
				$query="SELECT * FROM ".$GLOBALS['P_TABLES']['student_alloc']." s, ".$GLOBALS['P_TABLES']['user_info']." u WHERE s.center_id='$center_id' AND s.hall_id = '$hall_id' AND s.room_no='$room_no' AND u.index_no=s.index_no ORDER BY exam_no";
				$stu_res=exec_query($query,Q_RET_MYSQL_RES);

				/*If the number of student for the given room is zero do not generate attendance sheets*/
				$students_room=get_num_rows();
				if($students_room==0){
					continue;	
				}
				
				$serial=1;

      		while($stu_row=mysql_fetch_assoc($stu_res)){
					/*this is how page starts*/
					/*Style, sub_head, notes, table, and table headers will be added to the html for each $max_rows no of lines */
					if($serial == 1 || ($serial-1)%($max_rows) == 0){
						$SHEET=$sheet_style;
						$SHEET.=$sheet_head;
						$SHEET.=$sheet_note;
						$SHEET.=$sheet_table;
						$SHEET.=$sheet_thead;
					}

					/*fill the rows of the table with the students details*/
					$SHEET.='<tr>';
					$SHEET.='<td align="center">'.$serial.'</td>';	
					$SHEET.='<td align="center">'.$stu_row['exam_no'].'</td>';	
					$SHEET.='<td>'.strtoupper($stu_row['surname']).'</td>';	
					$SHEET.='<td align="left">'.$stu_row['nic'].'</td>';	
      			$SHEET.='<td></td>';	
					$SHEET.="</tr>";

					/*this is how page breaks*/
					/*Pdf will be written if the $serial == total no of students in the room or $serial = $max_rows*/
					if(($serial != 1 && $serial%$max_rows == 0) || $serial == $students_room){
						$SHEET.="</table>";
						//echo $SHEET;

						/*Add a page to the sheet*/
						$this->pdf->AddPage();
						$this->pdf->Write(0, '', '', 0, 'C', true, 0, false, false, 0);

						$this->pdf->SetFont('helvetica', 'B', 9);
						/*write table to the sheet*/
						$this->pdf->writeHTML($SHEET, true, false, false, false, 'L');

						$this->pdf->SetFont('helvetica', 'B', 9);
						$this->pdf->writeHTML($sheet_footer, true, false, false, false, 'L');
					}

					/*Increment serial for the room*/
					$serial++;
      		}
			}
		}
	}
}
?>
