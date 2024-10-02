<?php
class ReservationStatusData {
	public static $tablename = "reservation_status";

	public function __construct(){
		$this->name = "";
		$this->is_selectable_user = "";
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where ".self::$tablename.".id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ReservationStatusData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename."";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ReservationStatusData());
	}
}

?>