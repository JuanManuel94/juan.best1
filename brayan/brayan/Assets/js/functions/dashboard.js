let statistics_month_year,statistics_year,chart_type_payments,table;
window.addEventListener('load', function(){
  $('#search-trans').datetimepicker({
    locale: 'es',
    format: 'MM/YYYY'
  }).on('dp.change', function(e){
    var date = e.date.format(e.date._f);
    transactions_month(date);
    payments_type(date);
  });
  $('#search-trans').val(moment().format('MM/YYYY'));
  $('#search-year').datetimepicker({
    locale: 'es',
    format: 'YYYY'
  }).on('dp.change', function(e){
    var year = e.date.format(e.date._f);
    libre_services(year);
  });
  $('#search-year').val(moment().format('YYYY'));
  transactions_month(moment().format('MM/YYYY'));
  payments_type(moment().format('MM/YYYY'));
  libre_services(moment().format('YYYY'));
}, false);
function transactions_month(date){
  if(statistics_month_year){
    statistics_month_year.destroy();
  }
  var formatted_date = date.split('/');
  var month = formatted_date[0];
  var year = formatted_date[1];
  var new_date = month+"-"+year;
  const url = base_url+'/dashboard/transactions_month/'+new_date;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if(this.readyState == 4 && this.status == 200){
      const objData = JSON.parse(this.responseText);
      if(objData.status == 'success'){
        var ctx = document.getElementById('payments_month').getContext('2d');
        var degraded1 = ctx.createLinearGradient(0, 0, 0, 300);
          degraded1.addColorStop(0, 'rgba(0, 172, 172, 2)');
          degraded1.addColorStop(1, 'rgba(170, 227, 227, 0.0)');
        var degraded2 = ctx.createLinearGradient(0, 0, 0, 300);
          degraded2.addColorStop(0, '#003939');
          degraded2.addColorStop(1, '#00ACAC');
          $('#trans-year').text(objData.data.year);
          $('#trans-month').text(objData.data.month);
          $("#trans-total").text(
            currency_symbol + formatMoney(objData.data.total)
          );
        var datatrans = [];
        objData.data.payments.forEach((detail) => {
          datatrans.push(detail.total);
        });
        statistics_month_year = new Chart(ctx, {
          type: 'line',
          data: {
            labels: objData.data.days,
            datasets: [{
              label: 'Transacciones',
              data: datatrans,
              backgroundColor: degraded1,
              borderColor: degraded2,
              pointRadius: "0",
              pointHoverRadius: "0",
              borderWidth: 3
            }]
          },
          options: {
            maintainAspectRatio: false,
            legend: {
              display: false,
              labels: {
                fontColor: '#585757',
                boxWidth: 40
              }
            },
            tooltips: {
              mode: "index",
              intersect: false
            },
            hover: {
              mode: "nearest",
              intersect: true
            },
            scales: {
              xAxes: [{
                ticks: {
                  beginAtZero: true,
                  fontColor: '#585757'
                },
                gridLines: {
                  display: true,
                  color: "rgba(0, 0, 0, 0.07)"
                },
              }],
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  fontColor: '#585757'
                },
                gridLines: {
                  display: true,
                  color: "rgba(0, 0, 0, 0.07)"
                },
              }]
            }
          }
        });
      }
    }
  }
}
function payments_type(date){
  if(chart_type_payments){
    chart_type_payments.destroy();
  }
  var formatted_date = date.split('/');
  var month = formatted_date[0];
  var year = formatted_date[1];
  var new_date = month+"-"+year;
  const url = base_url+'/dashboard/payments_type/'+new_date;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if(this.readyState == 4 && this.status == 200){
      const objData = JSON.parse(this.responseText);
      if(objData.status == 'success'){
        var ctx = document.getElementById("payments_type").getContext('2d');
        var degraded1 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded1.addColorStop(0,'#fc4a1a');
        degraded1.addColorStop(1,'#fc4a1a');
        var degraded2 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded2.addColorStop(0,'#4776e6');
        degraded2.addColorStop(1,'#8e54e9');
        var degraded3 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded3.addColorStop(0,'#FFDF40');
        degraded3.addColorStop(1,'#ff6a00');
        var degraded4 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded4.addColorStop(0,'#388484');
        degraded4.addColorStop(1,'#388484');
        var degraded5 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded5.addColorStop(0, '#AAE3E3');
        degraded5.addColorStop(1, '#AAE3E3');
        var degraded6 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded6.addColorStop(0, '#F06997');
        degraded6.addColorStop(1, '#F06997');
        $('#type-year').text(objData.data.year);
        $('#type-month').text(objData.data.month);
        var payment_name = [];
        var payment_quantity = [];
        var payment_colors = [];
        var template = '';
        var bgtrans = '';
        var countrans = 0;
        var i = 0;
        objData.data.payments.forEach((detail) => {
          var total_quantity = parseInt(detail.quantity);
          var percentage = roundNumber(total_quantity / 500 * 100,2);
          if(percentage <= 100){
            countrans = percentage;
          }else{
            countrans = 100;
          }
          bgtrans = "bg-success";
          template += `
            <div class="widget-chart-info-progress">
              <b>${detail.payment_type}<?= $payments['payment_type'] ?></b>
              <span class="pull-right">${roundNumber(detail.total,2)}</span>
            </div>
            <div class="progress progress-sm m-b-15">
              <div class="progress-bar progress-bar-striped progress-bar-animated rounded-corner ${bgtrans}" style="width:${countrans}%;"></div>
            </div>
          `;
          payment_name.push(detail.payment_type);
          payment_quantity.push(detail.quantity);
          i++;
        });
        $('#transaction_summary').html(template);
        chart_type_payments = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: payment_name,
              datasets: [{
              backgroundColor: [
                degraded1,
                degraded2,
                degraded3,
                degraded4,
                degraded5,
                degraded6
              ],
              hoverBackgroundColor: [
                degraded1,
                degraded2,
                degraded3,
                degraded4,
                degraded5,
                degraded6
              ],
              data: payment_quantity,
            }]
          },
          options: {
            maintainAspectRatio: false,
            cutoutPercentage: 75,
            legend: {
              position: 'bottom',
              display: true,
              labels: {
                boxWidth: 8
              }
            },
            tooltips: {
              displayColors: false,
            }
          }
        });
      }
    }
  }
}
function libre_services(year){
  if(statistics_year){
    statistics_year.destroy();
  }
  const url = base_url+'/dashboard/libre_services/'+year;
  const http = new XMLHttpRequest();
  http.open("GET", url, true);
  http.send();
  http.onreadystatechange = function () {
    if(this.readyState == 4 && this.status == 200){
      const objData = JSON.parse(this.responseText);
      if(objData.status == 'success'){
        var ctx = document.getElementById('libre_services').getContext('2d');
        var degraded1 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded1.addColorStop(0, '#FFBC75');
        degraded1.addColorStop(1, '#FFBC75');
        var degraded2 = ctx.createLinearGradient(0, 0, 0, 300);
        degraded2.addColorStop(0, '#FF7599');
        degraded2.addColorStop(1, '#FF7599');
        $('#serv-year').text(objData.data.year);
        var arrProducts = [];
        objData.data.products.forEach((detail) => {
          arrProducts.push(detail.total);
        });
        var arrServices = [];
        objData.data.services.forEach((detail) => {
          arrServices.push(detail.total);
        });
        statistics_year = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            datasets: [
              {
                label: 'Libre',
                data: arrProducts,
                backgroundColor: degraded1,
                borderColor: degraded1,
                pointRadius: "0",
                pointHoverRadius: "0",
                borderWidth: 3
              },{
                label: 'Servicio',
                data: arrServices,
                backgroundColor: degraded2,
                borderColor: degraded2,
                pointRadius: "0",
                pointHoverRadius: "0",
                borderWidth: 3
              }
            ]
          },
          options: {
            maintainAspectRatio: false,
            legend: {
              display: true,
              labels: {
                fontColor: '#585757',
                boxWidth: 40
              }
            },
            tooltips: {
              mode: "index",
              intersect: false
            },
            hover: {
              mode: "nearest",
              intersect: true
            },
            scales: {
              xAxes: [{
                ticks: {
                  beginAtZero: true,
                  fontColor: '#585757'
                },
                gridLines: {
                  display: true,
                  color: "rgba(0, 0, 0, 0.07)"
                },
              }],
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  fontColor: '#585757'
                },
                gridLines: {
                  display: true,
                  color: "rgba(0, 0, 0, 0.07)"
                },
              }]
            }
          }
        });
      }
    }
  }
}
