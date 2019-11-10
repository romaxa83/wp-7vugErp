/*
 * выввод сообщения пользователю 
 * принимает title_message - заголов сообщения , descr_message - тело сообщения , type - стиль сообщения
*/
function warning(title_message, descr_message, type = 'danger') {
    $('.noty_layout').append('<div class="alert alert-' + type + ' fadeInRight animated"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>' + title_message + '</h4><p>' + descr_message + '</p></div>');
    $(".alert.alert-danger, .alert.alert-success").fadeIn( "slow", function(event) {
        var self = this;
        setTimeout(function() {
            $(self).removeClass('fadeInRight').addClass('fadeOutRight fadeOutRight').fadeOut( "slow", function() {
                $(self).remove();
            }); 
        }, 5000); 
    });
}
// вывод сообщений ошибок формы
$('form').on('afterValidate',function (event, messages, errorAttributes){
    for(indexMsg in messages){
        text = messages[indexMsg];
        if(text.length > 0){
            warning('Ошибка', text , 'danger');
        }
    }
});
/*
 * функция распарсиваня сообщений 
 * принимает массив сообщений
*/
function ShowMSG(msg){
    if(typeof(msg.description) === 'string'){
        if(msg.description !== ''){
            warning(msg.title, msg.description, msg.type);
        }
    }else{
        for(var i = 0;i < msg.description.length;i++){
            if(msg.description[i] !== ''){
                warning(msg.title, msg.description[i], msg.type);
            }
        }
    }
}
//евент для показа панели
$('.show-panel').on('click',function(){
    var panel = $(this).attr('data-panel');
    $('#'+ panel + '-panel').toggleClass('hidden');
    $('.control-panel').toggleClass('hidden');
});
//евент для скрытия панели
$('.hide-panel').on('click',function(){
    $('.control-panel').toggleClass('hidden');
    $(this).closest('.panel').toggleClass('hidden');
});
//евент для показа формы
$('.show-form').on('click',function(){
    var form = $(this).attr('data-form');
    $('#'+ form + '-form').toggleClass('hidden');
    $('.hide-form[data-form='+ form +']').show();
    $(this).hide();
});
//евент для скрытия формы
$('.hide-form').on('click',function(){
    var form = $(this).attr('data-form');
    $('#'+ form + '-form').toggleClass('hidden');
    $('.show-form[data-form='+ form +']').show();
    $('.hide-form[data-form='+ form +']').hide();
});
$( document ).ready(function(){
    window.addEventListener('online',  testConnect);
    window.addEventListener('offline',  testConnect);
    //прячем прелоадер
    $('.yiierp-loader').hide();
    //стилизация select
    $('.custom-select').selectmenu();
    //инициализация subselect для поля категорий в каталоге товаров
    if($('#filter-panel #category_filter').length > 0){
        $('#filter-panel #category_filter').SubSelect2({
          'width' : '100%',
          'data' : {
            'url' : '/category/get-list-category',
            'type' : 'post'
          },
          'placeholder' : {
            'text' : 'Категории'
          },
          'open' : false
        });
    }
    //вывод warning сообщения c session
    var msg = $('body').data('warning');
    if (msg) {
        $.ajax({
            url: '/base/unset-warning',
            type: 'POST',
            beforeSend: function() {
                ShowMSG(msg);
            }
        });
    }
});
//сброс фильтра
$('.reset-filter').on('click', function (e) {
    e.preventDefault();
    $('.form-group').children().val('');
    $(this).submit();
});
//Проверка на ввод type="float" блокирования ввода алфавита
$('body').on('keypress','[type="float"],[type="number"]', function(event) {
    var reg = /[0-9]|\./;
    if(event.charCode && !reg.test(event.key) && event.key !== ','){
        event.preventDefault();
    }else{
        var replace = $(this).val().replace(',','.');
        $(this).val(replace);
    }
});
// евент изменения курса ajax-ом
$('.nav-currency').on('click', '.save_currency', function () {
    var curr = $('input[name="currency_value"]').val();
    $.ajax({
        url: '/site/change-usd',
        type: 'post',
        data: {curr: curr}
    });
});
//изменения статуса для магазина
$('body').on('click','.publish_status',function(){
    $('.yiierp-loader').show();
    var id = $(this).attr('data-id'); 
    var status = $(this).is(':checked');
    var type = $(this).attr('data-type');
    (status === true) ? status = 1 : status = 0;
    if(type === 'product') var url = '/product/change-publish-status';
    if(type === 'category') var url = '/category/change-publish-status';
    if(type === 'vproduct') var url = '/variant-product/change-publish-status';
    $.ajax({
        url: url,
        type: 'post',
        data: {id:id,status:status},
        success: function(res){
            if(type == 'product' && $('.show-var-prod[data-id-product='+ id +']').hasClass('remove-var-prod')) {
                if(status){
                    $('tr[data-product='+ id +'] .table-view-var-prod').find('.publish_status').each(function() {
                        $(this).prop('disabled', false);
                    });
                } else {
                    $('tr[data-product='+ id +'] .table-view-var-prod').find('.publish_status').each(function() {
                        $(this).prop('disabled', true);
                        $(this).prop('checked', false);
                    });
                }
            }
            if(type == 'category'){
                arrayId = JSON.parse(res);
                arrayId.forEach(function(element) {
                    if(status === 1){
                        $('#publish-status_' + element).prop('checked','checked');   
                    }else{
                        $('#publish-status_' + element).prop('checked',''); 
                    }
                });
            }
            warning('Success','Вы изменили статус публикацый для магазина','success');
        }
    });
    $('.yiierp-loader').hide();
});
//иницилизация life edit 
$('body').on('click','.live-edit',function(){
    typeData = $(this).attr('data-type-data');
    value = $(this).text();
    $(this).removeClass('live-edit');
    if(typeData == 'float'){
        step = '0.' + pad('0' + '' ,$('#table-add-prod').attr('data-float-' + $(this).attr('data-currency')) - 1) + 1;
    }else{
        step = 'number';
    }
    $(this).empty().append('<input class="live-edit-changed-data" type="number" min="0" value="' + value + '" style="width:100%" data-value="'+ value +'" step="' + step + '">');
    $('.live-edit-changed-data').focus();
});
//сбор данных life edit 
$('body').on('blur','.live-edit-changed-data',function(){
    var obj = $(this);
    var value = obj.val().replace(',','.');
    if($(this).attr('data-value') == value){
        obj.closest('td').empty().text(value).addClass('live-edit');
        return false;
    }
    $('.yiierp-loader').show();
    var transaction_id = window.location.search.substr(4);
    var tr = obj.closest('tr');
    var typeLifeEdit = obj.closest('td').attr('data-type');
    var field = obj.closest('td').attr('data-field');
    var productId = tr.attr('data-base-id');
    var variantId = tr.attr('data-variant-id');
    $.ajax({
        url : '/live-edit/entry',
        type : 'post',
        data : {
           typeLifeEdit : typeLifeEdit,
           field : field ,
           value : Math.abs(value),
           productId : productId,
           variantId : variantId,
           transaction_id : transaction_id,
           variant : (variantId === '') ? false : true
        },
        success : function(res){
            $('.yiierp-loader').hide();
            var data = JSON.parse(res);
            if(data.status !== 'ok'){
                warning('Warning',data.text);
            }else{
                warning('Success','Изменения прошли удачно','success');
            }
            if(data.total_price){
                $('.summ').text(' --- '+ data.total_price.total_ua +' UAH/ '+ data.total_price.total_usd +' $');
            }
            if(data.field !== undefined && (data.field === 'start_price' || data.field === 'start_price_ua')){
                tr.find('[data-field="start_price"]').empty().text(data.value.usd).addClass('live-edit');
                tr.find('[data-field="start_price_ua"]').empty().text(data.value.ua).addClass('live-edit');
            }else{
                obj.closest('td').empty().text(data.value).addClass('live-edit');
            }
        }
    });
});
//показывавем side bar с чатом 
$('#show-chat').on('click',function(){
    $('.control-sidebar-bg, .control-sidebar ').css('right','0');
});
//скрываем side bar с чатом 
$('#hide-chat').on('click',function(){
    $('.control-sidebar-bg, .control-sidebar ').css('right','-230px');
});
//при скрывание меню перегенерить width таблиц , а так же занести в куки параметр 
$('.sidebar-toggle').on('click',function(){
    setTimeout(function(){
        $(".table-fix").DataTable().draw();
        var date = new Date(2020, 0, 32);
        $(".table-fix").DataTable().draw();
        if($('.skin-blue').hasClass('sidebar-collapse')){
            document.cookie = "menu=collapse;expires="+ date +";path=/";
        }else{
            document.cookie = "menu=show;expires="+ date +";path=/";
        }
    },500);
});