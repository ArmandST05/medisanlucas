<?php
class StateData {
	public static $tablename = "states";

	public function __construct(){
		$this->name = "";
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename." ORDER BY name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new StateData());
	}
}

?>