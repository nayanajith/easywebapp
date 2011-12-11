<?php
if((isset($_SESSION['user_id']) && $_SESSION['user_id'] == "") || isset($_SESSION['user_id']) == false ){

echo "Please login to the system <a href='javascript:open_page(\"courses\",\"login\")'>HERE</a>";
}else{


      if($GLOBALS['LAYOUT'] == 'pub'){
      if((isset($_REQUEST['reg_id']) && $_REQUEST['reg_id'] == "") || isset($_REQUEST['reg_id']) == false ){


	      echo "<h1>Confirmation form</h1>";
         echo "<hr style='border:1px solid silver;'/>";
         echo '<table> <tr><td width = 70% valign = "top" >';
	      echo "<table width=100%><tr><td style='vertical-align:top;valign:top' width=100%>";
	      echo "Please select a payment method and click confirm if u wish to attend this session. If u do not confirm your attendance a place cannot be reserved for you. However, you may apply at a later date given that there are still places available";
	      echo "<hr style='border:1px solid silver;'/>";
	   }else{
	      echo "<h1>Payment Selection</h1>";
         echo "<hr style='border:1px solid silver;'/>";
         echo '<table> <tr><td width = 70% valign = "top" >';
	      echo "<table width=100%><tr><td style='vertical-align:top;valign:top' width=100%>";
	      echo "Please select a payment method and click Make payment to proceed with your payment.";
	      echo "<hr style='border:1px solid silver;'/>";
	   }
	   	   
	   if(isset($_REQUEST['sid'])){
	      $sid = $_REQUEST['sid'];	   
	   }
	   
	   if(isset($_REQUEST['reg_id'])){
	   $table = $GLOBALS['MOD_P_TABLES']["reg"];
      $query = "SELECT * FROM ".$table." WHERE reg_id = '". $_REQUEST['reg_id']."'" ;
      $res = exec_query($query,Q_RET_MYSQL_RES);
      $row = mysql_fetch_array($res);
      $sid = $row['session_id']; 
	   }
	  
	   
	   $table = $GLOBALS['MOD_P_TABLES']["schedule"];
      $query = "SELECT * FROM ".$table." WHERE session_id = '". $sid."'" ;
      $res = exec_query($query,Q_RET_MYSQL_RES);
      $row = mysql_fetch_array($res);


	   $table2 = $GLOBALS['MOD_P_TABLES']["course"];
      $query2 = "SELECT * FROM ".$table2." WHERE course_id = '". $row['course_id']."'" ;
      $res2 = exec_query($query2,Q_RET_MYSQL_RES);
      $row2 = mysql_fetch_array($res2);


      echo '<p>Course Name: '.$row2['long_name'].' ('.$row2['short_name'].')</p>';
      echo '<p>Conducted by: '.$row2['lecturer'].'</p>';
      echo '<p> Session name: '.$row['session_name'].'</p>';
      echo '<p>Course Fee: '.$row2['course_fee'].'</p>';
      echo '<p> Held from '.$row['start_date'].' to '.$row["end_date"] .'</p>';      
      echo "<hr style='border:1px solid silver;'/>";
      	   
		$_SESSION['sid'] =  $sid;
	//	echo $formgen->gen_form(false,false);
	}else{
	//	echo $formgen->gen_form(true,true);
	//	echo $formgen->gen_filter();
	}
	echo "
		<script language='javascript'>
			function grid(){
				url='".gen_url()."&form=grid';
				open(url,'_self');
			}
		</script>
	";
	//$json_url=$formgen->gen_json($grid_array,$filter_string,false,null);
if($GLOBALS['LAYOUT'] != 'pub'){
	echo "</td><td width=40% style='vertical-align:top;valign:top'>";
	//echo $formgen->gen_data_grid($grid_array,null,$key1);
	echo "</td></tr></table>";
}else{
		echo "</td><td width=40% style='vertical-align:top;valign:top;'>";
		
		echo "</td></tr></table>";


		echo "<div align='right' >";

   echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "'.MODULE.'"/>
<input type = "hidden" name="page" value = "payment"/>
<input type = "hidden" name="paymeth" value = "ONLINE"/>'.'<button style = "align:right;font-size:12px;font-style:normal" dojoType="dijit.form.Button" type="submit" >Pay Online</button>
</form>';

   echo '<form action= ""  method="get">
<input type = "hidden" name="module" value = "'.MODULE.'"/>
<input type = "hidden" name="page" value = "payment"/>
<input type = "hidden" name="paymeth" value = "OFFLINE"/>'.'<button style = "align:right;font-size:12px;font-style:normal" dojoType="dijit.form.Button" type="submit" >Pay Offline</button>
</form>';

	echo	"</div>";



	
echo "</td><td valign = 'top'  style = 'border-left:1px solid silver'>";
echo '<h4>Course Application procedure</h4>';
echo "<ol>
		<li>Find a course that you are interested in completing from the <a href='javascript:open_page(\"courses\",\"courses\")'>Find Courses</a> Page</li>
		<li>Once you have found such a course click on the Apply button next to it to go to the course page</li>
		<li>In the course page, Apply for a session which you are able to attend. The available sessions are displayed at the bottom of that page</li>
		<li>Then you must confirm your attendance by selecting a payment method. Note that unless you confirm this, your place will not be reserved</li>
		<li>Once you have confirmed, you are then able to pay either online or offline</li>
		<li>If you pay online, your place will be confirmed as soon as the payment goes through</li>
		<li>If you pay offline, your place will be confirmed when the payment has been recieved. Until such time, the status of your application will be set to 'PENDING' </li>
			</ol>";
echo "</td></tr></table>";

}





}
?>
