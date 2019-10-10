//ok
function AjaxTransferProdValueDb(i = 0){
    $.ajax({
        url:'/transfer-data/transfer-prod-value-db',
        type:'post',
        data: {i: i},
        dataType:'JSON',
        success: function(res){
            console.log(res);
            if(res.finish !== true){
                AjaxTransferProdValueDb(res.i);
            }else{
                warning('success','Таблица транзакций заполнена','success');
                AjaxTransferOperArchiveTabelDb();
            }
        }
    });
}
//ок
function AjaxTransferOperArchiveTabelDb(i = 0){
    $.ajax({
        url:'/transfer-data/transfer-oper-archive-tabel-db',
        type:'post',
        data: {i: i},
        dataType:'JSON',
        success: function(res){
            console.log(res);
            if(res.finish !== true){
                AjaxTransferOperArchiveTabelDb(res.i);
            }else{
                warning('success','Таблица архива заполнена','success');
                AjaxTransferProductRequestTabelDb();
            }
        }
    });
}
//ок
function AjaxTransferProductRequestTabelDb(i = 0){
    $.ajax({
        url:'/transfer-data/transfer-product-request-tabel-db',
        type:'post',
        data: {i: i},
        dataType:'JSON',
        success: function(res){
            console.log(res);
            if(res.finish !== true){
                AjaxTransferProductRequestTabelDb(res.i);
            }else{
                warning('success','Таблица заявок менеджеров заполнена','success');
            }
        }
    });
}

function ClearOperationsAfterTransferData(){
    console.log('start ClearOperationsAfterTransferData');
    $.ajax({
        url:'/transfer-data/clear-operations',
        type:'post',
        success: function(res){
            if(res){
                ClearArchiveAfterTransferData();    
            }else{
                warning('error','Файл отсутвует');
            }
        }
    });
}

function ClearArchiveAfterTransferData(){
    console.log('start ClearArchiveAfterTransferData');
    $.ajax({
        url:'/transfer-data/clear-archive',
        type:'post',
        success: function(res){
            if(res){
                ClearProductRequestAfterTransferData();
            }else{
                warning('error','Файл отсутвует');
            }
        }
    });
}

function ClearProductRequestAfterTransferData(){
    console.log('start ClearProductRequestAfterTransferData');
    $.ajax({
        url:'/transfer-data/clear-product-request',
        type:'post',
        success: function(res){
            if(res){
                warning('success','Старые данные словили маслину и померли','success');
            }else{
                warning('error','Файл отсутвует');   
            }
        }
    });
}
