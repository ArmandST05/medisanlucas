<?php
$collectReservations = $_POST["collectReservations"];

foreach ($collectReservations as $reservation) {
	$saleDetails = OperationDetailData::getBySaleReservation($_POST["saleId"], $reservation);

	if (!$saleDetails) {
		OperationData::deleteSaleReservation($_POST["saleId"], $reservation); //Eliminar dato de cita anterior en caso de que exista.
		//Agregar a la venta la cita
		$newSaleReservation = new OperationData();
		$newSaleReservation->operation_id = $_POST["saleId"];
		$newSaleReservation->reservation_id = $reservation;
		$addedOperationReservation = $newSaleReservation->addSaleReservation();
		//Agregar a la venta los productos especificados en la receta
		$products = ReservationData::getProductsByReservation($reservation);
		foreach ($products as $product) {
			$productData = ProductData::getById($product->product_id);
			$typeId = $productData->type_id;
			$typeName = $productData->getType()->name;

			//VALIDAR EXISTENCIAS (Medicamentos/Insumos)
			if ($typeId == 3 || $typeId == 4) {
				$stock = OperationDetailData::getStockByProduct($product->product_id);

				//Si hay stock añadimos, si no registramos error
				if (floatval(1) > $stock) {
					$isStock = false;
					$error = array("id" => $product->product_id, "message" => "No hay suficiente cantidad de producto en inventario.");
					$errors[count($errors)] = $error;
					$_SESSION["editSaleErrors"] = $errors;
				} else $isStock = true;
			} else $isStock = true; //Conceptos ingresos

			if ($isStock == true) {
				$opDetail = new OperationDetailData();
				$opDetail->product_id = $product->product_id;
				$opDetail->operation_type_id = 2;
				$opDetail->operation_id = $_POST["saleId"];
				$opDetail->quantity = 1;
				$opDetail->price = $productData->price_out;
				$opDetail->date = date("Y-m-d H:i:s");
				$opDetail->reservation_id = $reservation;
				$add = $opDetail->add();

			}
		}
	}
}

echo '<script>window.location="index.php?view=sales/edit&id=' . $_POST["saleId"] . '";</script>';
