//заполнения таблицы добавления товара
$('#table-add-prod').on('select2:select','#opercoming-product_id',function(){
    var id = $(this).val();
    var transaction = $('#opercoming-transaction_id').val();
    var float_ua = $('#table-add-prod').attr('data-float-ua');
    var float_usd = $('#table-add-prod').attr('data-float-usd');
    if(id){
        $.ajax({
            url : '/operation/get-product-data',
            type : 'post',
            data : {id : $(this).val(), transaction: transaction, type: 'coming'},
            success: function(res){
                var data = JSON.parse(res);
                if(data.status === 'exist'){
                    warning('Error','Данный продукт уже присутствует в транзакций');
                } else {
                    $('#product_variant_table').empty();
                    $("input[name='OperComing[amount]']").val(data.product.amount).removeAttr('disabled').focus();
                    $("input[name='OperComing[start_price_ua]']").val(parseFloat(data.product.start_price_uah).toFixed(float_ua)).removeAttr('disabled');
                    $("input[name='OperComing[start_price]']").val(parseFloat(data.product.start_price).toFixed(float_usd)).trigger('input').removeAttr('disabled');
                    $("input[name='OperComing[price1]']").val(parseFloat(data.product.price1).toFixed(2)).removeAttr('disabled');
                    $("input[name='OperComing[price2]']").val(parseFloat(data.product.price2).toFixed(2)).removeAttr('disabled');
                    $("input[name='OperComing[product_id]']").val(data.product.id).removeAttr('disabled');
                    $("input[name='OperComing[type]']").val('p').removeAttr('disabled');
                    $("input[name='OperComing[transaction_id]']").removeAttr('disabled');                    
                    $('#add-product-form').attr('action', '/operation-coming/add-product');
                    if(data.variant === true){
                        $("input[name='OperComing[amount]']").attr('disabled', 'true');
                        $("input[name='OperComing[price1]']").attr('disabled', 'true');
                        $("input[name='OperComing[price2]']").attr('disabled', 'true');
                        $('#add-product-form').attr('action', '/operation-coming/add-v-product');
                        $('#product_variant_table').html(data.html);
                    }
                }
            }
        });
    }
});
//сброс фильтра
$('#product_variant_table').on('click','.reset_filter_chars',function(){
    var rows = $('.variant_product_row');
    var filters = $('.filter_chars');
    rows.each(function(){
        $(this).show();
    });
    filters.each(function(){
        $(this).find(':first').attr('selected','selected');
    });
});
//конвертация с usd в uah 
$('#add-product-form').on('input', '#opercoming-start_price',function () {
    var curr = $('#table-add-prod').attr('data-currency-usd');
    var usd = $(this).val();
    var float_ua = $('#table-add-prod').attr('data-float-ua');

    $('#ua-price').val(parseFloat(usd * curr).toFixed(float_ua));
});
//конвертация с uah в usd 
$('#add-product-form').on('input', '#ua-price',function () {
    var curr = $('#table-add-prod').attr('data-currency-usd');
    var ua = $(this).val();
    var float_usd = $('#table-add-prod').attr('data-float-usd');

    $('#opercoming-start_price').val(parseFloat(ua / curr).toFixed(float_usd));
});
//визуальный подсчет обшего количества вариаций 
$('#product_variant_table').on('input','.amount-var-prod-coming',function(){
    var amountBase = $("input[name='OperComing[amount]']");
    var amountVariant = $('.amount-var-prod-coming');
    var amount = 0;
    $.each(amountVariant,function(){
        if($(this).val() > 0){
            amount = amount + parseInt($(this).val());
        }
    });
    amountBase.val(amount);
});

$('.table-info').on('click','.btn-delete-product-coming',function(){
    var tr = $(this).closest('tr');
    var baseId = tr.attr('data-base-id'); 
    var variantId = tr.attr('data-variant-id');
    var transactionId = window.location.search.substr(1).split('=');
    $.ajax({
        url : '/operation-coming/delete-product',
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