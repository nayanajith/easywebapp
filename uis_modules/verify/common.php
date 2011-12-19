<?php
/*
 * Print debug messages
 */
function debug($msg,$id,$color){
	if(DEBUG){
		echo "<span style='color:".$color."'>[".$id."]</span>".$msg."<br>";
	}
}

/*
 * Database connection and disconnection
 */
function openDB() {
	$GLOBALS['CONNECTION'] = mysql_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS']);
	mysql_select_DB($GLOBALS['DB'], $GLOBALS['CONNECTION']);
}

function closeDB() {
	mysql_close($GLOBALS['CONNECTION']);
}

/*
 * connect ta custom database
 *
 */

function openDB2($DB) {
	$GLOBALS['CONNECTION'] = mysql_connect($GLOBALS['DB_HOST'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASS']);
	mysql_select_DB($DB, $GLOBALS['CONNECTION']);
}




/*
 * Convert number to text for 1,2,3
 */
function num_to_text($num){
	$text="";
	switch ($num){
		case 1:
			$text="First";
			break;
		case 2:
			$text="Second";
			break;
		case 3:
			$text="Third";
			break;
	}
	return $text;
}


/*
 * Extract Examination id and return ac year ex year and semester
 */
function exam_detail($eid){
	return array(
		"semester"=>num_to_text(substr($eid, -1, 1)),
		"ac_year" =>num_to_text(substr($eid, -2, 1)),
		"ex_year" =>2000+(int)substr($eid, 0, -2)
	);
}



/*
 * XML marks and detail file paths
 */
function xml_marks(){
	return TMP.$_SESSION['username'].$_SESSION['password'].$GLOBALS['xml_marks'];
}
function xml_detail(){
	return TMP.$_SESSION['username'].$_SESSION['password'].$GLOBALS['xml_detail'];
}

//Descriptive degree array
$descDegree=array(
	"Bachelor of Computer Science with Honours",				//0
	"Bachelor of Computer Science",								//1
	"Bachelor of Science (Computer Science) with Honours",//2
	"Bachelor of Science (Computer Science)",					//3
	"Not Completed"													//4
);

//Descriptive Class array
$descClass=array(
	"First Class(Honours)", 			//0
	"First Class",							//1
	"Second Class(Upper Division)",  //2
	"Second Class(Lower Division)",  //3
	"Pass",									//4
	"Pending"								//5
);

/*
 * Mapping of grades and gpv
 */
$gradeExp = array(
"AB"=>0.00,"NC"=>0.00,
"NR"=>0.00,"MC"=>0.00,"NA"=>0.00
);

$gradeGpv = array(
   "A+"=>4.25,"A"=>4.00,"A-"=>3.75,
   "B+"=>3.25,"B"=>3.00,"B-"=>2.75,
   "C+"=>2.25,"C"=>2.00,"C-"=>1.75,
   "D+"=>1.25,"D"=>1.00,"D-"=>0.75,
   "E"=>0.00,"AB"=>0.00,"NC"=>0.00,
   "NR"=>0.00,"MC"=>0.00,"NA"=>0.00,
	"0"=>0.00
);

function getGradeGpv($grade){
	global $gradeGpv;
	if(!empty($gradeGpv[strtoupper(trim($grade))])){
		return $gradeGpv[strtoupper(trim($grade))];
	}else{
		return 0;
	}
}

/*
 * Array to hold minimum marks to obtain a particular Grade
 */
$minGradeMark=array(
'D-'=>20,'D'=>30,'D+'=>40,
'C-'=>45,'C'=>50,'C+'=>55,
'B-'=>60,'B'=>65,'B+'=>70,
'A-'=>75,'A'=>80,'A+'=>90
);

function getMinMarkC($grade){
	global $minMark;
	return $minMark[stroupper(trim($grade))];
}


/*
 * Return grade for mark
 */
function getGradeC($Mark){
	$Grade = "NN";
	if (strtoupper($Mark) == 'AB') {
		$Grade = "AB";
	}elseif ($Mark < 20) {
		$Grade = "E";
	} elseif ($Mark < 30) {
		$Grade = "D-";
	} elseif ($Mark < 40) {
		$Grade = "D";
	}elseif ($Mark < 45) {
		$Grade = "D+";
	}elseif ($Mark < 50) {
		$Grade = "C-";
	}elseif ($Mark < 55) {
		$Grade = "C";
	}elseif ($Mark < 60) {
		$Grade = "C+";
	}elseif ($Mark < 65) {
		$Grade = "B-";
	}elseif ($Mark < 70) {
		$Grade = "B";
	}elseif ($Mark < 75) {
		$Grade = "B+";
	}elseif ($Mark < 80) {
		$Grade = "A-";
	}elseif ($Mark < 90) {
		$Grade = "A";
	}elseif ($Mark < 101) {
		$Grade = "A+";
	}
	return $Grade;
}

/*Course excpetions*/
$courseNoneGrade	= array('ENH1001','ENH1002');
$courseNoneCredit	= array('SCS3026','ICT3015','ICT3016');


/*
 * Check for none grade courses
 */
function isNoneGrade($courseid){
	global $courseNoneGrade;
	if(in_array($courseid, $courseNoneGrade)){
		return true;
	}else{
		return false;
	}
}

/*
 * Check for none credit courses
 */
function isNoneCredit($courseid){
	global $courseNoneCredit;
	if(in_array($courseid, $courseNoneCredit)){
		return true;
	}else{
		return false;
	}
}


/*
 * Return year of the course
 *
 */
function courseYear($courseid){
	$query="SELECT syear FROM courses WHERE courseid='$courseid'";
	$result  = mysql_query($query, $GLOBALS['CONNECTION']);
	$row = mysql_fetch_array($result);
	return $row['syear'];
}

/*
 * Return Credits of the given course unit
 */
function getCredits($courseid){
	$query   ="
		SELECT credits_L,credits_p 
		FROM courses 
		WHERE courseid='$courseid'
		";
	$result  = mysql_query($query, $GLOBALS['CONNECTION']);
	$row = mysql_fetch_array($result);

	if(!isNoneGrade($courseid) && !isNoneCredit($courseid)){
		return $row['credits_L']+$row['credits_p'];
	}else{
		return 0;
	}
}

function getCourseName($courseid){
	$query   ="
		SELECT DISTINCT CourseName 
		FROM courses 
		WHERE courseid='$courseid'
		";
	$result  = mysql_query($query, $GLOBALS['CONNECTION']);
	$row = mysql_fetch_array($result);
	return $row['CourseName'];
}

/*
 * Return array of exam ids for a given batch
 */
function get_examids($batch){

	//grab last tow chars of the batch
	$reg    =substr($batch,-2,2);

	//special case: 2002/2003(A)
	if(!is_numeric($reg)){
		$reg  = "3";
	}else{
		$reg  = (int)substr($batch,-2,2);
	}
}

/*
 * Return array of exam ids for a given batch
 */

function getExamYear($examid){
	//grab first tow chars of the batch
	$reg    =substr($examid,0,2);
	return "20".$reg;
}

/*
 * Execute queries through this function to grab total handl over queris
 */
function execQuery($query,$connection){
	$result  = mysql_query($query, $connection);
	$row = mysql_fetch_array($result);

	echo "<blink>".sizeof($row)."</blink>";
	return $result;
}

/*
 * Verify the course according to the index no
 */
function isCS($indexno){
	preg_match('/\d+020\d+/', $indexno, $matches);
	if(empty($matches[0])){
		return true;
	}else{
		return false;
	}
}


/*
 * Detect Internet Explorer
 */
function is_msie() {
	$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if (strstr($user_agent, 'MSIE') != false) {
		return true;
	}
	return false;
}

/*
 * Detect crome browser
 */
function is_chrome() {
	$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if (strstr($user_agent, 'Chrome') != false) {
		return true;
	}
	return false;
}

/*
 * Detect opera browser
 */
function is_opera() {
	$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	if (strstr($user_agent, 'Opera') != false) {
		return true;
	}
	return false;
}



/*
 * Drow information box with given information
 */
function drow_box($content, $title, $color, $width) {

	if ($width == 0)
	$width = "";
	elseif ($width == '%')
	$width = "width:100%";
	else
	$width = "width:".$width."px";

	$height = null;
	if (is_msie()) {
		$height = 'height:100px;';
	}
	echo "<div class='round' style='border:1px solid silver;min-width:50px; ".$width."; ".$height.";position:relative;background-color:".$color.";z-index:1'>";
	if ($title) {
		echo "<div class=menutitlebar style='background:#C9D7F1;'>".$title."</div>";
	}
	echo "<div style='padding:7px;color:gray'>";
	echo "$content";
	echo "</div>";
	echo "</div>";

}

/*
 * hover for msie
 */
function msie_hover($ht, $hb, $nt, $nb, $eid) {
	if (isMsie()) {
		return "id=".$eid." style='color:".$nt.";background-color:".$nb.";' onmouseover='".$eid.".style.color=\"".$ht."\"; ".$eid.".style.backgroundColor=\"".$hb."\"' onmouseout='".$eid.".style.color=\"".$nt."\"; ".$eid.".style.backgroundColor=\"".$nb."\"'";
	}
}

/*
 * Style table names of database
 */
function style_text($ROW_TEXT) {
	return str_replace("_", " ", ucfirst($ROW_TEXT));
}



/*
 * Print Header of the reports
 */
function print_header($title){
	echo "
<body style='background:silver;'>
<div align=center class=a4stat >
<br><table>
<tr><td align=right><img src='".$GLOBALS['logo']."' height=60 ></td>
<td><h3>".TITLE_LONG."</h3></td></tr>
<tr><td colspan=2 align=center><h4>$title</h4></td></tr></table><hr>";	
}

/*
 * Print Footer of the reports
 */
function print_footer(){
	echo "<hr><h4>".date('D jS \of F Y')."</h4></div></body></html>";
}

?>
