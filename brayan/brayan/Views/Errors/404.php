<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="author" content="<?= DEVELOPER ?>">
        <meta name="theme-color" content="#00acac">
        <?php
            if(!empty($data['business']['favicon'])){
                if($data['business']['favicon'] == "favicon.png"){
                    $favicon = base_style().'/images/logotypes/'.$data['business']['favicon'];;
                }else{
                    $favicon_url = base_style().'/uploads/business/'.$data['business']['favicon'];
                    if(@getimagesize($favicon_url)){
                        $favicon = base_style().'/uploads/business/'.$data['business']['favicon'];
                    }else{
                        $favicon = base_style().'/images/logotypes/favicon.png';
                    }
                }
            }else{
                $favicon = base_style().'/images/logotypes/favicon.png';
            }
        ?>
        <!-- ================== INICIO ICONO ================== -->
        <link rel="icon" type="image/x-icon" href="<?= $favicon ?>">
        <!-- ================== FIN ICONO ===================== -->
    	<!-- ================== INICIO ARCHIVOS CSS =========== -->
        <link rel="stylesheet" href="<?= base_style() ?>/css/default/app.min.css" >
        <!-- ================== FIN ARCHIVOS CSS ============== -->
        <!-- ================== INICIO TITULO ================= -->
        <title><?= $data['page_name'] ?></title>
        <!-- ================== FIN TITULO =================== -->
    </head>
    <style>body {
  margin: 0;
  padding: 0;
  background-color: #1a1a1a; }

.conten {
  position: fixed;
  top: 4em;
  width: 100%;
  text-align: center; }
  .conten__img {
    width: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center; }
    .conten__img img {
      width: 300px;
      display: block;
      -webkit-animation: animates 2s linear infinite alternate;
      animation: animates 2s linear infinite alternate; }
  .conten__number {
    position: absolute;
    font-size: 5.5rem;
    font-family: "Gill Sans", sans-serif;
    font-weight: 600;
    top: 9rem;
    color: rgba(255, 255, 255, 0.5);
    -webkit-animation: animatestext 2s linear infinite alternate;
    animation: animatestext 2s linear infinite alternate; }
  .conten__error {
    color: #ffffff;
    width: 50%;
    margin: 2em auto;
    font-family: "Gill Sans", sans-serif; }
  .conten__button {
    background-color: #0077d8f4;
    color: #ffffff;
    font-weight: 600;
    font-family: "Gill Sans", sans-serif;
    padding: 0.8em 1em;
    border-radius: 50px;
    text-decoration: none; }

@-webkit-keyframes animates {
  0% {
    -webkit-transform: translateX(5%);
    transform: translateX(5%); }
  100% {
    -webkit-transform: translateX(-5%);
    transform: translateX(-5%); } }

@keyframes animates {
  0% {
    -webkit-transform: translateX(5%);
    transform: translateX(5%); }
  100% {
    -webkit-transform: translateX(-5%);
    transform: translateX(-5%); } }

@-webkit-keyframes animatestext {
  0% {
    -webkit-transform: rotate(5deg);
    transform: rotate(5deg); }
  100% {
    -webkit-transform: rotate(-5deg);
    transform: rotate(-5deg); } }

@keyframes animatestext {
  0% {
    -webkit-transform: rotate(5deg);
    transform: rotate(5deg); }
  100% {
    -webkit-transform: rotate(-5deg);
    transform: rotate(-5deg); } }
</style>

<body>
    
    <div class="conten">
        <div class="conten__img">
            <img src="<?= base_style() ?>/images/default/404.png" alt="">
            <p class="conten__number">
                404
            </p>
        </div>
        <div class="conten__Description">
            <p class="conten__error">
                UPSSSS!!!! Algo salio mal, pagina no encontrada.
            </p>
        <a href="javascript:window.history.back();" class="conten__button">Regresar al panel</a>
        </div>
    </div>
    <!-- ================== INICIO ARCHIVOS JS ======== -->
        <script src="<?= base_style() ?>/js/app.min.js"></script>
        <!-- ================== END ARCHIVOS JS =========== -->
   
</body>
</html>
