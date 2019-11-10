//форматирует количества товара 
function pad(str, max) {
    str = str.toString();
    return str.length < max ? pad("0" + str, max) : str;
}
$(document).ready(function () {
    $('#table-add-prod input').attr('disabled', 'disabled');
    if ($('.product-category').length > 0) {
        $('.product-category').SubSelect2({
            'width': '100%',
            'data': {
                'url': '/category/get-list-category?placeholderOption=true',
                'type': 'post'
            },
            'placeholder': {
                'text': 'Категории'
            },
            'open': false
        }).val(0).trigger('change.select2');
    }
    $('.choose-prod select').select2({
        ajax: {
            url: '/product/get-product-list',
            dataType: 'json',
            type: 'post',
            data: function (params) {
//#toDo
//                page = $('#opercoming-product_id').attr('data-page');
//                limit = 10;
//                if(page > (params.page || 1)){                    
//                    limit = page * limit;
//                    page = 1;
//                }else{
//                    $('#opercoming-product_id').attr('data-page',params.page);
//                }
                return {
                    value: params.term,
                    category: $('.product-category').val(),
                    page: params.page || 1,
                    limit: 10
                };
            },
            processResults: function (data) {
                return {
                    results: data.item,
                    pagination: {
                      more: (data.item.length * 10) < data.totalCount
                    }
                };
            }
        },
        placeholder: "Выбрать товар",
        minimumResultsForSearch: 1,
        escapeMarkup: function (markup) {return markup;},
        templateResult: function (item) {
            return (item.id) ? '(' + pad(item.amount, 6) + ') - ' + item.text : item.text;
        }
    }).select2('open');
});
//фильтр для вариций товара в создании накладной
$('body').on('change', '.filter_chars', function () {
    var obj = $('.filter_chars');
    var filter = [];
    var show = [];
    var rows = $('.variant_product_row');
    obj.each(function () {
        if ($(this).val() > 0) {
            filter.push($(this).find(':selected').text());
        }
    });
    rows.each(function (i, el) {
        var product_chars = $(el).find('.string_chars').text();
        filter.forEach(function (item, index) {
            if (product_chars.indexOf(item) + 1) {
                show.push(true);
            } else {
                show.push(false);
            }
        });
        if (show.indexOf(false) !== -1) {
            $(this).hide();
        } else {
            $(this).show();
        }
        show = [];
    });
});
//подтягивает товары по категорий 
$('#table-add-prod,#adjustment-content').on('select2:select', '.product-category', function () {
    $('.choose-prod select').attr('data-page',1);
    $('.choose-prod select').select2('open');
    $('#product_variant_table').empty();
    $('#table-add-prod input').attr('disabled', 'disabled');
});
//евент отвечает за добавления товара в приход/расход
$('.add-prod').on('click', function () {
    var form = $('#add-product-form');
    $('.yiierp-loader').show();
    $.ajax({
        url: form.attr('action'),
        async: false,
        type: 'post',
        data: form.serialize(),
        success: function (res) {
            var data = JSON.parse(res);
            $('#coming-goods tbody').append(data.view);
            if (data.type == 'error') {
                ShowMSG(data.msg);
            } else {
                $('#product_variant_table').empty();
                form.trigger('reset');
                $('#table-add-prod input').attr('disabled', 'disabled');
                $('#coming-goods tbody').find('.dataTables_empty').remove();
                $('.product-category, .choose-prod select').val(0).trigger('change.select2');
                if (data.total_price.total_uah && data.total_price.total_usd) {
                    $('.summ').text(' --- ' + data.total_price.total_uah + ' UAH / ' + data.total_price.total_usd + ' USD');
                }
            }
            $('.yiierp-loader').hide();
        }
    });
    return false;
});
//формирирования транзакций 
$('.ok-transaction').on('click', function () {
    $('.yiierp-loader').show();
    var form = $('#form-where-whence');
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize()
    });
});
//закрытия транзакций 
$('.close-transaction').on('click', function (e) {
    e.preventDefault();
    var id = $(this).closest('tr').attr('data-key');
    $.ajax({
        url: '/operation/close-transaction',
        type: 'post',
        data: {trans_id: id}
    });
});
// глобальная переменная для использования таблицы которая инициализируется в getTableTransaction
var com_goods_table;
//отображения информаций о транзакций
$('.get-transaction-table').on('click', function (e) {
    e.preventDefault();
    $('.yiierp-loader').show();
    var id = $(this).closest('tr').attr('data-key');
    var type = $(this).closest('tr').attr('data-type');
    var url = $(this).attr('href');
    $.ajax({
        url: url,
        type: 'post',
        data: {trans_id: id, type: type},
        success: function (res) {
            data = JSON.parse(res);
            $('.transaction-info').html(data.view);
            if(!data.empty){
                com_goods_table = $('#coming-goods').DataTable({
                    scrollCollapse: true,
                    scrollY: 600,
                    scrollX: true,
                    fixedColumns: true,
                    "bFilter": false,
                    "iDisplayLength": 100,
                    paging: false,
                    sort: false,
                    destroy: true,
                    fixedHeader: {
                        header: true,
                        footer: true
                    }
                });
            }
            $('html,body').animate({scrollTop: $('.transaction-info').offset().top}, 1000);
            $('.yiierp-loader').hide();
        }
    });
});
//розкрывания скрытых полей при отображения информаций о транзакций
$('.transaction-info').on('click', '.show-more-info', function () {
    $('.yiierp-loader').show();
    var obj = $(this);
    var hclass = obj.data('class');
    if (obj.hasClass('fa-plus')) {
        obj.removeClass('fa-plus');
        obj.addClass('fa-minus');
        $('#coming-table-container').removeClass('hide-' + hclass);
        if (com_goods_table)
            com_goods_table.columns.adjust();
    } else {
        obj.removeClass('fa-minus');
        obj.addClass('fa-plus');
        $('#coming-table-container').addClass('hide-' + hclass);
        if (com_goods_table)
            com_goods_table.columns.adjust();
    }
    $('.yiierp-loader').hide();
});
//скрытия информаций о транзакций
$('.transaction-info').on('click', '.btn-del-info', function () {
    $('.transaction-info').empty();
});
//отмена транзакций удаления товара -> удаления транзакций
$('.cancel-transaction').on('click', function () {
    $.ajax({
        url: '/operation/cancel-transaction',
        type: 'post',
        data: {type: $(this).attr('data-type'), id: $(this).attr('data-id')}
    });
});