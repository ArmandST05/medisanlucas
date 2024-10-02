<?php
$actualDate = date('Y-m-d');
$startDate = (isset($_GET["sd"])) ? $date = $_GET["sd"] : $actualDate;
$endDate = (isset($_GET["ed"])) ? $date = $_GET["ed"] : $actualDate;
$user = UserData::getLoggedIn();
$userType = $user->user_type;

$sales = OperationDetailData::getSalesByDatesProductType($startDate, $endDate, 4);

?>

<section class="content">
  <div class="row">
    <div class="col-md-12">
    </div>
    <div class="col-md-12">
      <form>
        <input type="hidden" name="view" value="reports/product-sales">
        <div class="row">
          <div class="col-md-3">
            <label>Fecha inicio</label>
            <input type="date" name="sd" value="<?php echo $startDate ?>" class="form-control">
          </div>
          <div class="col-md-3">
            <label>Fecha fin</label>
            <input type="date" name="ed" value="<?php echo $endDate ?>" class="form-control">
          </div>

          <div class="col-md-2">
            <br>
            <input type="submit" class="btn btn-success btn-block" value="Procesar">
          </div>

          <?php if ($userType == "su") : ?>
            <div class="col-md-2">
              <br>
              <input type="submit" class="btn btn-primary btn-block" value="Exportar Excel" id="btnExport">
            </div>
          <?php endif; ?>
        </div>

      </form>

    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-12">
      <div class="clearfix"></div>
      <h1>Reporte ventas por producto</h1>

      <table id="datosexcel" border='1' class="table table-bordered table-hover">
        <thead>
          <th>Código de barras</th>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Total</th>
        </thead>

        <?php
        foreach ($sales as $sale) : ?>
          <tr>
            <td><?php echo $sale->barcode ?></td>
            <td><?php echo $sale->product_name ?></td>
            <td><?php echo $sale->quantity ?></td>
            <td><?php echo $sale->total ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      </table>

    </div>
  </div>
</section>

<script src="assets/jquery.btechco.excelexport.js"></script>
<script src="assets/jquery.base64.js"></script>

<script type="text/javascript">
  $(document).ready(function() {

    var dataTable = $('#datosexcel').DataTable({

      "language": {
        "sProcessing": "Procesando...",
        "sLengthMenu": "Mostrar _MENU_ registros",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix": "",
        "sSearch": "Buscar:",
        "sUrl": "",
        "sInfoThousands": ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
          "sFirst": "Primero",
          "sLast": "Último",
          "sNext": "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      },
      "ordering": false,
      "processing": true,
      "responsive": true,
      "scrollX": true,
      "bPaginate": false,
      "order": [[3, 'desc']],
    });

    $("#btnExport").click(function(e) {
      $("#datosexcel").btechco_excelexport({
        containerid: "datosexcel",
        datatype: $datatype.Table,
        filename: 'Reporte productos ventas'
      });

    });

  });
</script>