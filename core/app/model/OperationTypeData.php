<?php
class OperationTypeData {
	public static $tablename = "operation_types";

	public function __construct(){
		$this->name = "";
	}

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (name) ";
		$sql .= "value (\"$this->name\")";
		Executor::doit($sql);
	}

	public static function delById($id){
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$id";
		Executor::doit($sql);
	}

	public function del(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id)
	{
		$sql = "SELECT * FROM " . self::$tablename . " WHERE id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationDetailData());
	}

	public static function getByName($name)
	{
		$sql = "SELECT * FROM ".self::$tablename." WHERE name=\"$name\"";
		$query = Executor::doit($sql);
		return Model::one($query[0], new OperationDetailData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." order by created_at desc";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new OperationTypeData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$cnt++;
		}
		return $array;
	}


}

?>