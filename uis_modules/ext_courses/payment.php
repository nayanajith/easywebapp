<h3>Verify your details and do the payment</h3>
<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}
print_r($_SESSION);

echo "<br/><br/><br/><div align='right' class='buttonBar' >";
echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','available_courses')\">&laquo;&nbsp;Back</button>";
echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','pay_offline')\">Pay offline to bank</button>";
echo "<button dojoType='dijit.form.Button' type='submit' onClick=\"open_page('ext_courses','pay_online')\">Pay online&nbsp;&raquo;</button>";
echo "</div>";

?>
