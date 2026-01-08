<?php foreach($data['columns'] as $column) {?>
<?php 
  $tipos = [
    "varchar" => "text",
    "int" => "number",
    "decimal" => "number"
  ];

  $tipo = $tipos[$column['tipo']];
?>
  <div class="col-md-12 form-group">
    <label class="control-label text-uppercase">
      <?= $column['nombre'] ?>
      <?= $column['obligatorio'] == 1 ? "<span class='text-danger'>*</span>" : ""?>
    </label>
    <input 
      type="<?= $tipo?>" 
      class="form-control" 
      name="<?= $column['campo']?>"
      id="<?= $column['campo']?>"
      onkeypress="return numbersandletters(event)" 
      placeholder="INGRESE <?= $column['nombre']?>" 
      data-parsley-required="<?= $column['obligatorio'] == 1 ? 'true' : 'false'?>"
    >
  </div>
<?php } ?>