<?php

namespace LazarusPhp\LazarusBridge;

use App\System\Core\Functions;
use LazarusPhp\LazarusDb\Database\CoreFiles\Database;
use LazarusPhp\LazarusDb\SchemaBuilder\SchemaErrors;
use PDO;
use PDOException;

class DbQueries extends Database
{

    // Non Static properties
    protected $data = [];
    protected $isSelected = false;
    protected $name;
    // Used with Query Builder and SchemaBuilder to prevent sql Injections.
    protected static $param = [];
    // Match Against a set of arrays to support Binding.
    protected $stmt;

    // Static Properties.
    protected static $query = [];
    protected static $table;
    protected static $sql = "";

    public function __construct()
    {
        self::$table = "";
        parent::__construct();
    }

    protected function setParam($name, $value)
    {
        $id = uniqid(":" . $name . "_");
        self::$param[$id] = $value;
        return $id;
    }

    protected function validateArray(string $name): array
    {
        if(!is_array($this->data))
        {
            $this->data = [];
        }

        if(!in_array($name,$this->data) && !isset($this->data[$name]))
        {
            $this->data[] = $name; 
        }
        return $this->data;
     }

    protected function bindParams(): void
    {
        if (!empty(self::$param)) {
            // Prepare code
            foreach (self::$param as $key => $value) {
                $type = $this->getParamType($value);
                $this->stmt->bindValue($key, $value, $type);
            }
        }
    }

    // Get the Param Type
    protected function getParamType($value)
    {
        switch ($value) {
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case is_null($value):
                return PDO::PARAM_NULL;
            case is_int($value):
                return PDO::PARAM_INT;
            case is_string($value):
                return PDO::PARAM_STR;
            default;
                break;
        }
    }

    // Unbind
    private function unbind()
    {
        self::$param = [];
    }


    // Save functions.
    protected function save(string $sql = "")
    {
        $sql = !empty($sql) ? $sql : self::$sql;
        try {
            $this->stmt = $this->prepare($sql);
            if (!empty(self::$param) && count(self::$param)) $this->bindParams();
            $this->stmt->execute();

            if ($this->isSelected === true) {
                $this->stmt->closeCursor();
            }
            $this->unbind();

            return $this->stmt;
        } catch (PDOException $e) {
            SchemaErrors::generate("Cannot Write Schema", ["reason" => $e->getMessage()]);
        }
    }
}
