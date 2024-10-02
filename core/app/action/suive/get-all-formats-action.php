<?php
$conn = Database::getCon();
/* Database connection end */

//Storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
    1 => 'year', 
	2 => 'week_number',
	3 => 'start_date',
	4 => 'end_date'
);

// getting total number records without any search
$sql = "SELECT suive_formats.*, DATE_FORMAT(suive_formats.start_date,'%Y') AS year FROM suive_formats";

//mysqli_report (MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$query = mysqli_query($conn, $sql) or die("Error en la consulta.");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT suive_formats.*, DATE_FORMAT(suive_formats.start_date,'%Y') AS year, ";
	$sql.=" DATE_FORMAT(start_date,'%d/%m/%Y') AS start_date_format, ";
	$sql.=" DATE_FORMAT(end_date,'%d/%m/%Y') AS end_date_format ";
	$sql.=" FROM suive_formats ";
	$sql.=" WHERE (";
	$sql.=" DATE_FORMAT(start_date,'%d/%m/%Y') LIKE '%".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR DATE_FORMAT(end_date,'%d/%m/%Y') LIKE '".$requestData['search']['value']."%')";
	$query = mysqli_query($conn, $sql) or die("Error en la consulta.");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY id DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("Error en la consulta."); // again run query with limit
	
} else {	
	$sql = "SELECT suive_formats.*, DATE_FORMAT(suive_formats.start_date,'%Y') AS year ,";
	$sql.=" DATE_FORMAT(start_date,'%d/%m/%Y') AS start_date_format, ";
	$sql.=" DATE_FORMAT(end_date,'%d/%m/%Y') AS end_date_format ";
	$sql.=" FROM suive_formats ";
	$sql.=" ORDER BY start_date DESC LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("Error en la consulta.");	
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row["id"];
    $nestedData[] = $row["year"];
	$nestedData[] = $row["week_number"];
	$nestedData[] = $row["start_date_format"];
	$nestedData[] = $row["end_date_format"];
	if($row["path"]){
	$nestedData[] = '<td >
					<a href="storage_data/suive/'.$row["path"].'" target="_blank" class="btn btn-default btn-xs"><i class="fas fa-file"></i> '.$row["path"].'</a>
				</td>';
	}
	else{
		$nestedData[] = '<td></td>';
	}
	$nestedData[] = '<td >
					<a href="index.php?view=suive/report-suive-1&id='.$row["id"].'" target="_blank" class="btn btn-default btn-xs"><i class="fas fa-eye"></i> Reporte Fechas</a>
					<a href="index.php?view=suive/edit&id='.$row["id"].'" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a>
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
