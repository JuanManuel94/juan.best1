const componentLoading = (status = false) => {
  if (status) loading.style.display = "flex";
  else loading.style.display = "none";
};

const tableList = () => {
  const id = `list`;

  const apId = document.getElementById("apId").value;

  const getColumns = () => {
    const arrayColumns = [
      { data: "clientid", className: "text-center" },
      {
  data: "client",
  render: (data, type, full) => {
    return `<a href="${base_url}/customers/view_client/${full.encrypt_client}" target="_blank">${full.client}</a>`;
  }
},
      
      {
        data: "document",
      },
      {
  data: "mobile",
  render: (data, type, full) => {
    let mobile = full.mobile ? `<i class="fa fa-mobile mr-1"></i> ${full.mobile}` : "";
    let mobileOptional = full.mobile_optional ? `<br><i class="fa fa-mobile mr-1"></i> ${full.mobile_optional}` : "";
    
    return `${mobile}${mobileOptional}`;
  }
},

      { data: "ap_name" },
      {
        data: "net_ip",
        render: (data, type, full) => {
          if (data == "") {
            return "NO REGISTRADO"; 
          } else {
            const a = document.createElement("a");
            a.href = `http://${data}`;
            a.innerHTML = `<i class="fa fa-network-wired mr-1"></i> ${data}`;
            a.target = "_blank";
            return a.outerHTML;
          }
        },
      },
      {
        data: "latitud",
        render: (data, type, full) => {
          if (data == "") {
            return "";
          } else {
            const a = document.createElement("a");
            a.href = `${base_url}/customers/customer_location/${full.encrypt_client}`;
            a.target = "_blank";
            const icon = document.createElement("i");
            icon.className = "fa fa-map-marker-alt mr-1 text-danger";
            a.innerText = `${full.latitud}, ${full.longitud} `;
            a.appendChild(icon);
            return a.outerHTML;
          }
        },
      },
      { data: "address" },
      { data: "reference" },
      {
        data: "state",
        render: (data, type, full) => {
          if (full.state == 1) {
            return '<span class="label label-orange">INSTALACIÓN</span>';
          }
          if (full.state == 2) {
            return '<span class="label label-success">ACTIVO</span>';
          }
          if (full.state == 3) {
            return '<span class="label label-primary">SUSPENDIDO</span>';
          }
          if (full.state == 4) {
            return '<span class="label label-dark">CANCELADO</span>';
          }
          if (full.state == 5) {
            return '<span class="label label-indigo">GRATIS</span>';
          }
        },
      },
    ];
    // response
    return arrayColumns;
  };

  return datatableHelper(id, "Lista de AP Clientes", {
    ajax: {
      url: ` ${base_url}/apclientes/list_users_record/${apId}`,
      dataSrc: "",
    },
    deferRender: true,
    idDataTables: "1",
    columns: getColumns(),
  });
};

// load functions
document.addEventListener("DOMContentLoaded", tableList().render, false);
