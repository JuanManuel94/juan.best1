let loadingNap = { value: false };
let loadingAp = { value: false };

function getRouterSelect() {
  return $("#netRouter");
}

function getNetNameId() {
  return $("#netNameId");
}

function getZonaInput() {
  return $("#netZone");
}

function getZonaNameInput() {
  return $("#netName");
}

function getPasswordInput() {
  return $("#netPassword");
}

function getIpInput() {
  return $("#netIP");
}

function getLocalAddressInput() {
  return $("#netLocalAddress");
}

function getIPPickerModal() {
  return $("#IPPicker");
}

function getIPList() {
  return $("#ip-options-list");
}

function getTogglePassword() {
  return $(".toggle-password");
}

function getRefreshPassword() {
  return $(".refresh-password");
}

function getApClientContent() {
  return $("#content-ap_cliente_id");
}

function getApClientValue() {
  return $("#ap_cliente_value");
}

function getApClientLabel() {
  return $("#ap_cliente_id");
}

function getNapClientContent() {
  return $("#content-nap_cliente_id");
}

function getNapClientValue() {
  return $("#nap_cliente_value");
}

function getNapClientLabel() {
  return $("#nap_cliente_id");
}

function networkModes() {
  return [
    { mode: 1, title: "Nombre Simple Queue", type: "ap_client" },
    { mode: 2, title: "Nombre Secret PPPoE", type: "nap_client" },
  ];
}

function findNetworkMode(id) {
  return networkModes().find((item) => item.mode == id);
}

function searchRouters(serviceId) {
  const select = getRouterSelect();
  let urlLink = `${base_url}/customers/customer_plan_routers`;

  if (serviceId) {
    urlLink = `${urlLink}/${serviceId}`;
  }

  select.empty();
  select.append(
    `<option value="" disabled selected>Obteniendo lista...</option>`
  );

  return axios.get(urlLink).then(({ data }) => {
    select.empty();
    data.forEach((item, index) => {
      select.append(
        `<option 
          value="${item.id}" 
          data-zone-name="${item.zone_name}" 
          data-mode="${item.zone_mode}"
        >
          ${item.name}
        </option>`
      );

      if (index == 0) {
        select.val(item.id).trigger("change");
      }
    });
  });
}

function searchIp(isShow = true, querySearch) {
  if (isShow) {
    getIPPickerModal().modal("show");
  }

  getIPList().find("li").remove();
  getIPList().append(
    `<li class='list-group-item disabled'>Obteniendo lista...</li>`
  );

  const form = new FormData();
  form.append("id", getRouterSelect().val());

  if (querySearch) {
    form.append("querySearch", querySearch);
  }

  axios
    .post(`${base_url}/network/router_available_ips`, form)
    .then(({ data }) => {
      getIPList().find("li").remove();
      // validar si no es array
      if (!Array.isArray(data)) {
        data = Object.values(data);
      }
      // agregar li
      data.forEach((item) => {
        getIPList().append(`
          <li class="list-group-item">
            <a href='#'>${item}</a>
          </li>
        `);
      });
      // filtrar li
      getIPList()
        .find("li:not(.disabled) a")
        .on("click", function () {
          const selected = $(this).text();
          getIpInput().val(selected);
          const arrayIp = selected.split(".");
          arrayIp.pop();
          arrayIp.push("1");
          getLocalAddressInput().val(arrayIp.join("."));
          getIPPickerModal().modal("hide");
        });
    });
}

function search_nap_cliente() {
  searchComponent("nap_cliente_id", "nap", loadingNap)
    .then(
      ({
        input,
        value,
        renderNotFound,
        renderItem,
        closeContainer,
        clearBox,
        closeEvent,
      }) => {
        const url = `${base_url}/cajaNap/search_puertos?querySearch=${value}`;

        const actionItem = (input, item) => {
          input.value = item.nombre;
          $("#nap_cliente_value").val(item.id);
          closeContainer();
        };

        axios
          .get(url)
          .then(({ data }) => {
            if (!data.length) throw new Error();
            clearBox();
            data.forEach((ap) => {
              const item = renderItem(ap.nombre);
              item.id = `ap-${ap.id}`;
              item.onclick = () => actionItem(input, ap);
            });
          })
          .catch(() => renderNotFound())
          .finally(() => closeEvent());
      }
    )
    .catch((err) => alert_msg("warning", err.message));
}

function search_ap_cliente() {
  searchComponent("ap_cliente_id", "apcliente", loadingAp)
    .then(
      ({
        input,
        value,
        renderNotFound,
        renderItem,
        closeContainer,
        clearBox,
        closeEvent,
      }) => {
        const url = `${base_url}/apclientes/list_records?querySearch=${value}`;

        const actionItem = (input, item) => {
          input.value = item.nombre;
          $("#ap_cliente_value").val(item.id);
          closeContainer();
        };

        axios
          .get(url)
          .then(({ data }) => {
            if (!data.length) throw new Error();
            clearBox();
            data.forEach((ap) => {
              const item = renderItem(ap.nombre);
              item.id = `ap-${ap.id}`;
              item.onclick = () => actionItem(input, ap);
            });
          })
          .catch(() => renderNotFound())
          .finally(() => closeEvent());
      }
    )
    .catch((err) => alert_msg("warning", err.message));
}

function clearInput() {
  getPasswordInput().parent().parent().parent().hide();
  getLocalAddressInput().parent().parent().hide();
}

function changeRouter() {
  getRouterSelect().change(function () {
    const selected = $(this).find("option:selected");
    getZonaInput().val(selected.data("zone-name"));
    getIpInput().val("");
    getLocalAddressInput().val("");

    const mode = findNetworkMode(selected.data("mode"));
    if (!mode) return;

    getZonaNameInput().parent().siblings("label").text(mode.title);
    const parenPassword = getPasswordInput().parent().parent().parent();
    const parentLocal = getLocalAddressInput().parent().parent();

    if (mode.type == "nap_client") {
      parenPassword.show();
      parentLocal.show();
      getNapClientContent().show();
      getApClientContent().hide();
      getApClientValue().val(null);
      getApClientLabel().val(null);
    } else {
      parenPassword.hide();
      parentLocal.hide();
      getNapClientContent().hide();
      getApClientContent().show();
      getNapClientValue().val(null);
      getNapClientLabel().val(null);
    }

    getNetNameId().val(mode.mode).trigger("change");
  });
}

function changeTogglePassword() {
  getTogglePassword().click(function () {
    const input = $(this).siblings("input");
    if (input.attr("type") == "text") {
      input.attr("type", "password");
      $(this)
        .find("i")
        .removeClass("icon-eye-slash-open")
        .addClass("icon-eye-open");
    } else {
      input.attr("type", "text");
      $(this)
        .find("i")
        .addClass("icon-eye-slash-open")
        .removeClass("icon-eye-open");
    }
  });
}

function generatePassword(length, useUppercase, useNumbers, useSpecialChars) {
  let chars = "abcdefghijklmnopqrstuvwxyz";
  if (useUppercase) chars += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  if (useNumbers) chars += "0123456789";
  if (useSpecialChars) chars += "!@#$%^&*()_+~`|}{[]:;?><,./-=";

  let password = "";
  for (var i = 0; i < length; i++) {
    password += chars.charAt(Math.floor(Math.random() * chars.length));
  }

  return password;
}

function refreshPassword() {
  const value = generatePassword(16, true, true, false);
  getPasswordInput().val(value);
}

function loadComponentNetwork(id = "network_mount", serviceId) {
  return new Promise((resolve, reject) => {
    axios
      .get(`${base_url}/network/network_template`)
      .then(({ data }) => {
        const root = document.getElementById(id);
        root.innerHTML = data;
        clearInput();
        changeTogglePassword();
        changeRouter();
        searchRouters(serviceId).then(() => {
          resolve(root);
        });
      })
      .catch(reject);
  });
}

function loadComponentIP(id = "network_ip_mount") {
  axios
    .get(`${base_url}/network/network_ip_template`)
    .then(({ data }) => {
      const root = document.getElementById(id);
      root.innerHTML = data;
      document
        .getElementById("search-input")
        .addEventListener("keyup", function () {
          const querySearch = $(this).val();
          searchIp(false, querySearch);
        });
    })
    .catch(console.error);
}
