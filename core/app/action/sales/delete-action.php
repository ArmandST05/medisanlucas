<?php
$sale = OperationData::getById($_GET["id"]);
$operations = OperationDetailData::getAllProductsByOperationId($_GET["id"]);

foreach ($operations as $operation) {
	$operation->delete();
}

OperationPaymentData::deleteByOperationId($_GET["id"]);
OperationData::deleteReservationsBySaleId($_GET["id"]);
$sale->delete();

Core::redir("./index.php?view=sales/index");
?>