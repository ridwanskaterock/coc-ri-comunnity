function showDialog(id){
    var dialog = $("#"+id).data('dialog');
    if (!dialog.element.data('opened')) {
        dialog.open();
    } else {
        dialog.close();
    }
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

$(function(){
    $('.sidebar').on('click', 'li', function(){
        if (!$(this).hasClass('active')) {
            $('.sidebar li').removeClass('active');
            $(this).addClass('active');
        };
    });

    //window.history.pushState('page2', 'Title', 'http://localhost/page2.php');

});