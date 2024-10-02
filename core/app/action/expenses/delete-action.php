<?php
$operation = OperationData::getById($_GET["id"]);
$operation->delete();

OperationDetailData::deleteByOperationId($_GET["id"]);
OperationPaymentData::deleteByOperationId($_GET["id"]);

Core::redir("./index.php?view=expenses/index");
?>