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

const tableList = () => {
  const id = `list`;
  return datatableHelper(id, "Lista de AP Clientes", {
    ajax: {
      url: ` ${base_url}/campos/list_records`,
      dataSrc: "",
    },
    deferRender: true,
    idDataTables: "1",
    columns: [
      { data: "id", className: "text-center" },
      { data: "nombre" },
      { data: "tabla" },
      {
        data: "campos",
        render: (data, type, full) => {
          const container = document.createElement("div");
          full.campos.forEach((campo) => {
            const body = document.createElement("div");
            body.className = "row justify-between";
            const nombreEl = document.createElement("div");
            nombreEl.className = "col";
            nombreEl.innerHTML = `<b>Nombre: </b> ${campo.nombre}`;
            const tipoEl = document.createElement("div");
            tipoEl.className = "col";
            tipoEl.innerHTML = `<b>Tipo: </b> ${campo.tipo}`;
            const obligatorioEl = document.createElement("div");
            obligatorioEl.className = "col";
            obligatorioEl.innerHTML = `<b>Obligatorio: </b> ${
              campo.obligatorio ? "SI" : "NO"
            }`;
            body.appendChild(nombreEl);
            body.appendChild(tipoEl);
            body.appendChild(obligatorioEl);
            container.appendChild(body);
          });
          return container.outerHTML;
        },
      },
      {
        data: "id",
        className: "text-center",
        sWidth: "40px",
        render: (data, type, full) => {
          let optEdit = "";
          if (full.isEdit) {
            optEdit = btnAction(
              "Editar",
              "blue",
              "far fa-edit",
              `openUpdate('${data}')`
            );
          }
          return `<div>${optEdit}</div>`;
        },
      },
    ],
  });
};

const renderListTablas = () => {
  const component = document.getElementById("tablaId");
  axios
    .get(`${base_url}/campos/list_tablas`)
    .then(({ data }) => {
      $("#btn-save").prop("disabled", false);
      component.innerHTML = "";
      data?.forEach((item) => {
        const opt = document.createElement("option");
        opt.value = item.id;
        opt.innerText = item.nombre;
        component.appendChild(opt);
      });
    })
    .catch(() => {
      component.innerHTML = "";
    });
};

const openCreate = () => {
  document.querySelector("#text-title").innerHTML = "Nuevo Campo Personalizado";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#transactions").reset();
  $("#btn-save").prop("disabled", true);
  $("#transactions").parsley().reset();
  $("#modal-action").modal("show");
  document.getElementById("campo-container").style = "display: none";
  document.getElementById("campo-body").style = "display: block";
  document.getElementById("btn-delete").style = "display: none";
  const component = document.getElementById("transactions");
  renderListTablas();
  component.onsubmit = (e) => {
    e.preventDefault();
    create();
  };
};

const create = () => {
  const transactions = document.querySelector("#transactions");
  const isValid = $("#transactions").parsley().isValid();
  if (!isValid) return;
  loading.style.display = "flex";
  const formData = new FormData(transactions);
  fetch(`${base_url}/campos/save`, {
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

const renderListCampos = (id, itemId) => {
  document.getElementById("campo-body").style = "display: none";
  const component = document.getElementById("campoId");
  component.setAttribute("disabled", true);
  axios
    .get(`${base_url}/campos/list_campos/${id}`)
    .then(({ data }) => {
      component.innerHTML = "";
      data?.forEach((item, index) => {
        const opt = document.createElement("option");
        component.appendChild(opt);
        opt.value = item.id;
        opt.id = `option-${item.id}`;
        opt.innerText = item.nombre;
        component.onchange = ({ target }) => selectColumn(target.value, data);
        if (itemId == item.id) {
          opt.selected = true;
          selectColumn(item.id, data);
        } else if (index == 0) {
          selectColumn(item.id, data);
          opt.selected = true;
        } else {
          opt.selected = false;
        }
      });
    })
    .catch((err) => {
      console.log(err);
      component.innerHTML = "";
    })
    .finally(() => component.removeAttribute("disabled"));
};

const selectColumn = (id, data) => {
  const item = data?.find((item) => item.id == id);
  $("#nombre").val(item.nombre);
  $("#campo").val(item.campo);
  $("#obligatorio").val(item.obligatorio);
  $("#tipo").val(item.tipo);
  const component = document.getElementById("transactions");
  component.onsubmit = (evt) => update(evt, item);
  $("#btn-save").prop("disabled", false);
  document.getElementById("campo-body").style = "display: flex";
  const btnDelete = document.getElementById("btn-delete");
  btnDelete.style = "display: block";
  btnDelete.onclick = () => openRemove(item);
};

const openUpdate = (id) => {
  document.querySelector("#text-title").innerHTML = "Editar AP Cliente";
  document.querySelector("#text-button").innerHTML = "Actulizar Registro";
  document.getElementById("campo-container").style = "display: block";
  document.getElementById("campo-body").style = "display: none";
  $("#btn-save").prop("disabled", true);
  $("#transactions").parsley().reset();
  const component = document.getElementById("transactions");
  component.reset();
  renderListCampos(id);
  axios
    .get(`${base_url}/campos/select_tabla/${id}`)
    .then(({ data }) => {
      $("#modal-action").modal("show");
      const select = document.getElementById("tablaId");
      select.innerHTML = "";
      const opt = document.createElement("option");
      opt.value = data.id;
      opt.selected = true;
      opt.innerText = data.nombre;
      select.appendChild(opt);
    })
    .catch(() => null);
};

const update = (evt, item) => {
  evt.preventDefault();
  loading.style.display = "flex";
  const form = document.querySelector("#transactions");
  const data = new FormData(form);
  axios
    .post(`${base_url}/campos/update_record/${item.id}`, data)
    .then(({ data }) => {
      if (data.status == "error") throw new Error(data.msg);
      alert_msg("success", data.msg);
      tableList().refresh();
      renderListCampos(item.tablaId, item.id);
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};

const openRemove = (item) => {
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
          remove(item)
            .then((data) => {
              alsup.close();
              tableList().refresh();
              $('[data-toggle="tooltip"]').tooltip("hide");
              alert_msg("success", data.message);
              $("#modal-action").modal("hide");
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

const remove = (data) => {
  return new Promise((resolve, reject) => {
    fetch(`${base_url}/campos/remove_record/${data.id}`, {
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

// load functions
document.addEventListener("DOMContentLoaded", tableList().render, false);
window.addEventListener("load", () => $("#transactions").parsley(), false);
