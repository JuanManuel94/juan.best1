let table;
let table_name = "list";
document.addEventListener('DOMContentLoaded', function(){;
    table_configuration('#'+table_name,'Lista de unidades');
    table = $('#'+table_name).DataTable({
          "ajax":{
            "url": " "+base_url+"/unit/list_records",
            "dataSrc": ""
          },
          "deferRender": true,
          "idDataTables": "1",
          "columns": [
              {"data":"n",'className':'text-center'},
              {"data":"united"},
              {"data":"code"},
              {"data": "registration_date","render": function(data,type,full,meta){
                return moment(data).format('DD/MM/YYYY');
              },'className':'text-center'},
              {"data":"state","render": function(data,type,full,meta){
                  var state = '';
                  if(data == 1){
                      state = '<span class="label label-success">ACTIVO</span>';
                  }
                  if(data == 2){
                      state = '<span class="label label-danger">DESACTIVADO</span>';
                  }
                  return state;
              },'className':'text-center'},
              {"data":"options",'className':'text-center','sWidth':'40px'}
          ],
          initComplete: function(oSettings, json){
            $('#'+table_name+'_wrapper div.container-options').append($('#'+table_name+'-btns-tools').contents());
          }
    }).on('processing.dt', function (e, settings, processing) {
        if (processing) {
            loaderin('.panel-unit');
        }else{
            loaderout('.panel-unit');
        }
    });
    if(document.querySelector("#transactions")){
        var transactions = document.querySelector("#transactions");
        transactions.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions').parsley().isValid()){
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/unit/action';
                var formData = new FormData(transactions);
                request.open("POST",ajaxUrl,true);
                request.send(formData);
                request.onreadystatechange = function(){
                    if(request.readyState == 4 && request.status == 200){
                        var objData = JSON.parse(request.responseText);
                        if(objData.status == "success"){
                            $('#modal-action').modal("hide");
                            transactions.reset();
                            alert_msg("success",objData.msg);
                            refresh_table();
                        }else{
                            alert_msg("error",objData.msg);
                        }
                    }
                    loading.style.display = "none";
                    return false;
                }
            }
        }
    }
},false);
window.addEventListener('load', function(){
    $('#transactions').parsley();
}, false);
function update(idunit){
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('#transactions').parsley().reset();
    document.querySelector('#text-title').innerHTML = "Actualizar Unidad";
    document.querySelector('#text-button').innerHTML ="Guardar Cambios";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/unit/select_record/'+idunit;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
                document.querySelector("#idunit").value = objData.data.encrypt_id;
                document.querySelector("#code").value = objData.data.code;
                document.querySelector("#united").value = objData.data.united;
                document.querySelector("#listStatus").value = objData.data.state;
                $('#modal-action').modal('show');
            }else{
              alert_msg("error",objData.msg);
            }
        }
    }
}
function remove(idunit){
    var alsup = $.confirm({
        theme: 'modern',
        draggable: false,
        closeIcon: true,
        animationBounce: 2.5,
        escapeKey: false,
        type: 'info',
        icon: 'far fa-question-circle',
        title: 'ELIMINAR',
        content: 'Esta seguro que desea eliminar este registro.',
        buttons: {
            remove: {
                text: 'Aceptar',
                btnClass: 'btn-info',
                action: function () {
                    this.buttons.remove.setText('<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...');
                    this.buttons.remove.disable();
                    $('.jconfirm-closeIcon').remove();
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = base_url+'/unit/remove';
                    var strData = "idunit="+idunit;
                    request.open("POST",ajaxUrl,true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request.send(strData);
                    request.onreadystatechange = function(){
                        if(request.readyState == 4 && request.status == 200){
                            alsup.close();
                            var objData = JSON.parse(request.responseText);
                            if(objData.status == 'success'){
                                $('[data-toggle="tooltip"]').tooltip('hide');
                                alert_msg("success",objData.msg);
                                refresh_table();
                            }else if(objData.status == 'exists'){
                                $('[data-toggle="tooltip"]').tooltip('hide');
                                alert_msg("info",objData.msg);
                            }else{
                                $('[data-toggle="tooltip"]').tooltip('hide');
                                alert_msg("error",objData.msg);
                            }
                        }
                        return false;
                    }
                }
            },
            close: {
              text: 'Cancelar'
            }
        }
    });
}
function modal(){
    document.querySelector('#text-title').innerHTML = "Nueva Unidad";
    document.querySelector('#text-button').innerHTML ="Guardar Registro";
    document.querySelector('#idunit').value ="";
    document.querySelector("#transactions").reset();
    $('#transactions').parsley().reset();
    $('#modal-action').modal('show');
}
