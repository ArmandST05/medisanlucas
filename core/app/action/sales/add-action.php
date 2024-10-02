<?php
if (isset($_SESSION["cart"])) {
	$cart = $_SESSION["cart"];
	$totalProducts = 0;
	if (count($cart) > 0) {
		/// antes de proceder con lo que sigue vamos a verificar que:
		// haya existencia de productos
		// si se va a facturar la cantidad a facturar debe ser menor o igual al producto facturado en inventario
		$numSuccessfully = 0;
		$process = false;
		$errors = array();

		foreach ($cart as $reservationCart) {
			if($reservationCart && count($reservationCart) > 0 ){
				$totalProducts += count($reservationCart);
				foreach ($reservationCart as $cartProduct) {
					$quantity = OperationDetailData::getStockByProduct($cartProduct["id"]);
					if ($cartProduct["typeId"] == "1") {
						//Comisiones
						$numSuccessfully++;
					} else if ($cartProduct["quantity"] <= $quantity) {
						$stock = OperationDetailData::getStockByProduct($cartProduct["id"]);
						if ($cartProduct["quantity"] <= $stock) {
							$numSuccessfully++;
						} else {
							$error = array("id" => $cartProduct["id"], "message" => "No hay suficiente cantidad de producto en inventario.");
							$errors[count($errors)] = $error;
						}
					} else {
						$error = array("id" => $cartProduct["id"], "message" => "No hay suficiente cantidad de producto en inventario.");
						$errors[count($errors)] = $error;
					}
				}
			}
		}

		if ($totalProducts > 0 && $numSuccessfully == $totalProducts) {
			$process = true;
		}
		if ($process == false) {
			$_SESSION["errors"] = $errors;
			echo '<script>
				window.location="index.php?view=sales/new-details&patientId=' . $_POST["patientId"] . '&date=' . $_POST["date"] . '";
			</script>';
		} else if ($process == true) {
			$sale = new OperationData();
			$sale->user_id = $_SESSION["user_id"];
			$sale->total = $_POST["total"];
			$sale->discount = $_POST["discount"];
			$sale->discount_percentage = $_POST["discountPercentage"];
			$sale->description = $_POST["description"];
			$sale->date = $_POST["date"];

			$liquidated = floatval($_POST["total"]) - floatval($_POST["totalPayment"]);

			if ($liquidated <= 0) $sale->status_id = 1; //Liquidado
			else $sale->status_id = 0;

			if (isset($_POST["patientId"]) && $_POST["patientId"] != "") {
				$sale->patient_id = $_POST["patientId"];
				$newSale = $sale->addSale($_POST["patientId"]);
			}

			if (isset($newSale) && $newSale[1]) {
				//Add products
				foreach ($cart as $indexReservation =>$reservationCart) {
					if(isset($indexReservation) && $reservationCart && count($reservationCart) > 0){
						$newSaleReservation = new OperationData();
						$newSaleReservation->operation_id = $newSale[1];
						$newSaleReservation->reservation_id = $indexReservation;
						$addedOperationReservation = $newSaleReservation->addSaleReservation();
					}

					foreach ($reservationCart as $cartProduct) {
						$opDetail = new OperationDetailData();
						$opDetail->product_id = $cartProduct["id"];
						$opDetail->operation_type_id = 2;
						$opDetail->operation_id = $newSale[1];
						$opDetail->quantity = $cartProduct["quantity"];
						$opDetail->price = $cartProduct["price"];
						$opDetail->date = $_POST["date"];
						$opDetail->reservation_id = $indexReservation;
						$add = $opDetail->add();
					}
				}
				
				unset($_SESSION["cart"]);
				setcookie("selled", "selled");

				//Payments
				if (isset($_SESSION["payments"])) {
					$payments = $_SESSION["payments"];
					foreach ($payments as  $payment) {
						$newPayment = new OperationPaymentData();
						$newPayment->payment_type_id = $payment["id"];
						$newPayment->date = $_POST["date"];
						//En efectivo agregar validación por si paga extra es porque se le dará cambio pero esa cantidad no se registrará en el sistema.
						if ($payment["id"] == 1 && ($payment["quantity"] > floatval($_POST["total"]))) {
							$newPayment->total = $_POST["total"];
						} else {
							$newPayment->total = $payment["quantity"];
						}
						$newPayment->operation_id = $newSale[1];
						$add = $newPayment->add();

						unset($_SESSION["payments"]);
						setcookie("selled", "selled");
					}
				}

				print "<script>window.location='index.php?view=sales/details&id=$newSale[1]';</script>";
			}
		}
	}
	print "<script>window.location='index.php?view=sales/new';</script>";
}
