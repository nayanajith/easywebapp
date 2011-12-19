<?php
   		echo "<p><span style='color:red'>Note:</span>This verification only carries details of Certificates/Diploma/Higher Diploma from year 2002 onwards</p>";

   		echo "<dl>
	<dt><b>DIT</b></dt>
	<dd><u>Diploma in Information Technology</u> is a one year programme consisting of 8 courses (32 credits and 480 hours).</dd>
   <dt><b>HDIT</b></dt>
	<dd><u>Higher Diploma in Information Technology</u>  is a two year programme consisting of minimum of 16 courses (64 credits and 960 hours).</dd>
   <!--dt><b>BIT</b></dt>
	<dd><u>Degree in Information Technology</u>  is three year programme consisting of minimum of 21 courses and individual Project (96 credits and 1560 hours).</dd-->
</dl><center> ";	
   		echo "<br><fieldset class='round' style='width:300px;'><legend>DIT/HDIT Verification Form</legend>";
   		echo "<table><tr><td style='text-align:right'>Select Category</td><td><select name='category'>
	<option value='0'selected>-select-</option>
	<option value='1'>DIT</option>
	<option value='2'>HDIT</option>
</select></td></tr>";
   		echo "<tr><td style='text-align:right'>Index No</td><td><input type=text name=indexno size=6></td></tr>";
   		echo "<tr><td style='text-align:right'>Certificate No</td><td><input type=text name=certno size=6></td></tr>";
   		echo "<tr><td colspan=2 style='text-align:center' ><input type=submit value='Verify Certificate' name=verify></td></tr>";
   		echo "</table>";
   		echo "</fieldset></center>";

?>
