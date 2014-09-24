//
// Functions used by page navigation.
//
// By: Xin Chen
// Created on: 8/27/2013
//

function jumpToTopic(total, param_page) {
    var o = $("#gotoPage");
    if (o == null) return;

    var page = o.val().trim();
    if (page != '') {
        var p = parseInt(page);
        if (isNaN(p)) {
            alert('Not a page number: ' + page);
            o.focus();
        } else {
            if (p <= 0) p = 1;
            else if (p > total) p = total;
            //alert(page + ' of ' + total);
            window.location = getUrlFile() + '?' + getUrlParams(p - 1, param_page);
        }
    }
    else {
        o.focus();
    }
}

function getUrlFile() {
    var url = window.location.pathname;
    var filename = url.substring(url.lastIndexOf('/')+1);
    //alert(filename);
    return filename;
}

function getUrlParams(page, param_page) {
    var url = '';
    if (location.search) {
        var parts = location.search.substring(1).split('&');
        var t;

        for (var i = 0; i < parts.length; i++) {
            var nv = parts[i].split('=');
            if (!nv[0]) continue;

            t = '';
            if (nv[0] == param_page) { continue; }

            t = nv[0] + '=' + nv[1];
            if (url == '') url = t;
            else url += '&' + t;
        }

        t = param_page + '=' + page;
        if (url == '') url = t;
        else url += '&' + t;
    }
    return url;
}

