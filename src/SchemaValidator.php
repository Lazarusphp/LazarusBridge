<?php

namespace LazarusPhp\LazarusBridge;

use App\System\Core\Functions;
use LazarusPhp\LazarusBridge\DbQueries;
use LazarusPhp\LazarusDb\SchemaBuilder\SchemaErrors;
use PDO;

class SchemaValidator extends DbQueries
{

    // Flags
    protected static $hasTable = false;
    protected static $hasColumn = false;
    protected static $hasForeignKey = false;
    protected static $hasIndex = false;
    protected static $hasUnique = false;



public function __construct()
{
    parent::__construct();
    // self::$table = $table;

    // Validate that a table exists
}



// this method can stay private only needed when the constructor is called
// use getDate("table") method to pull column data

public function hasTable($table="")
{

    $table = !empty($table) ? $table : TABLE;

        $this-> isSelected === true;
        $query = "SELECT COUNT(*)";
        $query .= " FROM INFORMATION_SCHEMA.TABLES";
        $query .= " WHERE TABLE_SCHEMA={$this->setParam("db",$_ENV["dbname"])}";
        $query .= " AND TABLE_NAME =  {$this->setParam("table",$table)}";
        $query .= " LIMIT {$this->setParam("limit",1)}";

        $result = $this->save($query);

        if ($result && $result->fetchColumn() === 1) {
            // Return result
            self::$hasTable = true;
            return true;
        } else {
            return false;
        }
    }

    public function hasColumn(string $column)
    {
        if(self::$hasTable === false)
        {
            SchemaErrors::generate("Failed to load Column",["reason"=>"Sql Error : Table ".self::$table." Does not exist"]);
        }

        $query = "SELECT *";
        $query .= " FROM INFORMATION_SCHEMA.COLUMNS";
        $query .= " WHERE TABLE_SCHEMA = ".$this->setParam("db",$_ENV["dbname"])."";
        $query .= " AND TABLE_NAME = ".$this->setParam("table",TABLE)."";
        $query .= " AND COLUMN_NAME = ".$this->setParam("column",$column)."";

        $result = $this->save($query);
        if($result && $result->rowCount() === 1)
        {
            self::$hasColumn = true;
            $data = $this->validateArray("column");
            
            if(!$data)
            {
                SchemaErrors::generate("Failed to load Column",["reason"=>"Column Array Could not be created"]);
                return false;
            }

                $this->data["column"] = $result->fetch(); 
                return true;
            
           

        }
        else
        {
            self::$hasColumn = false;
            return false;
        }
    }


    public function hasIndexes(string $indexName="",string $indexValue = "")
    {
        if(!self::$hasTable)
        {
            SchemaErrors::generate("Failed to find Index",["reason"=>"Table Doesnt Exist"]);
            return false;
        }

        // Set the Parameters
        $this->isSelected === true;
        $indexName = $indexName;
        $indexValue = $indexValue;
    $query = "SELECT INDEX_NAME, COLUMN_NAME,NON_UNIQUE";
    $query .= " FROM INFORMATION_SCHEMA.STATISTICS ";
     $query .= " WHERE TABLE_SCHEMA = {$this->setParam("db",$_ENV["dbname"])}";
    $query .= " AND NON_UNIQUE = {$this->setParam("nu",1)}";
    $query .= " AND TABLE_NAME = {$this->setParam("table",self::$table)}";
    if(!empty($indexName))
    {
    $query .= " AND INDEX_NAME = {$this->setParam("indexName",$indexName)}";
    }
    
    if(!empty($indexValue) && !empty($indexName)){
    $query .= " AND COLUMN_NAME = {$this->setParam("indexValue",$indexValue)}";
    }

    $result = $this->save($query);
    // Check if array index key exists
    $data = $this->validateArray("index");

    if(!$data)
    {
        SchemaErrors::generate("Failed to find Index",["reason"=>"Index Array Could not be created"]);
        return false;
    }
    
    
    // Return the results
    if($result && $result->rowCount() > 1)
    {
        self::$hasIndex = true;
        $this->data["index"] = $result->fetchAll();
        return true;
    }
    elseif($result && $result->rowCount() === 1)
    {
        $this->data["index"] = $result->fetch();
        self::$hasIndex = true;
    }
    else
    {
        return false;
    }
    
    }

    public function hasUnique($indexName,$indexValue="")
    {
           if(!self::$hasTable)
        {
            SchemaErrors::generate("Failed to find Index",["reason"=>"Table Doesnt Exist"]);
            return false;
        }

        // Set the Parameters
        $this->isSelected === true;
        $indexName = $indexName;
        $indexValue = $indexValue;
    $query = "SELECT INDEX_NAME, COLUMN_NAME,NON_UNIQUE";
    $query .= " FROM INFORMATION_SCHEMA.STATISTICS ";
     $query .= " WHERE TABLE_SCHEMA = {$this->setParam("db",$_ENV["dbname"])}";
    $query .= " AND NON_UNIQUE = {$this->setParam("nu",0)}";
    $query .= " AND TABLE_NAME = {$this->setParam("table",self::$table)}";
    if(!empty($indexName))
    {
    $query .= " AND INDEX_NAME = {$this->setParam("indexName",$indexName)}";
    }
    
    if(!empty($indexValue) && !empty($indexName)){
    $query .= " AND COLUMN_NAME = {$this->setParam("indexValue",$indexValue)}";
    }

    $result = $this->save($query);
    // Check if array index key exists
    $data = $this->validateArray("index");

    if(!$data)
    {
        SchemaErrors::generate("Failed to find Index",["reason"=>"Index Array Could not be created"]);
        return false;
    }
    
    
    // Return the results
    if($result && $result->rowCount() > 1)
    {
        self::$hasIndex = true;
        $this->data["index"] = $result->fetchAll();
        return true;
    }
    elseif($result && $result->rowCount() === 1)
    {
        $this->data["index"] = $result->fetch();
        self::$hasIndex = true;
    }
    else
    {
        return false;
    }

    }

    public function hasForeignKey($name="")
    {
        
    $query =  "SELECT TABLE_SCHEMA, TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME ";
    $query .= "FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE ";
    $query .= "WHERE REFERENCED_TABLE_SCHEMA IS NOT NULL ";
 
    if(!empty($name))
    {
        $param = $this->setParam("name",$name);
        $param2 = $this->setParam("name",$name);
        $query .= " AND CONSTRAINT_NAME = {$param} ";
        $query .= " OR COLUMN_NAME = $param2 ";
    }
              
        $result = $this->save($query);

        $data = $this->validateArray("fk");

        if(!$data)
        {
            SchemaErrors::generate("Failed to find foreign key",["reason"=>"fk Array Could not be created"]);
            return false;
        }

        if($result && empty($name) &&  $result->rowCount() >= 1)
        {
            $this->data["fk"] = $result->fetchAll();
            return true;
        }
        elseif($result && !empty($name) && $result->rowCount() === 1)
        {
            $this->data["fk"] = $result->fetch();
            return true;
        }
        else{
            return false;
        }
    }

    // Extract the Data From the data array
    public function getData($type)
    {
        if(!isset($this->data))
        {
            return false;
        }

        if(isset($this->data[$type]))
        {
            return $this->data[$type];
        }
    }

}