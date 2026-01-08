$("body").tooltip({
  selector: "[data-toggle=tooltip]",
});
var concurrence = 15,
  timechar,
  interval;
var loading = document.querySelector("#loading");
function table_configuration(tableid, text) {
  var buttons = [
    {
      extend: "pageLength",
      className: "btn btn-white",
    },
    {
      extend: "colvis",
      text: '<i class="fas fa-list-ul"></i>',
      className: "btn btn-white",
    },
    {
      extend: "collection",
      autoClose: true,
      text: '<i class="fa fa-ellipsis-h"></i></span> ',
      className: "btn btn-white",
      buttons: [
        {
          extend: "print",
          exportOptions: {
            columns: ":visible",
          },
          title: text,
          text: '<i class="fas fa-print fa-lg"></i> Imprimir',
        },
        {
          extend: "csv",
          title: text,
          text: '<i class="far fa-file-excel fa-lg"></i> Exportar a csv',
        },
        {
          extend: "excel",
          title: text,
          text: '<i class="far fa-file-excel fa-lg"></i> Exportar a excel',
        },
        {
          extend: "pdf",
          exportOptions: {
            columns: ":visible",
          },
          title: text,
          orientation: "landscape",
          text: '<i class="far fa-file-pdf fa-lg"></i> Exportar a pdf',
        },
      ],
    },
  ];
  if (/iPhone|iPad|iPod|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    var buttons = [
      {
        extend: "pageLength",
        className: "btn btn-white",
      },
      {
        extend: "colvis",
        text: '<i class="fas fa-list-ul"></i>',
        className: "btn btn-white",
      },
      {
        extend: "collection",
        autoClose: true,
        text: '<i class="far fa-ellipsis-h fa-lg"></i></span>',
        className: "btn btn-white",
        buttons: [
          {
            extend: "csv",
            title: text,
            text: '<i class="far fa-file-excel fa-lg"></i> Exportar a csv',
          },
          {
            extend: "excel",
            title: text,
            text: '<i class="far fa-file-excel fa-lg"></i> Exportar a excel',
          },
          {
            extend: "pdf",
            exportOptions: {
              columns: ":visible",
            },
            title: text,
            orientation: "landscape",
            text: '<i class="far fa-file-pdf fa-lg"></i> Exportar a pdf',
          },
        ],
      },
    ];
  }
  $.extend(true, $.fn.dataTable.defaults, {
    "table-layout": "fixed",
    aProcessing: true,
    aServerSide: true,
    language: {
      zeroRecords: "Sin resultados encontrados",
      emptyTable: "No hay información",
      info: "Mostrando de _START_ al _END_ de un total de _TOTAL_",
      infoEmpty: "Mostrando 0 Registros",
      infoFiltered: "(filtrado de un total de _MAX_ registros)",
      lengthMenu: "_MENU_",
      loadingRecords: "Cargando...",
      processing: "Procesando...",
      search: "",
      searchPlaceholder: "Buscar...",
      paginate: {
        first: "Primero",
        last: "Ultimo",
        next: "<i class='fas fa-angle-right'></i>",
        previous: "<i class='fas fa-angle-left'></i>",
      },
      buttons: {
        pageLength: {
          _: "%d ",
          "-1": "Todos",
        },
      },
    },
    dom:
      "<'row'<'col-sm-8'B <'container-options'>><'col-sm-4'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    lengthMenu: [
      [concurrence, 25, 50, 100, -1],
      [
        concurrence + " Registros",
        "25 Registros",
        "50 Registros",
        "100 Registros",
        "Mostrar todos",
      ],
    ],
    buttons: buttons,
    pageLength: concurrence,
    responsive: true,
    ordering: false,
    autoWidth: true,
    stateSave: false,
    colReorder: false,
    bDestroy: true,
    order: [[1, "desc"]],
  });
}
function download_files(url, archive) {
  axios({
    url: url,
    method: "GET",
    responseType: "blob",
  }).then((response) => {
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", archive);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
}
function send_message() {
  let cell = document.querySelector("#listMobiles").value;
  let message = document.querySelector("#message_text").value;
  let url = "https://api.whatsapp.com/send/?phone=" + cell + "&text=" + message;
  window.open(url);
  $("#modal-message").modal("hide");
}
function loaderout(idclase) {
  $(idclase).removeClass("panel-loading");
  $(idclase).find(".panel-loader").remove();
}
function loaderin(idclase) {
  var targetBody = $(idclase).find(".panel-body");
  var spinnerHtml =
    '<div class="panel-loader"><span class="spinner-small"></span></div>';
  $(idclase).addClass("panel-loading");
  $(targetBody).prepend(spinnerHtml);
}
function refresh_table() {
  table.ajax.reload(null, false);
}
function refresh_internet() {
  table_internet.ajax.reload(null, false);
}
function refresh_personalized() {
  table_personalized.ajax.reload(null, false);
}
function generateAvatar(text) {
  var colors = [
    "#1abc9c",
    "#16a085",
    "#f1c40f",
    "#f39c12",
    "#2ecc71",
    "#27ae60",
    "#e67e22",
    "#d35400",
    "#3498db",
    "#2980b9",
    "#e74c3c",
    "#c0392b",
    "#9b59b6",
    "#8e44ad",
    "#bdc3c7",
    "#34495e",
    "#2c3e50",
    "#95a5a6",
    "#7f8c8d",
    "#ec87bf",
    "#d870ad",
    "#f69785",
    "#9ba37e",
    "#b49255",
    "#b49255",
    "#a94136",
  ];
  var initials =
    text.split(" ")[0].charAt(0).toUpperCase() +
    text.split(" ")[1].charAt(0).toUpperCase();
  var charIndex = initials.charCodeAt(0) - 65;
  var colorIndex = charIndex % 19;
  // Element
  const canvas = document.createElement("canvas");
  const context = canvas.getContext("2d");
  // Width
  canvas.width = 200;
  canvas.height = 200;
  // Draw background
  context.fillStyle = colors[colorIndex];
  context.fillRect(0, 0, canvas.width, canvas.height);
  // Draw text
  context.font =
    "bold 100px HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial,Lucida Grande, sans-serif";
  context.fillStyle = "#ffffff";
  context.textAlign = "center";
  context.textBaseline = "middle";
  context.fillText(initials, canvas.width / 2, canvas.height / 2);
  return canvas.toDataURL("image/png");
}
function month_letters(month) {
  var months = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Setiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];
  var number_month = parseInt(month);
  if (!isNaN(number_month) && number_month >= 1 && number_month <= 12) {
    var letter_month = months[number_month - 1];
  }
  return letter_month;
}
function roundNumber(number, decimals) {
  var newString;
  decimals = Number(decimals);
  if (decimals < 1) {
    newString = Math.round(number).toString();
  } else {
    var numString = number.toString();
    if (numString.lastIndexOf(".") == -1) {
      numString += ".";
    }
    var cutoff = numString.lastIndexOf(".") + decimals;
    var d1 = Number(numString.substring(cutoff, cutoff + 1));
    var d2 = Number(numString.substring(cutoff + 1, cutoff + 2));
    if (d2 >= 5) {
      if (d1 == 9 && cutoff > 0) {
        while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          if (d1 != ".") {
            cutoff -= 1;
            d1 = Number(numString.substring(cutoff, cutoff + 1));
          } else {
            cutoff -= 1;
          }
        }
      }
      d1 += 1;
    }
    if (d1 == 10) {
      numString = numString.substring(0, numString.lastIndexOf("."));
      var roundedNum = Number(numString) + 1;
      newString = roundedNum.toString() + ".";
    } else {
      newString = numString.substring(0, cutoff) + d1.toString();
    }
  }
  if (newString.lastIndexOf(".") == -1) {
    newString += ".";
  }
  var decs = newString.substring(newString.lastIndexOf(".") + 1).length;

  for (var i = 0; i < decimals - decs; i++) newString += "0";
  return newString;
}
function alert_msg(type, msg) {
  if (type == "error") {
    $.gritter.add({
      title: "ERROR",
      text: msg,
      time: "4000",
      image: '<i class="fas fa-exclamation-triangle"></i>',
      class_name: "alert-error",
    });
  }
  if (type == "warning") {
    $.gritter.add({
      title: "ATENCIÓN",
      text: msg,
      time: "4000",
      image: '<i class="fas fa-exclamation-triangle"></i>',
      class_name: "alert-warning",
    });
  }
  if (type == "success") {
    $.gritter.add({
      title: "OPERACIÓN EXITOSA",
      image: '<i class="fa fa-thumbs-up"></i>',
      text: msg,
      time: "4000",
      class_name: "alert-success",
    });
  }
  if (type == "info") {
    $.gritter.add({
      title: "ATENCIÓN",
      image: '<i class="fa fa-info-circle"></i>',
      text: msg,
      time: "4000",
      class_name: "alert-info",
    });
  }
  if (type == "loader") {
    if (msg) {
      msg = msg;
    } else {
      msg = "Enviando datos";
    }
    $.gritter.add({
      image: '<i class="fa fa-spinner fa-lg fa-spin"></i>',
      title: "PROCESANDO...",
      text: msg,
      sticky: true,
      time: "",
      class_name: "alert-loader",
    });
  }
}
function numbersandletters(e) {
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = " áéíóúabcdefghijklmnñopqrstuvwxyz0123456456789:,.@-#$";
  especiales = "8-37-39-46-58";

  tecla_especial = false;
  for (var i in especiales) {
    if (key == especiales[i]) {
      tecla_especial = true;
      break;
    }
  }
  if (letras.indexOf(tecla) == -1 && !tecla_especial) {
    return false;
  }
}
function letters(e) {
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = " áéíóúabcdefghijklmnñopqrstuvwxyz-";
  especiales = "8-37-39-46";

  tecla_especial = false;
  for (var i in especiales) {
    if (key == especiales[i]) {
      tecla_especial = true;
      break;
    }
  }
  if (letras.indexOf(tecla) == -1 && !tecla_especial) {
    return false;
  }
}
function document_number(e) {
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = "abcdefghijklmnñopqrstuvwxyz0123456456789@-_+*/";
  especiales = "8-37-39-46-58";

  tecla_especial = false;
  for (var i in especiales) {
    if (key == especiales[i]) {
      tecla_especial = true;
      break;
    }
  }
  if (letras.indexOf(tecla) == -1 && !tecla_especial) {
    return false;
  }
}
function account(e) {
  tecla = document.all ? e.keyCode : e.which;
  if (tecla == 8) {
    return true;
  }
  patron = /[0-9-]/;
  tecla_final = String.fromCharCode(tecla);
  return patron.test(tecla_final);
}
function ips_number(e) {
  tecla = document.all ? e.keyCode : e.which;
  if (tecla == 8) {
    return true;
  }
  patron = /[0-9.]/;
  tecla_final = String.fromCharCode(tecla);
  return patron.test(tecla_final);
}
function numbers(e) {
  tecla = document.all ? e.keyCode : e.which;
  if (tecla == 8) {
    return true;
  }
  patron = /[0-9]/;
  tecla_final = String.fromCharCode(tecla);
  return patron.test(tecla_final);
}
function decimal(e) {
  tecla = document.all ? e.keyCode : e.which;
  if (tecla == 8) {
    return true;
  }
  patron = /[0-9.]/;
  tecla_final = String.fromCharCode(tecla);
  return patron.test(tecla_final);
}
function mail(e) {
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key).toLowerCase();
  letras = "abcdefghijklmnñopqrstuvwxyz0123456456789.@";
  especiales = "8-37-39-46-58";

  tecla_especial = false;
  for (var i in especiales) {
    if (key == especiales[i]) {
      tecla_especial = true;
      break;
    }
  }
  if (letras.indexOf(tecla) == -1 && !tecla_especial) {
    return false;
  }
}

Object.defineProperty(Array.prototype, "chunk", {
  value: function (chunkSize) {
    var R = [];
    for (var i = 0; i < this.length; i += chunkSize)
      R.push(this.slice(i, i + chunkSize));
    return R;
  },
});

function formatDecimal(money) {
  const format = `${money}`.split(",");
  return format.join("");
}

function formatDecimalInput(id) {
  const value = $(`#${id}`).val();
  return formatDecimal(value);
}

function formatMoney(number) {
  const value = formatDecimal(number || 0);
  const current = new Intl.NumberFormat("ja-JP", { decimalDigits: 2 }).format(
    parseFloat(value)
  );
  const moneda = current.split(".");
  return `${moneda[0]}.${moneda[1] || "00"}`;
}

function formatMoneyInput(id) {
  const number = $(`#${id}`).val();
  const format = formatMoney(number);
  $(`#${id}`).val(format);
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("google-selected")?.addEventListener("click", () => {
    $("#modal-map").modal("hide");
  });

  document.getElementById("modal-map-close")?.addEventListener("click", () => {
    $("#modal-map").modal("hide");
  });
});
