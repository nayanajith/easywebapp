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
   $key can hold different types
   $Key -> 'string'
   $key -> array('id','label')
   $key -> array(
      'id'=array('label1','label2')
   )

   Eg:
   $key -> Array(
      [rid] => Array(
         [0] => ministry_title
         [1] => ministry_address 
      )
   )

   */
   public function __construct($table,$key,$filter='',$order_by=null,$id=null,$default="-none-") {
      $this->key        =$key;   
      $this->table      =$table;
      $this->default    =$default;
      $this->pre_filter =$filter;
      $this->order_by   =$order_by;
      $this->id         =$id;   
      $this->fields     =$id;   
      $this->searchAttr =$key;
      if(is_array($key)){
         $this->key_a        =$key;   
         if(key($key)===0){ //distinguish associative arrays
            $this->key =$key[0];  //hidden field of the select box 
            $this->searchAttr =$key[0];
         }else{
            $this->key =key($key);  //hidden field of the select box 
            if(is_array($key[key($key)])){
               $this->fields  	=',CONCAT('.implode(',"/",',$key[key($key)]).") label";   //displaying field of the select box
               $this->searchAttr =' CONCAT('.implode(',"/",',$key[key($key)]).") ";   //to use with like operator
               //$this->searchAttr =$key[key($key)][0];
            }else{
               $this->fields  	=','.$key[key($key)].' label';   //displaying field of the select box
               $this->searchAttr =$key[key($key)];   //displaying field of the select box
               //$this->searchAttr =$key[key($key)];
            }
         }
      }else{
         $this->fields=",".$this->key." label";
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
      if (array_key_exists('id', $_REQUEST) && $_REQUEST['id'] != '') {
         $this->filter="WHERE $this->id ='".$_REQUEST['id']."' ".$this->pre_filter;
      }elseif (array_key_exists('label', $_REQUEST)) {
         $this->filter = $_REQUEST['label'];
         $this->filter = str_replace("*", "%", $this->filter);
         $this->filter="WHERE $this->searchAttr LIKE '".$this->filter."' ".$this->pre_filter;
      }else{
         $this->filter="WHERE $this->searchAttr LIKE '%' ".$this->pre_filter;
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
      return "SELECT DISTINCT $this->key $this->fields FROM $this->table $this->filter $this->order_by LIMIT $this->start,$this->count";
   }


   /*
   Fill the data in to array
   */
   public function gen_json_data(){
      $res=array();
      $res=array_merge($res,exec_query($this->_gen_query()));
      //Default value will be appended to the end of the list according to the user request
      if(!is_null($this->default)){
         if(isset($this->key_a) && is_array($this->key_a)){
            if(is_array($this->key_a[key($this->key_a)])){
               $res[]=array(key($this->key_a)=>'NULL','label'=>$this->default);
            }else{
               $res[]=array(key($this->key_a)=>'NULL','label'=>$this->default);
            }
         }else{
            $res[]=array($this->key=>'NULL','label'=>$this->default);
         }
      }

      //Return as JSON formatted data
      return "{}&&".json_encode(array("identifier"=>$this->key,"label"=>"label","items"=>$res));
   }
}


