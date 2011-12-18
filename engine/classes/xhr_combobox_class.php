<?php
/*
This class provide supportive function to add an xhr combo box to the toolbar
*/
class XHR_Combobox{
   function __construct(){
   }
   
   /*
   Return json for the filtering select at the frontend
     */
   function json_data($table,$key,$filter=null,$order_by=null,$id=null){
      include A_CLASSES.'/qread_store_class.php';
      $filter=$filter==null?"":" AND $filter";
   
      header('Content-Type', 'application/json');
      $query_read_store = new Query_read_store($table,$key,$filter,$order_by,$id);
      echo $query_read_store->gen_json_data();
   }

   function get_val($param_name){
      return isset($_SESSION[PAGE][$param_name])?$_SESSION[PAGE][$param_name]:'';
   }
   
   /*
   Label to be added to toolbar   
   */
   function label($id,$label){
      echo  "
      var ".$id."_label=new dijit.form.Button({
            label: '$label',
          disabled:true,
      });
      ";
      return $id."_label";
   }
   
   function store($id){
      d_r('dojox.data.QueryReadStore');
      echo "
      var ".$id."_store = new dojox.data.QueryReadStore({
         url:'".gen_url()."&data=json&form=main&action=store&id=$id',
         jsId:'".$id."_store',
      });
      ";
   }

   function static_store($id,$items_arr){
      d_r('dojo.data.ItemFileReadStore');
      echo "
         var ".$id."_store = new dojo.data.ItemFileReadStore({
            data:{   
               identifier: '$id',
               items: [{\"$id\":\"".implode($items_arr,'"},{"'.$id.'":"')."\"}]
              },
            jsId:'".$id."_store'
         });
      ";
   }
   
   /*
    *Generate a combobox which will be inserted in the toolbar
    */
     function combo_box($id,$page_size,$value,$width=80,$html,$source_array,$target){
      //if html true request html other than setting parameter
      $onchange="set_param('$id',this.value)";
      if($html){
         $onchange="set_param('$id',this.value);source_array=new Array('".implode("','",$source_array)."');request_html('$target',source_array,null)";
      }

      d_r('dijit.form.ComboBox');
      echo  "
       var ".$id."_combo = new dijit.form.ComboBox({
         jsId:'$id',
         id:'$id',
         name:'$id',
         style:'width:".$width."px;',
         value:'$value',
         searchAttr: '$id',
         pageSize: '$page_size',
         onChange:function(){".$onchange.";},
         store : ".$id."_store
      },'".$id."_select');
      ";
      return $id."_combo";
   }
   
  /*
   * javascript function to set values for parameters
   */ 
   function param_setter(){
      echo  "
      function set_param(key,value) {
         dojo.xhrPost({
            url       : '".gen_url()."&form=main&action=param&data=json&param='+key+'&'+key+'='+value,
              handleAs :'json',
              load       : function(response, ioArgs) {        
                  update_status_bar(response.status_code,response.info);
               },
               error : function(response, ioArgs) {
                  update_status_bar('ERROR',response);
               }
         });
      }";
   }    

   /*
    * Request html and place the data in a html tag
    */
   function html_requester(){
      echo "
      //Request html from back end and set it as inner html of target element 
      function request_html(target,sourse_array,action_) {
         var content_obj   =document.getElementById(target);
         var url='';   

         //Append all the values in elements of given ides of source_array
         if(sourse_array){
            for(var i=0;i<sourse_array.length;i++){
               var tmp_val   =document.getElementById(sourse_array[i]).value;
               if(tmp_val=='')return;
               url+='&'+sourse_array[i]+'='+tmp_val;
            }
         }

         //Action will be html by default unless it is set explicitly
         var action='html';
         if(action_){
            action=action_;
         }

         //Update the progress bar 
         update_status_bar('OK','Processing...');
         update_progress_bar(10);

         //If index number is blank return 
         dojo.xhrPost({
            url       : '".gen_url()."&form=main&data=json&action='+action+url,
            handleAs :'text',
            form      :target,
            load       : function(response, ioArgs) {        
               content_obj.innerHTML=response;
               update_status_bar('OK','Done');
               update_progress_bar(100);
               //Parse the content placed inside content_obj
               dojo.parser.parse(content_obj);
            },
            error : function(response, ioArgs) {
                 update_status_bar('ERROR',response);
            }
         });
      }
      ";
   }

   /*
    * generate a javascript function which support to submit a xhr request
    */
   function form_submitter($form){
      echo "
      function submit_form(action,fields){
         switch(action){
            case 'print':
               window.open('".gen_url()."&form=main&action='+action,'width=800px,height=600px');
               return;
            break;   
            case 'csv':
            case 'pdf':
               download('".gen_url()."&form=main&action='+action);
               return;
            break;   
            case 'reload':
               request_html('$form',source_array);
               return;
            break;   
            case 'delete':
            break;   
         }

         update_status_bar('OK','Processing...');
         update_progress_bar(10);

         /*User should confirm deletion*/
         if(action=='delete' && !confirm('Confirm Deletion!')){
            update_status_bar('ERROR','deletion canceled');
            update_progress_bar(0);
            return;   
         }

         if (action=='delete' || dijit.byId('$form').validate()) {
            dojo.xhrPost({
               url         : '".gen_url()."&form=main&action='+action, 
               handleAs    : 'json',
               form        : '$form', 
            
               handle: function(response,ioArgs){
                  update_status_bar(response.status_code,response.info);
                  if(response.status_code == 'OK'){
                     update_progress_bar(100);
                  }
               },
            
               load: function(response) {
                  update_status_bar('OK','rquest sent successfully');
                  update_progress_bar(50);
               }, 
               error: function() {
                  update_status_bar('ERROR','error on submission');
                  update_progress_bar(0);
               }
            });
            return false;
         }else{
            update_status_bar('ERROR','Form contains invalid data.  Please correct first');
            return false;
         }
         return true;
      }
   ";
   }

   /*
   add comboboxes to the toolbar
   */
   protected $toolbar_ref_set=false;
   function add_to_toolbar($obj){
      if(!$this->toolbar_ref_set){
         echo "var toolbar = dijit.byId('toolbar');";
         $this->toolbar_ref_set=true;
      }
      echo "toolbar.addChild($obj);";
   }

   function gen_xhr_static_combo($id,$label,$value,$width,$item_array,$source_array=null,$target=null){
      $this->static_store($id,$item_array);
      $this->add_to_toolbar($this->label($id,$label));
      if(is_array($source_array)){
         $this->add_to_toolbar($this->combo_box($id,20,$value,$width,true,$source_array,$target));
      }else{
             $this->add_to_toolbar($this->combo_box($id,20,$value,$width,false,null,null));
      }
   }

   /*
    * This function will generate a combobox with xhr capabilities
    * id:
    * label:
    * value:
    * width:
    * page_size:
    * source_array:
    * target:
    */
   protected $html_req_done=false;
   function gen_xhr_combobox($id,$label,$value,$width,$page_size,$source_array=null,$target=null){
      $this->store($id);
      $this->add_to_toolbar($this->label($id,$label));
      if(is_array($source_array)){
         $this->add_to_toolbar($this->combo_box($id,$page_size,$value,$width,true,$source_array,$target));
      }else{
             $this->add_to_toolbar($this->combo_box($id,$page_size,$value,$width,false,null,null));
      }
   }
}    
?>
