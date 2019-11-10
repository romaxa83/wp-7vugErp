//функция рекурсивно вызывает метод контроллера который проверяет файлы архиваций и чистит журнал логов
function AjaxForClearLog(key=0){
    $.ajax({
        url:'/logger/default/clear',
        type:'post',
        data: {key:key},
        success: function(res){
            if(res != 'stop'){
                data = JSON.parse(res);
                AjaxForClearLog(data.key);
            }else{
                $('.yiierp-loader').hide(); 
                location.reload();
            }
        }
    });
}
//функция рекурсивно вызывает метод контроллера который архивирует журнал логов
function AjaxForArchiveLog(key=0,i=0) {
    $.ajax({
        url: '/logger/default/get-data',
        type: 'post',
        data: {key:key,index:i},
        success: function(res){
            data = JSON.parse(res);
            if(data.index != data.countAction){
                AjaxForArchiveLog(data.key,data.index);
            }else{
                AjaxForClearLog();
            }
        },
        error: function(res){
            console.log(res);
        }
    });
}
//подготовка для архиваций логов
$('.log-archive').on('click',function (e) {
    e.preventDefault;
    $('.yiierp-loader').show();
    $.ajax({
        url:'/logger/default/archive',
        type:'post',
        success: function(res){
            if(res){
                AjaxForArchiveLog();
            }else{
                $('.yiierp-loader').hide();
                warning('Error','Логи пустые','danger');
            }
        } 
    });
});
//подгрузка (месяц/день) для архива журнала логов
$('#accordion-year').on('click','.get-more',function(){
    var month = $(this).attr('data-month');
    var year = $(this).attr('data-year');
    var type = $(this).attr('data-type');
    if(type == 'day'){
        var children = $('#collapse'+ month +'').children();
    }else{
        var children = $('#collapse'+ year +'').children();
    }
    if(children.children().length == 0){
        $.ajax({
            url: '/logger/default/get-'+ type +'',
            type: 'post',
            data: {year: year,month: month,type: type},
            success: function(res){
                data = JSON.parse(res);
                children.append(data.view);
            }
        });
    }
});