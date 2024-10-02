<?php
$product = ProductData::getById($_GET["id"]);
$product->deactivate();

/*
$operations = OperationDetailData::getAllByProductId($_GET["id"]);
foreach ($operations as $operation) {
	$operation->delete();
}
*/
Core::redir("./index.php?view=supplies/index");
?>