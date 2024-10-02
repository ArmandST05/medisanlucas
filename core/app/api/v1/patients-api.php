<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
$method = strtoupper($_SERVER['REQUEST_METHOD']);
$response = [];
$json = file_get_contents('php://input');
$data = json_decode($json);

if($data){
  $user = UserData::validateApi($data->username,$data->authenticationToken);

  if($user){
    switch ($method) {
      case 'POST':
        //Create Patient
        //Validar si el nombre del paciente está registrado
        if($data->name && ($data->sexId && $data->sexId >=1 && $data->sexId<=2) && ($data->categoryId && $data->categoryId >=1 && $data->categoryId<=3)){
          $isRegistered = PatientData::getByName(trim($data->name));
          if(!$isRegistered){  

            $patient = new PatientData();
            $patient->name = trim($data->name);
            $patient->sex_id = $data->sexId;
            $patient->curp = (isset($data->curp)) ? trim($data->curp):null;
            $patient->street = (isset($data->street)) ? trim($data->street):null;
            $patient->number = (isset($data->number)) ? trim($data->number):null;
            $patient->colony = (isset($data->colony)) ? trim($data->colony):null;
            $patient->cellphone = (isset($data->cellphone)) ? trim($data->cellphone):null;
            $patient->homephone = (isset($data->homephone)) ? trim($data->homephone):null;
            $patient->email = (isset($data->email)) ? trim($data->email):null;
            $patient->birthday = (isset($data->birthday)) ? $data->birthday: null;
            $patient->referred_by = (isset($data->referredBy)) ? trim($data->referredBy):null;
            $patient->relative_name = (isset($data->relativeName)) ? trim($data->relativeName):null;
            $patient->category_id = $data->categoryId;
            $patient->image = "";
            $newPatient = $patient->add();

            if($newPatient && $newPatient[1]){
              $response["error"] = false;
              $response["message"] = "Paciente registrado";
              $response["patientId"] = $newPatient[1];
              http_response_code(200);
            }else{
              $response["error"] = true;
              $response["message"] = "Error en el servidor al registrar el paciente";
              http_response_code(500);
            }
          }else{
            $response["error"] = true;
            $response["message"] = "El paciente ya esta registrado";
            http_response_code(403);
          }
        }
        else{     
          $response["error"] = true;
          $response["message"] = "No se recibieron todos los parametros requeridos o son invalidos";
          http_response_code(400);
        }
        break;
      //Update patient
      case 'PUT':
        if($data->patientId && $data->name && ($data->sexId && $data->sexId >=1 && $data->sexId<=2) && ($data->categoryId && $data->categoryId >=1 && $data->categoryId<=3)){
          $patient = PatientData::getById($data->patientId);
          if($patient){
            $patient->name = trim($data->name);
            $patient->sex_id = $data->sexId;
            $patient->curp = (isset($data->curp)) ? trim($data->curp):null;
            $patient->street = (isset($data->street)) ? trim($data->street):null;
            $patient->number = (isset($data->number)) ? trim($data->number):null;
            $patient->colony = (isset($data->colony)) ? trim($data->colony):null;
            $patient->cellphone = (isset($data->cellphone)) ? trim($data->cellphone):null;
            $patient->homephone = (isset($data->homephone)) ? trim($data->homephone):null;
            $patient->email = (isset($data->email)) ? trim($data->email):null;
            $patient->birthday = (isset($data->birthday)) ? $data->birthday: null;
            $patient->referred_by = (isset($data->referredBy)) ? trim($data->referredBy):null;
            $patient->relative_name = (isset($data->relativeName)) ? trim($data->relativeName):null;
            $patient->category_id = $data->categoryId;

            if($patient->update()){
              $response["error"] = false;
              $response["message"] = "Paciente actualizado";
              http_response_code(200);
            }else{
              $response["error"] = true;
              $response["message"] = "Error en el servidor al actualizar el paciente";
              http_response_code(500);
            }
          }else{
            $response["error"] = false;
            $response["message"] = "El paciente no existe";
            http_response_code(404);
          }
        }else{     
          $response["error"] = true;
          $response["message"] = "No se recibieron todos los parametros requeridos o son invalidos";
          http_response_code(400);
        }
        break;
      case 'GET':
        //Get all patients or specific patient
        $response["error"] = true;
        $response["message"] = "Método no autorizado";
        http_response_code(401);
        break;
      default:
        $response["error"] = true;
        $response["message"] = "Petición no válida";
        http_response_code(405);
        break;
    }
  }else{
    $response["error"] = true;
    $response["message"] = "Accion no autorizada, valida tu acceso";
    http_response_code(403);
  }
}else{
  $response["error"] = true;
  $response["message"] = "No se recibieron parametros";
  http_response_code(400);
}
echo json_encode($response);
?>
