<?php
$patient = PatientData::getById($_GET["patientId"]);
$medics = MedicData::getAll();
?>
<div class="row">
  <div class="col-md-12">
    <h1>Estado de cuenta <?php echo $_GET["name"] ?></h1>
    <div class="clearfix"></div>
    <?php
    $results = 0;
    $sales = OperationData::getAccountStatusByPatient($_GET["patientId"]);

    $results = count($sales);

    if (count($sales) > 0) {
      if ($results == 1) echo $results . " Resultado";
      else echo $results . " Resultados";
    ?>
      <table class="table table-bordered table-hover">
        <h5>
          <thead>
            <th>Paciente</th>
            <th>Teléfono Paciente</th>
            <th>Médico</th>
            <th>Fecha/Hora</th>
            <th>Conceptos</th>
            <th>Total</th>
            <th>Forma de pago</th>
            <th>Estatus</th>
          </thead>
          <?php
          foreach ($sales as $sale) :
            $medicsString = OperationData::getMedicsBySaleString($sale->id);
            $payments = OperationPaymentData::getAllByOperationId($sale->id);
          ?>
            <tr>
              <td><?php echo $patient->name; ?></td>
              <td><?php echo $patient->cellphone; ?></td>
              <td><?php echo $medicsString; ?></td>
              <td><?php echo $sale->day_name . " " . $sale->date_format; ?></td>
              <td>
                <?php
                $operationDetails = OperationDetailData::getAllByOperationId($sale->id);
                foreach ($operationDetails as $operationDetail) {
                  $product = $operationDetail->getProduct();
                  echo $operationDetail->quantity . " " . $product->name . "<br>";
                } ?>
              </td>
              <td>$<?php echo number_format($sale->total, 2); ?></td>
              <td>
                <?php
                foreach ($payments as $payment) {
                  echo $payment->getType()->name . ": $" . number_format($payment->total, 2) . "<br>";
                } ?>
              </td>
              <td>
                <?php
                  if ($sale->status_id == 1) echo '<b class="bg-success">PAGADA</b>';
                  else echo '<b class="bg-danger">PENDIENTE</b>'; 
                 ?>
              </td>
            </tr>
          <?php endforeach; ?>
      </table>
    <?php
    } else {
      echo "<p class='alert alert-danger'>No se encontraron resultados</p>";
    }
    ?>
  </div>
</div>
</div>
</div>