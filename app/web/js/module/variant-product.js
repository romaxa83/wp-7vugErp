//сбор данных , характеристик для формирование групп 
$('body').on('click','.variant-save-prod',function(e){
    e.preventDefault();
    var chars = $('.check-ajax').find('.char:checked');
    var data = {};
    var char = {};
    var i = 0;
    var key = '';
    data['id'] = $('.name-variant-product select').val();
    data['name'] = $('.name-variant-product #product-name :selected').text();
    data['amount'] = $('.field-product-min_amount').attr('data-amount');
    data['price1'] = $('#product-price1').val();
    data['price2'] = $('#product-price2').val(); 
    $.each(chars,function(){
        key = $(this).attr('data-id');
        char[i] = {
            key: key,
            value: $(this).attr('value')
        };
        i++;
    });
    xhr = true;
    if(data['name'] == '' || data['name'] == 'Виберите товар' || data['id'] == ''){
        xhr = false;
        warning('Ошибка','Товар для создания вариаций не был выбран ','danger');
    }
    if(data['amount'] == ''){
        xhr = false;
        warning('Ошибка','Количество товара не было передано','danger');
    }
    if(data['price1'] == ''){
        xhr = false;
        warning('Ошибка','Цена 1 не была заполненная','danger');
    }
    if(data['price2'] == ''){
        xhr = false;
        warning('Ошибка','Цена 2 не была заполненная','danger');
    }
    if(chars.length > 0){
        if(xhr == true){
            var id = $(this).closest('form').attr('id');
            $.ajax({
                url: '/variant-product/create-variant',
                type: 'post',
                data: {data:data,char:char},
                success: function(res){
                    res = JSON.parse(res);
                    $('#'+ id +'').hide();
                    $('.block-for-variant').empty().append(res);
                },
                error: function(){
                    warning('Ошибка','Указаны не всё данные','danger');
                }
            });
        }
    }else{
        warning('Ошибка','Выберите характеристику','danger');
    }
});
//показ таблицы вариативных товаров 
$('.show-var-prod').on('click',function() {
    var value = $(this).attr('data-id-product');
    if($(this).hasClass('remove-var-prod')){
        $('.custom-table > tbody').find('[data-product='+ value +']').remove();
        $(this).removeClass('remove-var-prod');
    }else{
        $('.yiierp-loader').show();
        $(this).addClass('remove-var-prod');
        var price_type = $('.table-var-prod').attr('data-price-type');
        $.ajax({
            url: '/variant-product/get-var-prod',
            data: {'value':value,'price_type':price_type},
            type: 'post',
            success: function(res) {
                $('.yiierp-loader').hide();
                console.log($('.custom-table > tbody').find('[data-key='+ value +']'));
                $('.custom-table > tbody tr[data-key='+ value +']').after(res);
            }
        });
    }
});
//возрат с списка вариативных товаров в форму создания продукта
$('body').on('click','.back-to-create',function(e){
    e.preventDefault;
    if($('#w3').length > 0){
        $('#w3').show();
    }else{
        $('#w2').show();
    }
    $('.block-for-variant').empty();
});
//евент удаления позиций вариаций
$('body').on('click','.delete-variant-in-create',function(e){
    e.preventDefault;
    var balance = parseInt($('.balance').text());
    var tr = $(this).closest('tr');
    balance = balance + parseInt(tr.find('.variant_amount').val());
    $('.balance').text(balance);
    tr.remove();
});
//евент отслежует что бы суммарное количесвто вариаций при создание не привышали кол-во базового
$('body').on('change','.variant_amount',function(){
    var balance = parseInt($('.balance').text());
    var change = $(this).val() - $(this).attr('data-previous');
    if(change > balance){
        $(this).val($(this).attr('data-previous'));
        $(this).attr('data-previous', $(this).val());
        warning('error','вы пытаетесь добавить товара больше чем есть в наличий','danger');
    }else{
        if($(this).val() > $(this).attr('data-previous')){
           balance = balance - change;
           $(this).attr('data-previous', $(this).val());
           $('.balance').text(balance);
        }else{
           balance = balance + (-change);
           $(this).attr('data-previous', $(this).val());
           $('.balance').text(balance);
        }
    }    
});