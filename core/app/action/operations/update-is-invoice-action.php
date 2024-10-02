<?php
/*SE UTILIZA EN LAS VISTAS DE VENTAS(INGRESOS) Y GASTOS */
    $operation = OperationData::getById($_POST["id"]);
	$operation->value = $_POST["value"];
	$operation->updateIsInvoice();	

    print "<script>window.history.back();</script>";
?>