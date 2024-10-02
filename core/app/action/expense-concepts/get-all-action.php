<?php
$conn = Database::getCon();
/* Database connection end */

// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'name',
    1 => 'category' 
);

// getting total number records without any search
$sql = "SELECT con.id,con.name,cat.name category_name FROM products con, expense_categories cat WHERE con.expense_category_id = cat.id AND con.type_id='2'";

$query=mysqli_query($conn, $sql) or die("./?action=expense-concepts/get-all: get InventoryItems");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT con.id,con.name,cat.name category_name";
	$sql.=" FROM products con, expense_categories cat";
	$sql.=" WHERE con.expense_category_id = cat.id AND con.type_id='2' ";
	$sql.=" AND (con.name LIKE '".$requestData['search']['value']."%' "; 
	$sql.=" OR cat.name LIKE '".$requestData['search']['value']."%') ";
	$query=mysqli_query($conn, $sql) or die("./?action=expense-concepts/get-all: get PO");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("./?action=expense-concepts/get-all: get PO"); // again run query with limit
	
} else {	
	$sql = "SELECT con.id,con.name,cat.name category_name";
	$sql.=" FROM products con, expense_categories cat";
	$sql.=" WHERE con.expense_category_id=cat.id";
	$sql.=" AND con.type_id='2'";
	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("./?action=expense-concepts/get-all: get PO");
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row["name"];
	$nestedData[] = $row["category_name"];
	
    $nestedData[] =  '<td >
			   <a href="index.php?view=expense-concepts/edit&id='.$row["id"].'" class="btn btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
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
