<?php
include A_CLASSES."/data_entry_class.php";
/*
function __construct($table=null,$key=null) 
public function load_modifier()
public function write_config()
public function get_field_width($type,$actual=false)
public function get_field_type($type)
public function get_data($filter=null)
public function gen_field_entry($field)
public function gen_filter_field_entry($field)
public function gen_form()
public function gen_filter()
public function ret_filter($filter_name)
public function add_filter()
public function delete_filter()
public function modify_filter()
public function gen_json($key_array,$filter=null,$return,$table=null)
public function gen_data_grid($key_array,$json_file,$key=null)
public function displayLinks(e)
public function gen_filtering_select($json_file)
public function xhr_filtering_select_data($table=null,$key=null,$filter=null)
public function gen_xhr_filtering_select($js_function,$key=null,$filter=null)
public function xhr_form_filler_data($qustion)
public function xhr_filter_filler_data($qustion)
public function gen_xhr_form_filler($js_function,$table=null,$key=null,$filter=null)
public function add_record()
public function modify_record()
public function delete_record($purge=true)
*/

/*
 class to generate the gui components of the form using dojo and php
 */
class Formgenerator_pub extends Formgenerator {

      /*
       return :Form  for the provided table with using custom configuration of each field
       */
      public function gen_form($captchar=null,$filter_selector=null){
         $table=$this->self['table'];

         if($this->data_load_key != null){
            $this->get_data();
         }


         //Form starts hear
         /*
         $form= "<div dojoType='dijit.form.Form' id='".$table."_frm' jsId='$table'
            encType='multipart/form-data'
            action='".$GLOBALS['PAGE_GEN']."';
            method='GET'>

            <button id='previous' onClick='dijit.byId('stackContainer').back()' dojoType='dijit.form.Button'>
            &lt;prev
              </button>
              <span dojoType='dijit.layout.StackController' containerId='stackContainer'>
              </span>
              <button id='next' onClick='dijit.byId('stackContainer').forward()' dojoType='dijit.form.Button'>
               next&gt;
              </button>
            <div dojoType='dijit.layout.StackContainer' id='stackContainer' width='200px'  style='min-height:200px;background-color:whitesmoke;'>
            ";
            */
         d_r('dijit.form.Form');
         //d_r('dijit.layout.StackController');
         //d_r('dijit.layout.StackContainer');
         $form= "<div dojoType='dijit.form.Form' id='".$table."_frm' jsId='$table'_frm
            encType='multipart/form-data'
            method='GET' >";

         //$form.="<center><span dojoType='dijit.layout.StackController' containerId='stackContainer'></span></center>";
         //$form.="<div dojoType='dijit.layout.StackContainer' id='stackContainer'>";
         $form.="<div >Required fields marked as <font color='red'>*</font>";

         
         
         /*Find first and last elements of the fields array*/
         reset($this->fields);
         $first   =key($this->fields);
         $last      =end($this->fields);
         reset($this->fields);


         /*Set form table background and padding/spacing*/
         //$form.= "<table cellspacing='0px' cellspacing='0px' class='form_table'>";

         foreach($this->fields as $field => $field_array){

            if($field != ""){
               
               /*IF the section ended in previouse field drow section header*/
               /*IF the field is the first field of the form drow section header*/
               if($field==$first && !isset($field_array['section'])){
                  $field_array['section']=' ';
               }

                           
               /*Set section header/footer as requested*/
               $section         ="";

               if(isset($field_array['section'])){
                  $section      ="</div>";

                  /*For first field remove </div>*/
                  if($field==$first){
                     $section      ="";
                  }

                  if($field_array['section'] !=''){
                     //d_r('dijit.layout.ContentPane');
                     //$section      .="<div dojoType='dijit.layout.ContentPane' title='".$field_array['section']."'>";
                     $section      .="<div><h4 style='background-color:#C9D7F1;padding:2px;text-align:center' class='bgCenter'>".$field_array['section']."</h4>";
                  }else{
                     //d_r('dijit.layout.ContentPane');
                     //$section      .="<div dojoType='dijit.layout.ContentPane' title='section'>";
                     $section      .="<div>";
                  }
               }
            
               $form.=$section;
               $form.= $this->gen_field_entry($field);


               /*If the element is last set section as end*/
               if($field==$last && !isset($field_array['section'])){
                  $form.= "</div>";
               }
            }
         }

         
         if($captchar && $this->data_load_key == null){
            //$form.="<div style='float:left'><p>For added security, please enter the verification code hidden in the image.</p>";
            $form.="<div style='padding:10px;'><h4 style='background-color:#C9D7F1;padding:2px;text-align:center' class='bgCenter'>The not so fine print</h4>";
            $form.="<p>For added security, please enter the verification code hidden in the image.</p>";
            $form.="<img src='?module=payment&page=captchar&data=true' style='border:1px solid #C9D7F1' ><br>";
            d_r('dijit.form.ValidationTextBox');
            $form.="Code <font color='red'>*</font><input type='text' dojoType='dijit.form.ValidationTextBox' name='captcha' id='security_code' jsId='security_code' required='true' style='width:100px' ></input></div>";
         }

         //$form.="<div align='center' style='float:right' ><button dojoType='dijit.form.Button' onClick=\"submit_form('add')\">Submit</button></div>";
         if($GLOBALS['LAYOUT']=='pub'){
            d_r('dijit.form.Button');
         if($this->data_load_key != null){
               $form.="<div align='right' style='padding:10px' class='bgCenter'  ><button dojoType='dijit.form.Button' onClick=\"submit_form('modify')\">Update</button></div>";
            }else{
               $form.="<div align='right' style='padding:10px' class='bgCenter'  ><button dojoType='dijit.form.Button' onClick=\"submit_form('add')\">Submit</button></div>";
            }
         }
         if($filter_selector){
            $form=$this->gen_xhr_form_filler('fill_form').$form;
            $form=$this->gen_xhr_filtering_select('fill_form').$form;
         }

         //form ends hear
         $form.= "</div>";
         $form.= "</div>";
         
         $hendle=   "update_status_bar(response.status,response.info);";

         if($GLOBALS['LAYOUT']='pub'){
            $hendle="
            if(response.status=='OK'){
               document.getElementById('".$table."_frm').innerHTML=\"<center><h3>Operation successful!</h3></center>\";
               open_page('payment','manage_payments');
            }
            ";
         }

         //Buttons of the form
         $form.= "
            <script type="text/javascript" >
            function submit_form(action){
               update_status_bar('OK','...');
               update_progress_bar(10);
               //alert(dojo.toJson(dijit.byId('".$table."_frm').getValues(), true));
               /*User should confirm deletion*/
               if(action=='delete' && !confirm('Confirm Deletion!')){
                  update_status_bar('ERROR','deletion canceled');
                  update_progress_bar(0);
                  return;   
               }
               if (dijit.byId('".$table."_frm').validate()) {
                  dojo.xhrGet({
                  url         : '".gen_url()."&form=main&action='+action, 
                  handleAs      : 'json',
                  //handleAs   : 'text',
                  form         : '".$table."_frm', 

                  handle: function(response,ioArgs){
                       //dijit.byId('".$table."_frm').attr('value', response); 
                     $hendle
                     update_progress_bar(100);
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
         </script>
         ";

   return $form;
   }
}
?>
