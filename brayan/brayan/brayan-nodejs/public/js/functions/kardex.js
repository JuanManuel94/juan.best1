let table;
document.addEventListener('DOMContentLoaded', function(){
    table_configuration('#list','Kardex valorizado');
    table = $('#list').DataTable({
          "ajax":{
            "url": " "+base_url+"/kardex/list_records",
            "dataSrc": ""
          },
          "deferRender": true,
          "idDataTables": "1",
          "columns": [
              {"data": "description","render": function(data,type,full,meta){
                  return '<a href="'+base_url+'/kardex/detail/'+full.encrypt+'">'+data+'</a>';
              }},
              {"data":"number_income"},
              {"data":"cost_incomes"},
              {"data":"total_income"},
              {"data":"number_departure"},
              {"data":"cost_departures"},
              {"data":"total_departure"},
              {"data":"balance_amount"},
              {"data":"cost_balance"},
              {"data":"total_balance"}
          ],
          initComplete: function(oSettings, json){
            /*$("#list_wrapper div.container-options").html('<div class="options-group btn-group m-r-5">\
            <button type="button" class="btn btn-white" onclick="modal();"><i class="fas fa-plus mr-1"></i>Nuevo</button>\
            </div>');*/
          }
    });
},false);
