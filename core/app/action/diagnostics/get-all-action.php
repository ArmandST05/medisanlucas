<?php
$conn = Database::getCon();

/* Database connection end */

//Storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
    1 => 'catalog_key', 
    2 => 'name', 
);

// getting total number records without any search
$sql = "SELECT * FROM diagnostics WHERE is_active ='1'";

$query=mysqli_query($conn, $sql) or die("./?action=patients: get InventoryItems");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if(!empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT * ";
	$sql.=" FROM diagnostics ";
	$sql.=" WHERE is_active ='1' AND (";
	$sql.=" catalog_key LIKE '%".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR name LIKE '".$requestData['search']['value']."%')";
	$query=mysqli_query($conn, $sql) or die("./?action=patients: get PO");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("./?action=patients: get PO"); // again run query with limit
	
} else {	
	$sql = "SELECT * ";
	$sql.=" FROM diagnostics WHERE is_active ='1'";
	$sql.=" ORDER BY name ASC  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("./?action=patients: get PO");
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row["catalog_key"];
    $nestedData[] = $row["name"];
	$nestedData[] = '<td>
				<!--<a href="index.php?view=diagnostics/edit&id='.$row["id"].'" rel="tooltip" title="Editar" class="btn btn-simple btn-warning btn-xs"><i class="fas fa-pencil-alt"></i></a>
				<a href="index.php?action=diagnostics/deactivate&id='.$row["id"].'" rel="tooltip" title="Eliminar" onClick="return confirmar()" class=" btn-simple btn btn-danger btn-xs"><i class="far fa-trash-alt"></i></a>-->
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
