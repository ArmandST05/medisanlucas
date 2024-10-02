<?php
$operation = OperationData::getById($_GET["id"]);
$operations = OperationDetailData::getAllProductsByOperationId($_GET["id"]);
foreach ($operations as $op) {
	$op->delete();
}

$operation->delete();
Core::redir("./index.php?view=inventory/index-outputs");
?>