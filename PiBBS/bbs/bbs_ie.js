// 
// Adjustment of format using jquery for IE.
// Used in index.php and forum.php
//
// nth-child selector does not work in IE 6,7,8.
// It's possible to specify even/odd tr's class, then use css to format the rows.
// But this jquery works too and is more clean.
//
// To use this, include as:
// <!--[if IE]><script type="text/javascript" src="bbs_ie.js"></script><![endif]-->
//
// @By: Xin Chen
// @Created on: 8/29/2013
// @Last modified: 8/29/2013
//

$(document).ready(function() {
    if ($("#bbs_forum_list").length > 0) {
        //alert('id found');
        $("#bbs_forum_list tr:nth-child(odd)").css('background-color', '#eeeeee');

        $("#bbs_forum_list tr:nth-child(odd)").not('tr:first').hover(
            function() {$(this).css('background-color', '#ccccff');}, 
            function() {$(this).css('background-color', '#eeeeee');}
        );

        $("#bbs_forum_list tr:nth-child(even)").hover(
            function() {$(this).css('background-color', '#ccccff');},
            function() {$(this).css('background-color', '#ffffff');}
        );

        $("#bbs_forum_list tr:nth-child(1)").css('background-color', '#ccccff');
        $("#bbs_forum_list tr").css({
            'height': '25px', 
            'vertical-align': 'middle', 
            'display': 'block'
        });
        
    }

    if ($("#bbs_post").length > 0) {
        $("#bbs_post tr:nth-child(odd)").css('background-color', '#eeeeee');
    }

    if ($("#bbs_user").length > 0) {
        $("#bbs_user tr:nth-child(odd)").css('background-color', '#eeeeee');
        $("#bbs_user td:nth-child(1)").css({
            'width': '150px',
            'text-align': 'right',
            'padding-right': '10px'
        });
        $("#bbs_user td:nth-child(2)").css({
            'text-align': 'left',
            'padding-left': '10px'
        });
        $("#bbs_user tr:nth-child(odd)").hover(
            function() {$(this).css('background-color', '#ccccff');},
            function() {$(this).css('background-color', '#eeeeee');}
        );
    }
});
