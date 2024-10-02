<?php
$concept = OperationDetailData::getById($_GET["conceptId"]);	
$concept->delete();
Core::redir("./index.php?view=expenses/edit&id=".$_GET["expenseId"]."");
?>