const btnAction = (title, className, icon, action) => {
  return `
    <a href="javascript:;" 
      class="ml-2 ${className}"
      data-toggle="tooltip"
      data-original-title="${title}"
      onclick="${action};"
    >
      <i class="${icon}"></i>
    </a>`;
};

const componentLoading = (status = false) => {
  if (status) loading.style.display = "flex";
  else loading.style.display = "none";
};

const renderResumen = () => {
  const simbol = $("#moneda_simbol").val();
  axios.get(`${base_url}/otrosPagos/resumen`).then(({ data }) => {
    const { ingreso, egreso } = data;
    document.getElementById(
      "ingreso-today"
    ).innerText = `${simbol} ${formatMoney(ingreso.today)}`;
    document.getElementById(
      "ingreso-total"
    ).innerText = `${simbol} ${formatMoney(ingreso.total)}`;
    document.getElementById(
      "egreso-today"
    ).innerText = `${simbol} ${formatMoney(egreso.today)}`;
    document.getElementById(
      "egreso-total"
    ).innerText = `${simbol} ${formatMoney(egreso.total)}`;
  });
};

const tableList = () => {
  const id = `list`;

  const getColumns = () => {
    const columns = JSON.parse(document.getElementById("columns").innerText);
    const arrayColumns = [
      { data: "id", className: "text-center" },
      {
        data: "fecha",
        render: (data) => {
          return moment(data).format("DD/MM/YYYY");
        },
      },
      {
        data: "monto",
        render(data) {
          return formatMoney(data);
        },
      },
      {
        data: "tipo",
        render: (data) => {
          const span = document.createElement("span");
          const tipo = data == "INGRESO" ? "success" : "danger";
          span.className = `badge badge-sm  badge-${tipo}`;
          span.innerText = data;
          return span.outerHTML;
        },
      },
      { data: "operador" },
      { data: "descripcion" },
      {
        data: "state",
        sWidth: "150px",
        className: "text-center",
        render: (data) => {
          const span = document.createElement("span");
          const icons = {
            NORMAL: "badge-light",
            PENDIENTE: "badge-warning",
            PAGADO: "badge-success",
          };
          span.className = `badge badge-sm ${icons[data]}`;
          span.innerText = data;
          return span.outerHTML;
        },
      },
    ];
    // agregar columnas personalizadas
    columns?.forEach((item) => arrayColumns.push({ data: item.campo }));
    // agregar opciones
    arrayColumns.push({
      data: "id",
      className: "text-center",
      sWidth: "40px",
      render: (data, type, full) => {
        let optDelete = "";
        let optEdit = "";
        if (full.isRemove) {
          optDelete = btnAction(
            "Eliminar",
            "red",
            "far fa-trash-alt",
            `openRemove('${data}')`
          );
        }
        if (full.isEdit) {
          optEdit = btnAction(
            "Editar",
            "blue",
            "far fa-edit",
            `openUpdate('${data}')`
          );
        }
        return `<div>${optEdit}${optDelete}</div>`;
      },
    });
    // response
    return arrayColumns;
  };

  const dataHelper = datatableHelper(id, "Lista Otros Ingresos & Egresos", {
    ajax: {
      url: `${base_url}/otrosPagos/list_records`,
      dataSrc: "",
      data: function (data) {
        const dateStart = $("#dateStart").val();
        const dateOver = $("#dateOver").val();
        if (dateStart) data.dateStart = dateStart;
        if (dateOver) data.dateOver = dateOver;
      },
    },
    deferRender: true,
    idDataTables: "1",
    columns: getColumns(),
  });

  const refresh = () => {
    dataHelper.refresh();
    renderResumen();
  };

  const render = () => {
    dataHelper.render();
    renderResumen();
  };

  const api = () => dataHelper.api();

  return { render, refresh, api };
};

const openCreate = () => {
  document.querySelector("#text-title").innerHTML = "Nuevo Ingreso/Egreso";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  $("#modal-action").modal("show");
  changeTipo();
  const component = document.getElementById("transactions");
  component.onsubmit = (e) => {
    e.preventDefault();
    create();
  };
};

const create = () => {
  const transactions = document.querySelector("#transactions");
  const isValid = $("#transactions").parsley().isValid();
  if (!isValid) return;

  // Asegúrate de establecer un valor en el campo state antes de enviar
  if ($("#tipo").val() === "INGRESO") {
    $("#state").val("NORMAL"); // Establece un valor explícito
  }

  loading.style.display = "flex";
  const formData = new FormData(transactions);

  fetch(`${base_url}/otrosPagos/save`, {
    method: "post",
    body: formData,
  })
    .then((dataJson) => dataJson.json())
    .then((data) => {
      if (!data.status) throw new Error(data.message);
      $("#modal-action").modal("hide");
      transactions.reset();
      alert_msg("success", data.message);
      tableList().refresh();
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};

// Función para obtener la fecha actual en formato YYYY-MM-DD
function getCurrentDate() {
  const today = new Date();
  return today.toISOString().split("T")[0]; // Formato YYYY-MM-DD
}

// Configurar el valor del campo de fecha al abrir el modal
$("#modal-action").on("show.bs.modal", function () {
  const fechaInput = document.querySelector("#fecha");
  if (!fechaInput.value) {
    fechaInput.value = getCurrentDate(); // Establece la fecha actual si está vacío
  }
});

const openUpdate = (id) => {
  document.querySelector("#text-title").innerHTML = "Editar Registro";
  document.querySelector("#text-button").innerHTML = "Actualizar Registro";

  const component = document.getElementById("transactions");
  component.reset();

  axios
    .get(`${base_url}/otrosPagos/select_record/${id}`)
    .then(({ data }) => {
      $("#transactions").parsley().reset();
      $("#modal-action").modal("show");
      $("#objectId").val(data.id);
      $("#tipo").val(data.tipo);
      $("#fecha").val(data.fecha);
      $("#descripcion").val(data.descripcion);
      $("#monto").val(data.monto);
      $("#state").val(data.state || "NORMAL"); // Valor predeterminado si falta
      $("#currentType").val(data.state);

      const columns = JSON.parse(document.getElementById("columns").innerText);
      columns?.forEach((item) => $(`#${item.campo}`).val(data[item.campo]));

      component.onsubmit = (evt) => update(evt, data.id);
      changeTipo(); // Asegúrate de ajustar la visibilidad y valores
    })
    .catch(() => null);
};

const update = (evt, id) => {
  evt.preventDefault();
  loading.style.display = "flex";
  const form = document.querySelector("#transactions");
  const data = new FormData(form);
  axios
    .post(`${base_url}/otrosPagos/update_record/${id}`, data)
    .then(({ data }) => {
      if (data.status == "error") throw new Error(data.msg);
      alert_msg("success", data.msg);
      tableList().refresh();
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};

const openRemove = (id) => {
  const alsup = $.confirm({
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
          componentLoading(true);
          remove(id)
            .then((data) => {
              alsup.close();
              tableList().refresh();
              $('[data-toggle="tooltip"]').tooltip("hide");
              alert_msg("success", data.message);
            })
            .catch((err) => {
              $('[data-toggle="tooltip"]').tooltip("hide");
              alert_msg("info", err.message);
            })
            .finally(() => {
              $(".jconfirm-closeIcon").remove();
              componentLoading();
            });
        },
      },
      close: {
        text: "Cancelar",
      },
    },
  });
};

const remove = (id) => {
  return new Promise((resolve, reject) => {
    fetch(`${base_url}/otrosPagos/remove_record/${id}`, {
      method: "post",
    })
      .then((dataJSON) => dataJSON.json())
      .then((data) => {
        if (!data.status) throw new Error(data.message);
        resolve(data);
      })
      .catch(reject);
  });
};

const checkDate = () => {
  const start = $("#dateStart").val();
  const end = $("#dateOver").val();
  if (!start || !end) return;
  tableList().refresh();
};

const changeTipo = () => {
  const tipo = $("#tipo").val(); // Obtén el valor del tipo
  const container = $("#state-container"); // Contenedor del campo estado
  const state = $("#state"); // Campo de estado

  if (tipo === "INGRESO") {
    container.hide(); // Oculta el contenedor
    state.val("NORMAL"); // Valor predeterminado para ingreso
    state.removeAttr("data-parsley-required"); // Desactiva la validación para ingreso
  } else {
    container.show(); // Muestra el contenedor
    const currentState = $("#currentType").val(); // Obtén el estado actual si existe
    state.val(currentState || "PENDIENTE"); // Valor predeterminado para egreso
    state.attr("data-parsley-required", "true"); // Activa la validación para egreso
  }
};

function formatDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
}

// load functions
document.addEventListener("DOMContentLoaded", function () {
  const startDate = moment().startOf("year");
  const overDate = moment().endOf("month");
  $("#dateStart").val(startDate.format("YYYY-MM-DD"));
  $("#dateOver").val(overDate.format("YYYY-MM-DD"));
  checkDate();
});
document.addEventListener("DOMContentLoaded", tableList().render, false);
window.addEventListener("load", () => $("#transactions").parsley(), false);
