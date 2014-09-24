//
// Javascript for register function.
//
// @Created on: 7/23/2013
// @Last modified: 7/23/2013
//

$('#txt_first_name').focus();

function changeCaptcha(o) {
    document.getElementById('imgCaptcha').src = "../func/captcha.php?" + Math.random();
}

function validate_reg() {
    if ($('#e_captcha').length == 1) { $('#e_captcha').html(''); }
    if ($('#e_reg_code').length == 1) { $('#e_reg_code').html(''); } // do only when id exists.
    $('#e_passwd').html('');
    $('#e_login').html('');
    $('#e_email').html('');
    $('#e_last_name').html('');
    $('#e_first_name').html('');

    var ok = 1;
    if ($('#e_captcha').length == 1 && $.trim( $('#txtCaptcha').val() ) == '') {
        $('#e_captcha').html('Cannot be empty');
        $('#txtCaptcha').focus();
        ok = 0;
    }
    //if ($('#txt_reg_code').length == 1 && validate_reg_code() == 0) { // do only when id exists.
    if ($('#txt_reg_code').length == 1 && check_valid('reg_code') == 0 ) {
        $('#txt_reg_code').focus();
        ok = 0;
    }
    if (validate_passwd() == 0) {
        $('#txt_passwd').focus();
        ok = 0;
    }
    //if (validate_login() == 0) {
    if (check_valid('login') == 0) {
        $('#txt_login').focus();
        ok = 0;
    }
    //if (validate_email() == 0) {
    if (check_valid('email') == 0) {
        $('#txt_email').focus();
        ok = 0;
    }
    if ($.trim( $('#txt_last_name').val() ) == '') {
        $('#e_last_name').html('Cannot be empty');
        $('#txt_last_name').focus();
        ok = 0;
    }
    if ($.trim( $('#txt_first_name').val() ) == '') {
        $('#e_first_name').html('Cannot be empty');
        $('#txt_first_name').focus();
        ok = 0;
    }

    if (ok) {
        document.forms[0].submit();
    }
}

function validate_email() {
    var ok = 1;
    var v = $.trim( $('#txt_email').val() );

    if (v == '') {
        $('#e_email').html('Cannot be empty');
        ok = 0;
    }
    else if (! /^([a-zA-Z0-9]+[._-])*[a-zA-Z0-9]+@[a-zA-Z0-9-_\.]+\.[a-zA-Z]+$/.test( v ) ) {
        $('#e_email').html('Invalid email');
        ok = 0;
    }

    return ok;
}

function validate_login() {
    var ok = 1;
    var v = $.trim( $('#txt_login').val() );

    if (v == '') {
        $('#e_login').html('Cannot be empty');
        ok = 0;
    }
    else if (v.length < 4) {
        $('#e_login').html('Length should &gt;= 4');
        ok = 0;
    }
    else if( /[^a-zA-Z0-9]/.test( v ) ) {
        $('#e_login').html('Contains invalid character');
        ok = 0;
    }
    else if( /^[^a-zA-Z]/.test( v ) ) {
        $('#e_login').html('Should start with a letter');
        ok = 0;
    }

    return ok;
}

function validate_passwd() {
    var ok = 1;
    var v = $.trim( $('#txt_passwd').val() );
    var v2 = $.trim( $('#txt_2_passwd').val() );

    if (v != v2) {
        $('#e_passwd').html('Two passwords not equal');
        ok = 0;
    }
    else if (v == '') {
        $('#e_passwd').html('Cannot be empty');
        ok = 0;
    }
    else if (v.length < 8) {
        $('#e_passwd').html('Length should &gt;= 8');
        ok = 0;
    }
    else if( /[^a-zA-Z0-9]/.test( v ) ) {
        $('#e_passwd').html('Contains invalid character');
        ok = 0;
    }
    return ok;
}

function validate_reg_code() {
    var ok = 1;
    var v = $.trim( $('#txt_reg_code').val() );
    if (v == '') {
        $('#e_reg_code').html('Cannot be empty');
        ok = 0;
    }
    return ok;
}

function check_valid(id) {
    var v, o, msg;
    if (id == "login") {
        if (! validate_login()) return 0;
        v = $.trim( $("#txt_login").val() );
        o = $("#e_login");
        msg = 'Login is used';
    }
    else if (id == "email") {
        if (! validate_email()) return 0;
        v = $.trim( $("#txt_email").val() );
        o = $("#e_email");
        msg = 'Email is used';
    }
    else if (id == "reg_code") {
        if (! validate_reg_code()) return 0;
        v = $.trim( $("#txt_reg_code").val() );
        o = $("#e_reg_code");
        msg = 'Invalid code';
    }
    else {
        return;
    }
    //alert('check: ' + id);

    $.post("check_valid.php", { f: id, v: v }, function(data, status) {
        if (status == "success") {
            o.html(data == 0 ? msg : ''); // show alert only when it exists.
            return data == 1; // if 1, valid. else not.
        } else {
            o.html(''); // cannot connect to server. show nothing. leave check to submit.
            return 1; // ok
        }
    }, 5);
}


