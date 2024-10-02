<?php
//Obtener configuración para validar si se agregará una comisión automática cada vez que se registre un pago de tarjeta
$configuration = ConfigurationData::getAll();
$isCardCommission = $configuration["active_card_commission"];
$totalCardCommission = (isset($configuration["card_commission_value"]) ? $configuration["card_commission_value"] : 0);
$operation = OperationData::getById($_POST["saleId"]);

if (count($_POST) > 0) {
  $totalSale = floatval($_POST["totalSale"]);
  $totalPayment = floatval($_POST["totalPayment"]);
  $actualPayment = $totalPayment + floatval($_POST["total"]);

  //Validar si la cantidad a pagar no supera el total de la venta
  if($actualPayment <= $totalSale){
    $paymentDetail = new OperationPaymentData();
    $paymentDetail->payment_type_id = $_POST["paymentType"];
    $paymentDetail->operation_id = $_POST["saleId"];
    $paymentDetail->date = $_POST["date"]." ".date("H:i:s");

    //Forma de pago en tarjeta se calcula el total a pagar + la comisión si en la configuración se estableció que se cobraría comisión
    if ($isCardCommission->value && ($_POST["paymentType"] == 2 || $_POST["paymentType"] == 3)) {
      $commissionPrice = $_POST["total"] * floatval($totalCardCommission);
      $paymentDetail->total = $_POST["total"] + $commissionPrice;

      $operationDetail = new OperationDetailData();
      $operationDetail->product_id = 1; //Comisión
      $operationDetail->operation_operation_type_id = 2; // 1 - entrada
      $operationDetail->operation_id = $_POST["saleId"];
      $operationDetail->quantity = "1";
      $operationDetail->price = $commissionPrice;
      $operationDetail->date = $_POST["date"]." ".date("H:i:s");
      $add = $operationDetail->add();

      //Ya que se agregó una nuevo producto a la venta se actualiza el total
      $operation->total = $totalSale + $commissionPrice;
      $operation->updateTotal();

    } else {
      $paymentDetail->total = $_POST["total"];
    }
    $paymentDetail = $paymentDetail->add();

    //Validar si la venta se liquidó o no
    $isLiquidated = $totalSale - $actualPayment;
    if ($isLiquidated == 0) $statusId = 1;
    else $statusId = 0;

    //Actualizar datos de la venta (estatus y fecha de creación)
    $operation->status_id = $statusId;
    $operation->updateStatus();
    $operation->created_at = $_POST["date"]." ".date("H:i:s");
    $operation->updateDate();

    OperationDetailData::updateDate($_POST["saleId"], ($_POST["date"]." ".date("H:i:s")));
    print "<script>window.location='index.php?view=sales/edit&id=" . $_POST["saleId"] . "';</script>";
  }
  else{
    echo '<script> 
      alert("La cantidad pagada no puede superar el total de la venta.");
      window.location="index.php?view=sales/edit&id=' . $_POST["saleId"] . '";
    </script>';
  }
}
