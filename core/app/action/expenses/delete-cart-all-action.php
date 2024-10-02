<?php
 unset($_SESSION["expense"]);
 unset($_SESSION["expensePaymentTypes"]);
 print "<script>window.location='index.php?view=expenses/new';</script>";

?>