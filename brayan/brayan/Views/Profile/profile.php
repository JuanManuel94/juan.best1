<?php head($data); ?>
<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
    <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
    <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>
<div class="row">
    <div class="col-lg-4 col-xlg-3 col-md-5 mb-3">
        <div class="card">
            <div class="card-body">
                <center class="m-t-20">
                  <?php
                      if(!empty($_SESSION['userData']['image'])){
                          if($_SESSION['userData']['image'] == "user_default.png"){
                              $image = base_style().'/images/default/user_default.png';
                          }else{
                              $url = base_style().'/uploads/users/'.$_SESSION['userData']['image'];
                              if(@getimagesize($url)){
                                  $image = $url;
                              }else{
                                  $image = base_style().'/images/default/user_default.png';
                              }
                          }
                      }else{
                          $image = base_style().'/images/default/user_default.png';
                      }
                  ?>
                    <input type="hidden" id="current_photo" name="current_photo" value="<?= $_SESSION['userData']['image'] ?>">
                    <div class="profilepic">
                        <img class="profilepic__image" src="<?= $image ?>" id="image_profile"/>
                        <div class="profilepic__content">
                          <input type="file" id="file" name="image_profile">
                          <label for="file" class="profilepic__text"><i class="fas fa-camera f-s-20 mr-1"></i>Elegir imagen</label>
                        </div>
                    </div>
                    <h4 class="card-title m-t-10"><?=  ucwords(strtolower($_SESSION['userData']['names']." ".$_SESSION['userData']['surnames'])) ?></h4>
                    <h6 class="card-subtitle"><?= ucwords(strtolower($_SESSION['userData']['profile'])) ?></h6>
                </center>
            </div>
            <div><hr></div>
            <div class="card-body">
                <small class="text-muted">
                    <i class="far fa-envelope mr-1"></i>Correo
                </small>
                <h6><?= $_SESSION['userData']['email'] ?></h6>
                <small class="text-muted p-t-30 db">
                    <i class="fas fa-mobile-alt mr-1"></i>Teléfono Móvil
                </small>
                <h6><?= $_SESSION['userData']['mobile'] ?></h6>
            </div>
        </div>
    </div>
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a href="#tab-data" data-toggle="tab" class="nav-link active">
                    <span class="d-sm-none"><i class="fa fa-cog"></i></span>
                    <span class="d-sm-block d-none">Información personal</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#tab-password" data-toggle="tab" class="nav-link">
                    <span class="d-sm-none"><i class="fa fa-lock"></i></span>
                    <span class="d-sm-block d-none">Cambiar contraseña</span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
			<div class="tab-pane fade active show" id="tab-data">
				<form autocomplete="off" name="transactions_data" id="transactions_data">
                    <div class="form-group">
                        <label>Nombres</label>
                        <input type="text" class="form-control text-uppercase" value="<?= $_SESSION['userData']['names'] ?>" name="names" id="names" onkeypress="return letters(event)" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label>Apellidos</label>
                        <input type="text" class="form-control text-uppercase" value="<?= $_SESSION['userData']['surnames'] ?>" name="surnames" id="surnames" onkeypress="return letters(event)" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label>Celular</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['userData']['mobile'] ?>" name="mobile" id="mobile" onkeypress="return numbers(event)" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['userData']['email'] ?>" name="email" id="email" onkeypress="return mail(event)" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-blue">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
			</div>
			<div class="tab-pane fade" id="tab-password">
                <form autocomplete="off" name="transactions_password" id="transactions_password">
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" class="form-control" name="password" id="password" onkeypress="return numbersandletters(event)" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <label>Repita contraseña</label>
                        <input type="password" class="form-control" name="repeat_password" id="repeat_password" onkeypress="return numbersandletters(event)" data-parsley-required="true">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-blue">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
			</div>
		</div>
    </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
