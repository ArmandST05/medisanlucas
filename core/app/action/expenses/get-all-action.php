<?php
$conn = Database::getCon();
/* Database connection end */

// storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;
$user = UserData::getLoggedIn();
$userType = $user->user_type;

$columns = array(
    // datatable column index  => database column name
    0 => 'id',
    1 => 'day_name',
    2 => 'date',
    3 => 'total',
    4 => 'description',
    5 => 'pag',
    6 => 'is_invoice',
    7 => 'invoice_number',
    8 => 'bank',
    9 => 'status_id'

);

// getting total number records without any search
$sql = "SELECT o.description,o.bank,o.invoice_number,o.id,o.total,o.created_at,o.is_invoice,o.status_id,
CONCAT(ELT(WEEKDAY(o.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name, 
DATE_FORMAT(o.created_at,'%d/%m/%Y') as date 
FROM operations o 
WHERE operation_type_id = '1' 
AND operation_category_id = '3'";

$query = mysqli_query($conn, $sql) or die("./?action=expenses: get InventoryItems");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if (!empty($requestData['search']['value'])) {
    // if there is a search parameter
    $sql = "SELECT  o.description,o.bank,o.invoice_number,o.id,o.total,o.created_at,o.is_invoice,o.status_id,
    CONCAT(ELT(WEEKDAY(o.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name, 
    DATE_FORMAT(o.created_at,'%d/%m/%Y') as date ";
    $sql .= " FROM operations o";
    $sql .= " WHERE operation_type_id = '1' AND operation_category_id = '3' ";
    $sql .= " AND id LIKE '%" . $requestData['search']['value'] . "%' ";    // $requestData['search']['value'] contains search parameter
    $sql .= " OR invoice_number LIKE '%" . $requestData['search']['value'] . "%' ";
    $sql .= " OR description LIKE '%" . $requestData['search']['value'] . "%' ";
    $query = mysqli_query($conn, $sql) or die("./?action=expenses: get PO");
    $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

    $sql .= " ORDER BY o.created_at DESC  " . $requestData['start'] . " ," . $requestData['length'] . "   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
    $query = mysqli_query($conn, $sql) or die("./?action=expenses: get PO"); // again run query with limit

} else {
    $sql = "SELECT o.description,o.bank,o.invoice_number,o.id,o.total,o.created_at,o.is_invoice,o.status_id,
    CONCAT(ELT(WEEKDAY(o.created_at) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS day_name, 
    DATE_FORMAT(o.created_at,'%d/%m/%Y') as date ";
    $sql .= " FROM operations o";
    $sql .= " WHERE operation_type_id = '1' AND operation_category_id = '3'";
    $sql .= " ORDER BY o.created_at DESC LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
    $query = mysqli_query($conn, $sql) or die("./?action=expenses: get PO");
}

$data = array();
while ($row = mysqli_fetch_array($query)) {  // preparing an array
    $nestedData = array();
    if ($row["status_id"] == 1) {
        $t = "success";
    } else {
        $t = "danger";
    }
    if ($userType == "su") {

        $nestedData[] =  '<td >
               <a href="index.php?action=expenses/delete&id=' . $row["id"] . '" class="btn btn-xs btn-danger" onClick="return confirmar()"><i class="glyphicon glyphicon-trash"></i></a>
                     </td>';
        $nestedData[] =  '<td >
			   <a href="index.php?view=expenses/edit&id=' . $row["id"] . '" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>
		     	     </td>';
    } else {
        $nestedData[] =  '<td ></td>';
        $nestedData[] =  '<td ></td>';
    }


    $nestedData[] = '<td>' . $row["id"] . "</td>";
    $nestedData[] = '<td>' . $row["day_name"] . "</td>";
    $nestedData[] = '<td>' . $row["date"] . "</td>";
    $nestedData[] = '<td>$' . number_format($row["total"], 2) . "</td>";
    $nestedData[] = '<td>' . $row["description"] . "</td>";
    //PAGOS 
    $tPa = "";
    $tBa = "";
    $totalPayment = OperationPaymentData::getTotalByOperationId($row["id"]);
    $nestedData[] = '<td>$' . number_format($totalPayment->total, 2) . '</td>';

    if ($row["is_invoice"] == 1) {
        $nestedData[] =     '<td>
                 <form method="POST" role="form" action="index.php?action=operations/update-is-invoice">
                 <button type="submit" class="btn btn-xs btn-success">Facturar</button>
                 <input type="hidden" name="value" id="value" value="0">
			     <input type="hidden" name="id" value="' . $row["id"] . '">
				 </form></td>';

        $nestedData[] = '<td>
                  <form method="POST" role="form" autocomplete="off" action="index.php?action=operations/update-invoice-number">
                 <input type="text" name="invoice_number" id="invoice_number" value="' . $row["invoice_number"] . '">
			     <input type="hidden" name="id" id="id" value="' . $row["id"] . '">
				 </form></td>';
    } else {
        $nestedData[] = '<td>
        <form method="POST" role="form" action="index.php?action=operations/update-is-invoice">
        <button type="submit" class="btn btn-xs btn-danger">No Facturar</button>
        <input type="hidden" name="value" id="value" value="1">
        <input type="hidden" name="id" value="' . $row["id"] . '">
        </form></td>';
        $nestedData[] = '<td><label>No aplica</label></td>';
    }

    /*ANTES TENÍA VALORES EN ESPECÍFICO, REVISAR SI SE PERMITE CAPTURAR EL NOMBRE DEL BANCO */
    $nestedData[] =  "<td><label>No aplica</label></td>";

    if ($row["status_id"] == 1) {
        $nestedData[] = '<td><b class="success">PAGADO</b></td>';
    } else {
        $nestedData[] = "<td><b>PENDIENTE</b></td>";
    }
    $data[] = $nestedData;
}

$json_data = array(
    "draw"            => intval($requestData['draw']),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
    "recordsTotal"    => intval($totalData),  // total number of records
    "recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
    "data"            => $data   // total data array
);

echo json_encode($json_data);  // send data as json format
