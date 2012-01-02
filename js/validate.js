function getElementsByClassName(parent, needle) {
	var my_array = parent.getElementsByTagName("*");
	var retvalue = new Array();
	var i;
	var j;
	for (i = 0, j = 0; i < my_array.length; i++) {
		var c = " " + my_array[i].className + " ";
		if (c.indexOf(" " + needle + " ") != -1)
			retvalue[j++] = my_array[i];
	}
	return retvalue;
}

var num_fields;
var email_fields;
var password_fields;
var nonempty_fields;

function getEle(id) {
	return document.getElementById(id);
}

function putFieldMessage(tdid, mesg) {
	var parent = tdid.name + "_td";
	var td = document.getElementById(parent);
	var msg_field = getElementsByClassName(td, 'field_msg')[0];
	if (msg_field) {
		if (mesg == '') {
			msg_field.innerHTML = "";
		} else {
			//msg_field.innerHTML = "<span style='color:red;'>&lt;</span><span style='background:Khaki;border:1px solid red;font-size:9px;color:black' class=mesg>"
			//		+ mesg + "</span>";
			msg_field.innerHTML = "<span class='baloon_body' >"+mesg+"" +
					"<div class='baloon_tail_bg'>" +
					"<div class='baloon_tail_margin baloon_tail_top_margin'>&nbsp;</div>" +
					"<div class='baloon_tail_margin baloon_tail_bottom_margin'>&nbsp;</div>" +
					"</div></span>";
		}
	}
}

function checkEmail(email_id) {
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var email = email_id.value;
	if (email == "") {
		putFieldMessage(email_id, 'Field empty');
		return false;
	} else if (filter.test(email)) {
		putFieldMessage(email_id, '');
		return true;
	} else {
		putFieldMessage(email_id, 'Please enter a valid email address');
		return false;
	}
}

function checkNumber(number_id) {
	var number = number_id.value;
	if (number == "") {
		putFieldMessage(number_id, 'Field empty');
		return false;
	} else if (isNaN(number)) {
		putFieldMessage(number_id, 'Please Enter a valid number');
		return false;
	} else {
		putFieldMessage(number_id, '');
		return true;
	}
}

function checkPassword(password_id) {
	var password_val = password_id.value;
	var confirmation = confirmation.value;
	if (confirmation == "" || password_val == "" ) {
		putFieldMessage(password_id, 'Field empty');
		return false;
	} else if (confirmation == password_val) {
		putFieldMessage(password_id, 'Password Confirmation failed!');
		return false;
	} else {
		putFieldMessage(password_id, '');
		return true;
	}
}



function checkEmpty(nonempty_id) {
	var nonempty = nonempty_id.value;
	if (nonempty == "") {
		putFieldMessage(nonempty_id, 'Field empty');
		return false;
	} else {
		putFieldMessage(nonempty_id, '');
		return true;
	}
}

function validateAll() {
	var state = new Array();
	var j = 0;
	for (i = 0; i < num_fields.length; i++) {
		num_field = num_fields[i];
		state[j++] = checkNumber(num_field);
	}
	for (i = 0; i < email_fields.length; i++) {
		email_field = email_fields[i];
		state[j++] = checkEmail(email_field);
	}
   for (i = 0; i < password_fields.length; i++) {
		password_field = password_fields[i];
		state[j++] = checkPassword(password_field);
	}
	for (i = 0; i < nonempty_fields.length; i++) {
		nonempty_field = nonempty_fields[i];
		state[j++] = checkEmpty(nonempty_field);
	}

	valid = true;
	for (i = 0; i < j; i++) {
		valid = valid && state[i];
	}
	return valid;
}

function realtime_validator() {
	num_fields = getElementsByClassName(document, 'number_field');
	email_fields = getElementsByClassName(document, 'email_field');
	password_fields = getElementsByClassName(document, 'password_field');
	nonempty_fields = getElementsByClassName(document, 'nonempty_field');

	if (document.all) {
		for (i = 0; i < num_fields.length; i++) {
			var num_field = num_fields[i];
			num_field.onblur = function() {
				validateAll();
			};
		}
      for (i = 0; i < password_fields.length; i++) {
			var password_field = password_fields[i];
			password_field.onblur = function() {
				validateAll();
			};
		}
		for (i = 0; i < email_fields.length; i++) {
			var email_field = email_fields[i];
			email_field.onblur = function() {
				validateAll();
			};
		}
		for (i = 0; i < nonempty_fields.length; i++) {
			var nonempty_field = nonempty_fields[i];
			nonempty_field.onblur = function() {
				validateAll();
			};
		}
	} else {
		for (i = 0; i < num_fields.length; i++) {
			num_field = num_fields[i];
			num_field.setAttribute("onblur", "checkNumber(" + num_field.name
					+ ");");
		}
		for (i = 0; i < email_fields.length; i++) {
			email_field = email_fields[i];
			email_field.setAttribute("onblur", "checkEmail(" + email_field.name
					+ ");");
		}
		for (i = 0; i < password_fields.length; i++) {
			password_field = password_fields[i];
			password_field.setAttribute("onblur", "checkPassword(" + password_field.name
					+ ");");
		}
		for (i = 0; i < nonempty_fields.length; i++) {
			nonempty_field = nonempty_fields[i];
			nonempty_field.setAttribute("onblur", "checkEmpty("
					+ nonempty_field.name + ");");
		}
	}
}

