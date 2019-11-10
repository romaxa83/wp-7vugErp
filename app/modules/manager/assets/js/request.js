//
var amountProductRequest;
$('body').on('focus','.product-request-amount',function(){
    amountProductRequest = $(this).val();
});
//ajax для изменения количество позиций заявки
$('body').on('blur','.product-request-amount',function(){
    var newValue = $(this).val();
    var blockWithAllAmount = $(this).closest('tr').find('.all-request-amount .value');
    if(newValue != amountProductRequest){
        $.ajax({
            url: '/manager/request/change-amount-product',
            type: 'post',
            data: {
                amount : Math.abs($(this).val()),
                product_id : $(this).closest('tr').attr('data-product-id'),
                request_id : $(this).closest('tbody').attr('data-request-id')
            },
            success: function(res){
                data = JSON.parse(res);
                if(data.status){
                    warning('Success','количество изменено','success');
                    if(newValue > amountProductRequest){
                        allValue = parseInt(blockWithAllAmount.text()) + (newValue - amountProductRequest);
                    }else{
                        allValue = parseInt(blockWithAllAmount.text()) - (amountProductRequest - newValue);
                    }
                    blockWithAllAmount.text(allValue);
                }else{
                    warning('error','Произошла ошибка','error');
                }
            }
        });
    }
});