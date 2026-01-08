// Proveedores - Funciones JavaScript
$(document).ready(function () {
    var table = $('#tableSuppliers').DataTable({
        ajax: { url: '/warehouse/suppliers/list', dataSrc: 'data' },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'ruc', defaultContent: '-' },
            { data: 'contact_name', defaultContent: '-' },
            { data: 'phone', defaultContent: '-' },
            { data: 'email', defaultContent: '-' },
            {
                data: null,
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${data.id}"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}"><i class="fa fa-trash"></i></button>
                    `;
                }
            }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json' }
    });

    $('#formSupplier').submit(function (e) {
        e.preventDefault();
        var id = $('#supplierId').val();
        var url = id ? '/warehouse/suppliers/update' : '/warehouse/suppliers/create';

        $.post(url, $(this).serialize(), function (response) {
            if (response.success) {
                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                $('#modalSupplier').modal('hide');
                $('#formSupplier')[0].reset();
                $('#supplierId').val('');
                table.ajax.reload();
            } else {
                $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-error' });
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $.get('/warehouse/suppliers/select/' + id, function (response) {
            if (response.success) {
                var s = response.supplier;
                $('#supplierId').val(s.id);
                $('input[name="name"]').val(s.name);
                $('input[name="trade_name"]').val(s.trade_name);
                $('input[name="ruc"]').val(s.ruc);
                $('input[name="contact_name"]').val(s.contact_name);
                $('input[name="phone"]').val(s.phone);
                $('input[name="mobile"]').val(s.mobile);
                $('input[name="email"]').val(s.email);
                $('input[name="website"]').val(s.website);
                $('input[name="address"]').val(s.address);
                $('input[name="city"]').val(s.city);
                $('input[name="payment_terms"]').val(s.payment_terms);
                $('input[name="notes"]').val(s.notes);
                $('.modal-title').text('Editar Proveedor');
                $('#modalSupplier').modal('show');
            }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        $.confirm({
            title: 'Eliminar Proveedor',
            content: '¿Está seguro?',
            type: 'red',
            buttons: {
                eliminar: {
                    btnClass: 'btn-danger',
                    action: function () {
                        $.post('/warehouse/suppliers/delete', { id: id }, function (response) {
                            if (response.success) {
                                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                                table.ajax.reload();
                            }
                        });
                    }
                },
                cancelar: function () { }
            }
        });
    });

    $('#modalSupplier').on('hidden.bs.modal', function () {
        $('#formSupplier')[0].reset();
        $('#supplierId').val('');
        $('.modal-title').text('Nuevo Proveedor');
    });
});
