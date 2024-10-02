<?php
class ProductData {
	public static $tablename = "products";

	public function __construct(){
		$this->name = "";
		$this->price_in = "";
		$this->price_out = "";
		$this->unit = "";
		$this->user_id = "";
		$this->presentation = "0";
		$this->created_at = "NOW()";
	}

	public function getExpenseCategory(){ return ProductData::getById($this->expense_category_id);}
	public function getType(){ return ProductTypeData::getById($this->type_id);}

	//PRODUCT TYPES
	//CONCEPTOS INGRESOS(1)
	//CONCEPTOS EGRESOS (2)
	//INSUMOS (3)
	//MEDICAMENTO (4)

	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (barcode,name,price_in,price_out,fraction,user_id,minimum_inventory,type_id,expense_category_id) 
        VALUES (\"$this->barcode\",\"$this->name\",\"$this->price_in\",\"$this->price_out\",\"$this->fraction\",\"$this->user_id\",\"$this->minimum_inventory\",4,8)";
		return Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." SET barcode=\"$this->barcode\",name=\"$this->name\",price_in=\"$this->price_in\",price_out=\"$this->price_out\",minimum_inventory=\"$this->minimum_inventory\",is_active_user=\"$this->is_active_user\" WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function delete(){
		$sql = "DELETE FROM ".self::$tablename." WHERE id = $this->id";
		Executor::doit($sql);
	}

	public function deactivate(){
		$sql = "UPDATE ".self::$tablename." SET is_active = 0 WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function updateCategory(){
		//Actualiza la categoría de un producto.
		$sql = "UPDATE ".self::$tablename." SET expense_category_id = $this->expense_category_id WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function update_image(){
		$sql = "UPDATE ".self::$tablename." SET image=\"$this->image\" WHERE id=$this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = $id";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ProductData());
	}

	public static function getAll($name){
		$sql = "SELECT * FROM ".self::$tablename." WHERE name like '%$name%'  AND (type_id='4' OR type_id='3') AND is_active = 1 
		ORDER BY name DESC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getByBarcode($barcode){
		$sql = "SELECT * FROM ".self::$tablename." WHERE barcode = '$barcode' AND is_active = 1 ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getByName($name){
		$sql = "SELECT * FROM ".self::$tablename." WHERE name='$name' AND is_active = 1 ";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getAllByPage($start_from,$limit){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id>=$start_from AND type_id='4' AND is_active = 1 limit $limit";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	//Obtiene los insumos o medicamentos cuyo nombre coincida con el parámetro de búsqueda
	public static function getLike($p){
		$sql = "SELECT * FROM ".self::$tablename." WHERE (type_id='4' OR type_id='1')  OR (name like '%$p%')";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	//Obtiene los productos de un tipo específico
	public static function getAllByTypeId($typeId){
		$sql = "SELECT * FROM ".self::$tablename." WHERE type_id='$typeId' AND is_active = 1 ORDER BY name";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getAllByUserId($user_id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE user_id=$user_id order by created_at desc";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

    public static function getTypePayId($id){
		$sql = "SELECT * FROM pay  WHERE typePay='EGRESOS' AND idSell='$id'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}
	
	/*-----------CONCEPTOS INGRESOS---------- */
	public function addIncomeConcept(){
		$sql = "INSERT INTO ".self::$tablename." (name,type_id,description,price_in,price_out) value (\"$this->name\",'1',\"$this->description\",\"$this->price_in\",\"$this->price_out\")";
		return Executor::doit($sql);
	}

	public function updateIncomeConcept(){
		$sql = "UPDATE ".self::$tablename." set name=\"$this->name\",description=\"$this->description\",price_in=\"$this->price_in\",price_out=\"$this->price_out\" WHERE id = $this->id";
		return Executor::doit($sql);
	}

	/*----------SUPPLIES------------ */
	public function addSupply(){
		$sql = "INSERT INTO ".self::$tablename." (name,minimum_inventory,user_id,type_id,expense_category_id) 
		VALUES ('$this->name','$this->minimum_inventory','$this->user_id','3',9)";
		return Executor::doit($sql);
	}

	public function updateSupply(){
		//Actualiza un producto de tipo supplies
		$sql = "UPDATE ".self::$tablename." SET name=\"$this->name\",minimum_inventory=\"$this->minimum_inventory\" WHERE id = $this->id";
		Executor::doit($sql);
	}
	
	/*----------EXPENSE CONCEPTS------------ */
	public function addExpenseConcept(){
		$sql = "INSERT INTO ".self::$tablename." (name,expense_category_id,type_id) 
		VALUES ('$this->name','$this->expense_category_id','$this->type_id')";
		return Executor::doit($sql);
	}

	public function updateExpenseConcept(){
		$sql = "UPDATE ".self::$tablename." SET name=\"$this->name\",expense_category_id=\"$this->expense_category_id\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	/*
	//MÉTODOS DE VISTA resupply-view
	public static function getLikeEnt($p){
		$sql = "SELECT * FROM ".self::$tablename." WHERE (type_id='4' OR type_id='3')  OR (name like '%$p%')";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}

	public static function getLikeSal($p){
		$sql = "SELECT * FROM ".self::$tablename." WHERE (barcode like '%$p%' or name like '%$p%' or id like '%$p%') AND (type_id='3')";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ProductData());
	}*/
}
