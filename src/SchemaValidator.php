<?php

namespace LazarusPhp\LazarusBridge;

use App\System\Core\Functions;
use LazarusPhp\LazarusDb\Database\CoreFiles\Database;
use LazarusPhp\LazarusDb\SchemaBuilder\CoreFiles\SchemaCore;
use LazarusPhp\LazarusBridge\DbQueries;
use LazarusPhp\LazarusDb\SchemaBuilder\Schema;
use LazarusPhp\LazarusDb\SchemaBuilder\SchemaErrors;
use PDO;

class SchemaValidator extends DbQueries
{

    // Generate a Tablename

    protected $rows;
    private $column;
    protected $errors = [];

    protected $data = [];
    protected static $tableData =[];

    // Flags
    protected static $hasTable = false;
    protected static $hasColumn = false;
    protected static $hasForeignKey = false;
    protected static $hasIndex = false;
    protected static $hasUnique = false;



public function __construct($table)
{

    // Create new array if value isnt set on load.
    if(!isset(self::$tableData))
    {
        self::$tableData = [];
    }

    parent::__construct();
    // Validate that a table exists
    return $this->hasTable($table);
}



// this method can stay private only needed when the constructor is called
// use getDate("table") method to pull column data

protected function hasTable($table="")
{
        self::$table = (!empty($table)) ? $table : self::$table;

        $this-> isSelected === true;
        $query = "SELECT COUNT(*)";
        $query .= " FROM INFORMATION_SCHEMA.TABLES";
        $query .= " WHERE TABLE_SCHEMA={$this->setParam("db",$_ENV["dbname"])}";
        $query .= " AND TABLE_NAME =  {$this->setParam("table",self::$table)}";
        $query .= " LIMIT {$this->setParam("limit",1)}";
        $result = $this->save($query);
        if ($result && $result->fetchColumn() >= 1) {

            // Set has table flag to true;
            self::$hasTable = true;
                // Set static table name.
            if(!self::$table){
                self::$table = $table;
            }
            $this->data["table"] = "table found";
            // Return result
            return true;
        } else {
            // Set the has Table flag to false.
            echo "no Table found";
            self::$hasTable = false;
            // Gebnerate Errors.
            SchemaErrors::generate("Cannot find Table",["reason"=>"Table does not exist"]);
            // Return false.
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
        $query .= " AND TABLE_NAME = ".$this->setParam("table",self::$table)."";
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
            SchemaErrors::generate("Cannot find Column",["reason"=>"Column does not exist"]);
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

    // Extract the Data From the data array
    public function getData()
    {
        if(!isset($this->data))
        {
            return false;
        }

        // Get Data type based on connection made.
        if(self::$hasTable)
        {
            $type = "table";
        }
        elseif(self::$hasIndex)
        {
            $type = "index";
        }
        elseif(self::$hasColumn)
        {
            $type = "column";       
        }
        elseif(self::$hasForeignKey)
        {
            $type = "foreignKey";
        }
        elseif(self::$hasUnique)
        {
            $type = "unique";
        }
        else
        {
            $type = null;
        }


        if($type === null)
        {
            return (object) $this->data;
        }
        else
        {
            if(!isset($this->data[$type]))
            {
                // Generate Error Here
                return false;
            }
            return (object) $this->data[$type];
        }
    }
}