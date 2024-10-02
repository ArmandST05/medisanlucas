<?php
$conn = Database::getCon();
/* Database connection end */

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'barcode',
    1 => 'name', 
	2 => 'price_in',
	3 => 'price_out',
    4 => 'minimum_inventory',
    5 => 'is_active'   
);

// getting total number records without any search
$sql = "SELECT id, barcode, name, price_in, price_out, minimum_inventory, is_active_user,fraction  FROM products WHERE type_id='4' AND is_active = '1'";

$query = mysqli_query($conn, $sql) or die("./?action=products/get-all");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if(!empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT id,barcode, name, price_in, price_out, minimum_inventory, is_active_user,fraction ";
	$sql.=" FROM products";
	$sql.=" WHERE type_id = '4' ";
	$sql.=" AND is_active = '1' ";
	$sql.=" AND name LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR barcode LIKE '".$requestData['search']['value']."%' ";
	
	$query=mysqli_query($conn, $sql) or die("./?action=products: get PO");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("./?action=products: get PO"); // again run query with limit
	
} else {	

	$sql = "SELECT id,barcode, name, price_in, price_out, minimum_inventory, is_active_user,fraction  ";
	$sql.=" FROM products";
	$sql.=" WHERE type_id='4'";
	$sql.=" AND is_active = '1' ";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("./?action=products/get-all: get PO");
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 
	$productName = $row["name"].(($row["fraction"]) ? "<br>FRACCION: ".$row["fraction"]:"");
	$nestedData[] = $row["barcode"];
    $nestedData[] = $productName;
	$nestedData[] = $row["price_in"];
	$nestedData[] = $row["price_out"];
    $nestedData[] = $row["minimum_inventory"];
    $nestedData[] = ($row["is_active_user"]) ? "ACTIVO": "INACTIVO";

    $nestedData[] =  '<td >
							<a href="index.php?view=products/edit&id='.$row["id"].'" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>
							<a href="index.php?action=products/deactivate&id='.$row["id"].'" class="btn btn-xs btn-danger" onclick="return confirmar()"><i class="fa fa-trash"></i></a>
							<!--<a href="index.php?action=products/delete&id='.$row["id"].'" class="btn btn-xs btn-danger" onclick="return confirmar()"><i class="fa fa-trash"></i></a>-->
				     </td>';	
	$data[] = $nestedData;    
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);
echo json_encode($json_data);  // send data as json format
?>
