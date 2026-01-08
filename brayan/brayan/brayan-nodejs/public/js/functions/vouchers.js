let table,table_series;
let table_name = "list";
document.addEventListener('DOMContentLoaded', function(){
    current_date();
    table_configuration('#'+table_name,'Lista de comprobantes');
    table = $('#'+table_name).DataTable({
          "ajax":{
            "url": " "+base_url+"/vouchers/list_records",
            "dataSrc": ""
          },
          "deferRender": true,
          "idDataTables": "1",
          "columns": [
              {"data":"n",'className':'text-center'},
              {"data":"voucher"},
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
            loaderin('.panel-vouchers');
        }else{
            loaderout('.panel-vouchers');
        }
    });
    table_configuration('#list_series','Lista de series');
    table_series = $('#list_series').DataTable({
          "idDataTables": "1"
    });
    if(document.querySelector("#transactions")){
        var transactions = document.querySelector("#transactions");
        transactions.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions').parsley().isValid()){
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/vouchers/action';
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
    if(document.querySelector("#transactions_serie")){
        var transactions_serie = document.querySelector("#transactions_serie");
        transactions_serie.onsubmit = function(e) {
            e.preventDefault();
            if($('#transactions_serie').parsley().isValid()){
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/vouchers/action_serie';
                var formData = new FormData(transactions_serie);
                request.open("POST",ajaxUrl,true);
                request.send(formData);
                request.onreadystatechange = function(){
                    if(request.readyState == 4 && request.status == 200){
                        var objData = JSON.parse(request.responseText);
                        if(objData.status == "success"){
                            $('#modal-serie').modal("hide");
                            transactions_serie.reset();
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
    $('#transactions_serie').parsley();
    $('#date').datetimepicker({locale:'es',format:'DD/MM/YYYY'});
    $('#date').val(moment().format('DD/MM/YYYY'));
}, false);
function current_date(){
    var date = new Date();
    var month = date.getMonth()+1;
    var dia = date.getDate();
    var year = date.getFullYear();
    if(dia<10){
        dia='0'+dia;
    }
    if(month<10){
        month='0'+month;
    }
    document.querySelector("#date").value = year+"-"+month+"-"+dia;
}
function view(idvoucher){
    $('[data-toggle="tooltip"]').tooltip('hide');
    document.querySelector('#text-view').innerHTML = "Lista de Series"
    var ajaxUrl = base_url+'/vouchers/list_series/'+idvoucher;
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            $('#modal-view').modal("show");
            document.querySelector('#listSeries').innerHTML = request.responseText;
        }
    }
}
function update(idvoucher){
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('#transactions').parsley().reset();
    document.querySelector('#text-title').innerHTML = "Actualizar Comprobante";
    document.querySelector('#text-button').innerHTML ="Guardar Cambios";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/vouchers/select_record/'+idvoucher;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
                document.querySelector("#idvoucher").value = objData.data.encrypt_id;
                document.querySelector("#voucher").value = objData.data.voucher;
                document.querySelector("#listStatus").value = objData.data.state;
                $('#modal-action').modal('show');
            }else{
              alert_msg("error",objData.msg);
            }
        }
    }
}
function remove(idvoucher){
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
                    var ajaxUrl = base_url+'/vouchers/remove';
                    var strData = "idvoucher="+idvoucher;
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
$('#fromc,#until').on('keyup change',function(){
    available();
});
function available(){
    var num1 = document.querySelector("#fromc").value;
    var num2 = document.querySelector("#until").value;
    if(parseInt(num1) <= parseInt(num2)){
        var sub = parseInt(num2) - parseInt(num1);
        var total = parseInt(sub) + 1;
        document.querySelector("#available").value = parseInt(total);
    }else if(parseInt(num1) > parseInt(num2)){
        document.querySelector("#fromc").value = 1;
        document.querySelector("#until").value = 1;
        document.querySelector("#available").value = 1;
    }
}
function add_serie(idvoucher){
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('#transactions_serie').parsley().reset();
    document.querySelector('#text-series').innerHTML ="Guardar Registro";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/vouchers/select_record/'+idvoucher;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status){
                var transactions_serie = document.querySelector("#transactions_serie");
                transactions_serie.reset();
                current_date();
                document.querySelector("#idserie").value = "";
                $('#date').val(moment().format('DD/MM/YYYY'));
                document.querySelector("#idvouchers").value = objData.data.encrypt_id;
                document.querySelector('#text-serie').innerHTML = 'Asignar Serie <i class="mdi mdi-chevron-right"></i> '+objData.data.voucher;
            }
        }
        $('#modal-serie').modal('show');
    }
}
function edit_serie(idvoucher){
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('#transactions_serie').parsley().reset();
    document.querySelector('#text-series').innerHTML ="Guardar Cambios";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/vouchers/select_serie/'+idvoucher;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
                document.querySelector('#text-serie').innerHTML = 'Actualizar Serie <i class="mdi mdi-chevron-right"></i> '+objData.data.voucher;
                document.querySelector("#idserie").value = objData.data.encrypt_id;
                document.querySelector("#idvouchers").value = objData.data.encrypt_voucher;
                document.querySelector("#date").value = moment(objData.data.date).format('DD/MM/YYYY');
                document.querySelector("#serie").value = objData.data.serie;
                document.querySelector("#fromc").value = objData.data.fromc;
                document.querySelector("#until").value = objData.data.until;
                document.querySelector("#available").value = objData.data.available;
                document.querySelector("#used").value = objData.data.until-objData.data.available;
            }
        }
        $('#modal-serie').modal('show');
    }
}
function modal(){
    document.querySelector('#text-title').innerHTML = "Nuevo Comprobante";
    document.querySelector('#text-button').innerHTML ="Guardar Registro";
    document.querySelector('#idvoucher').value ="";
    document.querySelector("#transactions").reset();
    $('#transactions').parsley().reset();
    $('#modal-action').modal('show');
}
