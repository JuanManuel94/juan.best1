// Tareas - Funciones JavaScript
$(document).ready(function () {
    // Inicializar Select2 para clientes
    $('.select2-client').select2({
        dropdownParent: $('#modalTask'),
        ajax: {
            url: '/customers/list',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { search: params.term }; },
            processResults: function (data) {
                return {
                    results: data.data.map(function (item) {
                        return { id: item.id, text: item.names + ' ' + item.surnames };
                    })
                };
            }
        },
        placeholder: 'Buscar cliente...',
        allowClear: true,
        minimumInputLength: 2
    });

    // Estado del usuario actual
    var currentUserId = window.currentUserId || null;
    var filterMode = 'all';

    // DataTable
    var table = $('#tableTasks').DataTable({
        ajax: { url: '/tasks/list', dataSrc: 'data' },
        columns: [
            {
                data: 'title',
                render: function (data, type, row) {
                    var priorityColors = { urgent: 'danger', high: 'warning', medium: 'info', low: 'secondary' };
                    var priorityIcon = row.priority === 'urgent' ? '<i class="fa fa-exclamation-triangle text-danger mr-1"></i>' : '';
                    return priorityIcon + '<strong>' + data + '</strong>' +
                        (row.task_type ? '<br><small class="text-muted">' + getTaskTypeName(row.task_type) + '</small>' : '');
                }
            },
            {
                data: 'status',
                render: function (data) {
                    var labels = { pending: 'PENDIENTE', in_progress: 'EN PROCESO', completed: 'COMPLETADO', cancelled: 'CANCELADO' };
                    return '<span class="badge badge-status-' + data + '">' + (labels[data] || data) + '</span>';
                }
            },
            {
                data: 'due_date',
                render: function (data, type, row) {
                    if (!data) return '-';
                    var date = new Date(data);
                    var today = new Date();
                    var isOverdue = date < today && row.status !== 'completed';
                    var formattedDate = date.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    var formattedTime = row.due_time ? ' ' + row.due_time : '';
                    return '<span class="' + (isOverdue ? 'text-danger font-weight-bold' : '') + '">' +
                        formattedDate + formattedTime + '</span>';
                }
            },
            {
                data: 'duration_hours',
                render: function (data, type, row) {
                    var hours = data || 0;
                    var mins = row.duration_minutes || 0;
                    if (hours === 0 && mins === 0) return '<span class="text-muted">-</span>';
                    return hours + 'h ' + mins + 'm';
                }
            },
            {
                data: null,
                render: function (data) {
                    if (data.assigned_names) {
                        return '<i class="fa fa-user-circle text-primary mr-1"></i>' + data.assigned_names + ' ' + data.assigned_surnames;
                    }
                    return '<span class="text-muted"><i class="fa fa-user-slash mr-1"></i>Sin asignar</span>';
                }
            },
            {
                data: null,
                render: function (data) {
                    if (data.client_names) {
                        return '<i class="fa fa-user text-success mr-1"></i>' + data.client_names + ' ' + data.client_surnames;
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info btn-edit" data-id="${data.id}" title="Editar">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-success btn-complete" data-id="${data.id}" ${data.status === 'completed' ? 'disabled' : ''} title="Completar">
                                <i class="fa fa-check"></i>
                            </button>
                            <button class="btn btn-danger btn-delete" data-id="${data.id}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[2, 'asc']],
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json',
            lengthMenu: 'Mostrar _MENU_ registros'
        },
        dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-5"i><"col-sm-7"p>>'
    });

    function getTaskTypeName(type) {
        var types = {
            installation: 'Instalación',
            support: 'Soporte',
            maintenance: 'Mantenimiento',
            collection: 'Cobranza',
            other: 'Otro'
        };
        return types[type] || type;
    }

    // Toggle buttons
    $('#btnAllTasks, #btnMyTasks, #btnTimeline').click(function () {
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');

        if ($(this).attr('id') === 'btnMyTasks') {
            filterMode = 'my';
        } else if ($(this).attr('id') === 'btnTimeline') {
            filterMode = 'timeline';
            // Could show a calendar view
        } else {
            filterMode = 'all';
        }
        applyFilters();
    });

    // Entries per page
    $('#entriesPerPage').change(function () {
        table.page.len($(this).val()).draw();
    });

    // Search input
    $('#searchInput').on('keyup', function () {
        table.search($(this).val()).draw();
    });

    // Filter button
    $('#btnFilter').click(function () {
        applyFilters();
    });

    function applyFilters() {
        var params = {
            status: $('#filterStatus').val(),
            priority: $('#filterPriority').val(),
            task_type: $('#filterType').val(),
            date: $('#filterDate').val()
        };

        if (filterMode === 'my' && currentUserId) {
            params.assigned_to = currentUserId;
        }

        table.ajax.url('/tasks/list?' + $.param(params)).load();
    }

    // Guardar tarea
    $('#formTask').submit(function (e) {
        e.preventDefault();
        var id = $('#taskId').val();
        var url = id ? '/tasks/update' : '/tasks/create';

        $.post(url, $(this).serialize(), function (response) {
            if (response.success) {
                $.gritter.add({ title: 'Éxito', text: response.message, class_name: 'gritter-success' });
                $('#modalTask').modal('hide');
                $('#formTask')[0].reset();
                $('#taskId').val('');
                table.ajax.reload();
            } else {
                $.gritter.add({ title: 'Error', text: response.message, class_name: 'gritter-error' });
            }
        });
    });

    // Editar tarea
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');
        $.get('/tasks/select/' + id, function (response) {
            if (response.success) {
                var task = response.task;
                $('#taskId').val(task.id);
                $('input[name="title"]').val(task.title);
                $('textarea[name="description"]').val(task.description);
                $('select[name="task_type"]').val(task.task_type);
                $('select[name="status"]').val(task.status);
                $('select[name="assigned_to"]').val(task.assigned_to);
                $('select[name="priority"]').val(task.priority);
                $('input[name="address"]').val(task.address);
                $('input[name="latitude"]').val(task.latitude);
                $('input[name="longitude"]').val(task.longitude);
                $('select[name="duration_hours"]').val(task.duration_hours || 1);
                $('select[name="duration_minutes"]').val(task.duration_minutes || 0);

                if (task.due_date) {
                    var dt = new Date(task.due_date);
                    var formatted = dt.toISOString().slice(0, 16);
                    $('input[name="due_date"]').val(formatted);
                }

                $('.modal-title').html('<i class="fa fa-edit mr-2"></i>Editar Tarea');
                $('#modalTask').modal('show');
            }
        });
    });

    // Completar tarea
    $(document).on('click', '.btn-complete', function () {
        var id = $(this).data('id');
        $.confirm({
            title: '<i class="fa fa-check-circle text-success"></i> Completar Tarea',
            content: '¿Marcar esta tarea como completada?',
            type: 'green',
            buttons: {
                confirmar: {
                    btnClass: 'btn-success',
                    action: function () {
                        $.post('/tasks/status', { id: id, status: 'completed' }, function (response) {
                            if (response.success) {
                                $.gritter.add({ title: 'Éxito', text: 'Tarea completada', class_name: 'gritter-success' });
                                table.ajax.reload();
                            }
                        });
                    }
                },
                cancelar: function () { }
            }
        });
    });

    // Eliminar tarea
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        $.confirm({
            title: '<i class="fa fa-trash text-danger"></i> Eliminar Tarea',
            content: '¿Está seguro de eliminar esta tarea?',
            type: 'red',
            buttons: {
                eliminar: {
                    btnClass: 'btn-danger',
                    action: function () {
                        $.post('/tasks/remove', { id: id }, function (response) {
                            if (response.success) {
                                $.gritter.add({ title: 'Éxito', text: 'Tarea eliminada', class_name: 'gritter-success' });
                                table.ajax.reload();
                            }
                        });
                    }
                },
                cancelar: function () { }
            }
        });
    });

    // Reset modal al cerrar
    $('#modalTask').on('hidden.bs.modal', function () {
        $('#formTask')[0].reset();
        $('#taskId').val('');
        $('.modal-title').html('<i class="fa fa-tasks mr-2"></i>Nueva Tarea');
    });

    // Set default date to today
    var today = new Date().toISOString().slice(0, 16);
    $('input[name="due_date"]').val(today);
});
