// Users functionality
let tableUsers;

$(document).ready(function () {
  // Hide loading
  $('#loading').fadeOut();

  // Initialize App
  App.init();

  // Initialize DataTable
  tableUsers = $('#tableUsers').DataTable({
    ajax: {
      url: base_url + '/users/list',
      dataSrc: 'data'
    },
    columns: [
      { data: 'id' },
      {
        data: null,
        render: function (data) {
          return data.names + ' ' + data.surnames;
        }
      },
      { data: 'username' },
      { data: 'profile' },
      { data: 'email' },
      {
        data: 'state',
        render: function (data) {
          if (data == 1) return '<span class="badge badge-success">Activo</span>';
          return '<span class="badge badge-danger">Inactivo</span>';
        }
      },
      {
        data: null,
        render: function (data) {
          return `
                        <div class="btn-actions">
                            <button class="btn btn-info btn-sm" onclick="editUser(${data.id})">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(${data.id})">
                                <i class="fa fa-trash"></i>
                            </button>
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
      url: base_url + '/users/create',
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
          tableUsers.ajax.reload();
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
      url: base_url + '/users/modify',
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
          tableUsers.ajax.reload();
        }
      },
      error: function () {
        $('#loading').fadeOut();
        showAlert('error', 'Error de conexión');
      }
    });
  });
});

// Edit user
function editUser(id) {
  $.ajax({
    url: base_url + '/users/select/' + id,
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
        $('#editProfileid').val(data.profileid);
        $('#editUsername').val(data.username);
        $('#editPassword').val('');
        $('#editState').val(data.state);
        $('#modalEdit').modal('show');
      }
    },
    error: function () {
      $('#loading').fadeOut();
      showAlert('error', 'Error de conexión');
    }
  });
}

// Delete user
function deleteUser(id) {
  $.confirm({
    title: 'Confirmar eliminación',
    content: '¿Está seguro de eliminar este usuario?',
    type: 'red',
    buttons: {
      confirmar: {
        text: 'Eliminar',
        btnClass: 'btn-red',
        action: function () {
          $.ajax({
            url: base_url + '/users/remove',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            beforeSend: function () {
              $('#loading').fadeIn();
            },
            success: function (response) {
              $('#loading').fadeOut();
              showAlert(response.status, response.msg);
              tableUsers.ajax.reload();
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
