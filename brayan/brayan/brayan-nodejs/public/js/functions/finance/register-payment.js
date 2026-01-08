// Registrar Pago - Funciones JavaScript
$(document).ready(function () {
    // Inicializar Select2 para búsqueda de clientes
    $('#selectClient').select2({
        ajax: {
            url: '/customers/list',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.names + ' ' + item.surnames + ' - ' + (item.document || 'Sin doc'),
                            client: item
                        };
                    })
                };
            },
            cache: true
        },
        placeholder: 'Buscar cliente...',
        minimumInputLength: 2
    });

    // Al seleccionar cliente
    $('#selectClient').on('select2:select', function (e) {
        var client = e.params.data.client;

        // Mostrar info del cliente
        $('#clientInfo').removeClass('d-none');
        $('#clientName').text(client.names + ' ' + client.surnames);
        $('#clientDoc').text(client.document || 'No registrado');
        $('#clientPhone').text(client.mobile || 'No registrado');
        $('#clientEmail').text(client.email || 'No registrado');

        // Guardar ID
        $('#paymentClientId').val(client.id);

        // Cargar facturas pendientes
        loadClientBills(client.id);
    });

    // Cargar facturas del cliente
    function loadClientBills(clientId) {
        $.get('/finance/payment/bills/' + clientId, function (response) {
            if (response.success && response.bills.length > 0) {
                $('#noBills').addClass('d-none');
                $('#billsList').removeClass('d-none');

                var html = '';
                response.bills.forEach(function (bill) {
                    html += `<tr>
                        <td>#${bill.id}</td>
                        <td>${new Date(bill.due_date).toLocaleDateString('es-PE')}</td>
                        <td>S/ ${parseFloat(bill.amount).toFixed(2)}</td>
                        <td><strong>S/ ${parseFloat(bill.remaining_amount).toFixed(2)}</strong></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary btn-select-bill" 
                                data-id="${bill.id}" data-amount="${bill.remaining_amount}">
                                <i class="fa fa-check"></i>
                            </button>
                        </td>
                    </tr>`;
                });
                $('#billsTableBody').html(html);
            } else {
                $('#noBills').removeClass('d-none').html(`
                    <i class="fa fa-check-circle fa-2x mb-2 text-success"></i>
                    <p>Este cliente no tiene deudas pendientes</p>
                `);
                $('#billsList').addClass('d-none');
            }
        });
    }

    // Seleccionar factura
    $(document).on('click', '.btn-select-bill', function () {
        $('.btn-select-bill').removeClass('btn-success').addClass('btn-primary');
        $(this).removeClass('btn-primary').addClass('btn-success');

        $('#paymentBillId').val($(this).data('id'));
        $('#paymentAmount').val($(this).data('amount'));
    });

    // Enviar formulario de pago
    $('#formPayment').submit(function (e) {
        e.preventDefault();

        if (!$('#paymentClientId').val()) {
            $.gritter.add({ title: 'Error', text: 'Debe seleccionar un cliente', class_name: 'gritter-error' });
            return;
        }

        if (!$('#paymentAmount').val() || parseFloat($('#paymentAmount').val()) <= 0) {
            $.gritter.add({ title: 'Error', text: 'Ingrese un monto válido', class_name: 'gritter-error' });
            return;
        }

        $.confirm({
            title: 'Confirmar Pago',
            content: '¿Registrar pago de S/ ' + parseFloat($('#paymentAmount').val()).toFixed(2) + '?',
            type: 'green',
            buttons: {
                confirmar: {
                    text: 'Sí, registrar',
                    btnClass: 'btn-success',
                    action: function () {
                        var formData = $('#formPayment').serialize();
                        $.post('/finance/payment/register', formData, function (response) {
                            if (response.success) {
                                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                                // Limpiar formulario
                                $('#formPayment')[0].reset();
                                $('#selectClient').val(null).trigger('change');
                                $('#clientInfo').addClass('d-none');
                                $('#noBills').removeClass('d-none').html(`
                                    <i class="fa fa-info-circle fa-2x mb-2"></i>
                                    <p>Seleccione un cliente para ver sus deudas</p>
                                `);
                                $('#billsList').addClass('d-none');
                            } else {
                                $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-error' });
                            }
                        });
                    }
                },
                cancelar: function () { }
            }
        });
    });
});
