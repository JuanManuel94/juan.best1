var loading = document.querySelector("#loading");
const pwShowHide = document.querySelectorAll(".showHidePw");
const pwFields = document.querySelectorAll("#password");
document.addEventListener(
  "DOMContentLoaded",
  function () {
    if (document.querySelector("#transactions")) {
      let transactions = document.querySelector("#transactions");
      transactions.onsubmit = function (e) {
        e.preventDefault();

        let username = document.querySelector("#username").value;
        let password = document.querySelector("#password").value;
        if (username == "" || password == "") {
          alert_msg(
            "warning",
            "El usuario y contraseña son campos obligatorios."
          );
          return false;
        } else {
          if (username.length < 2) {
            $("#username").focus();
            return false;
          }
          if (password.length < 2) {
            $("#password").focus();
            return false;
          }
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/login/validation";
          var formData = new FormData(transactions);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState != 4) return;
            if (request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                window.location.reload(false);
              } else if (objData.status == "warning") {
                alert_msg("warning", objData.msg);
                document.querySelector("#password").value = "";
              } else {
                alert_msg("error", objData.msg);
                document.querySelector("#password").value = "";
              }
            } else {
              alert_msg(
                "error",
                "Hubo un error, no se pudo realizar el proceso."
              );
            }
            loading.style.display = "none";
            return false;
          };
        }
      };
    }
    if (document.querySelector("#transactions_reset")) {
      var transactions_reset = document.querySelector("#transactions_reset");
      transactions_reset.onsubmit = function (e) {
        e.preventDefault();
        if ($("#transactions_reset").parsley().isValid()) {
          loading.style.display = "flex";
          var request = window.XMLHttpRequest
            ? new XMLHttpRequest()
            : new ActiveXObject("Microsoft.XMLHTTP");
          var ajaxUrl = base_url + "/login/reset";
          var formData = new FormData(transactions_reset);
          request.open("POST", ajaxUrl, true);
          request.send(formData);
          request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
              var objData = JSON.parse(request.responseText);
              if (objData.status == "success") {
                $("#modal-reset").modal("hide");
                transactions_reset.reset();
                alert_msg("success", objData.msg);
              } else if (objData.status == "not_exist") {
                $("#modal-reset").modal("hide");
                transactions_reset.reset();
                alert_msg("info", objData.msg);
              } else {
                alert_msg("error", objData.msg);
              }
            }
            loading.style.display = "none";
            return false;
          };
        }
      };
    }
  },
  false
);
window.addEventListener(
  "load",
  function () {
    $("#transactions_reset").parsley();
  },
  false
);
pwShowHide.forEach((eyeIcon) => {
  eyeIcon.addEventListener("click", () => {
    pwFields.forEach((pwField) => {
      if (pwField.type === "password") {
        pwField.type = "text";

        pwShowHide.forEach((icon) => {
          icon.classList.replace("fa-eye-slash", "fa-eye");
        });
      } else {
        pwField.type = "password";

        pwShowHide.forEach((icon) => {
          icon.classList.replace("fa-eye", "fa-eye-slash");
        });
      }
    });
  });
});
function modal() {
  document.querySelector("#text-title").innerHTML = "Restablecer Contraseña";
  document.querySelector("#text-button").innerHTML = "Enviar Solicitud";
  document.querySelector("#transactions_reset").reset();
  $("#transactions_reset").parsley().reset();
  $("#modal-reset").modal("show");
}
particlesJS({
  particles: {
    number: {
      value: 150,
      density: {
        enable: true,
        value_area: 800,
      },
    },
    color: {
      value: "#ffffff",
    },
    shape: {
      type: "circle",
      stroke: {
        width: 0,
        color: "#000000",
      },
      polygon: {
        nb_sides: 5,
      },
      image: {
        src: "img/github.svg",
        width: 100,
        height: 100,
      },
    },
    opacity: {
      value: 0.5,
      random: false,
      anim: {
        enable: false,
        speed: 1,
        opacity_min: 0.1,
        sync: false,
      },
    },
    size: {
      value: 3,
      random: true,
      anim: {
        enable: false,
        speed: 40,
        size_min: 0.1,
        sync: false,
      },
    },
    line_linked: {
      enable: true,
      distance: 150,
      color: "#ffffff",
      opacity: 0.4,
      width: 1,
    },
    move: {
      enable: true,
      speed: 11.22388442605866,
      direction: "none",
      random: false,
      straight: false,
      out_mode: "out",
      bounce: false,
      attract: {
        enable: false,
        rotateX: 600,
        rotateY: 1200,
      },
    },
  },
  interactivity: {
    detect_on: "canvas",
    events: {
      onhover: {
        enable: false,
        mode: "repulse",
      },
      onclick: {
        enable: true,
        mode: "push",
      },
      resize: true,
    },
    modes: {
      grab: {
        distance: 400,
        line_linked: {
          opacity: 1,
        },
      },
      bubble: {
        distance: 400,
        size: 40,
        duration: 2,
        opacity: 8,
        speed: 3,
      },
      repulse: {
        distance: 200,
        duration: 0.4,
      },
      push: {
        particles_nb: 4,
      },
      remove: {
        particles_nb: 2,
      },
    },
  },
  retina_detect: true,
});
