<?php
if(!isset($_SESSION['username'])){
	echo "Please login to the system.";
	return;
}
?>
<h3>Paying Offline to the bank</h3>
<h4>Instructions</h4>
<h4>NOTE:</h4>
<p style='color:blue'>This method only available for local users</p>
<ol>
<li>Please download the <a href='?module=donations&page=offline_voucher&data=true'><b>PDF</b></a> file of the payment voucher.
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
Senior Assistant Registrar/Academic and Publications,
UCSC,
No: 35 Reid Avenue,
Colombo 07.
</pre>
</ol>

<br><br><br><div align='right' class='buttonBar'  >
<button dojoType='dijit.form.Button' onClick="open_page('donations','donation_to')">&laquo;&nbsp;Back</button>
</div>
