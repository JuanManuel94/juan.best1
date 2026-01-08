<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="author" content="<?= DEVELOPER ?>">
  <meta name="theme-color" content="#00acac">

  <?php
    if (!empty($data['business']['favicon'])) {
      if ($data['business']['favicon'] === "favicon.png") {
        $favicon = base_style() . '/images/logotypes/' . $data['business']['favicon'];
      } else {
        $favicon_url = base_style() . '/uploads/business/' . $data['business']['favicon'];
        $favicon = @getimagesize($favicon_url) ? $favicon_url : base_style() . '/images/logotypes/favicon.png';
      }
    } else {
      $favicon = base_style() . '/images/logotypes/favicon.png';
    }
  ?>

  <link rel="icon" type="image/x-icon" href="<?= $favicon ?>">

  <!-- CSS -->
  <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/css/jquery-confirm.min.css">
  <link rel="stylesheet" href="<?= base_style() ?>/bookstores/gritter/css/jquery.gritter.css" />
  <link rel="stylesheet" href="<?= base_style() ?>/css/login.css">

  <title><?= $data['page_name'] ?></title>
</head>
<body class="pace-top">

  <div id="loading"><span class="loading-spinner"></span></div>

  <div class="login-cover">
    <?php
      if (!empty($data['business']['background'])) {
        $bg_url = base_style() . '/images/background/' . $data['business']['background'];
        $background = @getimagesize($bg_url) ? $bg_url : base_style() . '/images/background/bg-1.jpeg';
      } else {
        $background = base_style() . '/images/background/bg-1.jpeg';
      }
    ?>
    <div id="particles-js" class="login-cover-image" style="background-image: url(<?= $background ?>)" data-id="login-cover-image"></div>
    <div class="login-cover-bg"></div>
  </div>

  <div id="page-container" class="fade">
    <div class="login login-v2" data-pageload-addclass="animated fadeIn">

      <div class="login-header">
        <div class="brand" style="display: flex; justify-content: center; align-items: center;">
          <?php
            if (!empty($data['business']['logo_login'])) {
              if ($data['business']['logo_login'] === "superwisp_white.png") {
                $logo = base_style() . '/images/logotypes/' . $data['business']['logo_login'];
              } else {
                $logo_url = base_style() . '/uploads/business/' . $data['business']['logo_login'];
                $logo = @getimagesize($logo_url) ? $logo_url : base_style() . '/images/logotypes/superwisp_white.png';
              }
            } else {
              $logo = base_style() . '/images/logotypes/superwisp_white.png';
            }
          ?>
          <img src="<?= $logo ?>" class="img-responsive" style="max-width: 230px; height: auto;">
        </div>
        <!-- ÍCONO CANDADO ELIMINADO -->
      </div>

      <div class="login-content">
        <form name="transactions_password" id="transactions_password" autocomplete="off" class="margin-bottom-0">
          <!-- TEXTO CENTRADO -->
          <h4 class="text-white text-center mb-4"><u>Restaurar contraseña</u></h4>

          <input type="hidden" id="id" name="id" value="<?= $data['id']; ?>" required>
          <input type="hidden" id="email" name="email" value="<?= $data['email']; ?>" required>
          <input type="hidden" id="token" name="token" value="<?= $data['token']; ?>" required>

          <div class="form-group m-b-20" style="position: relative;">
            <input type="password" class="form-control form-control-lg" placeholder="Nueva contraseña" id="password" name="password">
            <i class="fa fa-eye-slash showPass" style="position: absolute; top: 50%; right: -20px; transform: translateY(-50%); cursor: pointer;"></i>
          </div>

          <div class="form-group m-b-20" style="position: relative;">
            <input type="password" class="form-control form-control-lg" placeholder="Repita la contraseña" id="passwordConfirm" name="passwordConfirm">
            <i class="fa fa-eye-slash showPassConfirm" style="position: absolute; top: 50%; right: -20px; transform: translateY(-50%); cursor: pointer;"></i>
          </div>

          <div class="login-buttons">
            <button type="submit" class="btn btn-success btn-block btn-lg">Guardar Cambios</button>
          </div>
        </form>
        <div class="m-t-10" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>&copy;
                        <?= date('Y') ?> <?= $_SESSION['businessData']['business_name'] ?>. Todos los derechos reservados.
                        </span>
                        
                    </div>
      </div>

    </div>
  </div>

  <script> const base_url = "<?= base_url(); ?>"; </script>

  <script src="<?= base_style() ?>/js/app.min.js"></script>
  <script src="<?= base_style() ?>/js/particles.min.js"></script>
  <script src="<?= base_style() ?>/js/functions.js"></script>
  <script src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
  <script src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
  <script src="<?= base_style() ?>/js/functions/<?= $data['page_functions_js']; ?>"></script>

</body>
</html>
