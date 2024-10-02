<?php
  $totalSale = floatval($_POST["totalSale"]-$_POST["discount"]);//Calcular total real
  $discountPercentage = $_POST["discountPercentage"];
  $totalPayment = floatval($_POST["totalPayment"]);

  $pendingPayment = $totalSale - $totalPayment;
  if($pendingPayment <= 0) $statusId = 1;
  else $statusId = 0;
  
  $operation = OperationData::getById($_POST["saleId"]);
  $operation->total = $totalSale;
  $operation->updateTotal();  
  $operation->status_id = $statusId;
  $operation->updateStatus();  
  $operation->description = $_POST["description"];
  $operation->updateDescription(); 
  $operation->discount = (($_POST["totalSale"]*$discountPercentage)/100);
  $operation->discount_percentage = $discountPercentage;
  $operation->updateDiscount(); 

  //print "<script>window.location='index.php?view=onesell&id=".$_POST['saleId']."';</script>";
  print "<script>window.location='index.php?view=sales/index';</script>";

?>
