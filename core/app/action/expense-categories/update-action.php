<?php
if(count($_POST)>0){
	$category = ExpenseCategoryData::getById($_POST["id"]);
	$category->name = $_POST["name"];
	$category->update();
	print "<script>window.location='index.php?view=expense-categories/index';</script>";
}
?>