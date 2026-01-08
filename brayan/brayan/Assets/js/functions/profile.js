document.addEventListener('DOMContentLoaded', function(){
    if(document.querySelector("#transactions_data")){
        var transactions_data = document.querySelector("#transactions_data");
        transactions_data.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions_data').parsley().isValid()){
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/users/modify_data';
                var formData = new FormData(transactions_data);
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
                                            location.reload();
                                        }
                                    }
                                }
                            });
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
    if(document.querySelector("#transactions_password")){
        var transactions_password = document.querySelector("#transactions_password");
        transactions_password.onsubmit = function(e){
            e.preventDefault();
            if($('#transactions_password').parsley().isValid()){
                var password = document.querySelector('#password').value;
                var repeat_password = document.querySelector('#repeat_password').value;
                if(password != "" || repeat_password != ""){
                    if(password != repeat_password){
                        alert_msg("info","Las contraseñas no son iguales.");
                        return false;
                    }
                    if(password.length < 5 ){
                        alert_msg("info","La contraseña debe tener un mínimo de 5 caracteres.");
                        return false;
                    }
                }
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/users/modify_password';
                var formData = new FormData(transactions_password);
                request.open("POST",ajaxUrl,true);
                request.send(formData);
                request.onreadystatechange = function(){
                    if(request.readyState == 4 && request.status == 200){
                        var objData = JSON.parse(request.responseText);
                        if(objData.status == "success"){
                            transactions_password.reset();
                            alert_msg("success",objData.msg);
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
    $('#transactions_data').parsley();
    $('#transactions_password').parsley();
    if(document.querySelector("#file")){
        var file = document.querySelector("#file");
        file.onchange = function(e) {
            let current_photo = document.querySelector("#current_photo").value;
            var uploadFoto = document.querySelector("#file").value;
            var fileimg = document.querySelector("#file").files;
            var nav = window.URL || window.webkitURL;
            if(uploadFoto !=''){
                var type = fileimg[0].type;
                if(type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png'){
                    alert_msg("info","¡La imagen debe estar en formato PNG, JPG o JPEG!");
                    if(document.querySelector('#image_profile')){
                        document.querySelector('#image_profile').src = "";
                    }
                    file.value="";
                    return false;
                }else{
                    if(document.querySelector('#image_profile')){
                        document.querySelector('#image_profile').src = "";
                    }
                    var objeto_url = nav.createObjectURL(this.files[0]);
                    document.querySelector('#image_profile').src = objeto_url;
                    loading.style.display = "flex";
                    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    let ajaxUrl = base_url+'/users/change_profile';
                    let formData = new FormData();
                    formData.append('current_photo',current_photo);
                    formData.append("photo", this.files[0]);
                    request.open("POST",ajaxUrl,true);
                    request.send(formData);
                    request.onreadystatechange = function(){
                      if(request.readyState == 4 && request.status == 200){
                            let objData = JSON.parse(request.responseText);
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
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            }else{
                                alert_msg("error",objData.msg);
                            }
                        }
                        loading.style.display = "none";
                        return false;
                    }
                }
            }else{
                alert_msg("error","¡No seleccionaste una imagen!");
                if(document.querySelector('#image_profile')){
                    document.querySelector('#image_profile').src = "";
                }
            }
        }
    }
}, false);
