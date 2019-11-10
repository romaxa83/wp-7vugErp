$('.сontrol-access-status').click(function(){
    var id = $(this).data("id");
    var value = +$(this).prop('checked');
    $.ajax({
        url: '/access/set-status',
        type: 'POST',
        data: {id:id, value:value}
    });
});