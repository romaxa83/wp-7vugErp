//создания vendor code из agent_id и category_id
function FormattedVendorCode() {
    route = location.pathname.split('/');
    if(route[2] === 'index' || route[2] === 'create'){
        var id_prod = $('#product-vendor_code').attr('data-id');
        id_prod = (id_prod === undefined) ? '000' : id_prod;

        var id_cat = $('#product-category_id').val();
        var id_agent = $('#product-agent_id').val();

        if(id_cat.length === 0){
            id_cat = '000';
        }else if(id_cat.length === 1){ 
            id_cat = '00' + id_cat;
        }else if(id_cat.length === 2){
            id_cat = '0' + id_cat;
        }

        if(id_agent.length === 0){
            id_agent = '000';
        }if(id_agent.length === 1){
            id_agent = '00' + id_agent;
        }else if(id_agent.length === 2){
            id_agent = '0' + id_agent;
        }

        var vendor = id_prod + id_cat + id_agent;
        $('#product-vendor_code').val(vendor);
    }
}
//создания списка характеристик
function FormattedCharsList(chars,charsName){
    var html = '';
    for(var indexChars in charsName){
        html += '<div class="groupChars">';
        html += '<div class="header-char expand-plus form-control collapsed" data-toggle="collapse" data-target="#body-char_'+ indexChars +'" data-id="'+ indexChars +'">'+ charsName[indexChars] + '</div>';
        html += '<div id="body-char_'+ indexChars +'" class="collapse">';
        $.each(chars[indexChars], function(index, value) {
            html += '<input class="char" type="checkbox" value="'+ index +'"  data-id="'+ indexChars +'" style="margin-left: 10px;">';
            html += '<span class="label_char">'+ value +'</span>';
        });         
        html += '</div>';
        html += '</div>';
    }
    return html;
}
//создания списка базовых продуктов для вариативных товаров
function FormattedProductList(product = null){
    var item = [];
    item.push(new Option('Виберите товар', '', false, false));
    if(product !== null){
        product.forEach(function(element){
            item.push(new Option(element.name, element.id, false, false));
        });
    }
    return item;
}
var timeout; 
//подтягивание продуктов
function get_variants(variant,cat = null) {
    if(timeout){
        clearTimeout(timeout);
    }
    timeout = setTimeout(
        function(){
            if(variant == 1){
                $('.name-base-product').show();
                $('.label-for-characteristics').hide();
                $('.name-variant-product .field-product-name').hide();
                $('.name-variant-product select,.name-variant-product label,.name-variant-product .select2-container').hide();
            }else{
                $.ajax({
                    url: '/product/get-data-for-create-variant-product',
                    type: 'post',
                    data: {category: cat},
                    success: function (res) {
                        var data = JSON.parse(res);
                        console.log(data);
                        $('.name-base-product').hide();
                        $('.name-variant-product .field-product-name').show();
                        $('.name-variant-product select').empty().select2({width: '100%'}).append(FormattedProductList(data.product));
                        if(data.type == 'empty'){
                            $('.name-variant-product .help-block').show().empty();
                            if($('#product-category_id').val() == ''){
                                $('.name-variant-product .help-block').append('Выберите категорию').css('color','red');
                            }else{
                                $('.name-variant-product .help-block').append(data.text).css('color','red');
                            }
                        }else{
                            $('.label-for-characteristics').show();
                            $('.name-variant-product select,.name-variant-product label').show();
                            $('.name-variant-product .help-block').hide().empty();
                            $('.check-ajax').html(FormattedCharsList(data.chars,data.charsName));
                        }
                    }
                });
            }   
        }
    ,100);
}
// рекурсивная функция запросов для экспорта товаров
function AjaxForExport(i,type) {
    var count = $('.csv_export').data('count') + 1;
    $.ajax({
        url: '/product/export',
        type: 'post',
        data: {offset: i,countPage: count,kind: type},
        beforeSend: function(){
            i++;
            $('.count-page-loader .limiter').text(i);
        },
        success: function(res){
            res = JSON.parse(res);
            if(i != count){
                AjaxForExport(i,res.kind);
            }else{
                $('.count-page-loader').remove();
                var url = "/uploads/product.csv";
                if(res.kind == 'excel'){
                    url = "/uploads/product.xls";
                }
                location.href = url;
                setTimeout(function () {
                    $.ajax({
                        url: '/product/del-export',
                        type: 'post',
                        data: {type: type}
                    });
                }, 5000);
                $('.yiierp-loader').hide();
            }
        },
        error: function(res){
            console.log(res);
        }
    });
}
//сохранения измененой категорий 
var select;
function applyEdit(){
    if(select.val()){
        var NewCategoryId = select.select2('val');
        var ProductId = select.closest('tr').attr('data-key');
        $('.yiierp-loader').show();
        $.ajax({
            url : '/product/change-category',
            type : 'post',
            data : {id : NewCategoryId,ProductId : ProductId},
            success : function(res){
                var CategoryName = JSON.parse(res);
                $(select).off('change');
                select.closest('tr').find('.category-name').text(CategoryName);
                $('.yiierp-loader').hide();
            }
        });
    }        
}
//инициализация subselect после нажатия на категорию товара и при условий включеного режима редактирования
$('.category-name').on('click',function(){
    select = $(this).next('.life-edit-category-body').SubSelect2({
        'width' : 500, 
        'flag' : true, 
        'flagTarget' : '.switch-edit-mode',
        'data' : {
            'url' : '/category/get-list-category',
            'type' : 'post'
        },
        'placeholder' : {
            'text' : ''
        },
        'open' : true
    });
    $(".table-fix").DataTable().draw();
    $(select).on('change',applyEdit);
    $(select).on('select2:close',function(){
        select.SubSelect2('destroy');
        $(".table-fix").DataTable().draw();
    });
});
//вкл/выкл режим редактирования 
$('.switch-edit-mode').on('change',function(){
    var status = $(this).prop('checked');
    if(!status){
        $(select).trigger('select2:close');
    }
    $.ajax({
        url : '/base/switch-edit-mode',
        type : 'post',
        data : {status : status},
        success : function(){
           warning('Success',(status) ? 'Режим редактора успешно включен' : 'Режим редактора успешно выключен','success');
        }
    });
});
//евент на измениния создания с вариативного на базовый и обратно
$('#product-is_variant').on('change', function () {
    var variant = $(this).val();
    var id = $(this).closest('form').attr('id');
    var category = $('#product-category_id').select2('val');
    $('.save-prod').toggleClass('variant-save-prod');
    if (variant == 1) {
        $('#product-agent_id').prop('disabled',false);
        $('#product-category_id').prop('disabled',false);
        $('.check-ajax').empty();
        $('.label-for-characteristics').toggleClass('hidden');
    }
    var product = $('#product-category_id').select2('val');
    $('#'+ id +'').trigger("reset");
    $(this).val(variant);
    $('#product-vendor_code').val('000000000');
    get_variants(variant,category);
});
// сохранение старой категории для отмены
$('.field-product-category_id').on('click',function(){
    $('#product-category_id').attr('data-old', $('#product-category_id').val());
});
//при изменений категорий изменить vendor code и если создания вариативных подтянуть список товаров для создания вариций
// подтверждение изменения артикула
$('#product-category_id').on('select2:select',function(){
    if($('#product-is_variant').val() == 2){
        get_variants($('#product-is_variant').val(),$(this).select2('val'));
    }
    FormattedVendorCode();
});
//выборе базового товара при создание вариативного подтягивание цен и тд.
$('body').on('change','#product-name',function(){
    $.ajax({
        url: '/product/get-product-values',
        type: 'post',
        data: {name: $(this).val()},
        success: function (res) {
            var data = JSON.parse(res);
            if(data){
                var float = $('.content-wrapper').attr('data-float-uah');
                $('.field-product-min_amount').attr('data-amount',data.product.amount);
                
                if (confirm("Найден такой товар, Подтянуть его характеристику?")) {
                    $('#product-price1').val(parseFloat(data.product.price1).toFixed(float));
                    $('#product-price2').val(parseFloat(data.product.price2).toFixed(float));
                    $('#product-agent_id').val(data.product.agent_id).trigger('change');
                    $('#product-vendor_code').val(data.product.vendor_code);
                    $('#product-min_amount').val(data.product.min_amount);
                    $('#product-margin').val(data.product.margin);
                    //get template category and clear name for base product
                    if($('#product-is_variant').val() == 1){
                        $('#product-category_id').val(data.product.category_id).trigger('change');
                        $('#product-name').val('');
                    }
                }
                
                data.product.vproducts.forEach(function(el){
                    var index = Object.keys(el.char_arr);
                    for (var i = 0;i < index.length;i++) {
                        $('#body-char_' + index[i]).find('[value = "'+ el.char_arr[index[i]] +'"]').prop({'checked': true,'disabled':true});
                    }
                });
            }
        }
    });
});
// сохранение старого агента для отмены
$('.field-product-agent_id').on('click',function(){
    $('#product-agent_id').attr('data-old', $('#product-agent_id').val());
});
//при выборе агента изменить vendor code
// подтверждение изменения артикула
$('#product-agent_id').on('change',function(){
    FormattedVendorCode();
});
//при изменения цены 1 вставить значения в цену 2
$('#product-price1').on('change keyup',function(){
   $('#product-price2').val($(this).val()); 
});
// экспорт товаров
$('body').on('click','.export',function () {
    $('.yiierp-loader').show();
    var type = $(this).data('type');
    var i = 0;
    $('.yiierp-loader').append('<div class="count-page-loader">' + '<span class="limiter">' + i + '</span> / ' + ($('.csv_export').data('count') + 1) + '</div>');
    $.ajax({
        url: '/product/del-export',
        type: 'post',
        data: {type: type},
        success: function(){
            AjaxForExport(i,type);
        }
    });
});
//временный блок , пока не будет предоставленая полная логика export 
$('.show-panel[data-panel="import"]').on("click", function (e) {
    e.preventDefault;
    $('#import-panel .hide-panel').click();
    warning('Нет доступа','Модуль находится в разработке','danger');
});
//скрипты для заявок менеджера
$('.check-view-product > input').on('click',function(){
    var obj = $(this);
    var id = obj.data('id');
    var check = obj.is(":checked") ? 1 : 0;
    var data = {
        id:id,
        check:check
    };
    $.ajax({
        url:'/product/change-view-product',
        type:'post',
        data:data,
        success: function(res){
            var data = JSON.parse(res);
            if(data.check == 1){
                warning('Товар ' + data.name,'открыт для просмотра менеджеру','success');
            } else {
                warning('Товар ' + data.name,'закрыт для просмотра менеджеру','success');
            }
        }
    });
});
//массово включить отображения товара для менеджеров (для текущий страницы пагинаций)
$('.select-on-check-all').on('click',function(){
    var check = $(this).is(":checked") ? 1 : 0;
    var checkbox = $('.check-view-product input[type=checkbox]');
    var arr = [];
    checkbox.each(function(){
        arr.push($(this).data().id);
    });
    if(arr != ''){
        $('.yiierp-loader').show();
        $.ajax({
            url: '/product/mass-change-view-product',
            type:'post',
            data: {data:arr,check:check},
            success: function(res){
                var data = JSON.parse(res);
                if(data.check == 1){
                    $('.yiierp-loader').hide();
                    warning('Выбраные товары','открыты для просмотра менеджеру','success');
                } else {
                    $('.yiierp-loader').hide();
                    warning('Выбраные товары','закрыты для просмотра менеджеру','success');
                }
            }
        });
    }
});
//смена статуса для товара
$('body').on('change','.change-status-product',function(){
    var id = $(this).attr('data-id');
    var status = $(this).is(':checked');
    status === false ? status = 0 : status = 1;
    if(status == 1){
        $(this).closest('tr').find('.publish_status').removeAttr('disabled');
    }else{
        $(this).closest('tr').find('.publish_status').attr('disabled','true').prop('checked',false);
    }
    $.ajax({
        url:'/product/change-status',
        type:'post',
        data: {id:id,status:status},
        success: function(){
            warning('Success','Статус был изменен успешно','success');
        },
        error: function(res){
            warning('Error',res,'danger');
        }
    });
});