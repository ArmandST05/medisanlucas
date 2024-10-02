<?php

if(count($_POST)>0){
	$concept = ProductData::getById($_POST["id"]);
	$concept->name = $_POST["name"];
	$concept->description = $_POST["description"];
	$concept->price_in = $_POST["priceOut"];
	$concept->price_out = $_POST["priceOut"];
	$concept->updateIncomeConcept();

	print "<script>window.location='index.php?view=income-concepts/index';</script>";
}
?>