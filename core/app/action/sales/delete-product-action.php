<?php
$product = OperationDetailData::getById($_GET["detailId"]);	
$product->delete();
$saleDetails = OperationDetailData::getBySaleReservation($product->operation_id,$product->reservation_id);
if(!$saleDetails){//Si ya no hay productos asociados a esa cita de la venta, eliminar la relación
    OperationData::deleteSaleReservation($product->operation_id,$product->reservation_id);
}
Core::redir("./index.php?view=sales/edit&id=".$_GET["saleId"]."");
?>