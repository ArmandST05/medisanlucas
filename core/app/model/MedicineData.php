<?php
class MedicineData {
	public static $tablename = "medicines";
	public static $reservationDetailsTablename = "reservation_medicines";

	public function __construct(){
		$this->name = "";
		$this->is_selectable_user = "";
	}

	public function add(){
		$sql = "INSERT into ".self::$tablename." (generic_name,therapeutic_group_name,pharmaceutical_form,concentration,presentation,is_patient_editable) ";
		$sql .= "VALUE (\"$this->generic_name\",\"$this->therapeutic_group_name\",\"$this->pharmaceutical_form\",\"$this->concentration\",\"$this->presentation\",\"$this->is_patient_editable\")";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." SET generic_name=\"$this->generic_name\",therapeutic_group_name=\"$this->therapeutic_group_name\",pharmaceutical_form=\"$this->pharmaceutical_form\",
		concentration=\"$this->concentration\",presentation=\"$this->presentation\"
		WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function deactivate(){
		$sql = "update ".self::$tablename." set is_active='0' WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id = $this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new MedicineData());
	}

	public static function getBySearch($name){
		$sql = "SELECT CONCAT(id,'|',generic_name,'|',pharmaceutical_form,'|',concentration,'|',presentation) AS id,CONCAT('(',therapeutic_group_name,') ',generic_name,'|',pharmaceutical_form,'|',concentration,'|',presentation) AS text 
		FROM ".self::$tablename." WHERE (generic_name like '%$name%' OR therapeutic_group_name like '%$name%') AND is_active = 1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicineData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY name WHERE is_active = 1";
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicineData());
	}

	//RESERVATIONS
	public static function getAllByReservationId($reservationId){
		//Obtiene todos los nombres de los medicaments
	    $sql = "SELECT ".self::$tablename.".id,".self::$tablename.".generic_name, ".self::$tablename.".therapeutic_group_name,
				 ".self::$tablename.".pharmaceutical_form,".self::$tablename.".concentration,
				 ".self::$tablename.".presentation,
				".self::$reservationDetailsTablename.".id AS reservation_detail_id,
				".self::$reservationDetailsTablename.".prescription_number,
				".self::$reservationDetailsTablename.".quantity,
				".self::$reservationDetailsTablename.".frequency,
				".self::$reservationDetailsTablename.".duration,
				".self::$reservationDetailsTablename.".description
				FROM ".self::$tablename.",".self::$reservationDetailsTablename."
				WHERE ".self::$tablename.".id = ".self::$reservationDetailsTablename.".medicine_id
				AND ".self::$reservationDetailsTablename.".reservation_id = '$reservationId'
				ORDER BY ".self::$tablename.".generic_name ASC";
		
		$query = Executor::doit($sql);
		return Model::many($query[0],new MedicineData());
	}

	public function addByReservation(){
		$sql = "INSERT into ".self::$reservationDetailsTablename." (reservation_id,medicine_id)";
		$sql .= "value (\"$this->reservation_id\",\"$this->medicine_id\")";
		return Executor::doit($sql);
	}

	public static function getByReservationId($reservationDetailId){
		$sql = "SELECT * FROM ".self::$reservationDetailsTablename." WHERE id = '$reservationDetailId'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new MedicineData());
	}

	public static function getByMedicineReservation($reservationId,$medicineId){
		$sql = "SELECT * FROM ".self::$reservationDetailsTablename." 
			WHERE reservation_id = '$reservationId' 
			AND medicine_id = '$medicineId' LIMIT 1";
			$query = Executor::doit($sql);
		return Model::one($query[0],new MedicineData());
	}

	public function updateByReservation(){
		$sql = "UPDATE ".self::$reservationDetailsTablename." SET $this->column=\"$this->value\" WHERE id = '$this->id'";
		return Executor::doit($sql);
	}

	public function deleteByReservation($reservationMedicineId){
		$sql = "DELETE FROM ".self::$reservationDetailsTablename." WHERE id = $reservationMedicineId";
		return Executor::doit($sql);
	}
}

?>