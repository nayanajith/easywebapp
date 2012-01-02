<?php

class Query_read_store{

   protected $filter    = "";
   protected $pre_filter= "";
   protected $start      = 1;
   protected $count      = 10;
   protected $max_count   = 50;
   protected $key         = "IndexNo";
   protected $table      = "csstudent";
   protected $order_by   = null;
 
   /*
   Constructor of the query read store
   */
   public function __construct($table,$key,$filter='',$order_by=null,$id=null) {
      $this->key         =$key;   
      $this->table      =$table;
      $this->pre_filter   =$filter;
      $this->order_by   =$order_by;
      $this->id         =$id;   
      
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
      
      //add new to the front of the array
      //array_unshift($res,array($this->key=>'new'));
      //Return as JSON formatted data
      //log_msg('log_json',json_encode(array('items'=>$res)));
      //header('Content-Type', 'application/json');
      if(is_null($this->id)){
         return json_encode(array("identifier"=>$this->key,"label"=>$this->key,"items"=>$res));
      }else{
         return json_encode(array("identifier"=>$this->id,"label"=>$this->id,"items"=>$res));
      }
   }
}


