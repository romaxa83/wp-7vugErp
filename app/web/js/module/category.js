//смена статуса для категорий
$('body').on('change','.change-status-category',function(){
    var id = $(this).attr('data-id');
    var status = $(this).is(':checked');
    status === false ? status = 0 : status = 1;
    $.ajax({
        url:'/category/change-status',
        type:'post',
        data: {id:id,status:status},
        success: function(res){
            arrayId = JSON.parse(res);
            arrayId.forEach(function(element) {
                if(status === 1){
                    $('#category-status_' + element).prop('checked','checked');   
                }else{
                    $('#category-status_' + element).prop('checked',''); 
                }
            });
            warning('Success','Статус был изменен успешно','success');
        },
        error: function(res){
            warning('Error',res,'danger');
        }
    });
});
//создания категорий с формы создание товара 
$('.send-category').on('click', function (e) {
    $('.yiierp-loader').show();
    e.preventDefault();
    var form = $(this).closest('form');
    var chars = [];
    $('#category-charsname input:checked').each(function (i) {
        chars[i] = $(this).val();
    });
    $.ajax({
        url: '/category/create',
        data: form.serializeArray(),
        type: 'post',
        success: function (res) {
            var data = JSON.parse(res);
            if(data.type === 'error'){
                ShowMSG(data.msg);
            }else{
                var item = new Option(data.data.name, data.data.id, false, false);
                $('#product-category_id').append(item);
                warning('Success','Категория удачно создана','success');
            }
            form.trigger('reset');
        }
    });
    $('.yiierp-loader').hide();
});