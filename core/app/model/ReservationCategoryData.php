<?php
class ReservationCategoryData {
	public static $tablename = "reservation_categories";
	public static $months = ["01" => "Enero", "02" => "Febrero", "03" => "Marzo", "04" => "Abril", "05" => "Mayo", "06" => "Junio", "07" => "Julio", "08" => "Agosto", "09" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];

	public function __construct(){
		$this->created_at = date("Y-m-d H:i:s");
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY name DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ReservationCategoryData());
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ReservationCategoryData());
	}
}
