//проверка работоспособности local storeg
function storageAvailable(type) {
    try {
        var storage = window[type],
        x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
    }catch(e) {
        return false;
    }
}
//запись данных с local storeg 
var storageNextId = 1;
window.addItemLocalStoreg = function(data){
    localStorage.setItem(storageNextId,JSON.stringify(data));
    storageNextId++;
};
//получения данных с local storeg 
window.getLocalStoreg = function(){
    return localStorage;
};
//блокировка закрытия страницы        
var unloadEvent = function (e) {
    var confirmationMessage = "Warning: Leaving this page will result in any unsaved data being lost. Are you sure you wish to continue?";
    (e || window.event).returnValue = confirmationMessage;
    return confirmationMessage;
};
//проверка соединения с интернетом           
window.testConnect = function(xhr){
    var connect = navigator.onLine;
    if(xhr !== undefined){
        if(connect){
            var timeout;
            warning('Интернет снова подлючен','После перезагрузки можно продолжить работу','success');
            $(document).on("ajaxStop.mine", function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    location.reload();
                },2000);
            });
            SynchronizationData();
            window.removeEventListener("beforeunload", unloadEvent);
            $('.navbar-nav').css('border','');
            $('.yiierp-loader').hide();
            $(document).off('mine');
        }else{
            $('.navbar-nav').css('border','2px solid red');
            warning('Подключения интернета пропало','Действия будут сохранены , не закрывайте страницу','danger');
            window.addEventListener("beforeunload", unloadEvent);
        }
    }
    return connect;
};
//синхронизаций после появления интернета
window.SynchronizationData = function(){
    $('.yiierp-loader').show();
    var storeg = getLocalStoreg();
    if(storeg.length === 0){ return false; }
    var items = [];
    var arr = [];
    var ajax;
    for (var oneItem in storeg){
        if(oneItem === 'length'){
            break;
        }else{
            arr.key = oneItem;
            arr.item = storeg[oneItem];
            items.push(arr);
            arr = [];
        }
    }
    var connect = testConnect();
    if(items !== ''){
        items.forEach(function(element) {
            if(element.item !== ''){
                var item = JSON.parse(element.item);
                if(item.url !== undefined){
                    if(!connect) return false;
                    ajax = doAction(item);
                    if(ajax){
                        localStorage.removeItem(element.key);
                    }
                }
            }
        });
    }
};
//подготовка елемента и local storeg 
function formattedUrlAndData(item){
    var testUrl = [
        '/operation/send-in-archive',
        '/product/change-status',
        '/product/change-publish-status',
        '/category/change-status',
        '/category/change-publish-status',
        '/live-edit/entry'
    ];
    item.data = decodeURIComponent(item.data);
    if(testUrl.indexOf(item.url) > -1){
        item.data = {data : item.data,  oldUrl : item.url};
        item.url = '/local-storeg/check-error-ajax';
    }
    if(item.action === '/manager/request/create-empty-transaction'){ 
        var store_id = item.data.split('=');
        ClearEmptyTrans(store_id[1]); 
        return false;
    }
    if(item.action === 'filling-transaction'){ item.url = '/product-request/check-filling-transaction'; }
    return item;
}
//выполнения оффлайн действия 
window.doAction = function(item){
    var connect = testConnect();
    var item = formattedUrlAndData(item);
    if(connect && item){
        var ajax = $.ajax({
            url: item.url,
            async: false,
            type: 'post',
            data: item.data 
        });
        if(ajax.readyState >= 3 && ajax){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
};
//отлавливание конца ajax , проверка статуса ajax (если данные сервак получил то всё ок иначе операция не прошла)
$( document ).ajaxError(function(event, jqxhr, settings) {
    var connect = testConnect();
    if(jqxhr.status != 302 && !connect){
        warning('Success','Действия записано в local storeg','success');
        var url = settings.url;
        var data = {url : url ,data : settings.data};
        addItemLocalStoreg(data);
        $('.yiierp-loader').hide();
    }
});