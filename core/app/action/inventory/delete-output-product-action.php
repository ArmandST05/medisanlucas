<?php
$operation = OperationDetailData::getById($_GET["operationDetailId"]);
$operation->delete();

Core::redir("./index.php?view=inventory/edit-output&id=".$_GET["operationId"]."");
?>