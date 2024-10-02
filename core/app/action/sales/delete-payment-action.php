<?php
$payment = OperationPaymentData::getById($_GET["paymentId"]);
$payment->delete();

$totalSale = $_GET["totalSale"];
$totalPayment = $_GET["totalPayment"];

$isLiquidated = $totalSale - $totalPayment;

if ($isLiquidated <= 0) $statusId = 1;
else $statusId = 0;

OperationDetailData::updateTotalStatus($_GET["saleId"], $_GET["totalSale"], $statusId);
Core::redir("./index.php?view=sales/edit&id=" . $_GET["saleId"] . "");
