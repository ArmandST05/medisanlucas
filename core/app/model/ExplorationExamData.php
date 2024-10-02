<?php
class ExplorationExamData {
	public static $tablename = "exploration_exams";
	public static $tablenameOptions = "exploration_exam_options";
	public static $tablenameDetails = "reservation_details";

	public function __construct(){
		$this->name = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name) ";
		$sql .= "value (\"$this->name\")";
		return Executor::doit($sql);
	}

	public function delete(){
		$sql = "delete from ".self::$tablename." WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." WHERE id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ExplorationExamData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename." order by name asc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}
	
	public static function getLike($q){
		$sql = "select * from ".self::$tablename." WHERE name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}

	//DATOS DE LA CITA/CONSULTA
	public function addByReservation(){
		$sql = "INSERT INTO ".self::$tablenameDetails." (reservation_id,exploration_exam_id,value) ";
		$sql .= "value (\"$this->reservation_id\",\"$this->exploration_exam_id\",\"$this->value\")";
		return Executor::doit($sql);
	}

	public function updateByReservation(){
		$sql = "UPDATE ".self::$tablenameDetails." set value=\"$this->value\" WHERE id = '$this->id'";
		return Executor::doit($sql);
	}

	public function deleteByReservation(){
		$sql = "DELETE FROM ".self::$tablenameDetails." WHERE id = '$this->id'";
		return Executor::doit($sql);
	}

	public static function getByReservation($id){
		//Obtener si se registró el examen en la cita/consulta
		$sql = "SELECT * FROM ".self::$tablenameDetails." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ExplorationExamData());
	}

	public static function validateByReservation($reservationId,$explorationExamId){
		//Obtener si se registró el examen durante una reservación
		$sql = "SELECT * FROM ".self::$tablenameDetails." WHERE reservation_id = '$reservationId' and exploration_exam_id = $explorationExamId";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ExplorationExamData());
	}

	public static function getAllByTypeReservation($reservationId,$examType){
		//Obtiene todos los nombres de exámenes físicos y los datos del paciente en caso de registrar un examen en la cita.
	    $sql = "SELECT ".self::$tablename.".id, ".self::$tablename.".name, ".self::$tablenameDetails.".value
				FROM ".self::$tablename."
				LEFT JOIN ".self::$tablenameDetails." ON ".self::$tablename.".id = ".self::$tablenameDetails.".exploration_exam_id 
				AND ".self::$tablenameDetails.".reservation_id = '$reservationId'
				WHERE ".self::$tablename.".type_id = '$examType' 
				ORDER BY ".self::$tablename.".ordering ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}

	public static function getByTypeReservation($reservationId,$examType){
		//Obtiene los nombres de exámenos físicos que se han agregado en la cita.
	    $sql = "SELECT ".self::$tablenameDetails.".id, ".self::$tablename.".name, ".self::$tablenameDetails.".value
				FROM ".self::$tablenameDetails."
				INNER JOIN ".self::$tablename." ON ".self::$tablename.".id = ".self::$tablenameDetails.".exploration_exam_id 
				WHERE ".self::$tablename.".type_id = '$examType' 
				AND ".self::$tablenameDetails.".reservation_id = '$reservationId' 
				ORDER BY ".self::$tablename.".name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}

	public static function getByExamIdReservation($reservationId,$explorationExamId){
		//Obtiene los nombres de exámenos físicos que se han agregado en la cita.
	    $sql = "SELECT ".self::$tablenameDetails.".id, ".self::$tablename.".name, ".self::$tablenameDetails.".value
				FROM ".self::$tablenameDetails."
				INNER JOIN ".self::$tablename." ON ".self::$tablename.".id = ".self::$tablenameDetails.".exploration_exam_id 
				WHERE ".self::$tablename.".id = '$explorationExamId' 
				AND ".self::$tablenameDetails.".reservation_id = '$reservationId' ";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ExplorationExamData());
	}

	public static function getLastByPatientType($patientId,$typeId){
		$actualDateTime = date("Y-m-d H:i:s");
		//Obtiene los últimos datos capturados de cierta sección en la consulta para mostrarlos en el expediente del paciente.
	    $sql = "SELECT ".self::$tablename.".name, ".self::$tablenameDetails.".value 
				FROM ".self::$tablename."
				LEFT JOIN ".self::$tablenameDetails." ON ".self::$tablename.".id = ".self::$tablenameDetails.".exploration_exam_id 
				AND ".self::$tablenameDetails.".reservation_id = (SELECT id FROM reservations WHERE date_at <= '$actualDateTime' ORDER BY date_at DESC LIMIT 1)
				LEFT JOIN reservations ON ".self::$tablenameDetails.".reservation_id = reservations.id 
				WHERE reservations.patient_id = '$patientId'
				AND ".self::$tablename.".type_id = '$typeId'
				ORDER BY ".self::$tablename.".ordering ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ExplorationExamData());
	}

}

?>