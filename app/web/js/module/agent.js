/*
 * построения списка агентов 
 * принимает data - массив агентов 
*/        
function BuildAgentList(data){
    var select = $('#drop_for_search');
    var option = JSON.parse(data);
    var html = '';
    if(option.length > 0){
        html = '<ul>';
        for(i=0;i<option.length;i++){
            html += '<li class="item_search_provider" data-id="'+ option[i].id +'">' + option[i].firm + '</li>';
        }
        html += '</ul>';
    }else {
        html = '<ul><li>Поставшик не найден , добавте его! </li></ul>';
    }
    select.show().empty().append(html);
}
//создания агента с формы создания продукта
$('.send-agent').on('click', function (e) {
    $('.yiierp-loader').show();
    e.preventDefault();
    var form = $(this).closest('form');
    var firm = $('#agent-firm').val();
    var name = $('#agent-name').val();
    var address = $('#agent-address').val();
    var telephone = $('#agent-telephone').val();
    var data = $('#agent-data').val();
    var val = {'firm': firm, 'name': name, 'address': address, 'telephone': telephone, 'data': data};
    $.ajax({
        url: '/agent/create',
        type: 'post',
        data: {Agent: val},
        success: function (res) {
            if(res){
                var data = JSON.parse(res);
                if(data.type === 'error'){
                    ShowMSG(data.msg);
                }else{
                    var item = new Option(data.data.firm, data.data.id, false, false);
                    $('#product-agent_id').append(item);
                    warning('Success','Поставщик удачно создан','success');
                }
                form.trigger("reset");
            }
        }
    });
    $('.yiierp-loader').hide();
});
//смена статуса для агента
$('.change-status-agent').on('change',function(){
    var id = $(this).attr('data-id');
    var status = $(this).is(':checked');
    status === false ? status = 0 : status = 1;
    $.ajax({
        url:'/agent/change-status',
        type:'post',
        data: {id:id,status:status},
        success: function(res){
            if(res){
                warning('Success','Статус был изменен успешно','success');
            }
        },
        error: function(res){
            warning('Error',res,'danger');
        }
    });
});
//смена цены 
$('.new-price').on('click',function(){
   var id = $(this).attr('id');
   var price = $('.price-for-' + id + ' :selected').val();
    $.ajax({
        type: 'post',
        url:'/agent/change-price',
        data:{'id':id,'price':price},
        success:function() {
           location.reload();
        }
    });
});