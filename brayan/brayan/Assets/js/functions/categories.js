let table;
let table_name = "list";
const filebtn = document.querySelector("#import_categories");
const filetext = document.querySelector("#text-file");
document.addEventListener('DOMContentLoaded', function(){
    table_configuration('#'+table_name,'Lista de categorias');
    table = $('#'+table_name).DataTable({
          "ajax":{
            "url": " "+base_url+"/categories/list_records",
            "dataSrc": ""
          },
          "deferRender": true,
          "idDataTables": "1",
          "columns": [
              {"data":"n",'className':'text-center'},
              {"data":"category"},
              {"data":"description"},
              {"data":"associates","className": 'text-center'},
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
            $('#'+table_name+'_wrapper .dt-buttons').append($('#'+table_name+'-btns-exportable').contents());
            $('#'+table_name+'_wrapper div.container-options').append($('#'+table_name+'-btns-tools').contents());
          }
    }).on('processing.dt', function (e, settings, processing) {
        if (processing) {
            loaderin('.panel-categories');
        }else{
            loaderout('.panel-categories');
        }
    });
    if(document.querySelector("#transactions")){
        var transactions = document.querySelector("#transactions");
        transactions.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions').parsley().isValid()){
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/categories/action';
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
    if(document.querySelector("#transactions_import")){
        var transactions_import = document.querySelector("#transactions_import");
        transactions_import.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions_import').parsley().isValid()){
                if($("#import_categories").get(0).files.length == 0){
                    alert_msg("info","Selecionar un archivo excel para realizar el proceso.");
                    return false;
                }else{
                    var allowed_extensions = [".xls",".xlsx"];
                    var file = $("#import_categories");
                    var exp_reg = new RegExp("([a-zA-Z0-9\s_\\-.\:])+(" + allowed_extensions.join('|') + ")$");

                    if(!exp_reg.test(file.val().toLowerCase())){
                      alert_msg("warning","Selecionar una extensión permitida .xls o .xlsx.");
                      return false;
                    }
                    loading.style.display = "flex";
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = base_url+'/categories/import';
                    var formData = new FormData(transactions_import);
                    request.open("POST",ajaxUrl,true);
                    request.send(formData);
                    request.onreadystatechange = function(){
                        if(request.readyState == 4 && request.status == 200){
                          var objData = JSON.parse(request.responseText);
                          if(objData.status == "success"){
                                $('#modal-import').modal("hide");
                                alert_msg("success",objData.msg);
                                refresh_table();
                            }else if(objData.status == "warning"){
                                $('#modal-import').modal("hide");
                                alert_msg("warning",objData.msg);
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
    }
},false);
window.addEventListener('load', function(){
    $('#transactions').parsley();
}, false);
$('#import_categories').on('change',function(){
    document.querySelector('#text-file').value = this.files.item(0).name;
});
filetext.addEventListener("click", function() {
  filebtn.click();
});
function update(idcategory){
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('#transactions').parsley().reset();
    document.querySelector('#text-title').innerHTML = "Actualizar Categoria";
    document.querySelector('#text-button').innerHTML ="Guardar Cambios";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/categories/select_record/'+idcategory;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
                document.querySelector("#idcategory").value = objData.data.encrypt_id;
                document.querySelector("#category").value = objData.data.category;
                document.querySelector("#description").value = objData.data.description;
                document.querySelector("#listStatus").value = objData.data.state;
                $('#modal-action').modal('show');
            }else{
              alert_msg("error",objData.msg);
            }
        }
    }
}
function remove(idcategory){
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
                    var ajaxUrl = base_url+'/categories/remove';
                    var strData = "idcategory="+idcategory;
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
    document.querySelector('#text-title').innerHTML = "Nueva Categoria";
    document.querySelector('#text-button').innerHTML ="Guardar Registro";
    document.querySelector('#idcategory').value ="";
    document.querySelector("#transactions").reset();
    $('#transactions').parsley().reset();
    $('#modal-action').modal('show');
}
function modal_import(){
    $('[data-toggle="tooltip"]').tooltip('hide');
    document.querySelector('#text-title-import').innerHTML = "Importar Categorias";
    document.querySelector('#text-button-import').innerHTML = "Importar";
    document.querySelector("#transactions_import").reset();
    $('#modal-import').modal('show');
}
