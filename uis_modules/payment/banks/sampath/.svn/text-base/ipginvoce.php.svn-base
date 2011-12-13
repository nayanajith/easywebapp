<?php
/*
Usage of IPGInvoice java class
usage: java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGInvoice <args>
 -am <arg>   Amount
 -kp <arg>   Key directory path
 -mi <arg>   Merchant id
 -ru <arg>   Return URL
 -ti <arg>   Transaction id

Eg;Through wrapper shell script
./IPGInvoice.sh -am 25000 -kp ipgkeys/ -mi m -ru 'http://ucsc.lk/pg' -ti t
*/
class IGPInvoice{

	/*Url of the bank gateway*/
	protected $request_url	="https://epay.sipg.lk/ipg/Servlet_HyDirectPAcc";

	/*Url to call back form the bank*/
	protected $callback_url	="";

	/*Directory to store key files*/
	protected $igpkey_dir	="";

	/*Id of the UCSC to the bank*/
	protected $merchent_id	="";

	public function __construct($request_url,$callback_url,$igpkey_dir,$merchent_id){
		$this->request_url	=$request_url;
		$this->callback_url	=$callback_url;
		$this->igpkey_dir		=$igpkey_dir;
		$this->merchent_id	=$merchent_id;
	}

	public function gne_invoice($amount,$transaction_id){
		$invoice="";
		if(
		$this->request_url 	!= "" AND
      $this->callback_url	!= "" AND
      $this->igpkey_dir		!= "" AND
      $this->merchent_id	!= "" AND
		$amount 					!= "" AND
		$transaction_id 		!= ""
		){
			/*Generate invoice*/
			//$cmd_string	= BANK_A_ROOT."/sampath/IPGInvoice.sh -am '$amount' -kp '".$this->igpkey_dir."' -mi '".$this->merchent_id."' -ru '".$this->callback_url."' -ti '$transaction_id'";
			$cmd_string = "cd ".BANK_A_ROOT."/sampath; /usr/bin/java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGInvoice -am '$amount' -kp '".$this->igpkey_dir."' -mi '".$this->merchent_id."' -ru '".$this->callback_url."' -ti '$transaction_id'";

			//echo $cmd_string;
			//log_msg('gen_invoice',$cmd_string);

			$invoice		=exec($cmd_string);
			$invoice		=trim($invoice," ");
			return $invoice;
		}else{
			return false;	
		}
	}

	/**
	@param ret_string: return string from the bank side (callback)
	@return <transaction_ID>:<transaction_status>
	*/
	public function get_status($receipt){
			$invoice="";
		if(
		$receipt 	!= "" AND
      $this->igpkey_dir	
		){
			/*Generate invoice*/
			//$cmd_string	=BANK_A_ROOT."/sampath/IPGReceipt.sh -k '".$this->igpkey_dir."' -r '$receipt' ";
			$cmd_string = "cd ".BANK_A_ROOT."/sampath; /usr/bin/java -cp lib/commons-cli-1.2.jar:lib/iclient.jar:lib/ibmjceprovider.jar:lib/ibmpkcs.jar:. IPGReceipt -k '".$this->igpkey_dir."' -r '$receipt' ";
			
			//echo $cmd_string;
			//log_msg('gen_status',$cmd_string);

			$status	=exec($cmd_string);
			$status	=trim($status," ");
			return $status;
		}else{
			return false;	
		}

	}

}

//Test invoice/Receipt
/*
include "../config.php";
$bank			="sampath";
//$igpinvoice = new IGPInvoice($banks[$bank]["gateway_url"],$banks[$bank]["callback_url"],$banks[$bank]["igpkey_dir"]."/",$banks[$bank]["merchent_id"]);
$igpinvoice = new IGPInvoice($banks[$bank]["gateway_url"],$banks[$bank]["callback_url"],$banks[$bank]["igpkey_dir"]."/",$banks[$bank]["merchent_id"]);
echo $igpinvoice->gne_invoice(1000,"TRID");
$test_status='-6081889498084647830509218519790688081376939564860156282710405245765453001061062991309905241969421099618395922707919365347569718396879637888285984937502713014222486914225450821038978455239014928698315344411771266010079103487117421075658955187458791145748119931931512425230859864512765972516025211992948001425017288184717601272963821510895557152238974503509088151450546759762452807170196517491488746210909346008780797683227909573650545936832623936195988078457870947877527982277681027606626297004432523997930031584577397373979107887222926192672956473968477504944926999069438961252916558133554157403775227400265244718652310600746186386424851382117323446374479769700650120994009089693890723424769608081867656910740893486213392569179694278211180667968478699173749277106050788529412781548701354545009840317884251808385596516467287707360365226405038308193822605728911874336805042537659547394089';
echo $igpinvoice->get_status("kkkkkkkkkkkkkkkk");
*/
?>
