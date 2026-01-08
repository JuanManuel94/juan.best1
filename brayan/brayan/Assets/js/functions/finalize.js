let btnadd = document.querySelector(".btn-add");
let ticket = document.querySelector("#idticket");
let client = document.querySelector("#idclient");
document.addEventListener('DOMContentLoaded', function(){
  if(document.querySelector("#transactions")){
    var transactions = document.querySelector("#transactions");
    transactions.onsubmit = function(e){
      e.preventDefault();
      if($('#transactions').parsley().isValid()){
        loading.style.display = "flex";
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url+'/tickets/complete_ticket';
        var formData = new FormData(transactions);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
          if(request.readyState == 4 && request.status == 200){
            var objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
              var alsup = $.confirm({
                theme: 'modern',
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: 'success',
                icon: 'far fa-check-circle',
                title: 'OPERACIÓN EXITOSA',
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: 'Aceptar',
                    btnClass: 'btn-success',
                    action: function () {
                      history.back();
                    }
                  }
                }
              });
            }else if(objData.status == "info"){
              var alsup = $.confirm({
                theme: 'modern',
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: 'info',
                icon: 'far fa-question-circle',
                title: 'IMPORTANTE',
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: 'Aceptar',
                    btnClass: 'btn-info',
                    action: function () {
                      history.back();
                    }
                  }
                }
              });
            }else{
              var alsup = $.confirm({
                theme: 'modern',
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: 'red',
                icon: 'far fa-times-circle',
                title: 'ERROR',
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: 'Aceptar',
                    btnClass: 'btn-red',
                    action: function () {
                      history.back();
                    }
                  }
                }
              });
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
  uploadImage();
  $('#transactions').parsley();
  show_images(ticket.value);
  total_images(ticket.value);
  if(document.querySelector(".btn-add")){
      btnadd.onclick = function(e){
          let key = Date.now();
          let newElement = document.createElement("div");
          newElement.id = "div"+key;
          newElement.classList.add("item-image");
            newElement.innerHTML += `
              <div class="content-image text-center">
                <div class="container-image"></div>
                <div class="tools-image">
                  <a href="" class="btn btn-inverse btn-view-image m-b-5" data-lightbox="example-set"><i class="fas fa-image mr-1"></i>Ver imagen</a>
                  <button type="button" class="btn btn-info m-b-5 btn-download"><i class="fa fa-download mr-1"></i>Descargar</button>
                  <button type="button" class="btn btn-danger m-b-5 btn-delete notblock" onclick="removeImage('#div${key}')"><i class="fa fa-trash-alt mr-1"></i>Eliminar</button>
                  <input type="file" name="photo" id="img${key}" key="${key}" class="upload-file">
                  <label for="img${key}" class="btn btn-success m-b-5 btn-upload"><i class="fas fa-upload mr-1"></i>Elegir imagen</label>
                </div>
              </div>`;
          document.querySelector("#gallery").appendChild(newElement);
          document.querySelector("#div"+key+" .btn-upload").click();
          uploadImage();
      }
  }
},false);
document.querySelector('#radio_yes').addEventListener('click', function(e) {
  $('#cont-attention').hide('fast');
});
document.querySelector('#radio_not').addEventListener('click', function(e) {
  $('#cont-attention').hide('fast');
});
function show_images(idticket){
  let request = (window.XMLHttpRequest) ?  new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  let ajaxUrl = base_url+'/tickets/show_images/'+idticket;
  request.open("GET",ajaxUrl,true);
  request.send();
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      let objData = JSON.parse(request.responseText);
      if(objData.status == "success"){
        let htmlImage = "";
        let objImages = objData.data;
        for(let p = 0; p < objImages.length; p++){
          let key = Date.now()+p;
          htmlImage += `
          <div id="div${key}" class="item-image">
            <div class="content-image text-center">
              <div class="container-image">
                <img src="${objImages[p].url_image}" class="image-upload">
              </div>
              <div class="tools-image">
                <a href="${objImages[p].url_image}" class="btn btn-inverse m-b-5" data-lightbox="example-set"><i class="fas fa-image mr-1"></i>Ver imagen</a>
                <button type="button" class="btn btn-info m-b-5 btn-download" onclick="download_files('${objImages[p].url_image}','${objImages[p].image}')"><i class="fa fa-download mr-1"></i>Descargar</button>
          `;
          if(permission_remove === "1"){
            htmlImage += `<button type="button" class="btn btn-danger m-b-5 btn-delete" onclick="removeImage('#div${key}')" image="${objImages[p].image}"><i class="fa fa-trash-alt mr-1"></i>Eliminar</button>`;
          }
          htmlImage += `
              </div>
            </div>
          </div>
          `;
        }
        document.querySelector("#gallery").innerHTML = htmlImage;
      }
    }
  }
}
function total_images(idticket){
  let request = (window.XMLHttpRequest) ?  new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
  let ajaxUrl = base_url+'/tickets/number_images/'+idticket;
  request.open("GET",ajaxUrl,true);
  request.send();
  request.onreadystatechange = function(){
    if(request.readyState == 4 && request.status == 200){
      let objData = JSON.parse(request.responseText);
      if(objData.status == "success"){
        document.querySelector("#counter").value = objData.data;
      }
    }
  }
}
function uploadImage(){
  let uploadfile = document.querySelectorAll(".upload-file");
  uploadfile.forEach(function(uploadfile){
    uploadfile.addEventListener('change', function(){
      let parentId = this.getAttribute("key");
      let idFile = this.getAttribute("id");
      let uploadFoto = document.querySelector("#"+idFile).value;
      let fileimg = document.querySelector("#"+idFile).files;
      let prevImg = document.querySelector("#div"+parentId+" .content-image .container-image");
      let nav = window.URL || window.webkitURL;
      if(uploadFoto !=''){
        let type = fileimg[0].type;
        if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png'){
          alert_msg("info","¡La imagen debe estar en formato PNG, JPG o JPEG!");
          prevImg.innerHTML = "Archivo no válido";
          uploadFoto.value = "";
          return false;
        }else{
          let objeto_url = nav.createObjectURL(this.files[0]);
          prevImg.innerHTML = `<img class="img-responsive image-loading" src="${base_url}/Assets/images/default/loading.gif">`;
          let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
          let ajaxUrl = base_url+'/tickets/register_image';
          let formData = new FormData();
          formData.append('idclient',client.value);
          formData.append('idticket',ticket.value);
          formData.append("photo", this.files[0]);
          request.open("POST",ajaxUrl,true);
          request.send(formData);
          request.onreadystatechange = function(){
            if(request.readyState != 4) return;
            if(request.status == 200){
              let objData = JSON.parse(request.responseText);
              if(objData.status == "success"){
                prevImg.innerHTML = `<img class="img-responsive image-upload" src="${objData.url_image}">`;
                document.querySelector("#div"+parentId+" .content-image .tools-image .btn-delete").setAttribute("image",objData.image);
                if(permission_remove === "1"){
                  document.querySelector("#div"+parentId+" .content-image .tools-image .btn-delete").classList.remove("notblock");
                }
                document.querySelector("#div"+parentId+" .content-image .tools-image .btn-download").setAttribute("onclick","download_files('"+objData.url_image+"','"+objData.image+"')");
                document.querySelector("#div"+parentId+" .content-image .tools-image .btn-upload").classList.add("notblock");
                document.querySelector("#div"+parentId+" .content-image .tools-image .btn-view-image").setAttribute("href",objData.url_image);
                total_images(ticket.value);
                alert_msg("success",objData.msg);
              }else{
                alert_msg("error",objData.msg);
              }
            }
          }
        }
      }
    });
  });
}
function removeImage(element){
  var alsup = $.confirm({
    theme: 'modern',
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: 'info',
    icon: 'far fa-question-circle',
    title: 'ELIMINAR',
    content: 'Esta imagen será eliminada permanentemente.',
    buttons: {
      cancel: {
        text: 'Aceptar',
        btnClass: 'btn-info',
        action: function () {
          this.buttons.cancel.setText('<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...');
          this.buttons.cancel.disable();
          $('.jconfirm-closeIcon').remove();
          let nameImg = document.querySelector(element+' .btn-delete').getAttribute("image");
          var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
          var ajaxUrl = base_url+'/tickets/remove_image';
          let formData = new FormData();
          formData.append('idticket',ticket.value);
          formData.append("file",nameImg);
          request.open("POST",ajaxUrl,true);
          request.send(formData);
          request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
              alsup.close();
              var objData = JSON.parse(request.responseText);
              if(objData.status == "success"){
                let itemRemove = document.querySelector(element);
                itemRemove.parentNode.removeChild(itemRemove);
                total_images(ticket.value);
                alert_msg("success",objData.msg);
              }else{
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
