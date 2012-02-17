<?php
include_once ('../config.php');
include_once (A_CORE.'/common.php');
header("Content-type: application/javascript");
header("Content-Disposition: attachment; filename=\"common.js\"");
?>
/*
//Browser detection
dojo.isIE
dojo.isMozilla
dojo.isFF
dojo.isOpera
dojo.isSafari
dojo.isKhtml
dojo.isAIR 
dojo.isQuirks
dojo.isBrowser
dojo.isWebKit
dojo.isChrome

alert(dojo.isIE+':'+
dojo.isMozilla+':'+
dojo.isFF+':'+
dojo.isOpera+':'+
dojo.isSafari+':'+
dojo.isKhtml+':'+
dojo.isAIR +':'+
dojo.isQuirks+':'+
dojo.isBrowser+':'+
dojo.isWebKit+':'+
dojo.isChrome);
*/


/*--help viewer--*/
function help_dialog(){
	dojo.xhrPost({
      url 		: '?module='+get_request_value('module')+'&page='+get_request_value('page')+'&help=true',
  	   handleAs :'text',
  	   load 		: function(response, ioArgs) {	     
         help_Dialog = new dijit.Dialog({
            title: "Help",
            style: "width: 800px;"
         });

         var button="<br/><center><button dojoType='dijit.form.Button' onClick=\"window.open('?module='+get_request_value('module')+'&page='+get_request_value('page')+'&help=true&fullscreen=true','help_window');help_Dialog.hide()\" >Show in Fullscreen</button><button dojoType='dijit.form.Button' onClick=\"help_Dialog.hide()\" >OK</button></center>";
         help_Dialog.attr("content", response+button);
         help_Dialog.show();
  	   },
  	   error : function(response, ioArgs) {
  	  			update_status_bar('ERROR',response);
  	   }
   });
}


/*-- Seemless Downloader --*/
dojo.require('dojo.io.iframe');

//Seamless download using  iframe
function download(url){
	update_status_bar('OK','Processing...');

   //desable after testing
   window.open(url);return;

   //dojo iframe is creating to set the source of it   
	var iframe = dojo.io.iframe.create("downloader");

   //Downloading the file using iframe
   //TODO: this is sometime not work for chrome use browser detection and swtich the downloading method
	dojo.io.iframe.setSrc(iframe, url, true);
	update_status_bar('OK','Done');

   //DEBUG ONLY
	update_status_bar('OK',url);
	update_progress_bar(100);
}

/*-----------------------------------view_class mostly using these functions-----------------------------*/
//acquire GET request key,values from the current url 
function get_request_value( name ){
   name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
   var regexS = "[\\?&]"+name+"=([^&#]*)";
   var regex = new RegExp( regexS );
   var results = regex.exec( window.location.href );
   if( results == null ){
		return "";
   }else{
		return results[1];
	}
}

//Info dialog will  show the information as floating dialog box
function info_dialog(info,title,more_buttons,width,height){
   if(!more_buttons)more_buttons="";
   if(!title)title="Information";
   if(!width)width=300;
   if(!height)height=200;
   infoDialog = new dijit.Dialog({
      title: title,
      style: "width:"+width+"px;height:"+height+";"
   });

   var buttons="<br/><center>"+more_buttons+"<button dojoType='dijit.form.Button' onClick=\"infoDialog.hide()\" >OK</button></center>";
   infoDialog.attr("content", info+buttons);
   infoDialog.show();
}

//This function allow to copy the content of given object to clipboard
//Only work with IE TODO: try to fix with suppor of flash
function copy_to_clipboard(object){
   var obj=dojo.byId(object);
   obj.select();
   if(window.clipboardData){ 
      var r=clipboardData.setData('Text',obj.value);
      return 1; 
   }else{ 
      alert('please copy manually');
   }
}

/**
 * javascript version of gen_url
 * This returns the basic target from the url
 */
function gen_url(){
   var page_gen      ='<?php echo $GLOBALS['PAGE_GEN']; ?>';
   var module        =get_request_value('module');
   var page          =get_request_value('page');
   var filter_name   =get_request_value('filter_name')?'filter_name='.get_request_value('filter_name'):'';
   var program       =get_request_value('program');
   return page_gen+"?module="+module+"&page="+page+"&program="+program+filter_name;
}


/**
 * Set session parameters using XHR requests in backend
 */
function set_param(key,value) {
   var url=gen_url();
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
function request_html(target,source_array,action_) {
   var content_obj   =document.getElementById(target);
   var param='';   
   var url=gen_url();
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

function submit_form(action,target_module,target_page){
   var form='main';
   var url=gen_url();
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

/**
 * Populate the data in form for the selected key
 */
function form_filler(key,form) {
   if(!(key == '' || key == 'new')){
   dojo.xhrPost({
      url       : gen_url()+"&data=json&id='+"+key+"form="+form,
      handleAs :'json',
      load       : function(response, ioArgs) {        
         if(response.status && response.status == 'ERROR'){
            update_status_bar(response.status,response.info);
            update_progress_bar(50);
            return;
         }

         /*reset form*/
         dojo.forEach(dijit.byId('".$table."_frm').getDescendants(), function(widget) {
            if(!widget.store){
               widget.attr('value', null);
            }
         });
         /*fill the form with returned values from json*/
         for(var key in response){
            if(response[key]){
               if(response[key]['_type']=='Date'){
                  //Convert ISO standard date string to javascript Date object
                      dijit.byId(key).setValue(dojo.date.stamp.fromISOString(response[key]['_value'])); 
               }else{
                  //Handle different types of fields
                  switch(dijit.byId(key).type){
                     case 'checkbox':
                        switch(response[key]){
                           case '1':
                           case 'on':
                              dijit.byId(key).attr('checked',true); 
                           break;
                           case '0':
                           case 'off':
                           default:
                              dijit.byId(key).attr('checked',false); 
                           break;
                        }
                     break;
                     case 'radio':
                     break;
                     default:
                        dijit.byId(key).setValue(response[key]); 
                     break;
                  }
               }
            }
         }
      },
      error : function(response, ioArgs) {
           update_status_bar('ERROR',response);
      }
   });
   }
}


/*-----------------------related to filter form---------------------*/
function show_dialog(){
   formDlg = dijit.byId('filterDialog');
   formDlg.show();
}

/*clear the form first*/
function clear_form(frm){
   dojo.forEach(dijit.byId(frm).getDescendants(),function(widget){
      widget.attr('value', null);
    });
}   

/*
Sending filter data as json   
*/
function dialog_submit(arg_form,action){
   /*User should confirm deletion*/
   if(action=='delete'&&!confirm('Confirm deletion!')){
      return;
   }
   if(arg_form.validate()){
      var json_req=dojo.toJson(arg_form.getValues(), true);
      dojo.xhrPost({
         url: gen_url()+"&xhr=true&form=filter&filter='+json_req+'&action='+action, 
         handleAs:'text',
         handle: function(response){
            update_status_bar('OK',response.info);
         },

         load: function(response) {
            update_status_bar('OK','form successfully submitted');
         }, 
         error: function() {
            update_status_bar('ERROR','error on submission');
         }
      });

      /*
      for(var key in arg_array){
         update_status_bar(key);   
      }
      */
   }else{
      update_status_bar('ERROR','found invalid filed values');      
   }
}

/*---------------------------------------------------------------------------------------------------------------*/

