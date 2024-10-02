<?php



    $buy = new OperationDetailData();
	$idExp = $_GET["idSell"];
	$valor = $_GET["valor"];

	 $buy->updateIsInvoice($idExp,$valor);	

     print "<script>window.location='index.php?view=getSell&q=".$idExp."';</script>";



?>