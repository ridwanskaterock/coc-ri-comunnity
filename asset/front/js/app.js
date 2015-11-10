function showDialog(id){
    var dialog = $("#"+id).data('dialog');
    if (!dialog.element.data('opened')) {
        dialog.open();
    } else {
        dialog.close();
    }
}   

function hideDialog(id){
    var dialog = $("#"+id).data('dialog');
    dialog.close();
}

function cekLogin() {
    if (sessionUser) {
        return true;
    } else {
        $.Notify({
            caption: '',
            content: 'Please login first',
            type: 'warning'
        });

       showDialog('dialogLogin');
       return false;
   }
}

function pushMessage(t){
    var mes = 'Info|Implement independently';
    $.Notify({
        caption: mes.split("|")[0],
        content: mes.split("|")[1],
        type: t
    });
}


$.fn.loader = function(opsi){
    var opsi = $.extend({
        jarak       : 200,
        kecepatan   : 2000
    },opsi);
    $(this).html("<center><img src=\"<?= BASE_ASSET; ?>admin/img/ajax-loader1.gif\"></center>")
}

$.fn.inputEnabled = function(opsi){
    $(this).removeAttr('disabled');
};

$.fn.inputDisabled = function(opsi){
    $(this).prop('disabled', 'true');
};

    
$.fn.loader = function(opsi){
    $(this).html("<center ><img style=\"height:50px; width:50px; position:absolute; margin-top:-50px;margin-left:-25px;\" src=\"<?= BASE_ASSET; ?>admin/img/ajax-loader1.gif\"></center>");
};



$(function(){


    $('.sidebar').on('click', 'li', function(){
        if (!$(this).hasClass('active')) {
            $('.sidebar li').removeClass('active');
            $(this).addClass('active');
        };
    });


    //window.history.pushState('page2', 'Title', 'http://localhost/page2.php');

});