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

  const getColumns = () => {
    const columns = JSON.parse(document.getElementById("columns").innerText);
    const arrayColumns = [
      { data: "id", className: "text-center" },
      { data: "nombre" },
      {
        data: "ip",
        render: (data) => {
          const a = document.createElement("a");
          a.innerText = data;
          a.href = `http://${data}`;
          a.innerHTML = `<i class="fa fa-network-wired mr-1"></i> ${data}`;
          a.target = "_blank";
          return a.outerHTML;
        },
      },
      { data: "version" },
    ];
    // agregar columnas personalizadas
    columns?.forEach((item) => arrayColumns.push({ data: item.campo }));
    // agregar opciones
    arrayColumns.push(
      ...[
        { data: "countClientes", className: "text-center" },
        {
          data: "id",
          className: "text-center",
          sWidth: "40px",
          render: (data, type, full) => {
            let optClientes = btnAction(
              "Ver Clientes",
              "blue",
              "fa fa-users",
              `showUsers('${data}')`
            );
            let optFiles = btnAction(
              "Ver Archivos",
              "blue",
              "fa fa-file",
              `showFiles('ap_clientes', '${data}')`
            );
            let optEdit = "";
            let optDelete = "";
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
            return `<div>${optEdit}${optClientes}${optFiles}${optDelete}</div>`;
          },
        },
      ]
    );
    // response
    return arrayColumns;
  };

  return datatableHelper(id, "Lista de AP Clientes", {
    ajax: {
      url: ` ${base_url}/apclientes/list_records`,
      dataSrc: "",
    },
    deferRender: true,
    idDataTables: "1",
    columns: getColumns(),
  });
};

const openCreate = () => {
  document.querySelector("#text-title").innerHTML = "Nuevo AP Cliente";
  document.querySelector("#text-button").innerHTML = "Guardar Registro";
  document.querySelector("#transactions").reset();
  $("#transactions").parsley().reset();
  $("#modal-action").modal("show");
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
  loading.style.display = "flex";
  const formData = new FormData(transactions);
  fetch(`${base_url}/apclientes/save`, {
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

const openUpdate = (id) => {
  document.querySelector("#text-title").innerHTML = "Editar AP Cliente";
  document.querySelector("#text-button").innerHTML = "Actulizar Registro";
  const component = document.getElementById("transactions");
  component.reset();
  axios
    .get(`${base_url}/apclientes/select_record/${id}`)
    .then(({ data }) => {
      $("#transactions").parsley().reset();
      $("#modal-action").modal("show");
      $("#objectId").val(data.id);
      $("#nombre").val(data.nombre);
      $("#ip").val(data.ip);
      $("#version").val(data.version);
      const columns = JSON.parse(document.getElementById("columns").innerText);
      columns?.forEach((item) => $(`#${item.campo}`).val(data[item.campo]));
      component.onsubmit = (evt) => update(evt, data.id);
    })
    .catch(() => null);
};

const update = (evt, id) => {
  evt.preventDefault();
  loading.style.display = "flex";
  const form = document.querySelector("#transactions");
  const data = new FormData(form);
  axios
    .post(`${base_url}/apclientes/update_record/${id}`, data)
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
    fetch(`${base_url}/apclientes/remove_record/${id}`, {
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

const showUsers = (data) => {
  location.href = `apclientes/list_users/${data}`;
};

// load functions
document.addEventListener("DOMContentLoaded", tableList().render, false);
window.addEventListener("load", () => $("#transactions").parsley(), false);
