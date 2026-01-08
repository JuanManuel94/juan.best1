// Bills functionality
let tableBills;

$(document).ready(function () {
  // Hide loading
  $('#loading').fadeOut();

  // Initialize App
  App.init();

  // Initialize DataTable
  tableBills = $('#tableBills').DataTable({
    ajax: {
      url: base_url + '/bills/list',
      dataSrc: 'data'
    },
    columns: [
      {
        data: null,
        className: 'details-control text-center',
        orderable: false,
        defaultContent: '<i class="fa fa-plus-circle text-primary" style="cursor:pointer;"></i>'
      },
      {
        data: 'id',
        render: function (data) {
          return `<b>${String(data).padStart(6, '0')}</b>`;
        }
      },
      { data: 'legal_number' },
      {
        data: 'type',
        render: function (data) {
          return `<span class="badge badge-default">${data}</span>`;
        }
      },
      { data: 'client' },
      { data: 'emission_date' },
      { data: 'due_date' },
      {
        data: 'total',
        render: function (data) {
          return parseFloat(data).toFixed(2);
        }
      },
      {
        data: 'balance',
        render: function (data) {
          return parseFloat(data).toFixed(2);
        }
      },
      { data: 'payment_method' },
      {
        data: 'status',
        render: function (data) {
          if (data == 1) return '<span class="badge badge-success">PAGADO</span>';
          return '<span class="badge badge-danger">PENDIENTE</span>';
        }
      },
      {
        data: null,
        render: function (data) {
          return `
                        <div class="btn-group">
                            <button class="btn btn-xs btn-default" title="Ver PDF"><i class="fa fa-file-pdf text-primary"></i></button>
                            <button class="btn btn-xs btn-default" title="Eliminar"><i class="fa fa-times text-danger"></i></button>
                            <button class="btn btn-xs btn-default" title="Editar"><i class="fa fa-pencil-alt text-primary"></i></button>
                        </div>
                    `;
        }
      }
    ],
    language: {
      url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
    },
    order: [[1, 'desc']]
  });

  // Expand details listener
  $('#tableBills tbody').on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = tableBills.row(tr);
    var icon = $(this).find('i');

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass('shown');
      icon.removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-primary');
    } else {
      // Open this row
      // Format details row here if needed
      row.child('<div class="p-3 bg-light">Detalles de la factura...</div>').show();
      tr.addClass('shown');
      icon.removeClass('fa-plus-circle text-primary').addClass('fa-minus-circle text-danger');
    }
  });

});
