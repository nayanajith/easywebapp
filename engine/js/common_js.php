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

         var button="<br/><center><button dojoType='dijit.form.Button' onClick=\"window.open('?module='+get_request_value('module')+'&page='+get_request_value('page')+'&help=true','_blank');help_Dialog.hide()\" >Show in Fullscreen</button><button dojoType='dijit.form.Button' onClick=\"help_Dialog.hide()\" >OK</button></center>";
         help_Dialog.attr("content", response+button);
         help_Dialog.show();
  	   },
  	   error : function(response, ioArgs) {
  	  			update_status_bar('ERROR',response);
  	   }
   });
}


/*-- Downloader --*/
dojo.require('dojo.io.iframe');

//Seamless download using  iframe
function download(url){
	update_status_bar('OK','Processing...');
	var iframe = dojo.io.iframe.create("downloader");
	dojo.io.iframe.setSrc(iframe, url, true);
	update_status_bar('OK','Done');
   //DEBUG ONLY
	update_status_bar('OK',url);
	update_progress_bar(100);
}

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
