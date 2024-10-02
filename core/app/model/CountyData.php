<?php
class CountyData {
	public static $tablename = "counties";

	public function __construct(){
		$this->id = "";
		$this->name = "";
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new CountyData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CountyData());
	}
}
?>