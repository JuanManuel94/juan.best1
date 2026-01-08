let statistics,table;
$('#listYears').on('change',function(){
    table.ajax.url(base_url+"/payments/payment_summary/"+$(this).val()).load();
});
document.addEventListener('DOMContentLoaded', function(){
    table_configuration('#list','Resumen de transacciones');
    table = $('#list').DataTable({
        "ajax":{
            "url": " "+base_url+"/payments/payment_summary/"+$('#listYears').val(),
            "dataSrc": ""
        },
        "deferRender": true,
        "idDataTables": "1",
        "columns": [
            {"data":"months",'className':'text-center',"sWidth":"20px"},
            {"data":"month",'className':'text-center text-uppercase'},
            {"data":"total",'className':'text-center'},
            {"data":"payment",'className':'text-center'}
        ],
        "drawCallback": function(settings){
            if(statistics){
                statistics.destroy();
            }

            var months = [];
            var payments = [];
            var api = this.api().rows({search:'applied'}).data();
            var total_operations=0,total_charged=0;
            $.each(api, function( key, value ){
                months.push(value['month']);
                payments.push(parseFloat(value['count_payment']));
                total_operations += parseInt(value['total']);
                total_charged += parseFloat(value['count_payment']);
            });

            $(api.column(2).footer()).html(total_operations);
            $(api.column(3).footer()).html(currency_symbol + formatMoney(total_charged));
            
            var ctx = document.getElementById('payments').getContext('2d');
            var degraded1 = ctx.createLinearGradient(0, 0, 0, 300);
            degraded1.addColorStop(0, 'rgba(255, 70, 41, 2)');
            degraded1.addColorStop(1, 'rgba(115, 120, 125, 0.0)');
            var degraded2 = ctx.createLinearGradient(0, 0, 0, 300);
            degraded2.addColorStop(0, '#FF4629');
            degraded2.addColorStop(1, '#AA2E1B');

            var chart_data = {
                labels:months,
                datasets:[
                    {
                        label : 'Transacciones',
                        backgroundColor: degraded1,
                        borderColor: degraded2,
                        pointRadius: "0",
                        pointHoverRadius: "0",
                        borderWidth: 3,
                        data:payments
                    }
                ]
            };

            statistics = new Chart(ctx, {
                type:'line',
                data:chart_data,
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
    }).on('processing.dt', function (e, settings, processing) {
        if (processing) {
        loaderin('.panel-statistics');
        }else{
        loaderout('.panel-statistics');
        }
    }).on('draw', function (){});
},false);
