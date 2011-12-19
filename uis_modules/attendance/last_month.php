<html>
<head>
</head>
<body>
<?php
include('attendance.php');
include('holidays.php');
$attendance =new Attendance('last');
$attendance->gen_holiday($holidays); //Append holidys array from holidays.php
$attendance->print_calendar();
$attendance->print_summery();
?>
<h3>Legend</h3>
<table>
	<tr>
		<td style='color: green'>D</td>
		<td>: Duration</td>
	</tr>
	<tr>
		<td style='color: green'>S</td>
		<td>: State</td>
	</tr>
	<tr>
		<td style='color: green'>I</td>
		<td>: In Time</td>
	</tr>
	<tr>
		<td style='color: green'>O</td>
		<td>: Out Time</td>
	</tr>
	<tr>
		<td colspan=2>
		<hr >
		</td>
	</tr>
	<tr>
		<td class=day>&nbsp;</td>
		<td>: Normal</td>
	</tr>
	<tr>
		<td class=today>&nbsp;</td>
		<td>: Today</td>
	</tr>
	<tr>
		<td class=holiday>&nbsp;</td>
		<td>: Holiday</td>
	</tr>
</table>

<h4>Note</h4>
<ul>
	<li>Duration is calculated from <u>First In</u> to <u>Last Out</u>. The
	result duration may change according to the intermediate In/out events.</li>
	<li>The days where duration is less than 8 hrs are denoted as Less Hour </li>
</ul>
</body>
</html>


