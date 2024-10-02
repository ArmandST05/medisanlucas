<?php
class DiagnosticData {
	public static $tablename = "diagnostics";
	public static $reservation_details_tablename = "reservation_details";

	public function __construct(){
		$this->name = "";
		$this->is_selectable_user = "";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (catalog_key,name) ";
		$sql .= "value (\"$this->catalog_key\",\"$this->name\")";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set catalog_key=\"$this->catalog_key\", name=\"$this->name\" where id = $this->id";
		return Executor::doit($sql);
	}

	public function deactivate(){
		$sql = "update ".self::$tablename." set is_active='0' where id = $this->id";
		return Executor::doit($sql);
	}

	public function delete(){
		$sql = "delete from ".self::$tablename." where id = $this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new DiagnosticData());
	}

	public static function getBySearch($name){
		$sql = "SELECT CONCAT(id,'|',catalog_key,'|',name) AS id,CONCAT(catalog_key,'| ',name) AS text FROM ".self::$tablename." WHERE (catalog_key like '%$name%' OR name like '%$name%') AND is_active = 1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DiagnosticData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename."
		WHERE is_active = 1
		ORDER BY name ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DiagnosticData());
	}

	public static function getByEpidemiologicalCode($epidemiologicalCode){
		$sql = "SELECT * FROM ".self::$tablename."
		WHERE is_active = 1
		AND EPI_CLAVE = '$epidemiologicalCode'
		ORDER BY name ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DiagnosticData());
	}

	public static function getAllEpidemiologicalCodes(){
		$sql = "SELECT EPI_CLAVE as code FROM ".self::$tablename." 
		WHERE EPI_CLAVE != 'NO'
		GROUP BY EPI_CLAVE
		ORDER BY name ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DiagnosticData());
	}

	public static function getAllSuiveNotification(){
		$sql = "SELECT * FROM ".self::$tablename."
		WHERE is_active = '1' 
		AND is_suive_notification ='1'
		ORDER BY name";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DiagnosticData());
	}

	public static function getTotalByDiagnosticIdSexRange($diagnosticId,$sexId,$startDate,$endDate,$startAge,$endAge){
		$sql = "SELECT COUNT(reservation_details.id) AS total
		FROM reservation_details
		INNER JOIN reservations ON reservation_details.reservation_id = reservations.id
		INNER JOIN patients ON reservations.patient_id = patients.id
		WHERE reservation_details.diagnostic_id = '$diagnosticId'
		AND patients.sex_id = '$sexId'
		AND DATE_FORMAT(reservations.date_at,'%Y-%m-%d') >= '$startDate'
		AND DATE_FORMAT(reservations.date_at,'%Y-%m-%d') <= '$endDate'
		AND patients.birthday IS NOT NULL AND patients.birthday != '0000-00-00' 
		AND TIMESTAMPDIFF(YEAR, patients.birthday, DATE_FORMAT(reservations.date_at,'%Y-%m-%d')) >= '$startAge' 
		AND TIMESTAMPDIFF(YEAR, patients.birthday, DATE_FORMAT(reservations.date_at,'%Y-%m-%d')) <= '$endAge'";
	
		$query = Executor::doit($sql);
		return Model::one($query[0],new DiagnosticData());
	}

	
	public static function getTotalByDiagnosticIdSexNonAge($diagnosticId,$sexId,$startDate,$endDate){
		$sql = "SELECT COUNT(reservation_details.id) AS total
		FROM reservation_details
		INNER JOIN reservations ON reservation_details.reservation_id = reservations.id
		INNER JOIN patients ON reservations.patient_id = patients.id
		WHERE reservation_details.diagnostic_id = '$diagnosticId'
		AND patients.sex_id = '$sexId'
		AND DATE_FORMAT(reservations.date_at,'%Y-%m-%d') >= '$startDate'
		AND DATE_FORMAT(reservations.date_at,'%Y-%m-%d') <= '$endDate'
		AND (patients.birthday IS NULL OR patients.birthday = '0000-00-00')";
	
		$query = Executor::doit($sql);
		return Model::one($query[0],new DiagnosticData());
	}

	//RESERVATIONS
	public static function getAllByReservationId($reservationId){
		//Obtiene todos los nombres de exámenos físicos y los datos del paciente en caso de registrar un examen en la cita.
	    $sql = "SELECT ".self::$tablename.".id,".self::$tablename.".catalog_key, ".self::$tablename.".name,".self::$reservation_details_tablename.".id AS reservation_detail_id,
				".self::$reservation_details_tablename.".value
				FROM ".self::$tablename.",".self::$reservation_details_tablename."
				WHERE ".self::$tablename.".id = ".self::$reservation_details_tablename.".diagnostic_id
				AND ".self::$reservation_details_tablename.".reservation_id = '$reservationId'
				ORDER BY ".self::$tablename.".name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new DiagnosticData());
	}

	public static function getByDiagnosticReservation($reservationId,$diagnosticId){
		$sql = "SELECT * FROM ".self::$reservation_details_tablename." 
			WHERE reservation_id = '$reservationId' 
			AND diagnostic_id = '$diagnosticId' LIMIT 1";
			$query = Executor::doit($sql);
		return Model::one($query[0],new MedicineData());
	}

	public function addByReservation(){
		$sql = "insert into ".self::$reservation_details_tablename." (reservation_id,diagnostic_id)";
		$sql .= "value (\"$this->reservation_id\",\"$this->diagnostic_id\")";
		return Executor::doit($sql);
	}

	public static function getByReservation($reservation_detail_id){
		//Obtener registro de la tabla pivot de diagnósticos-reservación por id
		$sql = "select * from ".self::$reservation_details_tablename." where id = '$reservation_detail_id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new DiagnosticData());
	}

	public function updateByReservation(){
		$sql = "update ".self::$reservation_details_tablename." set value=\"$this->value\" where id = '$this->id'";
		return Executor::doit($sql);
	}

	public function deleteByReservation($reservationDiagnosticId){
		$sql = "delete from ".self::$reservation_details_tablename." WHERE id = $reservationDiagnosticId";
		return Executor::doit($sql);
	}
}

?>