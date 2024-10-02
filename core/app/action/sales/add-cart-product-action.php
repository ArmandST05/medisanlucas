<?php
/*29-03-2023 Se agregó la funcionalidad para que en las ventas se puedan cobrar varias citas simultáneamente */
$price = $_POST["price"];
$productData = ProductData::getById($_POST["productId"]);
$typeId = $productData->type_id;
$typeName = $productData->getType()->name;

$reservationId = (isset($_POST["reservationId"]))? $_POST["reservationId"]:"0";
$errors = [];

if (isset($_SESSION["cart"])){//Ya existe el carrito de compras
	//Validar si el producto no está repetido para agregarlo.
	$isCartAdded = false;
	$cart = $_SESSION["cart"][$reservationId];

	foreach ($cart as $cartDetail) {
		if ($cartDetail["id"] == $_POST["productId"]) {
			$isCartAdded = true;
			break;
		}
	}

	if ($isCartAdded == true) {
		echo '<script> 
			alert("Ya está en la lista.");
			window.location="index.php?view=sales/new-details&patientId=' . $_POST["patientId"] . '&date=' . $_POST["date"] . '";
		</script>';
	}
}

//VALIDAR EXISTENCIAS (Medicamentos/Insumos)
if ($typeId == 3 || $typeId == 4) {
	$stock = OperationDetailData::getStockByProduct($_POST["productId"]);

	//Si hay stock añadimos, si no registramos error
	if (floatval($_POST["quantity"]) > $stock) {
		$isStock = false;
		$error = array("id" => $_POST["productId"], "message" => "No hay suficiente cantidad de producto en inventario.");
		$errors[count($errors)] = $error;
		$_SESSION["errors"] = $errors;
	}
	else $isStock = true;
} else $isStock = true;//Conceptos ingresos

if ($isStock == true) {
	$product = array("id" => $_POST["productId"], "quantity" => $_POST["quantity"], "price" => $_POST["price"], "typeId" => $typeId, "typeName" => $typeName);
	$_SESSION["cart"][$reservationId][] = $product;
}

print "<script>window.location='index.php?view=sales/new-details&patientId=" . $_POST["patientId"] . "&date=" . $_POST["date"] . "';</script>";
