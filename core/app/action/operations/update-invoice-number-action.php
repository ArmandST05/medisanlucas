<?php
/*SE UTILIZA EN LAS VISTAS DE VENTAS(INGRESOS) Y GASTOS */
    $operation = OperationData::getById($_POST["id"]);
	$operation->invoice_number = $_POST["invoice_number"];
	$operation->updateInvoiceNumber();

     print "<script>window.history.back();</script>";
?>