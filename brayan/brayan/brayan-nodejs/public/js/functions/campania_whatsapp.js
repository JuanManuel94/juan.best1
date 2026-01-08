function wspContactoClear() {
  const content = document.getElementById("wsp-contactos");
  content.innerHTML = "";
}

function wspChatClear() {
  $("#file").val("");
  $("#message").val("");
  $("#wsp_btn").prop("disabled", true);
  const fileText = document.getElementById("file-text");
  fileText.innerText = "Subir archivo";
}

function wspClear() {
  wspContactoClear();
  wspChatClear();
}

function wspContactItem(client) {
  const parent = document.getElementById("wsp-contactos");
  const content = document.createElement("div");
  content.id = `content-${client.id}`;
  content.className = "pt-2 pb-2 pl-1 pr-1 border-bottom";
  const title = document.createElement("div");
  title.id = `title-${client.id}`;
  title.innerText = `${client.cliente}`;
  const phone = document.createElement("div");
  phone.className = "text-primary";
  phone.innerText = `(${client.country_code}${client.mobile})`;
  const close = document.createElement("i");
  close.id = `close-${client.id}`;
  close.className = "fas fa-trash close cursor-pointer";
  // save data
  const inputData = document.createElement("input");
  inputData.type = "hidden";
  inputData.name = "clients[]";
  inputData.value = JSON.stringify(client);
  title.appendChild(close);
  content.appendChild(title);
  content.appendChild(phone);
  content.appendChild(inputData);
  parent.appendChild(content);
  close.onclick = () => wspRemoveItem(client);
}

function wspRemoveItem(client) {
  const component = document.getElementById(`content-${client.id}`);
  component.remove();
  const form = new FormData(document.getElementById("form-wsp"));
  const counter = form.getAll("clients[]").length;
  if (counter) return;
  const select = document.getElementById("select-state");
  select.value = "";
}

function getConcacts(state = 0) {
  wspContactoClear();
  axios
    .get(`${base_url}/campania/list_users_record?state=${state}`)
    .then(({ data }) => {
      data?.forEach((item) => wspContactItem(item));
    })
    .catch((err) => console.log(err));
}

function validateBtn() {
  const message = $("#message").val();
  const file = $("#file").val();
  const isDisabled = message || file ? false : true;
  $("#wsp_btn").prop("disabled", isDisabled);
}

function changeFile() {
  document.getElementById("file").addEventListener("change", (e) => {
    validateBtn();
    const file = document.getElementById("file").files[0];
    const text = document.getElementById("file-text");
    if (!file) {
      text.innerText = "Subir Archivo";
    } else {
      text.innerText = file.name;
    }
  });
}

function changeState() {
  const state = document.getElementById("select-state").value;
  if (!state) return wspContactoClear();
  getConcacts(state);
}

function changeMessage() {
  document.getElementById("message").addEventListener("keyup", validateBtn);
}

function submitWsp() {
  document.getElementById("form-wsp").addEventListener("submit", async (e) => {
    e.preventDefault();
    const errors = [];
    const success = [];
    const file = document.getElementById("file").files[0];
    const form = new FormData(document.getElementById("form-wsp"));
    const data = form.getAll("clients[]").map((item) => {
      const client = JSON.parse(item);
      let message = document.getElementById("message").value;
      Object.keys(client).forEach((attr) => {
        message = message.replace(`{${attr}}`, client[attr]);
      });
      return {
        data: client,
        phone: `${client.country_code}${client.mobile}`,
        message,
        file,
      };
    });
    // loading
    loading.style.display = "flex";
    //enviar mesajes
    const execute = () => {
      return new Promise((resolve) => {
        data.forEach(async (item, index) => {
          await sendMessageWhatsapp(item)
            .then(() => {
              wspRemoveItem(item.data);
              success.push(item);
            })
            .catch(() => errors.push(item))
            .finally(() => {
              if (index + 1 == data.length) resolve(true);
            });
        });
      });
    };
    // executar
    execute().finally(() => {
      if (!errors.length) {
        wspClear();
        alert_msg("success", "Mensaje masivo enviado con exito!!");
      } else if (errors.length && success.length) {
        alert_msg("warning", "Ocurrio un error al enviar algunos mensajes!!");
      } else {
        alert_msg("error", "No se pudo enviar los mensajes!!");
      }
      // validar errores
      loading.style.display = "none";
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  submitWsp();
  changeMessage();
  changeFile();
});
