(function() {
    //переброс на форму создания массовой транзакций 
    $('.choose_mass_transaction').on('change',function(){
        if(this.checked){
            location.replace('/operation-mass-consumption/create');
        }
    });
    //евент на отображения магазина при выборе его из списка 
    $('#operations-where').on('change',function(){
        var selections = $(this).select2('data');
        var shop = [];
        $('.stores').empty();
        selections.forEach(function(element){
            if(element.text !== undefined){
                $('.stores').append(element.text + '</br>');
            }
        });
        $('.added-store').show();
    });
    //заполнения таблицы добавления товара
    $('.table-form-product-mass').on('change','.consumption-mass #operconsumption-product_id',function(){
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
                    } else {
                        $('#operconsumption-transaction_id').removeAttr('disabled');
                        $('#table-first-step').hide();
                        $(".stock-amount input").val(data.product.amount);
                        $('#operconsumption-amount').val(0);
                        if(data.product.is_variant === 2){
                            $('#add-product-form').attr('action', '/operation-mass-consumption/add-v-product');
                            $('#operconsumption-amount').attr('disabled',true);
                            $('#operconsumption-price').attr('disabled',true);
                            $('#product_variant_table').html(data.html);
                        }else{
                            $('#add-product-form').attr('action', '/operation-mass-consumption/add-product');
                            var price = $('.table-form-product').attr('data-type-price') === 1 ? data.product.price1 : data.product.price2;
                            var float = $('.table-form-product').attr('data-float-usd');
                            $('#operconsumption-amount').removeAttr('disabled');
                            $('#product_variant_table').empty();
                        }
                    }
                }
            });
        }
    });
    //триггер на добавления товара
    $('#trigger-mass-consumption').on('click',function(e){
        e.preventDefault();
        $('.add-mass-prod-consumption').click();
    });
    //отправка данных для получения таблици расприделения товара по магазинам 
    $('.add-mass-prod-consumption').on('click',function(e){
        e.preventDefault();
        var transaction = window.location.search.substr(1);
        $.ajax({
            url : '/operation-mass-consumption/first-step',
            type : 'post',
            data : $('#add-product-form').serialize() + '&' + transaction,
            success : function(res){
                data = JSON.parse(res);
                if(data.status == 'error'){
                    ShowMSG(data.msg);
                }else{
                    $('#table-first-step').show();
                    $('#table-first-step').empty().append(data.view);
                    $('#product_variant_table').empty();
                    initTable('#table-first-step > table');
                    $('#operconsumption-amount').prop( "disabled", true );
                }
            }
        });
    });
    //отправка продукта на добавления в расходные транзакций 
    $('#table-first-step').on('click','.confirm-mass',function(){
        var tr = $('#table-first-step tbody tr');
        var arr = [];
        tr.map(function(index,element) {
            arr.push({
                id : $(element).attr('data-key-base'),
                id_variant : $(element).attr('data-key-variant'),
                amount : $(element).find('.amount-mass-consumption').val(),
                price : $(element).find('.price-mass-consumption').val(),
                indexShop : $(element).attr('data-transaction')
            });
            return ;
        });
        if(arr){
            $('.yiierp-loader').show();
            $.ajax({
                url : '/operation-mass-consumption/add-product',
                type : 'post',
                data : {data : arr},
                success : function(res){
                    var data = JSON.parse(res);
                    if(data.status !== 'errror'){
                        $('#table-first-step').hide();
                        $('#table-for-products tbody').show().append(data.html);
                        $('#table-for-products tbody').find('.dataTables_empty').remove();
                        $('#add-product-form').trigger('reset');
                        $('#operconsumption-product_id').val('').trigger('change');
                        $('#operconsumption-amount').prop( "disabled", false );
                        $('.product-category').val(0).trigger('change.select2');
                    }else{
                        ShowMSG(data.msg);
                    }
                    $('.yiierp-loader').hide();
                }
            });
        }
    });
    //формирирования массовых транзакций 
    $('.ok-mass-transaction').on('click',function(){
        var id = window.location.search.substr(1);
        $.ajax({
           url : '/operation-mass-consumption/ok-transaction',
           type : 'post',
           data : id
        });
    });
    //евент отлавливает фокус на поле количества базового котрый уходит на магазины 
    //записует значения до изменения для последующих расчетов 
    //показывает и позиционирует напротив активного input подсказку с остатком товара  
    var amount;
    $('#table-first-step').on('focus','.amount-mass-consumption',function(){
        amount = $(this).val();
        var position = $(this).offset();
        var balance = $('#block-for-balance');
        var top = position.top;
        var left = position.left - 150;
        balance.offset({top:top, left:left});
        balance.css('visibility','visible');
        $('#block-for-balance').text($(this).closest('tr').attr('data-balance'));
        return false;
    });
    //евент отвечает за то что бы общая сумма количества товаров на магазины не превысила количества на складе 
    $('#table-first-step').on('keyup','.amount-mass-consumption',function(){
        var balance = parseInt($(this).closest('tr').attr('data-balance'));
        if($(this).val() !== ''){
            var id = $(this).closest('tr').attr('data-key-base');
            var variantId = $(this).closest('tr').attr('data-key-variant');
            var newAmount = $(this).val();
            balance += parseInt(amount);
            if(balance === 0 && newAmount > amount){
                $(this).val(amount);
                return false;
            }else{
                if(newAmount < balance){
                    balance -= newAmount;
                }else{
                    $(this).val(balance);
                    balance = 0;
                }
            }
            if(variantId !== undefined){
                $('#table-first-step tbody tr[data-key-base="'+ id +'"][data-key-variant="'+ variantId +'"]').attr('data-balance',balance);
            }else{
                $('#table-first-step tbody tr[data-key-base="'+ id +'"]').attr('data-balance',balance);
            }
            amount = $(this).val();
        }else{
            $(this).val(amount);
        }
        $('#block-for-balance').text(balance);
    });
    //прячет подсказку с остатком товара  
    $('body').on('blur','.amount-mass-consumption',function(){
        $('#block-for-balance').css('visibility','');
    });
    
    $('#table-for-products').on('click','.btn-delete-product-mass-consumption',function(){
        var tr = $(this).closest('tr');
        var baseId = tr.attr('data-base-id'); 
        var variantId = tr.attr('data-variant-id');
        var transactionId = tr.attr('data-transaction-id');
        $.ajax({
            url : '/operation-mass-consumption/delete-product',
            type : 'post',
            async : false,
            data : {base : baseId, variant : variantId, transaction : transactionId},
            success : function(){
                warning('success','Товар удален','success');
                tr.remove();
            }
        });
    });
})();