<?php
if(count($_POST)>0){
	$medicine = MedicineData::getById($_POST["id"]);
	$medicine->generic_name = strtoupper(trim($_POST["genericName"]));
	$medicine->therapeutic_group_name = strtoupper(trim($_POST["therapeuticGroupName"]));
	$medicine->pharmaceutical_form = strtoupper(trim($_POST["pharmaceuticalForm"]));
	$medicine->concentration = strtoupper(trim($_POST["concentration"]));
	$medicine->presentation = strtoupper(trim($_POST["presentation"]));
	$medicine->update();

	setcookie("updatedMedicine","true");
	print "<script>window.location='index.php?view=medicines/index';</script>";
}
?>