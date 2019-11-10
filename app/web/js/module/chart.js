$(document).ready(function(){
    if($('.chart-report').length > 0){
        createDiagramAndTable();
    }
});
$('select[name="filter_selection"]').on('change',function(){
  createDiagramAndTable();
});
function createDiagramAndTable() {
  var obj = $('select[name="filter_selection"]');
  var value = obj.val();
  var chart = {
    type:'pie',
    plotBackgroundColor: null,
    plotBorderWidth: null,
    plotShadow: false
  };
  var tooltip = {
    pointFormat: '<b>{series.name}</b>: <b>{point.y}</b>',
    valueDecimals: 2,
    valueSuffix: '% ({point.x})'
  };
  var plotOptions = {
    pie: {
      cursor: 'pointer',
      showInLegend: true,
      dataLabels: {
        enabled: true,
        format: '<b>{point.name}</b>',
        style: {
          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
        }
      },
      events:{
        click:function(event){
          $('.table_product').empty();
          $.ajax({
              url: '/chart/table-product-for-agent?type='+value,
              type: 'post',
              data:{name:event.point.name},
              success:function(res){
                var arr = $.parseJSON(res);
                buildTable(arr);
                buildSummaryTable(arr);
              }
          })
        }
      }
    }
  };
  $.ajax({
    url:'/chart/chart-construct',
    type: 'post',
    data:{data:value},
    success:function(res){
      $('.table_product').empty();
      var data = $.parseJSON(res);
      var series=[];
      series['0'] = {'name':'Процентное соотношение','data':data['0']};
      var json = {};   
      json.chart = chart; 
      json.title = data['1'] == 'agent'?{text:'Поставщики'}:{text:'Категории'};    
      json.tooltip = tooltip;  
      json.series = series;
      json.plotOptions = plotOptions;
      $('#container').highcharts(json); 
    }
  }); 
  $.ajax({
    url: '/chart/table-product-for-agent?type='+value,
    type: 'post',
    success:function(res){
      var arr = $.parseJSON(res);
      buildSummaryTable(arr);
    }
  });
  function buildSummaryTable(arr) {
    var total = '';
    var total_amount = new Number();
    var total_cost_price = new Number();
    var total_price1 = new Number();
    var total_price2 =new Number();
    for(var i = 0;i<arr.length;++i){
      total_amount += Number(arr[i].amount);
      total_cost_price += Number(arr[i].cost_price);
      total_price1 += Number(arr[i].all_price1);
      total_price2 += Number(arr[i].all_price2);
    }
    total +=   '<div style="font-weight:800">' + 'Всего кол-во' + ' - ' + total_amount + '</div>' +
            '<div style="font-weight:800">' + 'Общая цена прихода' + ' - ' + total_cost_price.toFixed(2) + '</div>' +
            '<div style="font-weight:800">' + 'Цена 1 общая' + ' - ' + total_price1 + '</div>' +
            '<div style="font-weight:800">' + 'Цена 2 общая' + ' - ' + total_price2.toFixed(2) + '</div>';
   
    return $('#summary_table').empty().append(total);
  }
  function buildTable(arr){
    var td = '';
    var total_amount = new Number();
    var total_cost_price = new Number();
    var total_price1 = new Number();
    var total_price2 =new Number();
    for(var i = 0;i<arr.length;++i){
      td += '<tr>';
      td += '<td>'+ arr[i].name +'</td>';
      td += '<td>'+ arr[i].amount +'</td>';
      td += '<td>'+ arr[i].cost_price +'</td>';
      td += '<td>'+ arr[i].price1 +'</td>';
      td += '<td>'+ arr[i].price2 +'</td>';
      td += '<tr>';           
    }
    return $('.table_product').empty().css('display','block').append('<table class="custom-table v3">' +
      '<thead>' +
          '<tr>' +
            '<th>Название</th>'+
            '<th>Кол-во</th>'+
            '<th>Цена прихода</th>'+
            '<th>Цена 1</th>'+
            '<th>Цена 2</th>'+
          '</tr>'+
        '</thead>' +
        '<tbody>' + td + '</tbody></table>'); 
  }    
}