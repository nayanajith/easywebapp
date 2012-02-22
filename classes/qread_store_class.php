<?php

class Query_read_store{

   protected $filter    = "";
   protected $pre_filter= "";
   protected $start      = 1;
   protected $count      = 10;
   protected $max_count  = 50;
   protected $key        = "IndexNo";
   protected $table      = "csstudent";
   protected $order_by   = null;
 
   /*
   Constructor of the query read store
   */
   public function __construct($table,$key,$filter='',$order_by=null,$id=null) {
      $this->key        =$key;   
      $this->table      =$table;
      $this->pre_filter =$filter;
      $this->order_by   =$order_by;
      $this->id         =$id;   
      if(is_array($key)){
         $this->key_a        =$key;   
         if(key($key) === 0){ //distinguish associative arrays
            $this->key =$key[0];  //hidden field of the select box 
         }else{
            $this->key =key($key);  //hidden field of the select box 
            if(is_array($key[key($key)])){
               $this->id  =',CONCAT('.implode('," > ",',$key[key($key)]).") label";   //displaying field of the select box
            }else{
               $this->id  =','.$key[key($key)].' label';   //displaying field of the select box
            }
         }
      }
      
   }
      
   /*
   Generating filtering query according to the $_REQUEST
   $_REQUEST(
      'q'=>,
      'start'=>,
      'count'=>,
   );
   */
   public function _gen_query(){
      if (array_key_exists($this->key, $_REQUEST)) {
         $this->filter = $_REQUEST[$this->key];
         $this->filter = str_replace("*", "%", $this->filter);
         $this->filter="WHERE $this->key LIKE '".$this->filter."' ".$this->pre_filter;
      }else{
         $this->filter="WHERE $this->key LIKE '%' ".$this->pre_filter;
      }

      //Fetch the starting index of the query
      if (array_key_exists("start", $_REQUEST)) {
         $this->start = $_REQUEST['start'];
      }
   
      //Fetch the No of items to be selected from the query
      if (array_key_exists("count", $_REQUEST)) {

         $this->count = $_REQUEST['count'];
         if($this->count >$this->max_count){
            $this->count =$this->max_count;
         }   
         //log_msg("count",$this->count);
      }

      if(is_null($this->order_by)){
         $this->order_by="ORDER BY $this->key";
      }
      return "SELECT DISTINCT $this->key $this->id FROM $this->table $this->filter $this->order_by LIMIT $this->start,$this->count";
   }


   /*
   Fill the data in to array
   */
   public function gen_json_data(){
      $res=exec_query($this->_gen_query());
      if(isset($this->key_a) && is_array($this->key_a)){
         if(is_array($this->key_a[key($this->key_a)])){
            $res[]=array(key($this->key_a)=>'NULL','label'=>'-none-');
         }else{
            $res[]=array(key($this->key_a)=>'NULL','label'=>'-none-');
         }
      }else{
         $res[]=array($this->key=>'-none-');
      }
      //Return as JSON formatted data
      return json_encode(array("identifier"=>$this->key,"label"=>"label","items"=>$res));
   }
}


