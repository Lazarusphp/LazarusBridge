<?php

namespace LazarusPhp\LazarusBridge;

use App\System\Core\Functions;
use LazarusPhp\LazarusDb\Database\CoreFiles\Database;
use PDO;
use PDOException;

class DbQueries extends Database
{

    public function __construct()
    {
        parent::__construct();
    }

    // Non Static properties
    protected $data = [];
    protected $name;

    // Used with Query Builder and SchemaBuilder to prevent sql Injections.
    protected $param = [];
    protected $stmt;

    // Static Properties.
    protected static $query = [];
    protected static $table;
    protected static $sql = "";

    protected $isSelected = false;

    protected function setParam($name,$value)
    {
        $uid = ":".uniqid($name);
        $this->param["$uid"] = $value;
        return $uid;
    }

    // Bind Params 
     protected function bindParams(): void
    {
        if (!empty($this->param)) {
            // Prepare code
            foreach ($this->param as $key => $value) {
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

    protected function save(string $sql = "")
    {
       $sql = !empty($sql) ? $sql : self::$sql;
        // Functions::dd($this->param);
      try {

            $this->stmt = $this->prepare($sql);
            
            // Bind Params
            $this->bindParams();
            
            if($this->stmt->execute())
            {
            if($this->isSelected === true)
            {
                $this->stmt->closeCursor();
                $this->isSelected = false;
            }
                unset($this->param);
                return $this->stmt;
            }
        } catch (PDOException $e) {
                  throw $e;
        }
   }
}
