<?php
$payment = OperationPaymentData::getById($_GET["paymentId"]);
$payment->delete();
Core::redir("./index.php?view=expenses/edit&id=".$_GET["expenseId"]."");

?>