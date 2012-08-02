<?php

class File_uploader{

/*Path to save the file*/
protected $base_path="/tmp";
protected $file_name="test.tmp";

/**
@param base_path : path to save the file
@param input_name : filename to save/delete the file
*/
function __construct($base_path,$file_name){
   $this->base_path   =$base_path;
   $this->file_name   =$file_name;
}

/**
@param form_name : Name for the form
@param input_name : Name for the file input type
@param btn_label : Label to used as the button value
@param callback  : function to callback when the uploading is successful
@param file_id       : file identification helper at server side $_REQUEST['file_id']
*/
function client_side_handle($form_name,$input_name,$btn_label,$callback,$file_id){

   /*Final form and javascript strings will be appended to this variable */
   $frm="";

   /*If the requested file already exists file delete form will be returned*/
   if(file_exists($this->base_path."/".$this->file_name)){
      /*Delete form*/
      $frm .="<form name='$form_name' id='$form_name' method='post'  solid silver;padding:5px'>
   <button dojoType='dijit.form.Button' >Delete
        <script type='dojo/method' event='onClick' args='evt'>
         ".$form_name."_delete('$input_name');
      </script>
   </button>
</form>";
   }else{
      /*Upload form*/
      $frm.="<form name='$form_name' id='$form_name' enctype='multipart/form-data' method='post' style='padding:5px'>
   <label for='$input_name'>Choose file</label>
   <input type='file' name='$input_name' id='$input_name' ><br><br>
   <button dojoType='dijit.form.Button' >$btn_label
        <script type='dojo/method' event='onClick' args='evt'>
         ".$form_name."_upload('$input_name');
      </script>
   </button>
</form>";
   }

/*dojo.io.iframe.send request to uplod file and dojo.xhrGet request to delete*/
js("
   /*Change the form in to deleter*/
   var delete_frm_inner_".$form_name."='<input type=\"hidden\" name=\"".$input_name."_file_id\" value=\"$file_id\" >'+
   '<button dojoType=\"dijit.form.Button\" onClick=\"".$form_name."_delete(\'$input_name\')\">Delete'+
   '</button>';

   /*Change the form into uploader*/
   var upload_frm_inner_".$form_name."='<label for=\"$input_name\">Choose file</label>'+
   '<input type=\"hidden\" name=\"".$input_name."_file_id\" value=\"$file_id\" >'+
   '<input type=\"file\" name=\"$input_name\" id=\"$input_name\" ><br><br>'+
   '<button dojoType=\"dijit.form.Button\" onClick=\"".$form_name."_upload(\'$input_name\')\">$btn_label'+
   '</button>';

   /*Change between upload/delete*/
   var frm_".$form_name."_div=document.getElementById('$form_name');


   dojo.require('dojo.io.iframe');

   /**
   File upload request sent using dojo.io.iframe.send request   
   */
   function ".$form_name."_upload(frm_name){
      /*The file id should not be blank to upload/delete a file*/
      //var the_file_id=dijit.byId('$file_id').getValue();
      var the_file_id=document.getElementById('$file_id').value;
      if(the_file_id == ''){
         alert('Please select $file_id before uploading!');
         return false;
      }else{
         /*Update status bar*/
         update_status_bar('...');
         update_progress_bar(10);

         var td = dojo.io.iframe.send({
            url         : '".gen_url()."&form=file&action=upload&file_name='+frm_name+'&file_id='+the_file_id,
            form         : '$form_name',
            method      : 'post',
            content      : {fnx:1},
            timeoutSeconds: 5,
            preventCache: true,
            handleAs      : 'json',
      
            handle: function(response, ioArgs){
               if(response.status == 'OK'){
                  update_status_bar('file uploaded successfully');
                  update_progress_bar(100);
                  frm_".$form_name."_div.innerHTML=delete_frm_inner_".$form_name.";
               }else{
                  update_status_bar('file uploading failed');
               }

            },                     
      
            load: function(response, ioArgs){
               update_status_bar('file uploaded successfully');
               update_progress_bar(50);
            },
      
            error: function (response, ioArgs) {
               update_status_bar('error on submission');
               update_progress_bar(0);
            }
         });
         return false;
      }
   }

   /**
   File delete request sent as dojo.xhrGet AJAX request   
   */
   function ".$form_name."_delete(frm_name){
      /*The file id should not be blank to upload/delete a file*/
      //var the_file_id=dijit.byId('$file_id').getValue();
      var the_file_id=document.getElementById('$file_id').value;

      if(the_file_id == ''){
         alert('Please select $file_id before Deleting!');
         return false;
      }else{
         /*Update status bar*/
         update_status_bar('...');
         update_progress_bar(10);

         dojo.xhrGet({
         url         : '".gen_url()."&form=file&action=delete&file_name='+frm_name+'&file_id='+the_file_id, 
         handleAs      : 'json',
         form         : '".$form_name."', 

         handle: function(response){
            if(response.status == 'OK'){
               update_status_bar('file deleted successfully');
               update_progress_bar(100);
               frm_".$form_name."_div.innerHTML=upload_frm_inner_".$form_name.";
            }else{
               update_status_bar('file deletion failed');
            }
         },

         load: function(response) {
            update_progress_bar(50);
            update_status_bar('form successfully submitted');
         }, 
         error: function() {
            update_progress_bar(0);
            update_status_bar('error on submission');
         }
      });
      return false;
      }
   }
");

return $frm;
}


   /**
   @param valid_file_types : Array of file types (mime types ) which are valid eg: ['image/gif','image/jpg','image/pjpeg'];
   @param max_file_size      : Maximum size of the file which can be uploaded
   @param base_path         : The path which files should saved
   @param file_rename      : new name for the file ( if null the actual name will be used )
   @param input_name         : Name of the file input field from the client side 
   @param file_overwrite   : If true files will be overwritten if the similar file is uploaded else return error
   
   return  -100 : invalid file ( not satisfying the file type and size conditions )
   return  -101 : file exists
   return  Array(size,type) :  if file action is successful return an array of file size and other its information
   return file upload error code
   */
   function server_side_handle($valid_file_types,$max_file_size,$input_name,$file_overwrite){
      /*file type and size validation*/
      if(in_array($_FILES[$input_name]["type"],$valid_file_types) && ($_FILES[$input_name]["size"] <= $max_file_size)){
         
         /*Check for errors in file if errors are then return error code*/
         if ($_FILES[$input_name]["error"] > 0){
             return $_FILES[$input_name]["error"];
          }else{
            /*set the file name, if a custom file name is defined use it as the file name*/
            $f_name=$_FILES[$input_name]["name"];
            if(isset($this->file_name) && $this->file_name != null){
               $f_name=$this->file_name;
            }

            /*Full path to save the file*/
            $f_name=$this->base_path."/".$f_name;

            /*If the file exists deside what to do according to the user request if overwrite=true overwrite the file*/
             if(file_exists($f_name)){
               if(isset($file_overwrite) && $file_overwrite == true){
                  unlink($f_name);
                  move_uploaded_file($_FILES[$input_name]["tmp_name"],$f_name);

                  /*return file size and type for further reference by the caller*/
                  return array($_FILES[$input_name]["size"],$_FILES[$input_name]["type"]);
               }else{
                  /*If the file_overwrite is false and file exists return -101*/
                  return -101;
               }
             }else{
               /*If the file does not exists just cop the file from the temp*/
               move_uploaded_file($_FILES[$input_name]["tmp_name"],$f_name);
               return array($_FILES[$input_name]["size"],$_FILES[$input_name]["type"]);
            }
          }
          }else{
             return -100;
          }
   }

   /**
   Delete the file
   */
   function delete_file(){
      $f_name=$this->base_path."/".$this->file_name;
      if(file_exists($f_name)){
         if(unlink($f_name)){
            return 1;
         }else{
            return -1;
         }
      }else{
         return -1;
      }
   }
}
?>
