<?php
$operations = OperationDetailData::getAllByProductId($_GET["id"]);

foreach ($operations as $operation) {
	$operation->delete();
}
$product = ProductData::getById($_GET["id"]);
$product->delete();

Core::redir("./index.php?view=products/index");
?>