<?php

interface Sqlinterface {
    public function FindAll($table);
    public function Find($table, $id);
    public function ReadOne($table, $id);
    public function ReadFirstTen($table);
}

abstract class Database {
    protected $connection;

    public function connect() {
        $this->connection = mysqli_connect("localhost", "root", "", "mikudayo");
        return $this->connection;
    }

    abstract public function createDatabase($name);
    abstract public function createTable($sql);
    abstract public function readTable($table);
    abstract public function updateTable($sql, $params);
    // abstract public function deleteTable($table);
}

class Mysqldb extends Database implements Sqlinterface {

    public function createDatabase($name) {
        return mysqli_query($this->connection, "CREATE DATABASE IF NOT EXISTS $name");
    }

    public function createTable($sql) {
        return mysqli_query($this->connection, $sql);
    }

    public function readTable($table) {
        return mysqli_query($this->connection, "SELECT * FROM $table");
    }

    public function updateTable($sql, $params) {
        $stmt = mysqli_prepare($this->connection, $sql);
        $types = "";
        foreach ($params as $param) {
            $types .= is_int($param) ? "i" : "s";
        }
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        return mysqli_stmt_execute($stmt);
    }

    // public function deleteTable($table) {
    //     return mysqli_query($this->connection, "DROP TABLE $table");
    // }

    public function FindAll($table) {
        return $this->readTable($table);
    }

    public function Find($table, $id) {
        $stmt = mysqli_prepare($this->connection, "SELECT * FROM $table WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function ReadOne($table, $id) {
        return mysqli_fetch_assoc($this->Find($table, $id));
    }

    public function ReadFirstTen($table) {
        return mysqli_query($this->connection, "SELECT * FROM $table LIMIT 10");
    }
}

class TestDatabase {
    public function mikudayo($result, $name) {
        echo $result ? "[OK] $name<br>" : "[BLAD] $name<br>";
    }
}

$db = new Mysqldb();
$test = new TestDatabase();

$db->connect();
$test->mikudayo($db->connect(), "Polaczenie");

$db->createDatabase("mikudayo");

mysqli_select_db($db->connect(), "mikudayo");

$db->createTable("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
)");

$test->mikudayo($db->updateTable(
    "INSERT INTO users (name, email) VALUES (?, ?)",
    ["Skibidi Toaleta", "jan@test.pl"]
), "Dodawanie");

$test->mikudayo($db->FindAll("users"), "FindAll");
$test->mikudayo($db->Find("users", 1), "Find");
$test->mikudayo($db->ReadOne("users", 1), "ReadOne");
$test->mikudayo($db->ReadFirstTen("users"), "ReadFirstTen");

$test->mikudayo($db->updateTable(
    "UPDATE users SET name = ? WHERE id = ?",
    ["Jan", 1]
), "Aktualizacja");

$test->mikudayo($db->readTable("users"), "Odczyt");
// $test->mikudayo($db->deleteTable("users"), "Usuwanie");
