// Customers functionality
let tableCustomers;

$(document).ready(function () {
    // Hide loading
    $('#loading').fadeOut();

    // Initialize App
    App.init();

    // Initialize DataTable
    tableCustomers = $('#tableCustomers').DataTable({
        ajax: {
            url: base_url + '/customers/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            {
                data: null,
                render: function (data) {
                    return `<b>${data.names} ${data.surnames}</b>`;
                }
            },
            { data: 'address' },
            {
                data: null,
                defaultContent: '<span class="text-muted">--</span>' // IP Placeholder
            },
            {
                data: null,
                defaultContent: '<span class="text-muted">--</span>' // MAC Placeholder
            },
            {
                data: 'contract_date',
                render: function (data) {
                    return data ? new Date(data).getDate() : '--';
                }
            },
            {
                data: null,
                render: function (data) {
                    return '<span class="text-danger">0.00</span>'; // Balance Placeholder
                }
            },
            {
                data: null,
                render: function (data) {
                    // Mikrosystem style icons
                    return `
                        <div class="btn-group">
                            <button class="btn btn-xs btn-default" title="Editar" onclick="editCustomer(${data.id})"><i class="fa fa-pencil-alt"></i></button>
                            <button class="btn btn-xs btn-default" title="Cortar servicio"><i class="fa fa-power-off text-danger"></i></button>
                            <button class="btn btn-xs btn-default" title="Eliminar" onclick="deleteCustomer(${data.id})"><i class="fa fa-trash"></i></button>
                            <button class="btn btn-xs btn-default" title="Más opciones"><i class="fa fa-cog"></i></button>
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

    // Create form submit
    $('#formCreate').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: base_url + '/customers/create',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('#loading').fadeIn();
            },
            success: function (response) {
                $('#loading').fadeOut();
                showAlert(response.status, response.msg);
                if (response.status === 'success') {
                    $('#modalCreate').modal('hide');
                    $('#formCreate')[0].reset();
                    tableCustomers.ajax.reload();
                }
            },
            error: function () {
                $('#loading').fadeOut();
                showAlert('error', 'Error de conexión');
            }
        });
    });

    // Edit form submit
    $('#formEdit').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: base_url + '/customers/modify',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('#loading').fadeIn();
            },
            success: function (response) {
                $('#loading').fadeOut();
                showAlert(response.status, response.msg);
                if (response.status === 'success') {
                    $('#modalEdit').modal('hide');
                    tableCustomers.ajax.reload();
                }
            },
            error: function () {
                $('#loading').fadeOut();
                showAlert('error', 'Error de conexión');
            }
        });
    });
});

// Edit customer
function editCustomer(id) {
    $.ajax({
        url: base_url + '/customers/select/' + id,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            $('#loading').fadeIn();
        },
        success: function (data) {
            $('#loading').fadeOut();
            if (data.id) {
                $('#editId').val(data.id);
                $('#editNames').val(data.names);
                $('#editSurnames').val(data.surnames);
                $('#editDocumentid').val(data.documentid);
                $('#editDocument').val(data.document);
                $('#editMobile').val(data.mobile);
                $('#editEmail').val(data.email);
                $('#editZoneid').val(data.zoneid);
                $('#editAddress').val(data.address);
                $('#editContractDate').val(data.contract_date);
                $('#editContractStatus').val(data.contract_status);
                $('#modalEdit').modal('show');
            }
        },
        error: function () {
            $('#loading').fadeOut();
            showAlert('error', 'Error de conexión');
        }
    });
}

// Delete customer
function deleteCustomer(id) {
    $.confirm({
        title: 'Confirmar eliminación',
        content: '¿Está seguro de eliminar este cliente?',
        type: 'red',
        buttons: {
            confirmar: {
                text: 'Eliminar',
                btnClass: 'btn-red',
                action: function () {
                    $.ajax({
                        url: base_url + '/customers/remove',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'json',
                        beforeSend: function () {
                            $('#loading').fadeIn();
                        },
                        success: function (response) {
                            $('#loading').fadeOut();
                            showAlert(response.status, response.msg);
                            tableCustomers.ajax.reload();
                        },
                        error: function () {
                            $('#loading').fadeOut();
                            showAlert('error', 'Error de conexión');
                        }
                    });
                }
            },
            cancelar: {
                text: 'Cancelar'
            }
        }
    });
}

// Alert function
function showAlert(type, message) {
    $.gritter.add({
        title: type === 'success' ? '¡Éxito!' : type === 'warning' ? 'Advertencia' : 'Error',
        text: message,
        class_name: 'gritter-' + type,
        time: 3000
    });
}
