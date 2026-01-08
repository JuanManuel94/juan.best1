// Otros Movimientos - Funciones JavaScript
$(document).ready(function () {
    // Fecha por defecto
    $('input[name="movement_date"]').val(new Date().toISOString().split('T')[0]);

    // Inicializar DataTable
    var table = $('#tableMovements').DataTable({
        ajax: {
            url: '/finance/other-movements/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: 'movement_date',
                render: function (data) {
                    return new Date(data).toLocaleDateString('es-PE');
                }
            },
            {
                data: 'type',
                render: function (data) {
                    return data === 'income'
                        ? '<span class="badge badge-success">Ingreso</span>'
                        : '<span class="badge badge-danger">Egreso</span>';
                }
            },
            { data: 'category' },
            { data: 'description' },
            { data: 'receipt_number', defaultContent: '-' },
            {
                data: 'amount',
                render: function (data, type, row) {
                    const color = row.type === 'income' ? 'text-success' : 'text-danger';
                    const sign = row.type === 'income' ? '+' : '-';
                    return `<span class="${color}">${sign} S/ ${parseFloat(data).toFixed(2)}</span>`;
                }
            }
        ],
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });

    // Filtrar
    $('#btnFilter').click(function () {
        var params = $.param({
            type: $('#filterType').val(),
            startDate: $('#filterStartDate').val(),
            endDate: $('#filterEndDate').val()
        });
        table.ajax.url('/finance/other-movements/list?' + params).load();
    });

    // Guardar movimiento
    $('#formMovement').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.post('/finance/other-movements/create', formData, function (response) {
            if (response.success) {
                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                $('#modalMovement').modal('hide');
                $('#formMovement')[0].reset();
                $('input[name="movement_date"]').val(new Date().toISOString().split('T')[0]);
                table.ajax.reload();
            } else {
                $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-error' });
            }
        });
    });
});
