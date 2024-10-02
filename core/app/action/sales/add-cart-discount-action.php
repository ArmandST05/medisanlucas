<?php
$discount = $_POST["discountSale"];
$_SESSION["cart-discount"] = $_POST["discountSale"];

print "<script>window.location='index.php?view=sales/new-details&patientId=" . $_POST["patientId"] . "&date=" . $_POST["date"] . "';</script>";
