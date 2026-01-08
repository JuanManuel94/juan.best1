function datatableHelper(id, title, configTable = {}) {
  table_configuration(`#${id}`, title);

  const render = () => {
    $.fn.dataTable.ext.errMode = "none";
    return $(`#${id}`)
      .DataTable({
        ...configTable,
        initComplete: function () {
          $(`#${id}_wrapper div.container-options`).append(
            $(`#${id}-btns-tools`).contents()
          );
        },
      })
      .on("processing.dt", function (e, settings, processing) {
        if (processing) {
          loaderin(".panel-runway");
        } else {
          loaderout(".panel-runway");
        }
      });
  };

  const refresh = () => {
    return $(`#${id}`).DataTable().ajax.reload();
  };

  const api = () => $(`#${id}`).DataTable();

  return { render, refresh, api };
}
