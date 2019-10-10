function checkAmountRequest(obj){
    var parent = obj.parent().parent();
    var amount_on_sklad = parent.find('.amount-on-stock').text();
    var amount = parent.find('.product-request-amount').val();
    if(parseInt(amount_on_sklad) < parseInt(amount)){
        warning('Вы добавляете товара больше','чем есть на складе');
        parent.css('outline','1px solid red');
        obj.prop('checked',false);
    }else{
        parent.css('outline','1px solid transparent');
    }
}
//наполнения транзакций товарами с заявки менеджера
function FillingTransFromProductRequest(id = null,store = null){
    var xhr = true;
    var products = $('.confirm-product');
    var arr_product = [];
    products.each(function(){
        if($(this).is(":checked")){
            var parent = $(this).parent().parent();
            var prod_id = parent.data('product-id');
            var vprod_id = parent.data('vproduct-id');
            var mid_arr = [];
            mid_arr.push(prod_id,vprod_id);
            arr_product.push(mid_arr);
        }
    });
    var data = {
        transaction_id: id,
        arr_product: arr_product,
        store_id: store,
        request_id: $('.table-request-body').data('request-id')
    };
    if(arr_product.length === 0) {
        xhr = false;
        warning('Не выбран товар для формировании транзакции', 'Потвердите товар');
    }
    if(xhr){
        $.ajax({
            url:'/manager/admin/filling-transaction',
            type:'post',
            data: data,
            success: function(res){
                if(res){
                    data = JSON.parse(res);
                    for(index in data){
                       warning('error',data[index]['error'][0] + ' для ' + data[index]['name']);
                    }
                }
                setTimeout(function(){
                    location.reload();
                },1000);
            }
        });
    }
}
//показать/спрятать товары с остатком 0 
$('#empty-in-stock').on('click', function () {
    $('.amount-on-stock[data-value="0"]').parent().toggleClass('hidden');
});
//удалить 1 товар из заявки
$('.remove-product-request').on('click', function () {
    var product = $(this).closest('tr');
    $.ajax({
        url: '/manager/admin/delete-row',
        async: false,
        type: 'post',
        data: {
            request_id : product.attr('data-request-id'),
            product_id : product.attr('data-product-id')
        },
        success: function(res){
            data = JSON.parse(res);
            if(data.status){
                product.remove();
                warning('success','Удаления успешно','success');
            }else{
                warning('error','Произошел баг','error');
            }
        }
    });
});
//очистка заявки
$('.clear-request').on('click',function(){
    var request_id = $(this).attr('data-request-id');
    confirm = confirm('Очистить тело заявки ?');
    if(confirm){
        $.ajax({
            url: '/manager/admin/clear-request',
            async: false,
            type: 'post',
            data: {request_id : request_id}
        });
    }
});
//потверждает все продукты
$('.confirm-all-product').on('click',function(){
    var check = $(this).is(":checked") ? 1 : 0;
    var checkbox = $('.confirm-product');
    checkbox.each(function(){
        var amount = $(this).closest('tr').find('.amount-on-stock').text();
        if(amount != 0){
            if(check == 1){
                $(this).prop('checked',true);
                checkAmountRequest($(this));
            } else {
                $(this).prop('checked',false);
            }
        }
    });
});
//потверждает 1 продукт
$('.confirm-product').on('click',function(){
    checkAmountRequest($(this));
});
//создания пустого обьекта operations . Вслучай удачного выполнения наполнения обьектаы
$('.create_transaction').on('click',function(){
    var store = $('.table-product-request-admin').data('store-id');
    if($('.confirm-product:checked').length > 0){
        $('.yiierp-loader').show();
        $.ajax({
            url: '/manager/admin/create-empty-transaction',
            type: 'post',
            data: {store_id : store},
            success: function(id){
                FillingTransFromProductRequest(id,store);
            }
        });
    }else{
        warning('Не выбран товар для формировании транзакции', 'Потвердите товар');
    }
});