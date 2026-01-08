function getRoot() {
  const key = location.pathname.split("/").pop();
  const allowed = {
    agregarPagos: "typepay",
    pendings: "listTypePay",
    bills: "listTypePay",
  };
  return allowed[key] || "listTypePay";
}

function statusMethodPayment(state = false) {
  console.log(`statusMethodPayment => ${state}`);
  $("#content-method-payment").prop(
    "style",
    `display: ${state ? "block" : "none"}`
  );
  $("#content-code-payment").prop(
    "style",
    `display: ${state ? "block" : "none"}`
  );
}

function list_runway() {
  axios
    .get(`${base_url}/runway/list_runway`)
    .then(({ data }) => {
      document.querySelector(`#${getRoot()}`).innerHTML = data;
      $(`#${getRoot()}`).select2({ width: "100%" });
    })
    .finally(() => statusMethodPayment(false));
}

function changeFormPayment() {
  $(`#${getRoot()}`).on("change", () => {
    statusMethodPayment(false);
    const value = $(`#${getRoot()}`).val();
    const component = document.getElementById("method_payment_id");
    axios
      .get(`${base_url}/runway/list_method_payments?form_payment_id=${value}`)
      .then(({ data }) => {
        if (!data.length) throw new Error("Sin opciones");
        component.innerHTML = "";
        data?.forEach((item) => {
          const opt = document.createElement("option");
          opt.value = item.id;
          opt.text = item.name;
          component.appendChild(opt);
        });
        // mostrar data
        statusMethodPayment(true);
        localStorage.setItem("methodPaymentForm", JSON.stringify(data));
        checkMethodPayment();
      })
      .catch(() => {
        statusMethodPayment(false);
        $("#method_payment_id").val(undefined);
        $("#code_payment").val(undefined);
        localStorage.removeItem("methodPaymentForm");
        component.innerHTML = "";
      });
  });
}

function changeMethodPayment() {
  $("#method_payment_id").on("change", () => {
    checkMethodPayment();
  });
}

function checkMethodPayment() {
  try {
    const value = $("#method_payment_id").val();
    const raw = localStorage.getItem("methodPaymentForm");
    const data = JSON.parse(raw);
    const item = data.find((i) => i.id.toString() === value);
    if (!item) throw new Error("No se encontró en metodo");
    if (!item.is_code) throw new Error("El código no es obligatorio");
    const label = item.display_code || "código";
    $("#content-code-payment").prop("style", "display: block");
    document.getElementById("display_payment").innerText = label;
  } catch (error) {
    $("#code_payment").val(undefined);
    $("#content-code-payment").prop("style", "display: none");
  }
}
