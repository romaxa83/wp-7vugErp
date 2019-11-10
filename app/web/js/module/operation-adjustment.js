(function() {   
    //построения строки базового товара
    function htmlAdjustmentProduct(data,float){
        var readonly = '';
        if(data.is_variant == 2) readonly = 'readonly';
        return '<tr data-id="'+ data.id +'">'+
                '<td><input name="id[]" type="text" value="'+ data.id +'" class="hidden" readonly><span class="name-adjustment">'+ data.name +'</span></td>'+
                '<td class="baseAmount"><input name="amount[p'+ data.id +']" class="form-control" type="number" value="'+ data.amount +'" min="0" '+ readonly +'></td>'+
                '<td><input name="cost_price[]" class="form-control" type="float" value="'+ parseFloat(data.cost_price).toFixed(float) +'" min="0"></td>'+
                '<td><input name="trade_price[]" class="form-control" type="float" value="'+ parseFloat(data.trade_price).toFixed(float) +'" min="0"></td>'+
                '<td class="text-center"><input name="start_price[]" class="form-control" type="float" value="'+ parseFloat(data.start_price).toFixed(float) +'" min="0"></td>'+
                '<td>'+ (data.price1 / parseInt($('.curr-value').text())).toFixed(2) +'</td>'+
                '<td class="text-center">'+ (data.price2 / parseInt($('.curr-value').text())).toFixed(2) +'</td>'+
                +'</tr>';
    }
    //построения строки вариативного товара
    function htmlAdjustmentVProduct(data,baseAmount){
        return '<tr>'+
                '<td><input  type="text" value="'+ data.id +'" class="hidden" readonly>'+ data.char_value +'</td>'+
                '<td class="text-center variantAmount" data-base-prod="'+ data.product_id +'" data-balance="'+ baseAmount +'"><input name="amount['+ data.product_id +']['+ data.id +']" type="number" class="form-control" value="'+ data.amount +'" min="0"></td>'+
                +'</tr>';
    }
    //добавления товара на корректировку
    $('#adjustment-content').on('click','.adjustment-add',function(){
        $.ajax({
            url : '/product/get-product-values',
            type : 'post',
            data : {name : $('.choose-prod select').val()},
            success: function(res){
                var data = JSON.parse(res);
                if($('[data-id="'+ data.product.id +'"]').length === 0){    
                    var float = $('.adjustment-table').attr('data-float');
                    var table = $('.adjustment-table');
                    $('.adjustment-save').removeClass('hidden');
                    table.removeClass('hidden');
                    var html = htmlAdjustmentProduct(data.product,float);
                    data.product.vproducts.forEach(function(element){
                        html += htmlAdjustmentVProduct(element,data.product.amount);
                    });
                    table.append(html);  
                    $('.choose-prod select').val(0).trigger('change.select2');
                }else{
                    warning('Error','Данный товар уже добавлен','danger');
                }   
            }
        });
    }); 
    //евент записует значения до изменения для последующих расчетов 
    var amount;
    $('.adjustment-table').on('focus','.variantAmount input',function(){
        amount = $(this).val();
        return false;
    });
    //евент подсчитует сумму кол-во вариаций 
    $('.adjustment-table').on('change','.variantAmount input',function(){
        if($(this).val() !== ''){
            var id = $(this).closest('td').attr('data-base-prod');
            var baseAmount = parseInt($('tr[data-id="'+ id +'"] .baseAmount input').val());
            var newAmount = $(this).val();
            if(newAmount > amount){
                baseAmount += (newAmount - amount);
            }else{
                baseAmount -= (amount - newAmount);
            }
            $('tr[data-id="'+ id +'"] .baseAmount input').val(baseAmount);
            amount = $(this).val();
        }else{
            $(this).val(amount);
        }
    });
    //применения корректировки 
    $('#adjustment-form').on('submit',function(event){
        event.preventDefault();
        form$ = $(this);
        $.ajax({
            beforeSend : function(){
              $('.adjustment-save').prop('disabled','disabled');
            },
            url : form$.attr('action'),
            type : 'post',
            data : form$.serialize(),
            success : function(res){
                data = JSON.parse(res);
                if(data.status){
                    warning('success','Корректировка создана','success');
                    setTimeout(function(){location.reload()},1000);
                }else{
                    for(var id in data.error){
                        tr = $('tr[data-id="'+ id +'"]');
                        warning('error','Введено не коректное значения для поля "' + data.error[id] + '"');
                        $('.adjustment-save').prop('disabled','');
                        tr.css('border','1px solid red');
                    }
                }   
            }
        });
    });
})();
