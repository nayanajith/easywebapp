/**
 * Set session parameters using XHR requests in backend
 */
function set_param(url,key,value) {
   dojo.xhrPost({
      url       : url+'&form=main&action=param&data=json&param='+key+'&'+key+'='+value,
        handleAs :'json',
        load       : function(response, ioArgs) {        
         update_status_bar(response.status,response.info);
            if(response.status == 'OK'){
               update_progress_bar(100);
            }
         },
         error : function(response, ioArgs) {
            update_status_bar('ERROR',response);
         }
   });
}   


/**
request html from the backend
*/
function request_html(url,target,source_array,action_) {
   var content_obj   =document.getElementById(target);
   var param='';   
   for(var i=0;i<sourse_array.length;i++){
      var tmp_val   =document.getElementById(sourse_array[i]).value;
      if(tmp_val=='')return;
      param+='&'+sourse_array[i]+'='+tmp_val;
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
      url      : url+'&form=main&data=json&action='+action+param,
      handleAs :'text',
      load     : function(response, ioArgs) {        
         update_status_bar('OK','Done');
         update_progress_bar(100);
         content_obj.innerHTML=response;
         dojo.parser.parse(content_obj);
      },
      error : function(response, ioArgs) {
         update_status_bar('ERROR',response);
      }
   });
}

/** Submit the given form
* tartget_module and target_page are optional. these parameters are used by public layout
*/

function submit_form(url,action,form,target_module,target_page){
   switch(action){
      case 'print':
         window.open(url+'&form=main&action='+action,'width=800px,height=600px');
         return;
      break;   
      case 'csv':
      case 'pdf':
         download(url+'&form=main&action='+action);
         return;
      break;   
      case 'reload':
         request_html(form,source_array);
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

   if (action=='delete' || dijit.byId(form).validate()) {
      dojo.xhrPost({
         url         : url+'&form=main&action='+action, 
         handleAs    : 'json',
         form        : form, 
      
         handle: function(response,ioArgs){
            update_status_bar(response.status_code,response.info);
            if(response.status_code == 'OK'){
               if(!target_module && !target_page){
                  update_progress_bar(100);
               }else{
                  window.open('?module='+module+'&page='+page,'_parent');
               }
            }else{
               update_status_bar('ERROR',response.info);
               if(document.getElementById('captcha_image'))reload_captcha();
               //update_status_bar('ERROR','Duplicate Entry!');
            }
         },
      
         load: function(response) {
            if(!target_module && !target_page){
               update_status_bar('OK','rquest sent successfully');
               update_progress_bar(50);
            }
         }, 
         error: function() {
            if(!target_module && !target_page){
               update_status_bar('ERROR','error on submission');
               update_progress_bar(0);
            }
         }
      });
      return false;
   }else{
      update_status_bar('ERROR','Form contains invalid data.  Please correct them and submit');
      return false;
   }
   return true;
}
