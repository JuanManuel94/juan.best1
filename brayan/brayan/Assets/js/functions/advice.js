let table;
document.addEventListener('DOMContentLoaded', function(){
    table_configuration('#list','Lista de correos electronicos');
    table = $('#list').DataTable({
          "ajax":{
            "url": " "+base_url+"/advice/list_records",
            "dataSrc": ""
          },
          "deferRender": true,
          "idDataTables": "1",
          "columns": [
              {"data":"n",'className':'text-center'},
              {"data":"client"},
              {"data":"affair"},
              {"data": "registration_date","render": function(data,type,full,meta){
                return moment(data).format('DD/MM/YYYY h:mm a');
              }},
              {"data":"sender"},
              {"data":"state","render": function(data,type,full,meta){
                  var state = '';
                  if(data == 1){
                      state = '<span class="label label-success">ENVIADO</span>';
                  }
                  if(data == 2){
                      state = '<span class="label label-danger">ERROR</span>';
                  }
                  return state;
              },'className':'text-center'},
              {"data":"options",'className':'text-center','sWidth':'40px'}
          ]
    }).on('processing.dt', function (e, settings, processing) {
        if (processing) {
            loaderin('.panel-advice');
        }else{
            loaderout('.panel-advice');
        }
    });
},false);
function resend(idbill,idemail){
    $('[data-toggle="tooltip"]').tooltip('hide');
    loading.style.display = "flex";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/advice/resend/'+idbill+'/'+idemail;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == 'success'){
                alert_msg("success",objData.msg);
                refresh_table();
            }else{
                alert_msg("error",objData.msg);
                refresh_table();
            }
        }
        loading.style.display = "none";
        return false;
    }
}
