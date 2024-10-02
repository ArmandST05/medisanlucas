<?php
if(count($_POST)>0){
	$concept = new ProductData();
	$concept->name = $_POST["name"];
	$concept->expense_category_id = $_POST["category"];
	$concept->type_id = 2;
	$concept->addExpenseConcept();

	print "<script>window.location='index.php?view=expense-concepts/index';</script>";
}
?>