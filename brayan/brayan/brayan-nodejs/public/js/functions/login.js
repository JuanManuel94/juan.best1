// Login functionality
$(document).ready(function () {
  // Hide loading
  $('#loading').fadeOut();

  // Initialize particles.js if exists
  if (typeof particlesJS !== 'undefined') {
    particlesJS('particles-js', {
      particles: {
        number: { value: 80 },
        color: { value: '#ffffff' },
        shape: { type: 'circle' },
        opacity: { value: 0.5 },
        size: { value: 3 },
        move: { enable: true, speed: 2 }
      }
    });
  }

  // Show/hide password
  $('.showHidePw').on('click', function () {
    const input = $('#password');
    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      $(this).removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      input.attr('type', 'password');
      $(this).removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  // Form submit
  $('#transactions').on('submit', function (e) {
    e.preventDefault();

    const username = $('#username').val().trim();
    const password = $('#password').val().trim();
    const remember = $('#remember').is(':checked') ? 1 : 0;

    if (!username || !password) {
      showAlert('warning', 'Por favor complete todos los campos');
      return;
    }

    $.ajax({
      url: base_url + '/login/validation',
      type: 'POST',
      data: {
        username: username,
        password: password,
        remember: remember
      },
      dataType: 'json',
      beforeSend: function () {
        $('#loading').fadeIn();
      },
      success: function (response) {
        $('#loading').fadeOut();
        if (response.status === 'success') {
          window.location.href = base_url + '/dashboard';
        } else {
          showAlert(response.status, response.msg);
        }
      },
      error: function () {
        $('#loading').fadeOut();
        showAlert('error', 'Error de conexión');
      }
    });
  });
});

// Modal for password reset
function modal() {
  $('#loginModal').modal('show');
}

// Reset password
function resetPassword() {
  const email = $('#email').val().trim();

  if (!email) {
    showAlert('warning', 'Por favor ingrese su correo electrónico');
    return;
  }

  $.ajax({
    url: base_url + '/login/reset',
    type: 'POST',
    data: { email: email },
    dataType: 'json',
    beforeSend: function () {
      $('#loading').fadeIn();
    },
    success: function (response) {
      $('#loading').fadeOut();
      showAlert(response.status, response.msg);
      if (response.status === 'success') {
        $('#loginModal').modal('hide');
      }
    },
    error: function () {
      $('#loading').fadeOut();
      showAlert('error', 'Error de conexión');
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
