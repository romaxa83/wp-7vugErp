
$('.mass-archive').on('click',function(){
    var checkAll = confirm('Выбрать всё записи на странице?');
    $('.mass-archive-checkbox').show();
    if(checkAll){
        $('.mass-archive-checkbox').attr('checked','checked');
    }
    $('.show-archive-panel').hide();
    $('.panel-archive').show();
});

$('.send-mass-archive').on('click',function(){
    var checkBox =  $('.mass-archive-checkbox');
    var checked = [];
    $.each(checkBox,function(){
        var item = $(this);
        if(item.is(':checked')){
            checked.push(item.closest('tr').attr('data-key'));
        }
    });
    if(checked){
        $.ajax({
            url : '/operation/mass-archive',
            type : 'post',
            data : {transactionId : checked},
        });
    }
});

$('.cancel-mass-archive').on('click',function(){
    $('.mass-archive-checkbox').removeAttr('checked');
    $('.mass-archive-checkbox').hide();
    $('.show-archive-panel').show();
    $('.panel-archive').hide();
});

$('.table').on('click','.send-archive-one',function(){
    $('.yiierp-loader').show();
    $.ajax({
        url : '/operation/send-in-archive',
        type : 'post',
        data : {id : $(this).attr('data-id')},
        success : function(res){
            var data = JSON.parse(res);
            if(data.status != 'error'){
                $.ajax({
                    url : '/operation/send-in-archive-value',
                    type : 'post',
                    data : {id : data}
                });
            }else{
                location.reload();
            }   
        }
    });
});