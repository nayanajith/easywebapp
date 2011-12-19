<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}
?>
<h3>Paying Offline to the bank</h3>
<h4>Instructions</h4>
<ol>
<li>Please download the <a href='?module=ext_courses&page=offline_voucher&data=true'><b>PDF</b></a> file of the payment voucher.
<li>There are four copies as given below,
<ol type='I'>
<li>UCSC copy 1 ( Post this to us)
<li>Candidate copy (Keep this with you)
<li>Thimbirigasyaya bank copy(Bank will keep this)
<li>Bank copy ( Bank will keep this)
</ol>
<li>You need to sign on each voucher stating the date of payment and handover to any branch of Peoples Bank with the required payment.
<li>The UCSC copy must be sent to UCSC, and please note that Handing over the copy of voucher is compulsory to process your application.
<pre style='font:inherit'>
<b>Postal address:</b>
Coordinator,
Computing Services Centre,
University of Colombo School of Computing,
No. 35, Reid Avenue,
Colomobo 07.
<br>
<b>Fax number:</b> 0112587235
</pre>
</ol>

<br><br><br><div align='right' class='buttonBar'  >
<button dojoType='dijit.form.Button' onClick="open_page('ext_courses','payment')">&laquo;&nbsp;Back</button>
</div>
