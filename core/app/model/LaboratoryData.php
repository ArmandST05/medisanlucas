<?php
class LaboratoryData {
	public static $tablename = "laboratories";

	public $name;
	public $id;
	public $created_at;
	public $is_active;

	public function __construct(){
		$this->id = "";
		$this->name = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (name) ";
		$sql .= "VALUE (\"$this->name\")";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." set name=\"$this->name\",is_active=\"$this->is_active\" WHERE id=$this->id";
		return Executor::doit($sql);
	}

	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new LaboratoryData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY is_active,name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new LaboratoryData());
	}

	public static function getByStatus($statusId){
		$sql = "SELECT * FROM ".self::$tablename." WHERE is_active = '$statusId'
		ORDER BY name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new LaboratoryData());
	}


	public static function getLike($q){
		$sql = "SELECT * FROM ".self::$tablename." WHERE name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new LaboratoryData());
	}

	public static function getAvailableByDate($startDateTime,$endDateTime,$reservationId = 0){
		//Obtiene los consultorios que no estén vinculados a una cita en el mismo horario
		$sql = "SELECT l.* 
		FROM ".self::$tablename." l
		WHERE l.is_active = 1
		AND (SELECT r.laboratory_id FROM ".ReservationData::$tablename." r
		WHERE r.laboratory_id = l.id
		AND (r.date_at BETWEEN '$startDateTime' AND '$endDateTime'
		OR r.date_at_final BETWEEN '$startDateTime' AND '$endDateTime')
		AND r.id != '$reservationId' LIMIT 1) IS NULL";
		$query = Executor::doit($sql);
		return Model::many($query[0],new LaboratoryData());
	}

}

?>