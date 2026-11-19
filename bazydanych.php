<?php
abstract class Database{
    public $id;
    public $name;
    public $age;

    public function __construct($id, $name, $age){
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
    }

    

}

class Mysqldb extends Database{

}
