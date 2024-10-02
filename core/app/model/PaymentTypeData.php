<?php
class PaymentTypeData {
	public static $tablename = "payment_types";

	public function __construct(){
		$this->name = "";
		$this->price_in = "";
		$this->price_out = "";
		$this->unit = "";
		$this->user_id = "";
		$this->presentation = "0";
		$this->created_at = "NOW()";
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new PaymentTypeData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY ordering ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new PaymentTypeData());
	}
}
?>