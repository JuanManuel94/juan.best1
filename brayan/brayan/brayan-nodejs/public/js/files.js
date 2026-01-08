function reloadList() {
  return $(`#list-files`).DataTable().ajax.reload();
}

function showFiles(tabla, id) {
  $("#modal-action-files").modal("show");
  $("#f_object_id").val(id);
  $("#f_tabla").val(tabla);

  return $(`#list-files`).DataTable({
    ajax: {
      url: `${base_url}/archivos/list_records?tabla=${tabla}&object_id=${id}`,
      dataSrc: "",
    },
    deferRender: true,
    idDataTables: "1",
    columns: [
      { data: "id", className: "text-center" },
      {
        data: "nombre",
        render(data, type, full) {
          const a = document.createElement("a");
          a.href = `${base_url}/archivos/download/${full.id}`;
          a.text = data;
          a.className = "text-primary";
          a.target = "_blank";
          return a.outerHTML;
        },
      },
      { data: "size" },
      {
        data: "id",
        render(data) {
          return btnAction(
            "Eliminar",
            "red",
            "far fa-trash-alt",
            `deleteFile(${data})`
          );
        },
      },
    ],
  });
}

const uploadFile = () => {
  const id = $("#f_object_id").val();
  const tabla = $("#f_tabla").val();
  const input = document.getElementById("upload-file");
  const files = input.files;
  if (!files.length) return;
  loading.style.display = "flex";
  const file = files[0];
  const formData = new FormData();
  formData.set("archivo", file);
  formData.set("object_id", id);
  formData.set("tabla", tabla);
  $("#upload-file").val(null);
  axios
    .post(`${base_url}/archivos/upload`, formData)
    .then(({ data }) => {
      if (!data.success) throw new Error(data.message);
      alert_msg("success", data.message);
      reloadList();
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};

const deleteFile = (id) => {
  loading.style.display = "flex";
  axios
    .post(`${base_url}/archivos/remove_record/${id}`)
    .then(({ data }) => {
      if (!data.success) throw new Error(data.message);
      alert_msg("success", data.message);
      reloadList();
    })
    .catch((err) => alert_msg("error", err.message))
    .finally(() => (loading.style.display = "none"));
};
