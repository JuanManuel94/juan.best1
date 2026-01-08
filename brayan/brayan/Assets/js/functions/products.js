let table;
let table_name = "list";
const filebtn = document.querySelector("#import_products");
const filetext = document.querySelector("#text-file");
document.addEventListener('DOMContentLoaded', function(){
    table_configuration('#'+table_name,'Lista de productos');
    table = $('#'+table_name).DataTable({
          "ajax":{
            "url": " "+base_url+"/products/list_records",
            "dataSrc": ""
          },
          "deferRender": true,
          "idDataTables": "1",
          "columns": [
              {"data":"internal_code",'className':'text-center'},
              {"data": "product","render": function(data,type,full,meta){
                  return '<a href="'+base_url+'/products/detail/'+full.encrypt+'">'+data+'</a>';
              }},
              {"data":"price",'className':'text-center'},
              {"data":"stock",'className':'text-center'},
              {"data":"united"},
              {"data":"category"},
              {"data":"provider"},
              {"data":"model"},
              {"data":"brand"},
              {"data":"serial_number"},
              {"data":"mac"},
              {"data":"state",'className':'text-center'}, 
              {"data":"options",'className':'text-center','sWidth':'40px'}
          ],
          initComplete: function(oSettings, json){
            $('#'+table_name+'_wrapper .dt-buttons').append($('#'+table_name+'-btns-exportable').contents());
            $('#'+table_name+'_wrapper div.container-options').append($('#'+table_name+'-btns-tools').contents());
          }
    }).on('processing.dt', function (e, settings, processing) {
        if (processing) {
            loaderin('.panel-products');
        }else{
            loaderout('.panel-products');
        }
    });
    if(document.querySelector("#transactions")){
        var transactions = document.querySelector("#transactions");
        transactions.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions').parsley().isValid()){
                var providers = document.querySelector("#listProviders").value;
                var categories = document.querySelector("#listCategories").value;
                if(providers == ""){
                  alert_msg("error","No seleccionaste ningún proveedor, seleccione uno.");
                  return false;
                }
                if(categories == ""){
                  alert_msg("error","No seleccionaste ningúna categoria, seleccione uno.");
                  return false;
                }
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/products/action';
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

                if($("#import_products").get(0).files.length == 0){
                    alert_msg("info","Selecionar un archivo excel para realizar el proceso.");
                    return false;
                }else{
                    var allowed_extensions = [".xls",".xlsx"];
                    var file = $("#import_products");
                    var exp_reg = new RegExp("([a-zA-Z0-9\s_\\-.\:])+(" + allowed_extensions.join('|') + ")$");

                    if(!exp_reg.test(file.val().toLowerCase())){
                      alert_msg("warning","Selecionar una extensión permitida .xls o .xlsx.");
                      return false;
                    }
                    loading.style.display = "flex";
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = base_url+'/products/import';
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
window.addEventListener('load', function() {
  list_categories();
  list_units();
  list_providers();
  $('#transactions').parsley();
  $("#mac").mask("99:99:99:99:99:99");
  if(document.querySelector("#image")){
      var file = document.querySelector("#image");
      file.onchange = function(e) {
          var uploadFoto = document.querySelector("#image").value;
          var fileimg = document.querySelector("#image").files;
          var nav = window.URL || window.webkitURL;
          if(uploadFoto !=''){
              var type = fileimg[0].type;
              var size = fileimg[0].size;
              if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png'){
                  alert_msg("info","¡La imagen debe estar en formato PNG, JPG o JPEG!");
                  if(document.querySelector('#img_product')){
                      document.querySelector('#img_product').src = "";
                  }
                  file.value="";
                  return false;
              }else{
                  if(document.querySelector('#img_product')){
                      document.querySelector('#img_product').src = "";
                  }
                  var objeto_url = nav.createObjectURL(this.files[0]);
                  document.querySelector('#img_product').src = objeto_url;
              }
          }else{
              alert_msg("error","¡No seleccionaste una imagen!");
              if(document.querySelector('#img_product')){
                  document.querySelector('#img_product').src = "";
              }
          }
      }
  }
},false);
$('#import_products').on('change',function(){
  document.querySelector('#text-file').value = this.files.item(0).name;
});
filetext.addEventListener("click", function() {
  filebtn.click();
});
$('#extra').on('change',function(){
  if($(this).is(':checked')){
    $('#cont-serie').show('fast');
    $('#cont-mac').show('fast');
  }else{
    $('#cont-serie').hide('fast');
    $('#cont-mac').hide('fast');
  }
});
function list_categories(){
    if(document.querySelector('#listCategories')){
        var ajaxUrl = base_url+'/categories/list_categories';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET",ajaxUrl,true);
        request.send();
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listCategories').innerHTML = request.responseText;
                $('#listCategories').select2({width: '100%'});
            }
        }
    }
}
function list_units(){
    if(document.querySelector('#listUnits')){
        var ajaxUrl = base_url+'/unit/list_units';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET",ajaxUrl,true);
        request.send();
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listUnits').innerHTML = request.responseText;
                $('#listUnits').select2({width: '100%'});
            }
        }
    }
}
function list_providers(){
    if(document.querySelector('#listProviders')){
        var ajaxUrl = base_url+'/providers/list_providers';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET",ajaxUrl,true);
        request.send();
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                document.querySelector('#listProviders').innerHTML = request.responseText;
                $('#listProviders').select2({width: '100%'});
            }
        }
    }
}
function update(idproduct){
    $('[data-toggle="tooltip"]').tooltip('hide');
    $('#transactions').parsley().reset();
    document.querySelector('#text-title').innerHTML = "Actualizar Producto";
    document.querySelector('#text-button').innerHTML ="Guardar Cambios";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url+'/products/select_record/'+idproduct;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
                document.querySelector("#idproduct").value = objData.data.encrypt_id;
                document.querySelector("#barcode").value = objData.data.barcode;
                document.querySelector("#product").value = objData.data.product;
                document.querySelector("#purchase_price").value = objData.data.purchase_price;
                document.querySelector("#sale_price").value = objData.data.sale_price;
                document.querySelector("#stock").value = objData.data.stock;
                document.querySelector("#stock_alert").value = objData.data.stock_alert;
                document.querySelector("#listCategories").value = objData.data.encrypt_category;
                $('#listCategories').select2({width:'100%'});
                document.querySelector("#listUnits").value = objData.data.encrypt_unit;
                $('#listUnits').select2({width:'100%'});
                document.querySelector("#listProviders").value = objData.data.encrypt_provider;
                $('#listProviders').select2({width:'100%'});
                document.querySelector("#model").value = objData.data.model;
                document.querySelector("#description").value = objData.data.description;
                document.querySelector("#brand").value = objData.data.brand;
                document.querySelector("#current_photo").value = objData.data.image;
                if(objData.data.extra_info == 1){
                  document.querySelector("#extra").checked = true;
                  $('#cont-serie').show('fast');
                  $('#cont-mac').show('fast');
                }else{
                  document.querySelector("#extra").checked = false;
                  $('#cont-serie').hide('fast');
                  $('#cont-mac').hide('fast');
                }
                document.querySelector("#serie").value = objData.data.serial_number;
                document.querySelector("#mac").value = objData.data.mac;
                if(objData.data.image == "no_image.jpg"){
                  document.querySelector('#img_product').src = base_url+"/Assets/images/default/no_image.jpg";
                }else{
                  fetch(objData.data.url_image,{ method:'HEAD'}).then(answer => {
                    if(answer.ok){
                      document.querySelector("#img_product").src = objData.data.url_image;
                    }else{
                      console.log("not");
                      document.querySelector('#img_product').src = base_url+"/Assets/images/default/no_image.jpg";
                    }
                  }).catch(err => console.log('Error:', err));
                }
                $('#modal-action').modal('show');
            }else{
              alert_msg("error",objData.msg);
            }
        }
    }
}
function remove(idproduct){
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
                    var ajaxUrl = base_url+'/products/remove';
                    var strData = "idproduct="+idproduct;
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
function view(idproduct){
    $(location).attr('href', base_url+"/products/detail/"+idproduct);
}
function modal(){
  document.querySelector('#text-title').innerHTML = "Nuevo Producto";
  document.querySelector('#text-button').innerHTML ="Guardar Registro";
  document.querySelector('#idproduct').value = "";
  document.querySelector('#img_product').src = base_url+"/Assets/images/default/no_image.jpg";
  $('#cont-serie').hide();
  $('#cont-mac').hide();
  document.querySelector("#transactions").reset();
  $('#transactions').parsley().reset();
  $('#modal-action').modal('show');
  list_categories();
  list_units();
  list_providers();
}
function modal_import(){
    $('[data-toggle="tooltip"]').tooltip('hide');
    document.querySelector('#text-title-import').innerHTML = "Importar Productos";
    document.querySelector('#text-button-import').innerHTML ="Importar";
    document.querySelector("#transactions_import").reset();
    $('#modal-import').modal('show');
}
function exports(){
    alert_msg('loader','Generando excel.');
    $('[data-toggle="tooltip"]').tooltip('hide');
    setTimeout(function(){
        $('#gritter-notice-wrapper').remove();
        alert_msg('success','Se exporto excel correctamente.');
        window.open(base_url+'/products/export', '_blank');
    }, 1000);
}
function filter(column,search){
    if(search == ""){
       table.search('').columns().search( '' ).draw();
    }else{
       column = parseInt(column);
       table.columns(column).search(search).draw();
    }
}
