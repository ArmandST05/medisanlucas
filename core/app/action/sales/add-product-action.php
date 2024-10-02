<?php
$saleDetails = OperationDetailData::getBySaleReservation($_POST["saleId"],$_POST["reservationId"]);
$product = ProductData::getById($_POST["productId"]);
$typeId = $product->type_id;

$numSucc = 0;
$isStock = false;
$errors = array();
$isProductAdded = false;
$index = 0;

//Validar si hay suficiente inventario
if ($typeId == "3" || $typeId == "4") {
	$quantity = OperationDetailData::getStockByProduct($_POST["productId"]);

	if ($_POST["quantity"] <= $quantity) {
		$numSucc++;
		$isStock = true;
	} else {
		$error = array("id" => $_POST["productId"], "message" => "No hay suficiente cantidad de producto en inventario.");
		$errors[count($errors)] = $error;
	}
} else $isStock = true;//Conceptos ingresos

if ($isStock == false) {
	$_SESSION["editSaleErrors"] = $errors;
	echo '<script>
		window.location="index.php?view=sales/edit&id=' . $_POST["saleId"] . '";
	</script>';
}
else if ($isStock == true) {
	//Si hay suficiente existencia procedemos a ver si el producto no está repetido para agregarlo.
	foreach ($saleDetails as $detail) {
		if ($detail->product_id == $_POST["productId"]) {
			$isProductAdded = true;
			break;
		}
		$index++;
	}
	if ($isProductAdded == true) {
		echo '<script> 
			alert("El producto ya está en la lista");
			window.location="index.php?view=sales/edit&id=' . $_POST["saleId"] . '";
		</script>';
	}else if($isProductAdded == false) {
		$opDetail = new OperationDetailData();
		$opDetail->product_id = $_POST["productId"];
		$opDetail->operation_type_id = 2;
		$opDetail->operation_id = $_POST["saleId"];
		$opDetail->quantity = $_POST["quantity"];
		$opDetail->price = $_POST["price"];
		$opDetail->date = date("Y-m-d H:i:s");
		$opDetail->reservation_id = $_POST["reservationId"];
		$add = $opDetail->add();
	}
}
print "<script>window.location='index.php?view=sales/edit&id=" . $_POST["saleId"] . "';</script>";
?>