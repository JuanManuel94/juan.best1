let table;
let table_name = "list";
var type_doc = document.querySelector("#listTypes");
document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Lista de usuarios");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url: " " + base_url + "/users/list_records",
          dataSrc: "",
        },
        deferRender: true,
        idDataTables: "1",
        columns: [
          { data: "n", className: "text-center" },
          { data: "fullname" },
          { data: "name_doc" },
          { data: "document" },
          { data: "profile" },
          { data: "cellphone" },
          {
            data: "state",
            render: function (data, type, full, meta) {
              var state = "";
              if (data == 1) {
                state = '<span class="label label-success">ACTIVO</span>';
              }
              if (data == 2) {
                state = '<span class="label label-danger">DESACTIVADO</span>';
              }
              return state;
            },
            className: "text-center",
          },
          { data: "options", className: "text-center", sWidth: "40px" },
        ],
        initComplete: function (oSettings, json) {
          $("#" + table_name + "_wrapper div.container-options").append(
            $("#" + table_name + "-btns-tools").contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-users");
        } else {
          loaderout(".panel-users");
        }
      });
    if (document.querySelector("#transactions")) {
      var transactions = document.querySelector("#transactions");
      transactions.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/users/action";
          var formData = new FormData(transactions);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-action").modal("hide");
                transactions.reset();
                alert_msg("success", objData.msg);
                refresh_table();
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
    /*$('#list tbody').on('click', 'tr', function() {
        var data = table.row(this).data();
        update(data.encrypt);
    });*/
  },
  false
);
window.addEventListener(
  "load",
  function () {
    list_profiles();
    list_documents();
    $("#transactions").parsley();
  },
  false
);
function update(iduser) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions").parsley().reset();
  document.querySelector("#text-title").innerHTML = "Actualizar Usuario";
  document.querySelector("#text-button").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/users/select_record/" + iduser;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#iduser").value = objData.data.encrypt_id;
        document.querySelector("#names").value = objData.data.names;
        document.querySelector("#surnames").value = objData.data.surnames;
        document.querySelector("#listTypes").value = objData.data.documentid;
        if (objData.data.documentid == 2) {
          document.querySelector("#document").setAttribute("maxlength", "11");
        } else {
          document.querySelector("#document").setAttribute("maxlength", "12");
        }
        document.querySelector("#document").value = objData.data.document;
        document.querySelector("#mobile").value = objData.data.mobile;
        document.querySelector("#email").value = objData.data.email;
        document.querySelector("#listProfiles").value =
          objData.data.encrypt_profile;
        $("#listProfiles").select2({ width: "100%" });
        document.querySelector("#username").value = objData.data.username;
        document.querySelector("#listStatus").value = objData.data.state;
        $("#modal-action").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function remove(iduser) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ELIMINAR",
    content: "Esta seguro que desea eliminar este registro.",
    buttons: {
      remove: {
        text: "Aceptar",
        btnClass: "btn-info",
        action: function () {
          this.buttons.remove.setText(
            '<i class="fas fa-spinner fa-spin icodialog"></i> Procesando...'
          );
          this.buttons.remove.disable();
          $(".jconfirm-closeIcon").remove();
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/users/remove";
          var strData = "iduser=" + iduser;
          request.open("POST", ajaxUrl, true);
          request.setRequestHeader(
            "Content-type",
            "application/x-www-form-urlencoded"
          );
          request.send(strData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              alsup.close();
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("success", objData.msg);
                refresh_table();
              } else if (objData.status == "exists") {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("info", objData.msg);
              } else {
                $('[data-toggle="tooltip"]').tooltip("hide");
                alert_msg("error", objData.msg);
              }
            }
            return false;
          };
        },
      },
      close: {
        text: "Cancelar",
      },
    },
  });
}
function list_documents() {
  if (document.querySelector("#listTypes")) {
    var ajaxUrl = base_url + "/users/list_documents";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listTypes").innerHTML = request.responseText;
      }
    };
  }
}
function list_profiles() {
  if (document.querySelector("#listProfiles")) {
    var ajaxUrl = base_url + "/profiles/list_profiles";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listProfiles").innerHTML =
          request.responseText;
        $("#listProfiles").select2({ width: "100%" });
      }
    };
  }
}
$("#listTypes").on("change", function () {
  $("#document").val("").focus();
  type_document($(this).val());
});
function type_document(value) {
  switch (value) {
    case "2":
      document.querySelector("#document").setAttribute("maxlength", "20");
      document.querySelector("#document").setAttribute("placeholder", "");
      break;
    case "4":
      document.querySelector("#document").setAttribute("maxlength", "20");
      document.querySelector("#document").setAttribute("placeholder", "");
      break;
    case "5":
      document.querySelector("#document").setAttribute("maxlength", "20");
      document.querySelector("#document").setAttribute("placeholder", "");
      break;
  }
}
function search_document() {
  var type = document.querySelector("#listTypes").value;
  var doc = document.querySelector("#document").value;
  if (doc != "") {
    if (type == 2) {
      $(".btn-search").html('<i class="fa fa-spinner fa-sm fa-spin"></i>');
      var request = window.XMLHttpRequest
        ? new XMLHttpRequest()
        : new ActiveXObject("Microsoft.XMLHTTP");
      var ajaxUrl = base_url + "/users/search_document/" + type + "/" + doc;
      request.open("GET", ajaxUrl, true);
      request.send();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var objData = JSON.parse(request.responseText);
          if (objData.status == "success") {
            document.querySelector("#names").value = objData.data.names;
            document.querySelector("#surnames").value = objData.data.surnames;
          } else {
            document.querySelector("#names").value = "";
            document.querySelector("#surnames").value = "";
            alert_msg("info", objData.msg);
          }
        }
        $(".btn-search").html('<i class="fa fa-search"></i>');
        return false;
      };
    } else {
      document.querySelector("#names").value = "";
      document.querySelector("#surnames").value = "";
      alert_msg("info", "La búsqueda solo es para DNI y Carnet de Extranjería.");
    }
  } else {
    if (type == 2) {
      document.querySelector("#names").value = "";
      document.querySelector("#surnames").value = "";
      alert_msg("error", "Ingrese el número de DNI.");
    } else if (type == 4) {
      document.querySelector("#names").value = "";
      document.querySelector("#surnames").value = "";
      alert_msg("error", "Ingrese el número de carnet de extranjería.");
    } else {
      document.querySelector("#names").value = "";
      document.querySelector("#surnames").value = "";
      alert_msg("info", "La búsqueda solo es para DNI y Carnet de Extranjería.");
    }
  }
}  
function modal() {
  document.querySelector("#text-title").innerHTML = "Nuevo usuario";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#iduser").value = "";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  list_profiles();
  list_documents();

  $("#modal-action").modal("show");
}
function open_message(country, number, user) {
  let message = "";
  let url =
    "https://api.whatsapp.com/send/?phone=" +
    country +
    number +
    "&text=" +
    message;
  window.open(url);
}
