<?php
/*
 * Copyright (C) 2011 Nayanajit Mahendra Laxaman mail: nml@ucsc.cmb.ac.lk
 * Payment gateway <---> third party message encription class
*/
class Message_crypt{
	//to stor symmetric key for the encryption/decrypteion
	protected $key		='';
	protected $openssl='';

	function __construct($key){
		//set key for the encryption/decryption
		$this->key = $key;

		//find openssl binary path
		//For windows install openssl and set the path to the binary
		//$this->openssl="C:/path/to/openssl.exe";
		$this->openssl=exec('which openssl');
	}

	//Encrypt the string using
	function encrypt($string){
		$tmpfname	= tempnam("/tmp", "en_");
		$handle		= fopen($tmpfname, "w");
		fwrite($handle,$string);
		fclose($handle);
   	$encrypted_string=shell_exec($this->openssl." des3 -e -salt -k '".$this->key."' -base64 -in '".$tmpfname."' | tr -d '\r\n'");
		unlink($tmpfname);
		return $encrypted_string;
	}
	
	//Decrypt the string using
	function decrypt($string){
		$tmpfname	= tempnam("/tmp", "de_");
		$handle		= fopen($tmpfname, "w");
		fwrite($handle, base64_decode($string));
		fclose($handle);
		$decrypted_string=shell_exec($this->openssl." des3 -d -salt -k '".$this->key."' -in '".$tmpfname."'");
		unlink($tmpfname);
		return $decrypted_string;
	}
	
	//Return encrypted json to be sent back to the third party
	function genReceipt($tp_ref_no,$tr_ref_no,$tax,$status){
		return $this->encrypt('{"tp_ref_no":"'.$tp_ref_no.'","tr_ref_no":"'.$tr_ref_no.'","tax":"'.$tax.'","status":"'.$status.'"}');
	}
	
	//decrypt the request and get the json, decode json to arra and return
	function getRequest($encrypted_request){
		$decrypted_request=$this->decrypt($encrypted_request);
		if($decrypted_request){
			return json_decode($decrypted_request,true);
		}else{
			return false;
		}
	}

	//decrypt the receipt and get the json ,decode the jason and return
	function getReceipt($encrypted_receipt){
		$decrypted_receipt=$this->decrypt($encrypted_receipt);
		if($decrypted_receipt){
			return json_decode($decrypted_receipt,true);
		}else{
			return false;
		}
	}

	//Generate encrypted json request to be sent to the payment gateway
	function genRquest(
		$tp_ref_id,
		$pay_for,
		$amount,
		$nic,
      $email,
		$full_name
	){
		$request_arr=array(
			"tp_ref_id"	=>$tp_ref_id,
			"pay_for"	=>$pay_for,
         "amount"		=>$amount,
			"nic"			=>$nic,
			"email"		=>$email,
			"full_name"	=>$full_name
		);
		$request_json=json_encode($request_arr);
		return $this->encrypt($request_json);
	}

	//Generate a random password to be used for the third party communication
	function genKey(){
		return shell_exec("openssl rand -base64 32");
	}
}
