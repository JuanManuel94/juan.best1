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
    const arrayColumns = [
      { data: "id", className: "text-center" },
      { data: "nombre" },
      { data: "tipo" },
      {
        data: "info",
        render: (data, type, full) => {
          if (full.conector == "ip") {
            const a = document.createElement("a");
            a.innerText = `${full.conector}: ${data}`;
            a.href = `http://${data}`;
            a.target = "_blank";
            return a.outerHTML;
          } else {
            return `${data} ${full.conector}`;
          }
        },
      },
      {
        data: "countClientes",
        className: "text-center",
        render(data, type, full) {
          const span = document.createElement("span");
          span.className = `badge badge-sm badge-${
            full.counterActivos ? "success" : "danger"
          }`;
          span.innerText = full.counterActivos ? "EN LINEA" : "DESCONECTADO";
          return span.outerHTML;
        },
      },
      {
        data: "countClientes",
        className: "text-center",
        render(data, type, full) {
          const span = document.createElement("span");
          span.className = "badge badge-sm badge-success";
          span.innerText = data;
          return span.outerHTML;
        },
      },
      {
        data: "counterActivos",
        className: "text-center",
        render(data, type, full) {
          const span = document.createElement("span");
          span.className = "badge badge-sm badge-warning";
          span.innerText = data;
          return span.outerHTML;
        },
      },
      {
        data: "counterSuspendidos",
        className: "text-center",
        render(data, type, full) {
          const span = document.createElement("span");
          span.className = "badge badge-sm badge-danger";
          span.innerText = data;
          return span.outerHTML;
        },
      },
      {
        data: "counterCancelados",
        className: "text-center",
        render(data, type, full) {
          const span = document.createElement("span");
          span.className = "badge badge-sm badge-danger";
          span.innerText = data;
          return span.outerHTML;
        },
      },
    ];
    // response
    return arrayColumns;
  };

  return datatableHelper(id, "Lista de Monitoreo", {
    ajax: {
      url: ` ${base_url}/monitoreo/list_records`,
      dataSrc: "",
    },
    deferRender: true,
    idDataTables: "1",
    columns: getColumns(),
  });
};

// load functions
document.addEventListener("DOMContentLoaded", tableList().render, false);
window.addEventListener("load", () => $("#transactions").parsley(), false);
