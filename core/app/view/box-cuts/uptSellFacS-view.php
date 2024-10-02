<?php



    $buy = new OperationDetailData();
	$idSell = $_GET["idSell"];
	$noFac = $_GET["noFac"];


	 $buy->updateInvoiceNumber($idSell,$noFac);	

     print "<script>window.location='index.php?view=getSell&q=".$idSell."';</script>";



?>