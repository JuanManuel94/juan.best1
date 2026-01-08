document.write(`<script src="https://maps.googleapis.com/maps/api/js?libraries=places,geometry&key=${key_google}&callback=initMap"></script>`);
let btnadd =  document.querySelector(".btn-add");
let client =  document.querySelector("#idclient");
let facility =  document.querySelector("#idfacility");
var marker,columna;
var map;
document.addEventListener('DOMContentLoaded', function(){
    if(document.querySelector("#transactions")){
        var transactions = document.querySelector("#transactions");
        transactions.onsubmit = function(e){
            e.preventDefault();
            let requiere_foto = false; // Solo valida si requiere_foto es true

                  if ($('#transactions').parsley().isValid()) {
                  let radio = document.querySelector('input[name="radio_option"]:checked').value;
                   let counter = document.querySelector("#counter").value;

                   if (radio == 1 && requiere_foto) { 
                    if (parseInt(counter) === 0) {
                    alert_msg("error", "No hay imágenes, agregué uno o más para poder completar la instalación.");
                   return false;
                 }       
             }

                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/installations/complete_installation';
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
                                        $(location).attr('href', base_url+"/installations");
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
                                  $(location).attr('href', base_url+"/installations");
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
                                  $(location).attr('href', base_url+"/installations");
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
window.addEventListener('load', function() {
    form_wizard();
    uploadImage();
    $('#transactions').parsley();
    show_images(facility.value);
    total_images(facility.value);
    $('#image-user').initial({
        height:45,
        width:45,
        charCount:2,
        fontSize:21,
        fontWeight:600
    });
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
}, false);
function form_wizard(){
    $('#wizard').smartWizard({
        selected: 0,
        theme: 'default',
        transitionEffect:'',
        transitionSpeed: 0,
        useURLhash: false,
        lang:{
            next: 'Siguiente',
            previous: 'Anterior'
        },
        showStepURLhash: false,
        toolbarSettings: {
            toolbarPosition: 'bottom',
            toolbarExtraButtons: [
                $('<button></button>').html('<i class="fas fa-save mr-2"></i>Guardar Cambios').attr('type', 'submit').addClass('btn btn-blue btn-finish d-none'),
            ]
        },
    });
	$('#wizard').on('leaveStep', function(e, anchorObject, stepNumber, stepDirection) {
		var res = $('form[name="transactions"]').parsley().validate('step-' + (stepNumber + 1));
		return res;
	});
	$('#wizard').keypress(function( event ) {
		if (event.which == 13 ) {
			$('#wizard').smartWizard('next');
		}
	});
    $("#wizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
        $('.srvdire').val($('.drprin').val());
        if(stepNumber==2){
            $('.btn-finish').removeClass( "d-none" );
            $('.sw-btn-group').hide();
        }else{
            $('.btn-finish').addClass( "d-none" );
            $('.sw-btn-group').show();
        }
    });
}
function my_location(){
   $('#latitud').val("");
   $('#longitud').val("");
    initMap();
}
function bottonLocation(map){
    const button_location = document.createElement("button");
    button_location.style.backgroundColor = "rgb(255, 255, 255)";
    button_location.style.border = "1px solid rgb(218,220,224)";
    button_location.style.borderRadius = "2px";
    button_location.style.boxShadow = "rgba(0, 0, 0, 0.3) 0px 1px 4px -1px";
    button_location.style.color = "#6c757d";
    button_location.style.cursor = "pointer";
    button_location.style.fontFamily = "Roboto,Arial,sans-serif";
    button_location.style.fontSize = "23px";
    button_location.style.margin = "30px 10px 0 0";
    button_location.style.width  = "40px";
    button_location.style.height  = "40px";
    button_location.style.textAlign = "center";
    button_location.innerHTML = '<i class="ion ion-md-locate"></i>';
    button_location.title = "Mostrar mi ubicación actual";
    button_location.type = "button";
    button_location.addEventListener("click", () => {
      my_location();
    });
    return button_location;
}
function initMap(){
    latLng = new google.maps.LatLng(15.1236255,-91.8150403);
    map = new google.maps.Map(document.getElementById('map-canvas'), {
      zoom: 16,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapTypeControlOptions: {style: google.maps.MapTypeControlStyle.DROPDOWN_MENU}
    });

    const centerControlDiv = document.createElement("div");
    const centerControl = bottonLocation(map);
    centerControlDiv.appendChild(centerControl);
    map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(centerControlDiv);

    if($('#latitud').val()=="" || $('#longitud').val()==""){

      if(navigator.geolocation){
        var options_coord = {
          enableHighAccuracy: true,
          timeout: 15000,
          maximumAge: 0
        };
        navigator.geolocation.getCurrentPosition(function(position,location_error,options_coord)  {
          latLng =	new google.maps.LatLng(position.coords.latitude,position.coords.longitude);

            marker = new google.maps.Marker({
              position: latLng,
              map: map,
              draggable: true
            });
            updateMarkerPosition(latLng);
            map.setCenter(latLng);
            var infowindow = new google.maps.InfoWindow( { content: "<h5 class='text-center f-w-600 mb-0'>Ubicación del cliente</h5>"} );
            infowindow.open( map, marker );

            google.maps.event.addListener(marker, 'dragend', function() {
              updateMarkerPosition(marker.getPosition());
            });

        }, function() {
            latLng=	new google.maps.LatLng(15.1236255,-91.8150403);

            marker = new google.maps.Marker({
                position: latLng,
                map: map,
                draggable: true
              });
            updateMarkerPosition(latLng);
            map.setCenter(latLng);
            var infowindow = new google.maps.InfoWindow( { content: "<h5 class='text-center f-w-600 mb-0'>Ubicación del cliente</h5>"} );
            infowindow.open( map, marker );

            google.maps.event.addListener(marker, 'dragend', function() {
            updateMarkerPosition(marker.getPosition());
            });
        });
      }
    }else{
        var latituds = $('#latitud').val();
         var longituds = $('#longitud').val();
        latLng=	new google.maps.LatLng(parseFloat(latituds),parseFloat(longituds));
        marker = new google.maps.Marker({
            position: latLng,
            map: map,
            draggable: true
          });
        updateMarkerPosition(latLng);
        map.setCenter(latLng);

        var infowindow = new google.maps.InfoWindow( { content: "<h5 class='text-center f-w-600 mb-0'>Ubicación del cliente</h5>"} );
        infowindow.open( map, marker );

        google.maps.event.addListener(marker, 'dragend', function() {
        updateMarkerPosition(marker.getPosition());
        });
    }
}
function updateMarkerPosition(latLng){
   $('#latitud').val(latLng.lat());
   $('#longitud').val(latLng.lng());
}
function show_images(idfacilty){
    let request = (window.XMLHttpRequest) ?  new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/installations/show_images/'+idfacilty;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status == "success"){
                let htmlImage = "";
                let objImages = objData.data;
                for (let p = 0; p < objImages.length; p++){
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
function total_images(idfacilty){
    let request = (window.XMLHttpRequest) ?  new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/installations/number_images/'+idfacilty;
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
    uploadfile.forEach(function(uploadfile) {
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
                    let ajaxUrl = base_url+'/installations/register_image';
                    let formData = new FormData();
                    formData.append('idclient',client.value);
                    formData.append('idfacility',facility.value);
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
                                total_images(facility.value);
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
                  var ajaxUrl = base_url+'/installations/remove_image';
                  let formData = new FormData();
                  formData.append('idfacility',facility.value);
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
                                total_images(facility.value);
                                alert_msg("success",objData.msg);
                            }else{
                                $('[data-toggle="tooltip"]').tooltip('hide')
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
