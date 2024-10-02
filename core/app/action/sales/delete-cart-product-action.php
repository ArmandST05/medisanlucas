<?php
if(isset($_GET["productId"])){
	$cart = $_SESSION["cart"];
	$reservationId = $_GET['reservationId'];
	
		$newReservationCart = null;
		foreach($cart[$reservationId] as $cartDetail){
			if($cartDetail["id"] != $_GET["productId"]){
				$newReservationCart[]= $cartDetail;
			}
		}
		$_SESSION["cart"][$reservationId] = $newReservationCart;
	}

print "<script>window.location='index.php?view=sales/new-details&patientId=".$_GET['patientId']."&date=".$_GET['date']."';</script>";

?>