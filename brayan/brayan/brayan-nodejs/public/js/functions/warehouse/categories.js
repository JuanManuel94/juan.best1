// Categorías - Funciones JavaScript
$(document).ready(function () {
    var table = $('#tableCategories').DataTable({
        ajax: { url: '/warehouse/categories/list', dataSrc: 'data' },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'description', defaultContent: '-' },
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

    $('#formCategory').submit(function (e) {
        e.preventDefault();
        var id = $('#categoryId').val();
        var url = id ? '/warehouse/categories/update' : '/warehouse/categories/create';

        $.post(url, $(this).serialize(), function (response) {
            if (response.success) {
                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                $('#modalCategory').modal('hide');
                $('#formCategory')[0].reset();
                $('#categoryId').val('');
                table.ajax.reload();
            } else {
                $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-error' });
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $.get('/warehouse/categories/select/' + id, function (response) {
            if (response.success) {
                $('#categoryId').val(response.category.id);
                $('input[name="name"]').val(response.category.name);
                $('textarea[name="description"]').val(response.category.description);
                $('.modal-title').text('Editar Categoría');
                $('#modalCategory').modal('show');
            }
        });
    });

    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        $.confirm({
            title: 'Eliminar Categoría',
            content: '¿Está seguro?',
            type: 'red',
            buttons: {
                eliminar: {
                    btnClass: 'btn-danger',
                    action: function () {
                        $.post('/warehouse/categories/delete', { id: id }, function (response) {
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

    $('#modalCategory').on('hidden.bs.modal', function () {
        $('#formCategory')[0].reset();
        $('#categoryId').val('');
        $('.modal-title').text('Nueva Categoría');
    });
});
