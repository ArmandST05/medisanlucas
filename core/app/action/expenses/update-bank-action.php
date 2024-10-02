<?php
    $operation = OperationData::getById($_POST["id"]);
	$operation->bank = $_POST["bank"];
	$operation->updateBank();	

     print "<script>window.location='index.php?view=expenses/index';</script>";
?>