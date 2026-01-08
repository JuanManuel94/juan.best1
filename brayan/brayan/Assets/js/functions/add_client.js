document.addEventListener(
  "DOMContentLoaded",
  function () {
    if (document.querySelector("#transactions")) {
      var transactions = document.querySelector("#transactions");
      transactions.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/customers/register_contract";
          var formData = new FormData(transactions);
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
                        $(location).attr(
                          "href",
                          base_url + "/customers/view_client/" + objData.id
                        );
                      },
                    },
                  },
                });
              } else if (objData.status == "exists") {
                alert_msg("warning", objData.msg);
              } else {
                alert_msg("error", objData.msg);
              }
            }
            loading.style.display = "none";
            return false;
          };
        } else {
          console.log("Parsley is invalid.");
        }
      };
    }
  },
  false
);

window.addEventListener(
  "load",
  function () {
    form_wizard();
    list_technical();
    list_documents();
    showDiscount();
    $("#insDate").datetimepicker({ locale: "es" });
    $("#insDate").val(moment().format("DD/MM/YYYY H:mm"));
    $("#transactions").parsley();
  },
  false
);

function form_wizard() {
  $("#wizard").smartWizard({
    selected: 0,
    theme: "default",
    transitionEffect: "",
    transitionSpeed: 0,
    useURLhash: false,
    lang: {
      next: "Siguiente",
      previous: "Anterior",
    },
    showStepURLhash: false,
    toolbarSettings: {
      toolbarPosition: "bottom",
      toolbarExtraButtons: [
        $("<button></button>")
          .html('<i class="fas fa-save mr-2"></i>Guardar Registro')
          .attr("type", "submit")
          .addClass("btn btn-blue btn-finish d-none"),
      ],
    },
  });

  $("#wizard").on(
    "leaveStep",
    function (e, anchorObject, stepNumber, stepDirection) {
      var res = $('form[name="transactions"]')
        .parsley()
        .validate("step-" + (stepNumber + 1));
      return res;
    }
  );

  $("#wizard").keypress(function (event) {
    if (event.which == 13) {
      $("#wizard").smartWizard("next");
    }
  });

  $("#wizard").on(
    "showStep",
    function (e, anchorObject, stepNumber, stepDirection) {
      $(".srvdire").val($(".drprin").val());
      if (stepNumber == 2) {
        $(".btn-finish").removeClass("d-none");
        $(".sw-btn-group").hide();
      } else {
        $(".btn-finish").addClass("d-none");
        $(".sw-btn-group").show();
      }
    }
  );
}

function list_technical() {
  if (document.querySelector("#listTechnical")) {
    var ajaxUrl = base_url + "/customers/list_technical";
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector("#listTechnical").innerHTML =
          request.responseText;
        $("#listTechnical").select2();
      }
    };
  }
}

$("#listPlan").on("click change", function () {
  type_plan($(this).val());
});

function type_plan(value) {
  switch (value) {
    case "1":
      $(".cont-day").show("fast");
      $(".cont-create").show("fast");
      $(".cont-gracia").show("fast");
      $(".cont-chk").show("fast");
      break;
    case "2":
      $(".cont-day").hide("fast");
      $(".cont-create").hide("fast");
      $(".cont-gracia").hide("fast");
      $(".cont-chk").hide("fast");
      check = document.querySelector("#chkDiscount");
      if (check.checked) {
        $(".cont-dis").hide("fast");
        $(".cont-month").hide("fast");
        check.checked = false;
      } else {
        $(".cont-dis").hide("fast");
        $(".cont-month").hide("fast");
      }
      break;
  }
}

function showDiscount() {
  check = document.querySelector("#chkDiscount");
  if (check.checked) {
    $(".cont-dis").show("fast");
    $(".cont-month").show("fast");
  } else {
    $(".cont-dis").hide("fast");
    $(".cont-month").hide("fast");
  }
}

$("#search_service").keyup(function () {
  let search = $(this).val();
  if (search != "") {
    var request = window.XMLHttpRequest
      ? new XMLHttpRequest()
      : new ActiveXObject("Microsoft.XMLHTTP");
    var ajaxUrl = base_url + "/customers/search_service";
    var strData = "search=" + search;
    request.open("POST", ajaxUrl, true);
    request.setRequestHeader(
      "Content-type",
      "application/x-www-form-urlencoded"
    );
    request.send(strData);
    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        document.querySelector(".search-input").classList.add("active");
        document.querySelector("#box-search").innerHTML = request.responseText;
      }
    };
  } else {
    document.querySelector(".search-input").classList.remove("active");
    document.querySelector("#box-search").innerHTML = "";
  }
});

function select_service(idservice) {
  var request = window.XMLHttpRequest
    ? new XMLHttpRequest()
    : new ActiveXObject("Microsoft.XMLHTTP");
  var ajaxUrl = base_url + "/customers/select_service/" + idservice;
  request.open("GET", ajaxUrl, true);
  request.send();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var objData = JSON.parse(request.responseText);
      if (objData.status) {
        document.querySelector("#search_service").value = "";
        document.querySelector(".search-input").classList.remove("active");
        document.querySelector("#box-search").innerHTML = "";
        document.querySelector("#idservice").value = objData.data.encrypt_id;
        document.querySelector("#service").value = objData.data.service;
        document.querySelector("#detail-service").value = objData.data.details;
        document.querySelector("#price-service").value = objData.data.price;
        loadComponentNetwork("network_mount", idservice).then(network_validate);
        loadComponentIP();
      } else {
        alert_msg("info", objData.msg);
      }
    }
  };
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
        $("#listTypes").select2({ minimumResultsForSearch: -1 });
      }
    };
  }
}

function search_document() {
  var type = document.querySelector("#listTypes").value;
  var doc = document.querySelector("#document").value;
  
  if (doc != "") {
    $(".btn-search").html('<i class="fa fa-spinner fa-sm fa-spin"></i>');
    var request = new XMLHttpRequest();
    var ajaxUrl = base_url + "/customers/search_document/" + type + "/" + doc;
    
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
      if (request.readyState == 4 && request.status == 200) {
        var objData = JSON.parse(request.responseText);
        
        if (objData.status == "success") {
          document.querySelector("#names").value = objData.data.names;
          document.querySelector("#surnames").value = objData.data.surnames;
          document.querySelector("#address").value = objData.data.address;
          
          generateNetName();

        } else {
          alert_msg(objData.status, objData.msg);
          document.querySelector("#document").value = "";
          document.querySelector("#names").value = "";
          document.querySelector("#surnames").value = "";
          document.querySelector("#address").value = "";

          generateNetName();
        }
      }
      
      $(".btn-search").html('<i class="fa fa-search"></i>');
    };
  } else {
    if (type == 2) alert_msg("error", "Ingrese el numero de DNI.");
    if (type == 3) alert_msg("error", "Ingrese el numero de RUC.");
    if (type == 4) alert_msg("error", "Ingrese el numero de carnet de extranjería.");
  }
}


function generateNetName() {
  const names = $("#names").val().trim().toUpperCase();
  const surnames = $("#surnames").val().trim().toUpperCase();
  const uppercaseValue = `${names} ${surnames}`.replace(/\s+/g, "-");
  getZonaNameInput().val(uppercaseValue);
}


function network_validate() {
  getPasswordInput().attr("data-placement", "false");
  getPasswordInput().attr("data-parsley-group", "step-3");
  getPasswordInput().attr("data-parsley-required", "false");
  getLocalAddressInput().attr("data-parsley-group", "step-3");
  getLocalAddressInput().attr("data-parsley-required", "false");
  generateNetName();
}

$(function () {
  $("#names,#surnames").on("input", function () {
    generateNetName();
  });
});
