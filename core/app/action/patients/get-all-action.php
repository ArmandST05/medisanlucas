<?php
$conn = Database::getCon();
$user_id = isset($_SESSION["user_id"])  ? $_SESSION["user_id"] : null ;

$user = UserData::getLoggedIn();
$user_type = $user->user_type;
/* Database connection end */

//Storing  request (ie, get/post) global array to a variable  
$requestData = $_REQUEST;

$columns = array( 
// datatable column index  => database column name
	0 => 'id',
    1 => 'name', 
	2 => 'street',
	3 => 'cellphone',
	4 => 'email',
    5 => 'referred_by',
	6 => 'category_id',
	7 => 'relative_name' 
);

// getting total number records without any search
$sql = "SELECT p.*,pc.name AS patient_category_name, pc.color AS patient_category_color FROM patients p, patient_categories pc WHERE p.category_id = pc.id";

//mysqli_report (MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$query = mysqli_query($conn, $sql) or die("Error en la consulta.");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

if( !empty($requestData['search']['value']) ) {
	// if there is a search parameter
	$sql = "SELECT p.*,pc.name as patient_category_name, pc.color as patient_category_color ";
	$sql.=" FROM patients p, patient_categories pc ";
	$sql.=" WHERE p.category_id = pc.id AND (";
	$sql.=" p.name LIKE '%".$requestData['search']['value']."%' ";    // $requestData['search']['value'] contains search parameter
	$sql.=" OR p.cellphone LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR p.email LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR p.relative_name LIKE '".$requestData['search']['value']."%' )";

	$query=mysqli_query($conn, $sql) or die("Error en la consulta.");
	$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result without limit in the query 

	$sql.=" ORDER BY id DESC   LIMIT ".$requestData['start']." ,".$requestData['length']."   "; // $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc , $requestData['start'] contains start row number ,$requestData['length'] contains limit length.
	$query=mysqli_query($conn, $sql) or die("Error en la consulta."); // again run query with limit
	
} else {	
	$sql = "SELECT p.*,pc.name as patient_category_name, pc.color as patient_category_color ";
	$sql.=" FROM patients p, patient_categories pc";
	$sql.=" WHERE p.category_id = pc.id ";
	$sql.=" ORDER BY id DESC  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query=mysqli_query($conn, $sql) or die("Error en la consulta.");	
}

$data = array();
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

	$nestedData[] = $row["id"];
    $nestedData[] = $row["name"];
	$nestedData[] = $row["street"];
	$nestedData[] = $row["cellphone"];
	$nestedData[] = $row["email"];
	$nestedData[] = $row["relative_name"];
    $nestedData[] = $row["referred_by"];
	$nestedData[] = "<td style=background-color:".$row['patient_category_color']."; ><b>".$row['patient_category_name']."</b></td>";

   if($user_type=="su"){
	$nestedData[] = '<td>
					<button class="btn btn-primary btn-xs" onclick="openStartQuickReservation('.$row["id"].')"><i class="fas fa-plus"></i>Iniciar consulta</button>
					<a href="index.php?view=patients/medical-record&patientId='.$row["id"].'" class="btn btn-default btn-xs"><i class="fas fa-folder-open"></i> Expediente</a>
					<a href="index.php?view=patients/account-status&patientId='.$row["id"].'&name='.$row["name"].'" class="btn btn-success btn-xs"><i class="fa fa-dollar-sign"></i> Estado de cuenta</a>
					<br><a href="index.php?view=reservations/patient-history&patientId='.$row["id"].'&name='.$row["name"].'" class="btn btn-info btn-xs"><i class="fas fa-history"></i> Historial</a>
					<a href="index.php?view=patients/edit&id='.$row["id"].'" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a>
					<button class="btn btn-danger btn-xs" onclick="deletePatient('.$row["id"].',`'.$row["name"].'`)"><i class="fas fa-trash"></i> Eliminar</button>  
				</td>';	
	}
	elseif($user_type=="mg"){
		$nestedData[] = '<td >
					<a href="index.php?view=patients/medical-record&patientId='.$row["id"].'" class="btn btn-default btn-xs"><i class="fas fa-folder-open"></i> Expediente</a>
					<a href="index.php?view=patients/account-status&patientId='.$row["id"].'&name='.$row["name"].'" class="btn btn-success btn-xs"><i class="fa fa-dollar-sign"></i> Estado de cuenta</a>
					<br><a href="index.php?view=reservations/patient-history&patientId='.$row["id"].'&name='.$row["name"].'" class="btn btn-info btn-xs"><i class="fas fa-history"></i> Historial</a>
					<a href="index.php?view=patients/edit&id='.$row["id"].'" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a>
					<button class="btn btn-danger btn-xs" onclick="deletePatient('.$row["id"].',`'.$row["name"].'`)"><i class="fas fa-trash"></i> Eliminar</button>  
				</td>';	
	}
	else if($user_type=="do"){
		$nestedData[] = '<td >
				<a href="index.php?view=patients/medical-record&patientId='.$row["id"].'" class="btn btn-default btn-xs"><i class="fas fa-folder-open"></i> Expediente</a>
				<a href="index.php?view=reservations/patient-history&patientId='.$row["id"].'&name='.$row["name"].'" class="btn btn-info btn-xs"><i class="fas fa-history"></i> Historial</a>
				</td>';	
	}else{		
		$nestedData[] = '<td >
					<a href="index.php?view=reservations/patient-history&patientId='.$row["id"].'&name='.$row["name"].'" class="btn btn-info btn-xs"><i class="fas fa-history"></i> Historial</a>
					<a href="index.php?view=patients/edit&id='.$row["id"].'" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a>
					</td>';	
	}
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
