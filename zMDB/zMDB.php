<?php
/**
 * zMDB | 0.0.1
 * BY zJerino | https://cuberico.xyz
 */

class DB_MySQL {
    public function __construct($info) {
        if (!isset($info["host"]) || !isset($info["user"]) || !isset($info["db"]) || !isset($info["pass"]) || !isset($info["port"])) return;

        $conn = new mysqli($info["host"], $info["user"], $info["pass"], $info["db"], $info["port"]);
        $this->conn = $conn;
    }
    public function query($sql) {
        $result = mysqli_query($this->conn, $sql);
        if ($result != false) {
            $this->last_query = $result;
            return true;
        } else {
            $this->error = mysqli_error($this->conn);
            return false;
        }
    }
}
class DB {
    public function __construct($info = array()) {
        $this->info = $info;
        $this->db = new DB_MySQL($this->info);
    }
    public function createTable($name, $colunms = array()) {
        $str = "";
        $name_table = $name;
        $str .= "`id` INT(16) NOT NULL AUTO_INCREMENT, ";
        foreach ($colunms as $key => $value) {
            if (is_array($value) && isset($value["name"])){
                $max = 20;
                $othrs = '';

                if (isset($value["max"]) && is_nan($value["max"])) $max = $value["max"];
                if (isset($value["others"])) $othrs = $value["others"];
                $name = $value["name"];
                
                $str .= "`$name` VARCHAR($max) $othrs CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL ,";
            }
        }
        $db = $this->info["db"];
        $prefix = $this->info["prefix"];
        $name_table = $prefix . $name_table;
        $SQL = "CREATE TABLE `$db`.`$name_table` ($str PRIMARY KEY (`id`)) ENGINE = InnoDB;";
        return $this->db->query($SQL);
    }
    public function insert($table = '', $obj = array()) {
        $prefix = $this->info["prefix"];
        $name_table = $prefix . $table;

        $keystr = "";
        $valuestr = "";
        $i = 0;
        $ia = count($obj);

        foreach ($obj as $key => $value) {
            $i++;
            if ($i < $ia) {
                $keystr .= "`$key`, ";
                $valuestr .= "'$value', ";
            } else if ($i == $ia) {
                $keystr .= "`$key`";
                $valuestr .= "'$value'";
            }
        }
        $SQL = "INSERT INTO `$name_table` ($keystr) VALUES ($valuestr)";
        return $this->db->query($SQL);
    }
    public function get($table = '', $key, $value) {
        $prefix = $this->info["prefix"];
        $name_table = $prefix . $table;

        $SQL = "SELECT * FROM `$name_table` WHERE `$key` = '$value'";
        if ($this->db->query($SQL)) {
            return $this->db->last_query->fetch_all()[0];
        } return false;
    }
    public function getAll($table = '') {
        $prefix = $this->info["prefix"];
        $name_table = $prefix . $table;

        $SQL = "SELECT * FROM `$name_table` WHERE 1";
        if ($this->db->query($SQL)) {
            return $this->db->last_query->fetch_all();
        } return false;
    }
    public function where($table = '', $req = array()) {
        $prefix = $this->info["prefix"];
        $name_table = $prefix . $table;

        $query = "";
        $i = 0;
        foreach ($req as $key => $value) {
            $i++;
            if ($i == 1) {
                $query .= "`$key` = '$value' ";
            } else {
                $query .= "AND `$key` = '$value' ";
            }
        }

        $SQL = "SELECT * FROM `$name_table` WHERE $query";
        if ($this->db->query($SQL)) {
            return $this->db->last_query->fetch_all();
        } return false;
    }
    public function update($table, $id, $replace) {
        $prefix = $this->info["prefix"];
        $name_table = $prefix . $table;

        $query = "";
        $i = 0;

        foreach ($replace as $key => $value) {
            $i++;
            if ($i == 1) {
                $query .= "`$key` = '$value' ";
            } else {
                $query .= ", `$key` = '$value' ";
            }
        }

        
        $SQL = "UPDATE `$name_table` SET $query WHERE `$name_table`.`id` = '$id'";
        if ($this->db->query($SQL)) {
            return true;
        } return false;
    }
}