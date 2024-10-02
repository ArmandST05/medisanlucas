<?php
//Obtener configuración para validar si se agregará una comisión automática cada vez que se registre un pago de tarjeta
$configuration = ConfigurationData::getAll();
$isCardCommission = $configuration["active_card_commission"]->value;
$totalCardCommission = (isset($configuration["card_commission_value"]) ? $configuration["card_commission_value"]->value : 0);
$totalSale = floatval($_POST["totalSale"]);

if (!isset($_SESSION["payments"]) || (isset($_SESSION["payments"]) && count($_SESSION["payments"]) == 0)) {
	//Validar si la cantidad del pago no supera el total de la venta en pagos que no sean en efectivo
	//En el pago de efectivo se permite superar el total para mostrar el cambio
	if ($_POST["paymentType"] == 1 || ($_POST["quantity"] <= $totalSale)) {
		if ($isCardCommission->value && ($_POST["paymentType"] == 2 || $_POST["paymentType"] == 3)) {

			//Forma de pago en tarjeta se calcula el total a pagar + la comisión
			$commissionPrice = $_POST["quantity"] * $totalCardCommission; //Se calcula el precio de la comisión.
			$quantity = $_POST["quantity"] + $commissionPrice; //Se calcula la cantidad del pago.

			//Forma de pago en tarjeta se agrega la comisión como un nuevo artículo en el detalle de venta.
			$cart = $_SESSION["cart"]; //Se obtienen los datos del carrito 
			$newProduct = array("id" => "1", "quantity" => "1", "price" => $commissionPrice, "typeId" => "3", "typeName" => "CONCEPTOS");
			$cart[] = $newProduct;
			$_SESSION["cart"] = $cart; //Se actualiza el detalle de venta agregando la comisión
		} else {
			$quantity = $_POST["quantity"];
		}

		$newPayment = array("id" => $_POST["paymentType"], "quantity" => $quantity);
		$_SESSION["payments"] = array($newPayment);
	} else {
		echo '<script> 
				alert("La cantidad pagada no puede superar el total de la venta.");
				window.location="index.php?view=sales/new-details&patientId=' . $_POST["patientId"] . '&date=' . $_POST["date"] . '";
			</script>';
	}
} else {
	$payments = $_SESSION["payments"]; //Pagos agregados
	$totalPayment = 0;

	//Se verifica que no exista el tipo de pago en la lista.
	foreach ($payments as $payment) {
		$totalPayment += $payment["quantity"];
		if ($payment["id"] == $_POST["paymentType"]) {
			$existingPayment = true;
			break;
			//Si ya se agregó el método de pago redireccionamos y mostramos alerta
			echo '<script> 
					alert("El tipo de pago ya está en la lista.");
					window.location="index.php?view=sales/new-details&patientId=' . $_POST["patientId"] . '&date=' . $_POST["date"] . '";
				</script>';
		}
	}

	//Agregamos el nuevo tipo de pago ya que no se ha agregado.
	if (!isset($existingPayment) || $existingPayment == false) {
		//Validar si la cantidad del pago no supera el total de la venta en pagos que no sean en efectivo
		//En el pago de efectivo se permite superar el total para mostrar el cambio
		$actualTotalPayment = $totalPayment + $_POST["quantity"];
		if ($actualTotalPayment <= $totalSale) {

			if ($isCardCommission->value && ($_POST["paymentType"] == 2 || $_POST["paymentType"] == 3)) {
				//Forma de pago en tarjeta se calcula el total a pagar + la comisión
				$commissionPrice = $_POST["quantity"] * $totalCardCommission; //Se calcula el precio de la comisión.
				$quantity = $_POST["quantity"] + $commissionPrice; //Se calcula la cantidad del pago.

				//Forma de pago en tarjeta se agrega la comisión como un nuevo artículo en el detalle de venta.
				$cart = $_SESSION["cart"]; //Se obtienen los datos del carrito.

				$newProduct = array("id" => "1", "quantity" => "1", "price" => $commissionPrice, "typeId" => "3", "typeName" => "CONCEPTOS");
				$cart[] = $newProduct;
				$_SESSION["cart"] = $cart; //Se actualiza el detalle de venta agregando la comisión
			} else {
				$quantity = $_POST["quantity"];
			}

			$newPayment = array("id" => $_POST["paymentType"], "quantity" => $quantity);
			$payments[] = $newPayment; //Añadir el nuevo pago 
			$_SESSION["payments"] = $payments;
		} else {
			echo '<script> 
				alert("La cantidad pagada no puede superar el total de la venta.");
				window.location="index.php?view=sales/new-details&patientId=' . $_POST["patientId"] . '&date=' . $_POST["date"] . '";
			</script>';
		}
	}
}
print "<script>window.location='index.php?view=sales/new-details&patientId=" . $_POST["patientId"] . "&date=" . $_POST["date"] . "';</script>";
