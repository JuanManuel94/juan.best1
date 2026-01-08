<?php
head($data);
modal("wspPlanillaModal", $data);
?>
<style>
  .card-container {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
    justify-content: center;
    padding: 20px;
  }

  .custom-card {
    width: 100%;
    min-height: 250px;
    border: 1px solid #4cce1f;
    transition: background 0.3s ease-in-out;
    position: relative;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: white;
  }

  .custom-card:hover {
    background: #808080;
    /* Gris */
  }

  .edit-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 26px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 12px;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }

  .custom-card:hover .edit-icon {
    opacity: 1;
  }


  .whatsapp-icon {
    width: 70px;
  }


  .logo-container {
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card-title {
    background: #20c997;
    color: white;
    font-weight: bold;
    text-align: center;
    padding: 12px 0;
    width: 100%;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
  }

  @media (max-width: 1200px) {
    .card-container {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  @media (max-width: 992px) {
    .card-container {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .card-container {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 480px) {
    .card-container {
      grid-template-columns: repeat(1, 1fr);
    }
  }
</style>

<!-- INICIO TITULO -->
<ol class="breadcrumb float-xl-right">
  <li class="breadcrumb-item"><a href="<?= base_url() ?>/dashboard"><?= $data['home_page'] ?></a></li>
  <li class="breadcrumb-item"><a href="javascript:window.history.back();"><?= $data['previous_page'] ?></a></li>
  <li class="breadcrumb-item active"><?= $data['actual_page'] ?></li>
</ol>
<h1 class="page-header"><?= $data['page_title'] ?></h1>

<div class="panel panel-default panel-runway2">
  <div class="panel-heading">
    <h4 class="panel-title"><?= $data['page_title'] ?></h4>
  </div>
</div>

<div class="panel panel-default panel-runway2">
  <div class="container-fluid pt-4 pb-4">
    <div class="card-container">
      <?php foreach ($data['messages'] as $message): ?>
        <pre style="display: none;" id="planilla_<?= $message['id'] ?>"><?= json_encode($message) ?></pre>

        <div class="custom-card cursor-pointer" onclick="modal('<?= $message['id'] ?>')">

          <div class="logo-container">
            <img src="<?= base_url() ?>/Assets/images/default/whatsapp.png" class="whatsapp-icon" alt="WhatsApp">
          </div>

          <div class="edit-icon">
            <i class="fas fa-pencil-alt"></i>
          </div>


          <div class="card-title" id="titulo_<?= $message['id'] ?>">
            <?= strtoupper($message['titulo']) ?>
          </div>

        </div>
      <?php endforeach ?>
    </div>
  </div>
</div>
<!-- FIN TITULO -->

<?php footer($data); ?>