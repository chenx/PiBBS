//
// Javascript for register function.
//
// @Created on: 7/23/2013
// @Last modified: 7/23/2013
//


function validate(submit) {
    $('#e_title').html('');
    $('#e_body').html('');
    $('#e_source').html('');

    var ok = 1;
    if ($.trim( $('#txt_body').val() ) == '') {
        $('#e_body').html('不能为空');
        $('#txt_body').focus();
        ok = 0;
    }
    if ($.trim( $('#txt_title').val() ) == '') {
        $('#e_title').html('不能为空');
        $('#txt_title').focus();
        ok = 0;
    }

    if (ok) {
        if (submit) $('#btnPreview').val('submit');
        //alert($('#btnPreview').val());
        document.forms[0].submit();
    }
}


function mgr_toggle(forum_id, action, type, id, thread_id, user_id, mode) {
    mode = mode ? '1' : '0';
    //alert('mgr: ' + forum_id + ', ' + action + ', ' + id + ', ' + mode);

    $.post("bbs_mgr.php", { f: forum_id, a: action, t:type, i: id, h: thread_id, u: user_id, m: mode }, function(data, status) {
        if (status == "success") {
            //alert(data);
            // data == 1: success, show color as green; else show color as pink.
            var color = (data == 1) ? '#eeffee' : '#ffeeee';
            $("#m" + action + "_" + id).css('background-color', color);
            return data == 1; // if 1, valid, else not.
        } else {
            alert("cannot connect to server");
            $("#m" + action + "_" + id).css('background-color', '#ffeeee');
            return 1;
        }
    }, 5);

}


function DoSearch(url_params) {
    var v = encodeURIComponent( $.trim( $("#searchTxt").val() ) );
    if (v == '') {
        $("#searchTxt").focus();
    } else {
        window.location = 'search.php?' + url_params + '&k=' + v;
    }
}

function OnEnterSearch(e, url_params) {
    var key = e.keyCode || e.which;

    if (key === 13) {
        DoSearch(url_params);
    }
    return false;
}


//
// Toggle help message for post.
// Called in bbs_terms_cn/en.php.
//
function toggle_help() {
    var m = document.getElementById("bbs_help_mode");
    var h = document.getElementById('bbs_help');
    if (h == null || m == null) return;
    if (m.innerHTML == '+') { 
        h.style.display='block'; 
        m.innerHTML = '-';
    }
    else { 
        h.style.display='none'; 
        m.innerHTML = '+';
    }
}

