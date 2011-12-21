<html>
<head>
</head>
<title>Certificate Verification System</title>
<?php
include_once("style.php");
?>
<style type='text/css'>
.items a {
	text-decoration: none;
}
*{
fnot-size:12px;
}
</style>
<?php

/*
 * Print the array as a list (formatted acoording to the provided values)
 * $items: Array of items to be print in ($key => $value) format or ($value) format
 * $url  : Former URL where values of the array is to be appended
 * $name : Name of the list to be printed at the top of the list
 * $selection: The selecte item to be hihglighted
 * $usekey: If this value is set/true $key will be used in URLs in ($key => $value) arrays
 */

function print_items($items,$url,$name,$selection,$usekey){

	echo "<ul class=items style='margin-left:-10px;'>\n"; foreach($items as $key => $item ){

		if(!$usekey){
			$key=$item;
		}

		//Generate/validate variables
		$escape=array('/','(',')');
		$href ="$url&$name=$key";
		$id   =str_replace($escape,"_",$key."_".$name);

		if($key==$selection){
			echo "<li><a href='$href' id='$id' title='$key'><div class=selected_folder>$item</div></a></li>\n";
		}else{
			echo "<li><a href='$href' id='$id' title='$key'><div class=normal_folder>$item</div></a></li>\n";
		}
	}
	echo "</ul>";
}

$course=$_GET['course'];

$names=array(
   "BIT"=>"BIT Certificates",
   "EXT"=>"External Courses",
   "SPC"=>"Special Courses"
   );
   ?>
<div class='browser_box round shadow' id=browser_box
	style='padding:10px;'>
<center><img src='logo.jpg'></center>
<h3>Certificate Verification System</h3>
   <?php
   print_items($names,"?",'course',$course,true);
   echo "<br><hr style='border-color:#C9D7F1;'>";
   echo "<div style='margin-top:20px;' >";
   switch($course){
   	case 'BIT':
			include('bit.php');
   		break;
   	case 'EXT':
			include('ext.php');
   		break;
   	case 'SPC':
			include('spc.php');
   		break;
   	default:
   		echo "<center>";
   		echo "
   		<ul>
   		<li>point1</li>		
   		<li>point2</li>		
   		<li>point3</li>		
   		</ul>	
   		";
   		echo "</center>";
   		break;

   }
   echo "<br><center><span style='font-size:10px; color:silver;'>&copy; 2002-2010, All rights reserved by University of Colombo School of Computing</span></center>";
   echo "</div>";
   ?></div>
