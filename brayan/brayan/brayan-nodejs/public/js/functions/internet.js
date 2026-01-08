// Internet Services functionality
let tableInternet;

$(document).ready(function () {
  // Hide loading
  $('#loading').fadeOut();

  // Initialize App
  App.init();

  // Initialize DataTable
  tableInternet = $('#tableInternet').DataTable({
    ajax: {
      url: base_url + '/services/internet/list',
      dataSrc: 'data'
    },
    columns: [
      { data: 'id' },
      {
        data: 'name',
        render: function (data) {
          return `<b>${data}</b>`;
        }
      },
      {
        data: 'download_speed',
        render: function (data) {
          return `${data} kbps`;
        }
      },
      {
        data: 'upload_speed',
        render: function (data) {
          return `${data} kbps`;
        }
      },
      {
        data: 'price',
        render: function (data) {
          return `$ ${parseFloat(data).toFixed(2)}`;
        }
      },
      {
        data: 'active_clients',
        className: 'text-center',
        render: function (data) {
          return `<span class="badge badge-success">${data}</span>`;
        }
      },
      {
        data: 'suspended_clients',
        className: 'text-center',
        render: function (data) {
          return `<span class="badge badge-warning">${data}</span>`;
        }
      },
      {
        data: null,
        render: function (data) {
          return `
                        <div class="btn-group">
                            <button class="btn btn-xs btn-default" title="Editar"><i class="fa fa-pencil-alt text-primary"></i></button>
                            <button class="btn btn-xs btn-default" title="Eliminar"><i class="fa fa-trash text-danger"></i></button>
                        </div>
                    `;
        }
      }
    ],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
    },
    order: [[0, 'asc']]
  });
});
