<?php
if(count($_POST)>0){
	$category = new ExpenseCategoryData();
	$name = $_POST["name"];
	$category->add($name);

	print "<script>window.location='index.php?view=expense-categories/index';</script>";
}
?>