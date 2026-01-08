// Routers functionality
let tableRouters;

$(document).ready(function () {
    // Hide loading
    $('#loading').fadeOut();

    // Initialize App
    App.init();

    // Initialize DataTable
    tableRouters = $('#tableRouters').DataTable({
        ajax: {
            url: base_url + '/routers/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: 'name',
                render: function (data, type, row) {
                    return `<b>${data}</b><br><small class="text-muted">${row.secondary_name}</small>`;
                }
            },
            { data: 'ip' },
            { data: 'model' },
            { data: 'version' },
            {
                data: 'clients',
                className: 'text-center',
                render: function (data) {
                    return `<span class="badge badge-primary">${data}</span>`;
                }
            },
            {
                data: 'status',
                className: 'text-center',
                render: function (data) {
                    if (data == 1) return '<span class="badge badge-success">CONECTADO</span>';
                    return '<span class="badge badge-danger">ERROR</span>';
                }
            },
            {
                data: null,
                render: function (data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-xs btn-default" title="Editar"><i class="fa fa-pencil-alt text-primary"></i></button>
                            <button class="btn btn-xs btn-default" title="Eliminar"><i class="fa fa-trash text-danger"></i></button>
                            <button class="btn btn-xs btn-default" title="Reparar"><i class="fa fa-wrench text-warning"></i></button>
                            <button class="btn btn-xs btn-default" title="Más opciones"><i class="fa fa-cog"></i></button>
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
