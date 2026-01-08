let timeout;
let alertVisible = false;

function resetTimer() {
  clearTimeout(timeout);
  timeout = setTimeout(showLogoutAlert, 10 * 60 * 1000); // 2 minutos
}

function showLogoutAlert() {
  Swal.fire({
    title: "Sesión cerrada",
    text: "Tu sesión ha sido cerrada por inactividad.",
    icon: "warning",
    background: "#000",
    color: "#FF0000",
    confirmButtonColor: "#FF0000",
    showConfirmButton: false,
  }).then(() => {
    window.location.href = `${base_url}/logout`;
  });

  alertVisible = true;
}

function forceLogout() {
  if (alertVisible) {
    window.location.href = `${base_url}/logout`;
  }
}

// Eventos para detectar actividad del usuario
document.addEventListener("mousemove", forceLogout);
document.addEventListener("keydown", forceLogout);
document.addEventListener("scroll", forceLogout);
document.addEventListener("click", forceLogout);
document.addEventListener("touchstart", forceLogout);

// Iniciar el temporizador
resetTimer();
