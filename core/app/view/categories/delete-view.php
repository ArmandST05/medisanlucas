<?php
$category = Categorydata::getById($_GET["id"]);
$products = ProductData::getAllByTypeId($category->id);
foreach ($products as $product) {
	$product->expense_category_id = null;
	$product->updateCategory();
}

$category->del();
Core::redir("./index.php?view=categories");


?>