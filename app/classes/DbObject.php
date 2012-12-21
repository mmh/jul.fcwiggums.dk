<?php

abstract class DBObject {
	private $key;
	private $data;
	abstract function getKeyField();
	abstract function getTableName();

	function __construct($data = null) {
		if ($data !== null && gettype($data) != 'array') {
			$data = DbConnection::getConnection()->selectRow($this->getTableName(), array($this->getKeyField() => $data));
		}
		if ($data !== null) {
			$this->key = $data[$this->getKeyField()];
			unset($data[$this->getKeyField()]);
		} else {
			$data = array();
		}
		$this->data = $data;
	}
	
	function getKeyFilter() {
		return array($this->getKeyField() => $this->key);
	}
		
	function save() {
		if ($this->key == null) {
			$this->key = DbConnection::getConnection()->insert($this->getTableName(), $this->data);
		} else {
			DbConnection::getConnection()->update($this->getTableName(), $this->data, $this->getKeyFilter());		
		}
	}
	
	function delete() {
		DbConnection::getConnection()->delete($this->getTableName(), $this->getKeyFilter());		
	}
	
	function set($values, $allowed_fields = null) {
		if ($allowed_fields != null) {
			$values = array_intersect_key($values, array_flip($allowed_fields));
		}
		$this->data = array_merge($this->data, $values);
	}
	
	function get($key) {
		return $this->data[$key];
	}

	function getKey() {
		return $this->key;
	}

	function __toString() {
		$result = get_class($this)."(".$this->key."):\n";
		foreach ($this->data as $key => $value) {
			$result .= " [".$key."] ".$value."\n";
		}
		return $result;
    }
    
    static function idsToObjects($class, $ids) {
    	$obj = new $class();
    	$key = $obj->getKeyField();
	    $objects = array();
		foreach ($ids as $id) {
			$objects[] = new $class($id[$key]);
		}
		return $objects;
    }

}
?>