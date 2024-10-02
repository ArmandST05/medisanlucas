<?php
if(count($_POST)>0){
	$concept = new ProductData();
	$concept->name = $_POST["name"];
	$concept->description = $_POST["description"];
	$concept->price_in = $_POST["priceOut"];
	$concept->price_out = $_POST["priceOut"];
	$concept->addIncomeConcept();
	print "<script>window.location='index.php?view=income-concepts/index';</script>";
}
