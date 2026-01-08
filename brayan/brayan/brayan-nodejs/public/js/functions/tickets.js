// Tickets functionality
let tableTickets;

$(document).ready(function () {
  // Hide loading
  $('#loading').fadeOut();

  // Initialize App
  App.init();

  // Initialize DataTable
  tableTickets = $('#tableTickets').DataTable({
    ajax: {
      url: base_url + '/tickets/list',
      dataSrc: 'data'
    },
    columns: [
      { data: 'id' },
      {
        data: 'id',
        render: function (data) {
          return `<span class="text-primary font-weight-bold">#${data}</span>`;
        }
      },
      { data: 'department' },
      { data: 'sender' },
      {
        data: 'subject',
        render: function (data) {
          return `<b>${data}</b>`;
        }
      },
      { data: 'technician' },
      { data: 'date' },
      {
        data: 'status',
        className: 'text-center',
        render: function (data) {
          if (data == 1) return '<span class="badge badge-warning">ABIERTO</span>';
          if (data == 2) return '<span class="badge badge-success">RESPONDIDO</span>';
          return '<span class="badge badge-danger">CERRADO</span>';
        }
      },
      {
        data: null,
        render: function (data) {
          return `
                        <div class="btn-group">
                            <button class="btn btn-xs btn-default" title="Ver Ticket"><i class="fa fa-eye text-primary"></i></button>
                            <button class="btn btn-xs btn-default" title="Eliminar"><i class="fa fa-trash text-danger"></i></button>
                        </div>
                    `;
        }
      }
    ],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
    },
    order: [[0, 'desc']]
  });
});
