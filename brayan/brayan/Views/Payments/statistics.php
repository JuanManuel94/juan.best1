<?php head($data); ?>
<!-- INICIO TITULO -->
<div class="panel panel-default panel-statistics">
    <div class="panel-heading">
        <h4 class="panel-title">RESUMEN DE TRANSACCIONES</h4>
        <div class="panel-heading-btn">
            <select class="form-control" name="listYears" id="listYears">
                <?php
                    $ano = date("Y");
                    for($i=2020;$i<$ano+1;$i++){
                        if($i == $ano){
                            echo '<option value="'.$i.'" selected>Año '.$i.'</option>';
                        }else{
                            echo '<option value="'.$i.'">Año '.$i.'</option>';
                        }
                    }
                ?>
            </select>
        </div>
    </div>
    <div class="panel-body border-panel">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-12 mb-3">
                <canvas id="payments" style="height:350px; width:100%"></canvas>
            </div>
            <div class="col-md-12 col-sm-12 col-12">
              <div class="table-responsive">
                <table id="list" class="table table-bordered dt-responsive nowrap dataTable dtr-inline collapsed" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Nº</th>
                            <th>Mes</th>
                            <th>Total Op.</th>
                            <th>Cobrado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                      <tr>
                        <th colspan="2">Totales</th>
                        <th></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>
<!-- FIN TITULO -->
<?php footer($data); ?>
