<?php
if(count($_POST)>0){
	$concept = ProductData::getById($_POST["id"]);
	$concept->name = $_POST["name"];
	$concept->expense_category_id = $_POST["category"];
	$concept->updateExpenseConcept();

print "<script>window.location='index.php?view=expense-concepts/index';</script>";

}
?>