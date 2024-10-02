<?php
$collectReservations = $_POST["collectReservations"];

foreach($collectReservations as $reservation){
	if(!isset($_SESSION["cart"][$reservation])){

		$products = ReservationData::getProductsByReservation($reservation);
		foreach($products as $product){
			$productData = ProductData::getById($product->product_id);
			$typeId = $productData->type_id;
			$typeName = $productData->getType()->name;

			//VALIDAR EXISTENCIAS (Medicamentos/Insumos)
			if ($typeId == 3 || $typeId == 4) {
				$stock = OperationDetailData::getStockByProduct($_POST["productId"]);

				//Si hay stock añadimos, si no registramos error
				if (floatval(1) > $stock) {
					$isStock = false;
					$error = array("id" => $product->product_id, "message" => "No hay suficiente cantidad de producto en inventario.");
					$errors[count($errors)] = $error;
					$_SESSION["errors"] = $errors;
				}
				else $isStock = true;
			} else $isStock = true;//Conceptos ingresos

			if ($isStock == true) {
				$product = array("id" => $product->product_id, "quantity" => 1, "price" => $productData->price_out, "typeId" => $typeId, "typeName" => $typeName);
				$_SESSION["cart"][$reservation][] = $product;
			}
		}
	}
}

print "<script>window.location='index.php?view=sales/new-details&patientId=" . $_POST["patientId"] . "&date=" . $_POST["date"] . "';</script>";