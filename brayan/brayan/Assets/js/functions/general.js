var business = document.querySelector("#idbusiness");
tinymce.init({
  selector: "#footer_text",
  width: "100%",
  language: "es",
  height: 300,
  statubar: true,
  plugins: [
    "advlist autolink link image lists charmap print preview hr anchor pagebreak",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "save table contextmenu directionality emoticons template paste textcolor",
  ],
  toolbar:
    "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
});
document.addEventListener(
  "DOMContentLoaded",
  function () {
    if (document.querySelector("#transactions_general")) {
      var transactions_general = document.querySelector(
        "#transactions_general"
      );
      transactions_general.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_general").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/business/update_general";
          var formData = new FormData(transactions_general);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                var alsup = $.confirm({
                  theme: "modern",
                  draggable: false,
                  closeIcon: false,
                  animationBounce: 2.5,
                  escapeKey: false,
                  type: "success",
                  icon: "far fa-check-circle",
                  title: "OPERACIÓN EXITOSA",
                  content: objData.msg,
                  buttons: {
                    Eliminar: {
                      text: "Aceptar",
                      btnClass: "btn-success",
                      action: function () {
                        location.reload();
                      },
                    },
                  },
                });
              } else {
                alert_msg("error", objData.msg);
              }
            }
            loading.style.display = "none";
            return false;
          };
        }
      };
    }
    if (document.querySelector("#transactions_basic")) {
      var transactions_basic = document.querySelector("#transactions_basic");
      transactions_basic.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/update_basic";
        var formData = new FormData(transactions_basic);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_invoice")) {
      var transactions_invoice = document.querySelector(
        "#transactions_invoice"
      );
      transactions_invoice.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        tinyMCE.triggerSave();
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/update_invoice";
        var formData = new FormData(transactions_invoice);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_logofac")) {
      var transactions_logofac = document.querySelector(
        "#transactions_logofac"
      );
      transactions_logofac.onsubmit = function (e) {
        e.preventDefault();
        var logo_fac = document.querySelector("#logo-fac").value;
        if (logo_fac == "") {
          alert_msg(
            "info",
            "Selecionar una foto para poder realizar este cambio."
          );
          return false;
        }
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/main_logo";
        var formData = new FormData(transactions_logofac);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_logo")) {
      var transactions_logo = document.querySelector("#transactions_logo");
      transactions_logo.onsubmit = function (e) {
        e.preventDefault();
        var logo_sesion = document.querySelector("#logo").value;
        if (logo_sesion == "") {
          alert_msg(
            "info",
            "Selecionar una foto para poder realizar este cambio."
          );
          return false;
        }
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/login_logo";
        var formData = new FormData(transactions_logo);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_favicon")) {
      var transactions_favicon = document.querySelector(
        "#transactions_favicon"
      );
      transactions_favicon.onsubmit = function (e) {
        e.preventDefault();
        var favicon = document.querySelector("#favicon").value;
        if (favicon == "") {
          alert_msg(
            "info",
            "Selecionar una foto para poder realizar este cambio."
          );
          return false;
        }
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/favicon";
        var formData = new FormData(transactions_favicon);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_background")) {
      var transactions_background = document.querySelector(
        "#transactions_background"
      );
      transactions_background.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/background";
        var formData = new FormData(transactions_background);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_google")) {
      var transactions_google = document.querySelector("#transactions_google");
      transactions_google.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/google";
        var formData = new FormData(transactions_google);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_reniec")) {
      var transactions_reniec = document.querySelector("#transactions_reniec");
      transactions_reniec.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/reniec";
        var formData = new FormData(transactions_reniec);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_email")) {
      var transactions_email = document.querySelector("#transactions_email");
      transactions_email.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/email";
        var formData = new FormData(transactions_email);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
    if (document.querySelector("#transactions_whatsapp")) {
      var transactions_whatsapp = document.querySelector(
        "#transactions_whatsapp"
      );
      transactions_whatsapp.onsubmit = function (e) {
        e.preventDefault();
        loading.style.display = "flex";
        var request = window.XMLHttpRequest
          ? new XMLHttpRequest()
          : new ActiveXObject("Microsoft.XMLHTTP");
        var ajaxUrl = base_url + "/business/whatsapp";
        var formData = new FormData(transactions_whatsapp);
        request.open("POST", ajaxUrl, true);
        request.send(formData);
        request.onreadystatechange = function () {
          if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status == "success") {
              var alsup = $.confirm({
                theme: "modern",
                draggable: false,
                closeIcon: false,
                animationBounce: 2.5,
                escapeKey: false,
                type: "success",
                icon: "far fa-check-circle",
                title: "OPERACIÓN EXITOSA",
                content: objData.msg,
                buttons: {
                  Eliminar: {
                    text: "Aceptar",
                    btnClass: "btn-success",
                    action: function () {
                      location.reload();
                    },
                  },
                },
              });
            } else {
              alert_msg("error", objData.msg);
            }
          }
          loading.style.display = "none";
          return false;
        };
      };
    }
  },
  false
);
window.addEventListener(
  "load",
  function () {
    $("#transactions_general").parsley();
    $("#listCurrency").select2({ width: "100%" });
    $("#listPrinters").select2({ width: "100%" });
    $("#listCountry").select2({ width: "100%" });
    if (document.querySelector("#logo-fac")) {
      var file = document.querySelector("#logo-fac");
      file.onchange = function (e) {
        var uploadFoto = document.querySelector("#logo-fac").value;
        var fileimg = document.querySelector("#logo-fac").files;
        var nav = window.URL || window.webkitURL;

        if (uploadFoto != "") {
          var type = fileimg[0].type;
          var size = fileimg[0].size;
          if (type != "image/png") {
            alert_msg("info", "¡La imagen debe estar en formato PNG!");
            if (document.querySelector("#image-logofac")) {
              document.querySelector("#image-logofac").src = "";
            }
            file.value = "";
            return false;
          } else if (size > 215040) {
            file.value = "";
            alert_msg("info", "¡La imagen no debe pesar más de 210 KB!");
          } else {
            if (document.querySelector("#image-logofac")) {
              document.querySelector("#image-logofac").src = "";
            }
            var objeto_url = nav.createObjectURL(this.files[0]);
            document.querySelector("#image-logofac").src = objeto_url;
          }
        } else {
          alert_msg("error", "¡No seleccionaste una imagen!");
          if (document.querySelector("#image-logofac")) {
            document.querySelector("#image-logofac").src = "";
          }
        }
      };
    }
    if (document.querySelector("#logo")) {
      var file = document.querySelector("#logo");
      file.onchange = function (e) {
        var uploadFoto = document.querySelector("#logo").value;
        var fileimg = document.querySelector("#logo").files;
        var nav = window.URL || window.webkitURL;

        if (uploadFoto != "") {
          var type = fileimg[0].type;
          var size = fileimg[0].size;
          if (type != "image/png") {
            alert_msg("info", "¡La imagen debe estar en formato PNG!");
            if (document.querySelector("#image-logo")) {
              document.querySelector("#image-logo").src = "";
            }
            file.value = "";
            return false;
          } else if (size > 215040) {
            file.value = "";
            alert_msg("info", "¡La imagen no debe pesar más de 210 KB!");
          } else {
            if (document.querySelector("#image-logo")) {
              document.querySelector("#image-logo").src = "";
            }
            var objeto_url = nav.createObjectURL(this.files[0]);
            document.querySelector("#image-logo").src = objeto_url;
          }
        } else {
          alert_msg("error", "¡No seleccionaste una imagen!");
          if (document.querySelector("#image-logo")) {
            document.querySelector("#image-logo").src = "";
          }
        }
      };
    }
    if (document.querySelector("#favicon")) {
      var file = document.querySelector("#favicon");
      file.onchange = function (e) {
        var uploadFoto = document.querySelector("#favicon").value;
        var fileimg = document.querySelector("#favicon").files;
        var nav = window.URL || window.webkitURL;

        if (uploadFoto != "") {
          var type = fileimg[0].type;
          var size = fileimg[0].size;
          if (type != "image/png" && type != "image/x-icon") {
            alert_msg("info", "¡La imagen debe estar en formato PNG!");
            if (document.querySelector("#image-favicon")) {
              document.querySelector("#image-favicon").src = "";
            }
            file.value = "";
            return false;
          } else if (size > 163840) {
            file.value = "";
            alert_msg("info", "¡La imagen no debe pesar más de 160 KB!");
          } else {
            if (document.querySelector("#image-favicon")) {
              document.querySelector("#image-favicon").src = "";
            }
            var objeto_url = nav.createObjectURL(this.files[0]);
            document.querySelector("#image-favicon").src = objeto_url;
          }
        } else {
          alert_msg("error", "¡No seleccionaste una imagen!");
          if (document.querySelector("#image-favicon")) {
            document.querySelector("#image-favicon").src = "";
          }
        }
      };
    }
  },
  false
);
