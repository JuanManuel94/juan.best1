var loading = document.querySelector("#loading");
const showPass = document.querySelectorAll(".showPass");
const pwFields = document.querySelectorAll("#password");
const pwShowHide = document.querySelectorAll(".showPassConfirm");
const pwFieldsCon = document.querySelectorAll("#passwordConfirm");
if(document.querySelector("#transactions_password")){
    let transactions_password = document.querySelector("#transactions_password");
        transactions_password.onsubmit = function(e) {
            e.preventDefault();

            let password = document.querySelector('#password').value;
            let passwordConfirm = document.querySelector('#passwordConfirm').value;

            if(password == "" || passwordConfirm == ""){
                alert_msg("error","Campos obligatorios.");
                return false;
            }else{
                if(password.length < 5 ){
                    alert_msg("info","La contraseña debe tener un mínimo de 5 caracteres.");
                return false;
                }
                if(password != passwordConfirm){
                    alert_msg("error","Las contraseñas no son iguales.");
                    return false;
                }
                loading.style.display = "flex";
                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url+'/login/update_password';
                var formData = new FormData(transactions_password);
                request.open("POST",ajaxUrl,true);
                request.send(formData);
                request.onreadystatechange = function(){
                if(request.readyState != 4) return;
                if(request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status == 'success'){
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
                                        $(location).attr('href', base_url+"/login");
                                    }
                                }
                            }
                        });
                    }else{
                        alert_msg("error",objData.msg);
                    }
                }else{
                    alert_msg("error","Error en el proceso.");
                }
                loading.style.display = "none";
            }
        }
    }
}
/* Ver y Ocultar contraseña*/
showPass.forEach(eyeIcon =>{
	eyeIcon.addEventListener("click", ()=>{
		pwFields.forEach(pwField =>{
			if(pwField.type ==="password"){
				pwField.type = "text";
				showPass.forEach(icon =>{
					icon.classList.replace("fa-eye-slash", "fa-eye");
				})
			}else{
				pwField.type = "password";
				showPass.forEach(icon =>{
					icon.classList.replace("fa-eye", "fa-eye-slash");
				})
			}
		})
	})
});
/* Ver y Ocultar contraseña*/
pwShowHide.forEach(eyeIcon =>{
	eyeIcon.addEventListener("click", ()=>{
		pwFieldsCon.forEach(pwConfirm =>{
			if(pwConfirm.type ==="password"){
				pwConfirm.type = "text";
				pwShowHide.forEach(icon =>{
					icon.classList.replace("fa-eye-slash", "fa-eye");
				})
			}else{
				pwConfirm.type = "password";
				pwShowHide.forEach(icon =>{
					icon.classList.replace("fa-eye", "fa-eye-slash");
				})
			}
		})
	})
});
particlesJS({
	"particles": {
		"number": {
				"value": 150,
				"density": {
						"enable": true,
						"value_area": 800
				}
		},
		"color": {
				"value": "#ffffff"
		},
		"shape": {
				"type": "circle",
				"stroke": {
						"width": 0,
						"color": "#000000"
				},
				"polygon": {
						"nb_sides": 5
				},
				"image": {
						"src": "img/github.svg",
						"width": 100,
						"height": 100
				}
		},
		"opacity": {
				"value": 0.5,
				"random": false,
				"anim": {
						"enable": false,
						"speed": 1,
						"opacity_min": 0.1,
						"sync": false
				}
		},
		"size": {
				"value": 3,
				"random": true,
				"anim": {
						"enable": false,
						"speed": 40,
						"size_min": 0.1,
						"sync": false
				}
		},
		"line_linked": {
				"enable": true,
				"distance": 150,
				"color": "#ffffff",
				"opacity": 0.4,
				"width": 1
		},
		"move": {
				"enable": true,
				"speed": 11.22388442605866,
				"direction": "none",
				"random": false,
				"straight": false,
				"out_mode": "out",
				"bounce": false,
				"attract": {
						"enable": false,
						"rotateX": 600,
						"rotateY": 1200
				}
		}
	},
	"interactivity": {
		"detect_on": "canvas",
		"events": {
				"onhover": {
						"enable": false,
						"mode": "repulse"
				},
				"onclick": {
						"enable": true,
						"mode": "push"
				},
				"resize": true
		},
		"modes": {
				"grab": {
						"distance": 400,
						"line_linked": {
								"opacity": 1
						}
				},
				"bubble": {
						"distance": 400,
						"size": 40,
						"duration": 2,
						"opacity": 8,
						"speed": 3
				},
				"repulse": {
						"distance": 200,
						"duration": 0.4
				},
				"push": {
						"particles_nb": 4
				},
				"remove": {
						"particles_nb": 2
				}
		}
	},
	"retina_detect": true
})
