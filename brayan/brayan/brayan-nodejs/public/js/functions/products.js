// Products functionality
let tableProducts;

$(document).ready(function () {
    // Hide loading
    $('#loading').fadeOut();

    // Initialize App
    App.init();

    // Initialize DataTable
    tableProducts = $('#tableProducts').DataTable({
        ajax: {
            url: base_url + '/warehouse/products/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: 'product',
                render: function (data, type, row) {
                    return `<b>${data}</b><br><small class="text-muted">${row.code}</small>`;
                }
            },
            { data: 'provider' },
            { data: 'serial' },
            { data: 'mac' },
            {
                data: 'cost',
                render: function (data) {
                    return `$ ${parseFloat(data).toFixed(2)}`;
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data) {
                    if (data == 1) return '<span class="badge badge-success">DISPONIBLE</span>';
                    if (data == 2) return '<span class="badge badge-warning">ASIGNADO</span>';
                    return '<span class="badge badge-danger">BAJA</span>';
                }
            },
            { data: 'client' },
            { data: 'entry_date' },
            {
                data: 'exit_date',
                render: function (data) {
                    return data ? data : '--';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        order: [[0, 'desc']]
    });
});
