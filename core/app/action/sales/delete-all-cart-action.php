<?php
 unset($_SESSION["cart"]);
 unset($_SESSION["payments"]);

print "<script>window.location='index.php?view=home';</script>";
?>