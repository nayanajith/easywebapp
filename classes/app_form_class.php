<?php


include A_CLASSES."/data_entry_pub_class.php";


class Writable_app_form{

   protected $table;
   protected $filter_table;
   protected $key; 
   protected $key2; 
   protected $grid_array; 
   protected $grid_array_long;
   protected $file_name;

   protected $formgen;

   /*Filtering string to keep the query chunk*/
   protected $filter_string;

   function __construct($table,$filter_table,$key, $key2, $grid_array, $grid_array_long,$data_load_key,$file_name){
      $this->table            =$table;
      $this->filter_table      =$filter_table;
      $this->key               =$key; 
      $this->key2               =$key2; 
      $this->grid_array         =$grid_array; 
      $this->grid_array_long   =$grid_array_long;
      $this->file_name         =$file_name;
      $this->formgen          = new Formgenerator_pub($table,$key,$file_name,$data_load_key);
   }

   function get_form_gen_class(){
      return $this->formgen;
   }

   /*generate csv with column headers*/
   function gen_csv(){
      $filter_str=$this->filter_string!=""?" WHERE ".$this->filter_string:"";
      include $this->table."_modif.php";
      $columns=array_keys($fields);
      $headers="";
      $comma="";

      foreach($columns as $column){
         $headers.=$comma."'$column' AS $column";
         $comma=",";
      }
      
      $fields=implode(",",$columns);
      $query="SELECT $headers FROM ".$this->table." UNION SELECT $fields FROM ".$this->table.$filter_str;
      
      $csv_file= tempnam(sys_get_temp_dir(), 'ucscsis').".csv";
      db_to_csv($query,$csv_file);
      header('Content-Type', 'application/vnd.ms-excel');
      header('Content-Disposition: attachment; filename='.$this->table.'.csv');
      readfile($csv_file);
      return;
   }

   /*Extract filter according to the filter_id in request string*/
   function set_filter_string(){
      $this->filter_string=$this->formgen->ret_filter($_REQUEST['filter_name']);
   }

   function record_action($action){
      switch($action){
          case 'add':
            return $this->formgen->add_record();
          break;
          case 'modify':
            return $this->formgen->modify_record();
          break;
          case 'delete':
            return $this->formgen->delete_record();
          break;
      }
   }
   
   function filter_action($action){
      switch($action){
          case 'add':
            return $this->formgen->add_filter();
          break;
          case 'modify':
            return $this->formgen->modify_filter();
          break;
          case 'delete':
            return $this->formgen->delete_filter();
          break;
      }
   }

   function return_record_json(){
      if(isset($_REQUEST['id'])){
         $this->formgen->xhr_form_filler_data($_REQUEST['id']);
      }else{
         $this->formgen->xhr_filtering_select_data(null,null,$this->filter_string);
      }
   }

   function return_filter_json(){
      if(isset($_REQUEST['id'])){
         $this->formgen->xhr_filter_filler_data($_REQUEST['id']);
      }else{
         $filter=$this->filter_string."table_name='".$this->table."'";
         $this->formgen->xhr_filtering_select_data($this->filter_table,'filter_name',$filter);
      }
   }

   function gen_record_grid($long){
      if($long){
         $json_url=$this->formgen->gen_json($this->grid_array_long,$this->filter_string,false);
         echo $this->formgen->gen_data_grid($this->grid_array_long,$json_url,$this->key);
      }else{
         $json_url=$this->formgen->gen_json($this->grid_array,$this->filter_string,false,null);
         echo $this->formgen->gen_data_grid($this->grid_array,$json_url,$this->key);
      }
   }


   function return_filtering_select_data(){
      $this->formgen->xhr_filtering_select_data($this->filter_table,'filter_name',"table_name='".$this->table."'");
   }

   function key_selector(){
      echo $this->formgen->gen_xhr_form_filler('fill_form');
      echo $this->formgen->gen_xhr_filtering_select('fill_form');
   }

   function gen_record_form($captchar=null){
      echo $this->formgen->gen_form($captchar);
   }

   function gen_filter_form(){
      echo $this->formgen->gen_filter();
   }



   
   function gen_javascript(){
   d_r('dojox.data.QueryReadStore');
?>


<script type='text/javascript'>
dojo.addOnLoad(function() {

   /*No toolbar for web_layout*/
   if(!dijit.byId("toolbar")){
      return;   
   }

   toolbar = new dijit.byId("toolbar");
   var filterStore = new dojox.data.QueryReadStore({
      url: "<?php echo gen_url(); ?>&data=json&form=select_filter"
   });

   var filteringSelect = new dijit.form.FilteringSelect({
       id: "filter_select",
       name: "state",
       value: "<?php echo isset($_REQUEST['filter_name'])?$_REQUEST['filter_name']:"aa"; ?>",
       store: filterStore,
       searchAttr: "filter_name",
       pageSize: '20',
       onChange:function(){change_filter(this.get("displayedValue"))},
   },"stateSelect");

   toolbar.addChild(filteringSelect);
   filteringSelect.setValue("<?php echo isset($_REQUEST['filter_name'])?$_REQUEST['filter_name']:"aa"; ?>");
});

function change_filter(filter_name){
   if(filter_name != ''){
      URL='<?php echo gen_url().(isset($_REQUEST['form'])?"&form=".$_REQUEST['form']:"");?>&filter_name='+filter_name;
      open(URL,'_self');
   }
}

function get_csv(){
   url='<?php echo gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:""); ?>&data=csv';
   open(url,'_self');
}


function grid(){
   url='<?php echo gen_url().(isset($_REQUEST['filter_name'])?"&filter_name=".$_REQUEST['filter_name']:""); ?>&form=grid';
   open(url,'_self');
}
</script>

<?php
   }
}

?>
