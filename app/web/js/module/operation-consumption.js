//заполнения таблицы добавления товара
$('.table-form-product').on('change','.consumption #operconsumption-product_id',function(){
    var id = $(this).val();
    var transaction = $('#operconsumption-transaction_id').val();
    if(id){
        $.ajax({
            url : '/operation/get-product-data',
            type : 'post',
            data : {id : $(this).val(), transaction: transaction, type: 'consumption', typePrice : $('.table-form-product').attr('data-type-price')},
            success: function(res){
                var data = JSON.parse(res);
                if(data.status === 'exist'){
                    warning('Error','Данный продукт уже присутствует в транзакций');
                }else {
                    $('#operconsumption-transaction_id').removeAttr('disabled');
                    $(".stock-amount input").val(data.product.amount);
                    $('#operconsumption-amount').val(0);
                    if(data.variant === true){
                        $('#add-product-form').attr('action', '/operation-consumption/add-v-product');
                        $('#operconsumption-amount').attr('disabled',true);
                        $('#operconsumption-price').attr('disabled',true);
                        $('#product_variant_table').html(data.html);
                    }else{
                        $('#add-product-form').attr('action', '/operation-consumption/add-product');
                        var price = $('.table-form-product').attr('data-type-price') == 1 ? data.product.price1 : data.product.price2;
                        var float = $('.table-form-product').attr('data-float-usd');
                        $('#operconsumption-amount').removeAttr('disabled');
                        $('#operconsumption-price').val(parseFloat(price).toFixed(float)).removeAttr('disabled');
                        $('#product_variant_table').empty();
                    }
                }
            }
        });
    }
});
//проверка на то что бы расход не привышал количества вариативного товара на складе 
$('#product_variant_table').on('change','.amount-var-prod-consumption',function(){
    var stock = parseInt($(this).closest('tr').find('.stock-amount-variant-consumption').text());
    if(parseInt($(this).val()) > stock){
        warning('Error','Вы добавляете товара больше,чем есть на складе');
        $(this).val(stock);
    }
});
//изменения типы цены при изменения магазина
$('#form-where-whence #operations-where').on('change',function(){
    $.ajax({
        url : '/agent/get-price-type',
        type : 'post',
        data : {id : $(this).val()},
        success: function(res){
            $('.table-form-product').attr('data-type-price',res);
            warning('Успех','магазин изменён, следущий товары будут добавлены по price'+ res,'success');
        }
    });
});
//триггер на добавления товара
$('#trigger-consumption').on('click',function(e){
    e.preventDefault();
    $('.add-prod-consumption').click();
});
//удаления товара 
$('.table-info').on('click','.btn-delete-product-consumption',function(){
    var tr = $(this).closest('tr');
    var baseId = tr.attr('data-base-id'); 
    var variantId = tr.attr('data-variant-id');
    var transactionId = window.location.search.substr(1).split('=');
    $.ajax({
        url : '/operation-consumption/delete-product',
        type : 'post',
        async : false,
        data : {base : baseId, variant : variantId, transaction : transactionId[1]},
        success : function(res){
            warning('success','Товар удален','success');
            var data = JSON.parse(res);
            $('.summ').text(' --- '+ data.total_price.ua +' UAH / '+ data.total_price.usd +' USD');
            tr.remove();
        }
    });
});