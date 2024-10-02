<?php
class ProductTypeData {
	//PRODUCT TYPES
	//CONCEPTO (1)
	//CONCEPTO EGRESOS (2)
	//INSUMOS (3)
	//MEDICAMENTO (4)

	public static $tablename = "product_types";

	public function __construct(){
		$this->name = "";
		$this->created_at = "NOW()";
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name,created_at) ";
		$sql .= "value (\"$this->name\",$this->created_at)";
		Executor::doit($sql);
	}

	public function delete(){
		$sql = "delete from ".self::$tablename." where id = $this->id";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\" where id = $this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductTypeData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename." order by name";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductTypeData());
	}
}

?>