<?php
class Transaction{
/*Modulus to generate the check digit*/
protected $modulus=5;

/*composition of the examination no*/
/*
 1  2  3  4 
[~][~][8][1]
1. program code -> BIT
2. pay_for code -> REG
3. transaction no. -> 1,2,3,
4. date (dmY) -> 25032011 
5. check digit -> 1
*/

protected $trans_id_digits		=4;
protected $tp_ref_id_digits   =8;
protected $date_digits			=6;
protected $code_seperator		="";

/*generate the index numbers*/
function gen_composite_id($program_code,$pay_for_code,$tp_ref_id){
	//get next transaction for the day for the program
	$next_arr=exec_query("SELECT count(*)+1 next FROM ".$GLOBALS['MOD_P_TABLES']['payment']." WHERE DATE(init_time)=CURRENT_DATE()",Q_RET_ARRAY);
	$trans_id=$next_arr[0]['next'];

	/*current date in 25032011 format*/
	$date				=date("dmy");
	
	$composite_no='';

	/*Fill zeros for program_id*/
	/*
	for($j=$this->program_id_digits;$j>strlen($program_id);$j--){
		$composite_no.='0';
	}
	$composite_no.=$program_id;
	*/

	/*Fill zeros for transaction_id*/
	for($j=$this->trans_id_digits;$j>strlen($trans_id);$j--){
		$composite_no.='0';
	}
	$composite_no.=$trans_id;
	$composite_no.=$date;


	$check=0;
	foreach(str_split($composite_no) as $digit){
		$check+=(int)$digit;
	}
	$check=($check%$this->modulus);

	/*Complete transaction id*/
	//return $program_code.$this->code_seperator.$pay_for_code.$this->code_seperator.$tp_ref_id.$this->code_seperator.$composite_no.$check;
	return $program_code.$this->code_seperator.$pay_for_code.$this->code_seperator.$tp_ref_id.$this->code_seperator.$composite_no;
}

function break_composite_id($composite_no){
		//$composite_no_arr=explode($this->code_seperator,$composite_no);
		return array(
			'program_code'	=>substr($composite_no,0,1),	
			'pay_for_code'	=>substr($composite_no,1,1),	
			'trans_id'		=>substr($composite_no,2,$this->tp_ref_id_digits),
			'date'			=>substr($composite_no,($this->tp_ref_id_digits+2),$this->date_digits)
		);	
	
}

}
