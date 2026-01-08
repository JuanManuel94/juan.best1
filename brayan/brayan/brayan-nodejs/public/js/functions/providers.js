let table;
let table_name = "list";
const filebtn = document.querySelector("#import_providers");
const filetext = document.querySelector("#text-file");
var type_doc = document.querySelector("#listTypes");
document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Lista de proveedores");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url: " " + base_url + "/providers/list_records",
          dataSrc: "",
        },
        deferRender: true,
        idDataTables: "1",
        columns: [
          { data: "n", className: "text-center" },
          { data: "provider" },
          { data: "name_doc" },
          { data: "document" },
          { data: "mobile" },
          { data: "associates", className: "text-center" },
          { data: "email" },
          { data: "address" },
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
          $("#" + table_name + "_wrapper .dt-buttons").append(
            $("#" + table_name + "-btns-exportable").contents()
          );
          $("#" + table_name + "_wrapper div.container-options").append(
            $("#" + table_name + "-btns-tools").contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-providers");
        } else {
          loaderout(".panel-providers");
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
          var ajaxUrl = base_url + "/providers/action";
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
    if (document.querySelector("#transactions_import")) {
      var transactions_import = document.querySelector("#transactions_import");
      transactions_import.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_import").parsley().isValid()) {
          if ($("#import_providers").get(0).files.length == 0) {
            alert_msg(
              "info",
              "Selecionar un archivo excel para realizar el proceso."
            );
            return false;
          } else {
            var allowed_extensions = [".xls", ".xlsx"];
            var file = $("#import_providers");
            var exp_reg = new RegExp(
              "([a-zA-Z0-9s_\\-.:])+(" + allowed_extensions.join("|") + ")$"
            );

            if (!exp_reg.test(file.val().toLowerCase())) {
              alert_msg(
                "warning",
                "Selecionar una extensión permitida .xls o .xlsx."
              );
              return false;
            }
            loading.style.display = "flex";
            var request = window.XMLHttpRequest
              ? new XMLHttpRequest()
              : new ActiveXObject("Microsoft.XMLHTTP");
            var ajaxUrl = base_url + "/providers/import";
            var formData = new FormData(transactions_import);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
              if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status == "success") {
                  $("#modal-import").modal("hide");
                  alert_msg("success", objData.msg);
                  refresh_table();
                } else if (objData.status == "warning") {
                  $("#modal-import").modal("hide");
                  alert_msg("warning", objData.msg);
                  refresh_table();
                } else {
                  alert_msg("error", objData.msg);
                }
              }
              loading.style.display = "none";
              return false;
            };
          }
        }
      };
    }
  },
  false
);
window.addEventListener(
  "load",
  function () {
    list_documents();
    $("#transactions").parsley();
  },
  false
);
$("#import_providers").on("change", function () {
  document.querySelector("#text-file").value = this.files.item(0).name;
});
filetext.addEventListener("click", function () {
  filebtn.click();
});
function update(idprovider) {
  $('[data-toggle="tooltip"]').tooltip("hide");
  $("#transactions").parsley().reset();
  document.querySelector("#text-title").innerHTML = "Actualizar Proveedor";
  document.querySelector("#text-button").innerHTML = "Guardar Cambios";
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/providers/select_record/" + idprovider;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        document.querySelector("#idprovider").value = objData.data.encrypt_id;
        document.querySelector("#provider").value = objData.data.provider;
        document.querySelector("#listTypes").value = objData.data.documentid;
        if (objData.data.documentid == 2) {
          document.querySelector("#document").setAttribute("maxlength", "11");
        }
        if (objData.data.documentid == 3) {
          document.querySelector("#document").setAttribute("maxlength", "11");
        }
        document.querySelector("#document").value = objData.data.document;
        document.querySelector("#mobile").value = objData.data.mobile;
        document.querySelector("#email").value = objData.data.email;
        document.querySelector("#address").value = objData.data.address;
        document.querySelector("#listStatus").value = objData.data.state;
        $("#modal-action").modal("show");
      } else {
        alert_msg("error", objData.msg);
      }
    }
  };
}
function remove(idprovider) {
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
          var ajaxUrl = base_url + "/providers/remove";
          var strData = "idprovider=" + idprovider;
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
    var ajaxUrl = base_url + "/providers/list_documents";
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
    case "3":
      document.querySelector("#document").setAttribute("maxlength", "20");
      document.querySelector("#document").setAttribute("placeholder", "");
      break;
  }
}
function search_document() {
  var type = document.querySelector("#listTypes").value;
  var doc = document.querySelector("#document").value;
  if (doc != "") {
    $(".btn-search").html('<i class="fa fa-spinner fa-sm fa-spin"></i>');
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/providers/search_document/" + type + "/" + doc;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var objData = JSON.parse(request.responseText);
        if (objData.status == "success") {
          document.querySelector("#provider").value =
            objData.data.business_name;
          document.querySelector("#address").value = objData.data.address;
        } else if (objData.status == "info") {
          document.querySelector("#provider").value = "";
          document.querySelector("#address").value = "";
          alert_msg("info", objData.msg);
        } else {
          document.querySelector("#provider").value = "";
          document.querySelector("#address").value = "";
          alert_msg("error", objData.msg);
        }
      }
      $(".btn-search").html('<i class="fa fa-search"></i>');
      return false;
    };
  } else {
    if (type == 2) {
      document.querySelector("#provider").value = "";
      document.querySelector("#address").value = "";
      alert_msg("error", "Ingrese el número de dni.");
    }
    if (type == 3) {
      document.querySelector("#provider").value = "";
      document.querySelector("#address").value = "";
      alert_msg("error", "Ingrese el número de ruc.");
    }
    if (type == 4) {
      document.querySelector("#provider").value = "";
      document.querySelector("#address").value = "";
      alert_msg("error", "Ingrese el número de carnet de extranjeria.");
    }
  }
}
function modal() {
  document.querySelector("#text-title").innerHTML = "Nuevo Proveedor";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#idprovider").value = "";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  list_documents();
  $("#modal-action").modal("show");
}

function modal_import() {
  $('[data-toggle="tooltip"]').tooltip("hide");
  document.querySelector("#text-title-import").innerHTML =
    "Importar Proveedores";
  document.querySelector("#text-button-import").innerHTML = "Importar";
  document.querySelector("#transactions_import").reset();
  $("#modal-import").modal("show");
}
function exports() {
  alert_msg("loader", "Generando excel.");
  $('[data-toggle="tooltip"]').tooltip("hide");
  setTimeout(function () {
    $("#gritter-notice-wrapper").remove();
    alert_msg("success", "Se exporto excel correctamente.");
    window.open(base_url + "/providers/export", "_blank");
  }, 1000);
}
function filter(column, search) {
  if (search == "") {
    table.search("").columns().search("").draw();
  } else {
    column = parseInt(column);
    table.columns(column).search(search).draw();
  }
}
