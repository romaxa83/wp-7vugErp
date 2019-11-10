function initTable(element = '.table-fix') {
    $(element).DataTable({
        scrollCollapse:true,
        scrollY: 600,
        scrollX: true,
        fixedColumns: true,
        bFilter: false,
        iDisplayLength: 100,
        paging: false,
        sort:false,
        destroy: true,
        autoWidth: false
    });
}

var com_goods_table;
function initComigGoodsTable() {
    com_goods_table = $('#coming-goods').DataTable({
        scrollCollapse:true,
        scrollY: 600,
        scrollX: true,
        fixedColumns: true,
        "bFilter": false,
        "iDisplayLength": 100,
        paging: false,
        sort:false,
        destroy: true,
        fixedHeader: {
            header: true,
            footer: true
        }
    });
}

$(document).ready( function () {
    if($('.not-init').length === 0){
        initTable();
    }
    $('.pagination li a').on('click',function(){
        initTable();
    });
} );