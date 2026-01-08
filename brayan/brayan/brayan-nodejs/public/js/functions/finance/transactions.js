// Transacciones - Funciones JavaScript
$(document).ready(function () {
    // Inicializar fecha por defecto
    $('input[name="transaction_date"]').val(new Date().toISOString().split('T')[0]);

    // Inicializar DataTable
    var table = $('#tableTransactions').DataTable({
        ajax: {
            url: '/finance/transactions/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: 'transaction_date',
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
            {
                data: null,
                render: function (data) {
                    return data.client_names ? data.client_names + ' ' + data.client_surnames : '-';
                }
            },
            {
                data: 'payment_method',
                render: function (data) {
                    const methods = {
                        'cash': 'Efectivo',
                        'transfer': 'Transferencia',
                        'card': 'Tarjeta',
                        'check': 'Cheque',
                        'other': 'Otro'
                    };
                    return methods[data] || data;
                }
            },
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
        table.ajax.url('/finance/transactions/list?' + params).load();
    });

    // Guardar transacción
    $('#formTransaction').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.post('/finance/transactions/create', formData, function (response) {
            if (response.success) {
                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                $('#modalTransaction').modal('hide');
                $('#formTransaction')[0].reset();
                $('input[name="transaction_date"]').val(new Date().toISOString().split('T')[0]);
                table.ajax.reload();
            } else {
                $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-error' });
            }
        });
    });
});
