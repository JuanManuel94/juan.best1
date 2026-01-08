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
        if ($data['business']['favicon'] == "favicon.png") {
            $favicon = base_style() . '/images/logotypes/' . $data['business']['favicon'];
            ;
        } else {
            $favicon_url = base_style() . '/uploads/business/' . $data['business']['favicon'];
            if (@getimagesize($favicon_url)) {
                $favicon = base_style() . '/uploads/business/' . $data['business']['favicon'];
            } else {
                $favicon = base_style() . '/images/logotypes/favicon.png';
            }
        }
    } else {
        $favicon = base_style() . '/images/logotypes/favicon.png';
    }
    ?>
    <!-- ================== INICIO ICONO ================== -->
    <link rel="icon" type="image/x-icon" href="<?= $favicon ?>">
    <!-- ================== FIN ICONO ===================== -->
    <!-- ================== INICIO ARCHIVOS CSS =========== -->
    <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css">
    <link rel="stylesheet" href="<?= base_style() ?>/css/jquery-confirm.min.css">
    <link rel="stylesheet" href="<?= base_style() ?>/bookstores/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" href="<?= base_style() ?>/css/login.css">
    <!-- ================== FIN ARCHIVOS CSS ============== -->
    <!-- ================== INICIO TITULO ================= -->
    <title>
        <?= $data['page_name'] ?>
    </title>
    <!-- ================== FIN TITULO =================== -->
</head>

<body class="pace-top">
    <div id="loading"><span class="loading-spinner"></span></div>
    <div class="login-cover">
        <?php
        if (!empty($data['business']['background'])) {
            $background_url = base_style() . '/images/background/' . $data['business']['background'];
            if (@getimagesize($background_url)) {
                $background = base_style() . '/images/background/' . $data['business']['background'];
            } else {
                $background = base_style() . '/images/background/bg-1.jpeg';
            }
        } else {
            $background = base_style() . '/images/background/bg-1.jpeg';
        }
        ?>
        <div id="particles-js" class="login-cover-image" style="background-image: url(<?= $background ?>)"
            data-id="login-cover-image"></div>
        <div class="login-cover-bg"></div>
    </div>
    <div id="page-container" class="fade">
        <div class="login login-v2" data-pageload-addclass="animated fadeIn">
            <div class="login-header">
                <div class="brand" style="display: flex; justify-content: center; align-items: center;">
                    <?php
                    if (!empty($data['business']['logo_login'])) {
                        if ($data['business']['logo_login'] == "superwisp_white.png") {
                            $logo = base_style() . '/images/logotypes/' . $data['business']['logo_login'];
                        } else {
                            $logo_url = base_style() . '/uploads/business/' . $data['business']['logo_login'];
                            if (@getimagesize($logo_url)) {
                                $logo = base_style() . '/uploads/business/' . $data['business']['logo_login'];
                            } else {
                                $logo = base_style() . '/images/logotypes/superwisp_white.png';
                            }
                        }
                    } else {
                        $logo = base_style() . '/images/logotypes/superwisp_white.png';
                    }
                    ?>
                    <img src="<?= $logo ?>" class="img-responsive" style="max-width: 200px; height: auto;">
                </div>
            </div>
            <div class="login-content">
                <form name="transactions" id="transactions" autocomplete="off" class="margin-bottom-0">
                    <div class="form-group m-b-20">
                        <input type="text" class="form-control form-control-lg" placeholder="Usuario" id="username"
                            name="username" value="<?php if (isset($_COOKIE["username"])) {
                                echo $_COOKIE["username"];
                            } ?>">
                    </div>

                    <div class="form-group mb-0" style="position: relative;">
                        <input type="password" class="form-control form-control-lg" placeholder="Contraseña"
                            id="password" name="password" value="<?php if (isset($_COOKIE["password"])) {
                                echo $_COOKIE["password"];
                            } ?>" style="padding-right: 40px;">
                        <i class="fa fa-eye-slash showHidePw"
                            style="position: absolute; top: 50%; right: -20px; transform: translateY(-50%); cursor: pointer;"></i>
                    </div>
                    <div class="checkbox checkbox-css m-b-20">
                        <input type="checkbox" id="remember" name="remember" <?php if (isset($_COOKIE["username"])) { ?>
                                checked <?php } ?>>
                        <label for="remember">Mantener sesion</label>
                    </div>
                    <div class="login-buttons">
                        <button type="submit" class="btn btn-success btn-block btn-lg">INGRESAR</button>
                    </div>
                    <div class="m-t-5">
                        ¿Olvidaste tu contraseña? <a href="javascript:;" onclick="modal();">Haz clic aquí</a>.
                    </div>
                    <div class="m-t-10" style="display: flex; justify-content: space-between; align-items: center;">
                        <span>&copy;
                        <?= date('Y') ?> <?= $_SESSION['businessData']['business_name'] ?>. Todos los derechos reservados.
                        </span>
                        
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    <?php
    modal("loginModal", $data);
    ?>
    <!-- ================== INICIO RUTA  ============== -->
    <script> const base_url = "<?= base_url(); ?>"; </script>
    <!-- ================== INICIO RUTA  ============== -->
    <!-- ================== INICIO ARCHIVOS JS ======== -->
    <script src="<?= base_style() ?>/js/app.min.js"></script>
    <script src="<?= base_style() ?>/js/particles.min.js"></script>
    <script src="<?= base_style() ?>/js/functions.js"></script>
    <script src="<?= base_style() ?>/js/jquery-confirm.min.js"></script>
    <script src="<?= base_style() ?>/bookstores/parsleyjs/parsley.js"></script>
    <script src="<?= base_style() ?>/bookstores/gritter/js/jquery.gritter.min.js"></script>
    <script src="<?= base_style() ?>/js/functions/<?= $data['page_functions_js']; ?>?v=<?= time(); ?>"></script>
    <!-- ================== FIN ARCHIVOS JS =========== -->
</body>

</html>