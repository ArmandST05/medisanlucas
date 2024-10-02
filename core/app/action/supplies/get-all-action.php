<?php
$conn = Database::getCon();
/* Database connection end */


// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'name',
    1 => 'minimum_inventory'
    
);

// getting total number records without any search
$sql = "SELECT id,name, minimum_inventory  FROM products WHERE type_id='3' AND is_active = '1'";

$query=mysqli_query($conn, $sql) or die("./?action=supplies: get InventoryItems");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT id,name, minimum_inventory ";
	$sql.=" FROM products";
	$sql.=" WHERE type_id='3' ";
	$sql.=" AND is_active = '1' ";
	$sql.=" AND name LIKE '".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$query=mysqli_query($conn, $sql) or die("./?action=supplies: get PO");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("./?action=supplies: get PO"); // again run query with limit
	
} else {	

	$sql = "SELECT id,name, minimum_inventory  ";
	$sql.=" FROM products";
	$sql.=" WHERE type_id='3' ";
	$sql.=" AND is_active = '1' ";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("./?action=supplies: get PO");
	
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row["name"];
	$nestedData[] = $row["minimum_inventory"];
   
    $nestedData[] =  '<td >
			   <a href="index.php?view=supplies/edit&id='.$row["id"].'" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-pencil"></i></a>
			   <a href="index.php?action=supplies/deactivate&id='.$row["id"].'" class="btn btn-xs btn-danger" onclick="return confirmar()"><i class="fas fa-trash"></i></a>
		       <!--<a href="index.php?action=supplies/delete&id='.$row["id"].'" class="btn btn-xs btn-danger" onclick="return confirmar()"><i class="fas fa-trash"></i></a>-->
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
