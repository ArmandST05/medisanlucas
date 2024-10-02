<?php
class CategoryMedicData {
	public static $tablename = "medic_categories";


	public function __construct(){
		$this->name = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "INSERT into ".self::$tablename." (name) ";
		$sql .= "value (\"$this->name\")";
		return Executor::doit($sql);
	}

	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." where id=$this->id";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." set name=\"$this->name\" where id=$this->id";
		return Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." where id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new CategoryMedicData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename;
		$query = Executor::doit($sql);
		return Model::many($query[0],new CategoryMedicData());
	}
	
	public static function getLike($q){
		$sql = "SELECT * FROM ".self::$tablename." where name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new CategoryMedicData());
	}
}

?>