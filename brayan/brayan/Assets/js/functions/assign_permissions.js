if(document.querySelector("#transactions")){
    var transactions = document.querySelector("#transactions");
    transactions.onsubmit = function(e) {
        e.preventDefault();
        var idprofile = document.querySelector('#idprofile').value;

        if(idprofile == ''){
            alert_msg("error","El campo perfil es obligatorio.");
            return false;
        }

        loading.style.display = "flex";
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url+'/permissions/assign_permissions';
        var formData = new FormData(transactions);
        request.open("POST",ajaxUrl,true);
        request.send(formData);
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                var objData = JSON.parse(request.responseText);
                if(objData.status){
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
