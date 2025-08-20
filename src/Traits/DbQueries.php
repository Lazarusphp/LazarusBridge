<?php
namespace LazarusPhp\LazarusBridge\Traits;

use PDO;
use PDOException;

trait DbQueries
{

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

    // Unbind
    private function unbind()
    {
        $this->param = [];
    }


    // Save functions.
    protected function save(string $sql = "")
    {
       $sql = !empty($sql) ? $sql : self::$sql; 
      try {
            $this->stmt = $this->prepare($sql);
            if (!empty($this->param)) $this->bindParams();
            $this->beginTransaction();
            $this->stmt->execute();
            $this->commit();
            // $this->unbind();
            return $this->stmt;
        } catch (PDOException $e) {
            $this->rollback();
            throw $e->getMessage();
        }
   }
 
}