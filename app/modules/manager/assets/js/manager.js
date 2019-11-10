//откат формы до первичного вида
function cancelAdjustment(){
    $('.adjustment-product-form').empty().hide(600);
    $('#add-request-product-form').show(600);
    $('#product_filter').val('').trigger('change');
    $('#requestproduct-amount').val('');
    setTimeout(function(){
        $('#product_filter').select2('open');
    },700);
}
//показать спрятать таблицу продуктов 
$('.collapse-manager-product').on('click',function(event){
    event.preventDefault();
    icon = $(this).find('i');
    icon.toggleClass('fa-eye-slash');
    icon.toggleClass('fa-eye');
});

$('.product-for-manager').one('shown.bs.collapse', function (e) {
    initTable('.custom-table');
});
$(document).ready(function(){
    //иницилизация поля для категорий
    $('#category_filter').SubSelect2({
        'width': '100%',
        'data': {
            'url': '/category/get-list-category?placeholderOption=true',
            'type': 'post'
        },
        'placeholder': {
            'text': 'Выберите товар категорию'
        },
        'open': false
    }).val(0).trigger('change');
    //иницилизация поля для продукта
    $('#product_filter').select2({
        width: '100%',
        ajax: {
            url: '/manager/request/get-product-list',
            dataType: 'json',
            type: 'post',
            data: function (params) {
                return {
                    category: $('#category_filter').val(),
                    value: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        },
        placeholder: "Выбрать товар",
        escapeMarkup: function (markup) {return markup;},
        templateResult: function (item) {
            return (item.id) ? '<span data-base="'+ item.is_variant +'">(' + item.price + ') - ' + item.text + '</span>': item.text;
        }
    }).on('select2:selecting',function(){
        $('#product_filter').attr('data-scrolltop', $('.select2-results__options').scrollTop());
    }).on('select2:open',function(){
        $('.select2-results__options').animate({scrollTop: $('#product_filter').attr('data-scrolltop')}, '100');
    });
});
$('#category_filter').on('select2:select',function(){
    $('#product_filter').select2('open');
});
//#toDo
$('#product_filter').on('select2:select',function(event){
    if(event.params.data.is_variant == 1){
        console.log('базовый товар');
    }else{
        console.log('вариативный товар');
    }
    console.log(event);
});
//добавления базового товара в заявку
$('.add-product').on('click', function () {
    var form = $('#add-request-product-form');
    $('.yiierp-loader').show();
    $.ajax({
        url: form.attr('action'),
        async: false,
        type: 'post',
        data: form.serialize(),
        success: function (res) {
            var data = JSON.parse(res);
            if(data.type == 1){
                $('.table-request-body').append(data.view);
                warning('success','товар добавлен в заявку','success');
                $('#product_filter').val('').trigger('change');
                $('#requestproduct-amount').val('');
                $('#product_filter').select2('open');
            }
            if(data.type == 2){
                ShowMSG(data.msg);
            }
            if(data.type == 3){
                form.hide(600);
                $('.adjustment-product-form').append(data.view).show(600);
                warning('Товар уже добавлен в заявку','Вы можете изменить его количество');
            }
            $('.yiierp-loader').hide();
        }
    });
    return false;
});
//потверждения изменения количества 
$('.adjustment-product-form').on('click','.add',function(){
    var form = $('#adjustment-amount-product');
    $('.yiierp-loader').show();
    $.ajax({
        url: form.attr('action'),
        async: false,
        type: 'post',
        data: form.serialize(),
        success: function (res) {
            var data = JSON.parse(res);
            if(data.status){
                $('.row-product[data-product-id='+ data.id +']').find('.product-request-amount').val(data.value);
                cancelAdjustment();
                warning('success','количество изменено','success');
            }else{
                for(index in data['error']['description']){
                    warning('error',data['error']['description'][index]);
                }
            }
            $('.yiierp-loader').hide();
        }
    });
    return false;
});
//возрат из формы изменения количества 
$('.adjustment-product-form').on('click','.back',function(){
    cancelAdjustment();
});
//потвердить заявку
$('.confirm-product-request').on('click',function(){
    var comment = $('#comment-request').val();
    var store_id = $('.product-request-index').data('store-id');
    var data = {
        comment:comment,
        store_id:store_id,
        request_id: $('.product-request-index').data('request-id')
    };
    $.ajax({
        url:'/manager/manager/confirm-request',
        type:'post',
        data:data,
        success:function(res){
            data = JSON.parse(res);
            if(data.status){
                warning('success','Заявка сформирована','success');
            }else{
                warning('error','Произошла ошибка','error');
            }
            setTimeout(function(){ location = '/manager/manager/index'; }, 1000);
        }
    });
});