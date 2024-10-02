<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>

<?php
$products = ProductData::getAllByTypeId(4);
?>
<div class="row">
  <div class="col-md-3">
    <input type="submit" class="btn btn-primary btn-block" value="Exportar" id="btnExport">

  </div>
  <div class="col-md-12">

    <h1>Inventario Medicamento</h1>
    <div class="clearfix"></div>
    <div class="col-md-8">
    </div>
    <hr>
    <table id='datosexcel' border='1' class="table table-bordered table-hover">
      <thead bgcolor="#eeeeee" align="center">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Disponible</th>
          <th>Tipo</th>
          <th>Fecha caducidad</th>

        </tr>
      </thead>
      <?php
      foreach ($products as $product) :
        $stock = OperationDetailData::getStockByProduct($product->id);
        $expirationDates = OperationDetailData::getAllExpirationDatesByProduct($product->id);
        $outputs = OperationDetailData::getTotalOutputsByProduct($product->id);
      ?>
        <tr>
          <td><?php echo $product->id ?></td>
          <td><?php echo $product->name ?></td>
          <td><?php echo $stock ?></td>
          <td><?php echo $product->getType()->name ?></td>
          <td>
            <?php
            $totalProduct = 0;
            $sumTotal = 0;
            $stockDate = 0;

            foreach ($expirationDates as $expirationDate) {
              $months = ["00" => "", "01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];
              //Sumatoria de la cantidad de productos en esa fecha de caducidad
              $totalProduct += $expirationDate->quantity;

              if ($totalProduct <= $expirationDate->quantity) {
                //Stock del producto con esa fecha de caducidad
                $stockDate = $expirationDate->quantity - $outputs->quantity;
                //Si todavía quedan productos con esa fecha de expiración, mostrar la cantidad
                if ($stockDate > 0) {
                  //Validar si les faltan más de 3 meses para caducar
                  if ($expirationDate->difference_month > 3)
                    echo "<span style='color:#000000;'><b> " . $stockDate . " </b> " . $expirationDate->expiration_year . "-  " . $months[$expirationDate->month] . " (Lote:" . $expirationDate->lot . ")". "  &nbsp&nbsp&nbsp" . $expirationDate->difference_month . " Meses</span><br>";
                  else
                    echo "<span style='color:#C14600;'><b> " . $stockDate . " </b> " . $expirationDate->expiration_year . "-  " . $months[$expirationDate->month] . " (Lote:" . $expirationDate->lot . ")". "  &nbsp&nbsp&nbsp" . $expirationDate->difference_month . " Meses</span><br>";
                }
              } else if ($totalProduct >= $expirationDate->quantity) {
                $sumTotal +=  $expirationDate->quantity;
                $stock = $totalProduct - $outputs->quantity;//Validar si todavía hay stock del producto

                if ($stock <= $expirationDate->quantity) {
                  if ($stock > 0) {
                    //Validar si les faltan más de 3 meses para caducar
                    if ($expirationDate->difference_month > 3)
                      echo "<span style='color:#000000;'><b> " . $stock . " </b> " . $expirationDate->expiration_year . "-  " . $months[$expirationDate->month] . " (Lote:" . $expirationDate->lot . ")". "  &nbsp&nbsp&nbsp" . $expirationDate->difference_month . " Meses</span><br>";
                    else
                      echo "<span style='color:#C14600;'><b> " . $stock . " </b> " . $expirationDate->expiration_year . "-  " . $months[$expirationDate->month] . " (Lote:" . $expirationDate->lot . ")". "  &nbsp&nbsp&nbsp" . $expirationDate->difference_month . " Meses</span><br>";
                  }
                } else {
                  //Validar si les faltan más de 3 meses para caducar
                  if ($expirationDate->difference_month > 3)
                    echo "<span style='color:#000000;'><b> " . $expirationDate->quantity . " </b> " . $expirationDate->expiration_year . "-  " . $months[$expirationDate->month] . " (Lote:" . $expirationDate->lot . ")". "  &nbsp&nbsp&nbsp" . $expirationDate->difference_month . " Meses</span><br>";
                  else
                    echo "<span style='color:#C14600;'><b> " . $expirationDate->quantity . " </b> " . $expirationDate->expiration_year . "-  " . $months[$expirationDate->month] . " (Lote:" . $expirationDate->lot . ")". "  &nbsp&nbsp&nbsp" . $expirationDate->difference_month . " Meses</span><br>";
                }
              }
            } ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</div>
</div>
<script type="text/javascript">
  $(document).ready(function() {

    $("#btnExport").click(function(e) {
      $("#datosexcel").btechco_excelexport({
        containerid: "datosexcel",
        datatype: $datatype.Table,
        filename: 'Reporte inventario'
      });

    });

  });
</script>
