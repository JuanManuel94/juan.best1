let table;
let table_name = "list";
var idfacilityT = document.querySelector("#idfacilityT");
var product = document.querySelector("#listProduct");
document.addEventListener(
  "DOMContentLoaded",
  function () {
    table_configuration("#" + table_name, "Lista de materiales");
    table = $("#" + table_name)
      .DataTable({
        ajax: {
          url:
            " " +
            base_url +
            "/installations/list_materials/" +
            idfacilityT.value,
          dataSrc: "",
        },
        deferRender: true,
        idDataTables: "1",
        columns: [
          { data: "n", className: "text-center" },
          { data: "product" },
          { data: "serie" },
          { data: "mac" },
          { data: "category" },
          { data: "quantity", className: "text-center" },
          { data: "price", className: "text-center" },
          { data: "total", className: "text-center" },
          { data: "product_condition", className: "text-center" },
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
          loaderin(".panel-tools");
        } else {
          loaderout(".panel-tools");
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
          var ajaxUrl = base_url + "/installations/register_material";
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
  },
  false
);
window.addEventListener(
  "load",
  function () {
    list_products();
  },
  false
);
function list_products() {
  if (document.querySelector("#listProduct")) {
    var ajaxUrl = base_url + "/products/list_products";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listProduct").innerHTML = request.responseText;
        $("#listProduct").select2({ width: "100%" });
      }
    };
  }
}
$("#listProduct").on("click change", function () {
  select_product($(this).val());
});
function select_product(idproduct) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/installations/select_product/" + idproduct;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status == "success") {
        if (objData.data.stock >= 1) {
          document.querySelector("#price").value = objData.data.sale_price;
          document.querySelector("#current_stock").value = objData.data.stock;
          document
            .querySelector("#quantity")
            .setAttribute("max", objData.data.stock);
          calculateTotal("#price", "#quantity", "#total");
          $("#btn-saved").prop("disabled", false);
        } else {
          alert_msg("warning", "El producto no tiene stock disponible.");
          document.getElementById("quantity").value = 1;
          document.getElementById("price").value = roundNumber(0, 2);
          calculateTotal("#price", "#quantity", "#total");
          $("#btn-saved").prop("disabled", true);
        }
      } else {
        alert_msg("info", objData.msg);
      }
    }
  };
}
$("#quantity").on("keyup change", function () {
  var stock = $("#current_stock").val();
  var quantity = $(this).val();
  if (parseInt(stock) < parseInt(quantity)) {
    alert_msg("warning", "La cantidad supera el stock.");
    document.getElementById("quantity").value = 1;
    calculateTotal("#price", "#quantity", "#total");
  }
  if (parseInt(quantity) <= 0) {
    alert_msg("error", "No puede ser negativo.");
    document.getElementById("quantity").value = 1;
    calculateTotal("#price", "#quantity", "#total");
  }
  if (parseInt(stock) > parseInt(quantity)) {
    calculateTotal("#price", "#quantity", "#total");
  }
});
function calculateTotal(price, qtycl, totalcl) {
  var total = $(price).val() * $(qtycl).val();
  total = roundNumber(total, 2);
  $(totalcl).val(total);
}
function conditions(value) {
  switch (value) {
    case "PRESTAMO":
      $("#divquantity").hide("fast");
      $("#divprice").hide("fast");
      $("#divtotal").hide("fast");
      break;
    case "VENTA":
      $("#divquantity").show("fast");
      $("#divprice").show("fast");
      $("#divtotal").show("fast");
      break;
  }
}
$("#listConditions").on("click change", function () {
  conditions($(this).val());
});
function remove_material(idtools) {
  var alsup = $.confirm({
    theme: "modern",
    draggable: false,
    closeIcon: true,
    animationBounce: 2.5,
    escapeKey: false,
    type: "info",
    icon: "far fa-question-circle",
    title: "ELIMINAR",
    content: "El stock del producto volverá estar disponible en el almacén.",
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
          var ajaxUrl = base_url + "/installations/remove_material";
          var strData = "idtools=" + idtools;
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
function modal() {
  document.querySelector("#text-title").innerHTML = "Agregar producto";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#idfacility").value = idfacilityT.value;
  document.querySelector("#transactions").reset();
  $("#btn-saved").prop("disabled", true);
  $("#modal-action").modal("show");
  conditions("PRESTAMO");
  list_products();
  select_product(product.value);
}
