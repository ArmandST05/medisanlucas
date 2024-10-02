<?php
if(count($_POST)>0){
  $medicine = new MedicineData();
  $medicine->generic_name = strtoupper(trim($_POST["genericName"]));
	$medicine->therapeutic_group_name = strtoupper(trim($_POST["therapeuticGroupName"]));
  $medicine->pharmaceutical_form = strtoupper(trim($_POST["pharmaceuticalForm"]));
  $medicine->concentration = strtoupper(trim($_POST["concentration"]));
  $medicine->presentation = strtoupper(trim($_POST["presentation"]));
  $medicine->is_patient_editable = 1;//El paciente puede editarlo
  $addedMedicine = $medicine->add();

print "<script>window.location='index.php?view=medicines/index';</script>";
}
?>