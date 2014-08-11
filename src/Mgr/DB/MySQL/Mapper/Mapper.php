<?php

/**
  @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

 */

namespace Mgr\DB\MySQL\Mapper;

/**
 * @name Mapper 
 * @description Handles ORM mapping logic
 * 
 */
class Mapper {

    /**
     *
     * @var \Mgr\DB\PDO $pdo
     * @description the pdo instance
     * 
     * */
    public $pdo;
    public $___recursive;
    public $___deleteColumns;

    public function __construct(\Mgr\DB\PDO\MgrPDO $pdo, $lock = true, $recursive = false, $allowDeleteColums=false) {
        $this->pdo = $pdo;
        $this->___recursive = $recursive;
        $this->___deleteColumns = $allowDeleteColums;

        if (!$lock) {
            $this->updateStructure();
        }
    }

    /**
     * @name getTableName
     * @description returns table name
     * 
     * @return string the table name
     */
    public function getTableName() {
        return end(explode('\\', get_called_class()));
    }

    /**
     * @name getTableExistingColums
     * @description returns table existing colums
     * 
     * @return array existingColums
     */
    private function getTableExistingColums() {
        try {
            $sql = " 
            SELECT
               *
            FROM
               INFORMATION_SCHEMA.COLUMNS
            WHERE 
              TABLE_NAME LIKE :table
              AND table_schema = :databaseName";

            $statement = $this->pdo->prepare($sql);
            $statement->bindParam(':table', $this->getTableName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->bindParam(':databaseName', $this->pdo->getDbName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->execute();

            $result = $statement->fetchAll();
            return $result;
        } catch (PDOException $Exception) {
            throw new MyDatabaseException($Exception->getMessage(), $Exception->getCode());
        }
    }

    /**
     * @name getTableForeignKeys
     * @description returns table foreign keys
     * 
     * @return array existingColums
     */
    private function getTableForeignKeys() {
        try {
            $sql = " 
            SELECT
               *
            FROM
               INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE 
              TABLE_NAME LIKE :table
              AND REFERENCED_TABLE_NAME IS NOT NULL 
              AND REFERENCED_COLUMN_NAME IS NOT NULL 
              AND table_schema = :databaseName";

            $statement = $this->pdo->prepare($sql);
            $statement->bindParam(':table', $this->getTableName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->bindParam(':databaseName', $this->pdo->getDbName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->execute();

            $result = $statement->fetchAll();
            return $result;
        } catch (PDOException $Exception) {
            throw new MyDatabaseException($Exception->getMessage(), $Exception->getCode());
        }
    }

    /**
     * @name getTableIndexes
     * @description returns table indexes
     * 
     * @return array existingColums
     */
    private function getTableIndexes() {
        try {
            $sql = "SELECT 
                     DISTINCT INDEX_NAME
                  FROM 
                     INFORMATION_SCHEMA.STATISTICS
                  WHERE 
                    TABLE_SCHEMA =  :databaseName
                    AND TABLE_NAME =  :table
                  ";

            $statement = $this->pdo->prepare($sql);
            $statement->bindParam(':table', $this->getTableName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->bindParam(':databaseName', $this->pdo->getDbName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->execute();

            $result = $statement->fetchAll();
            return $result;
        } catch (PDOException $Exception) {
            throw new MyDatabaseException($Exception->getMessage(), $Exception->getCode());
        }
    }

    /**
     * @name getProperties
     * @description returns running orm class properties
     * 
     * @return array properties
     */
    private function getProperties() {
        $properties = get_object_vars($this);
        unset($properties["pdo"]);
        unset($properties["___recursive"]);
        unset($properties["___deleteColumns"]);
        
        return $properties;
    }

    /**
     * @name createTable
     * @description returns create table sql script
     * 
     * 
     * @return string sqlCode
     */
    private function createTable() {

        //  save constraits, primary keys etc... to append at the bottom of th script
        $constraits = "";
        $sql = "CREATE TABLE {$this->getTableName()}( ";
        $properties = $this->getProperties();

        // add class properties
        foreach ($properties as $propertyName => $propertyScript) {
            $propArray = $this->convertPropertyToSql($propertyName, $propertyScript);

            //add the propery sql to create table script
            $sql.= $propArray["propertySql"];

            // add posible constraints example primary key etc to buffer to append at the end of the script
            $constraits.=$propArray["constraitsBuffer"];
        }
        $sql.=$constraits;


        $sql.=" ) ENGINE={$properties["__engine"]};";

        //get ORM object properties exept pdo
        $sql = preg_replace("/,\s+\)/", " ) ", $sql);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return true;
        } catch (PDOException $Exception) {
            throw new MyDatabaseException($Exception->getMessage(), $Exception->getCode());
            return false;
        }
    }

    /**
     * @name updateTable
     * @description returns update table sql script
     * 
     * 
     * @return string sqlCode
     */
    private function updateTable() {
        //  save constraits, primary keys etc... to append at the bottom of th script
        $constraits = "";
        $sql = "ALTER TABLE {$this->getTableName()} ";

        //get class properties
        $properties = $this->getProperties();

        //get table existing colums
        $tableExistingColumnes = $this->getTableExistingColums();

        //drop old foreign keys
        $tableForeignKeys = $this->getTableForeignKeys();
        $sql.=" DROP PRIMARY KEY, ";
        foreach ($tableForeignKeys as $foreignKey) {
            $sql.= " drop foreign key {$foreignKey["CONSTRAINT_NAME"]}, ";
        }

        //drop old table indexes
        $tableIndexes = $this->getTableIndexes();
        foreach ($tableIndexes as $index) {
            if ($index["INDEX_NAME"] != "PRIMARY")
                $sql.= " drop INDEX {$index["INDEX_NAME"]}, ";
        }

        foreach ($tableExistingColumnes as $column) {

            // if field exists on table
            if (array_key_exists($column["COLUMN_NAME"], $properties)) {
                $propArray = $this->convertPropertyToSql($column["COLUMN_NAME"], $properties[$column["COLUMN_NAME"]]);

                //add the propery sql to alter table script
                $sql.= "MODIFY " . $propArray["propertySql"];

                // add posible constraints example primary key etc to buffer to append at the end of the script
                $propArray["constraitsBuffer"] = str_replace("INDEX", "ADD INDEX", $propArray["constraitsBuffer"]);
                $propArray["constraitsBuffer"] = str_replace("FOREIGN KEY", "ADD FOREIGN KEY", $propArray["constraitsBuffer"]);
                $constraits.=$propArray["constraitsBuffer"];
                unset($properties[$column["COLUMN_NAME"]]);
            } else {
                //delete field form 
                
                if($this->___deleteColumns)
                   $sql.= "DROP " . $column["COLUMN_NAME"].", ";
            }
        }

        foreach ($properties as $propertyName => $propertyScript) {
            $propArray = $this->convertPropertyToSql($propertyName, $propertyScript);

            //add the propery sql to alter table script
            $sql.= "ADD " . $propArray["propertySql"];

            // add posible constraints example primary key etc to buffer to append at the end of the script
            $propArray["constraitsBuffer"] = str_replace("INDEX", "ADD INDEX", $propArray["constraitsBuffer"]);
            $propArray["constraitsBuffer"] = str_replace("FOREIGN KEY", "ADD FOREIGN KEY", $propArray["constraitsBuffer"]);


            $constraits.= $propArray["constraitsBuffer"];
            unset($properties[$column["COLUMN_NAME"]]);
        }

        $sql.=$constraits;

        //think for engine - to be removed
        $sql.=" ENGINE;";
        $sql = preg_replace("/,\s+ENGINE/", " ", $sql);

        //$sql.=" ENGINE={$properties["__engine"]};";
        // $sql = preg_replace("/,\s+ENGINE/", " ENGINE ", $sql);
        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return true;
        } catch (PDOException $Exception) {
            throw new MyDatabaseException($Exception->getMessage(), $Exception->getCode());
            return false;
        }
    }

    /**
     * @name convertPropertyToSql
     * @description converts class to sql script
     * 
     * @param type $name - the column name
     * @param type $script - the volumn sql
     * @return string
     */
    private function convertPropertyToSql($name, $script) {
        $returnScript = array();
        $name = str_replace("$", "", $name);

        // if propery starts width __ do nothing

        if (preg_match("/^__.*/", $name)) {
            $returnScript["constraitsBuffer"] = "";
            $returnScript["propertySql"] = "";
            return $returnScript;
        }


        //check from primary keys
        if (preg_match("/^#.*/", $script)) {
            $returnScript["propertySql"] = $name . " " . trim(str_replace("#", "", $script)) . ", ";
            $returnScript["constraitsBuffer"] = "CONSTRAINT pk PRIMARY KEY({$name}), ";
            return $returnScript;
        }

        //check for indexes
        if (preg_match("/^@.((?!>>).)*$/", $script)) {
            $returnScript["propertySql"] = $name . " " . trim(str_replace("@", "", $script)) . ", ";
            $returnScript["constraitsBuffer"] = "INDEX({$name}), ";
            return $returnScript;
        }



        //check for foreign keys
        if (preg_match("/^@.*>>.*$/", $script)) {

            //extract the script part
            $explode = explode(">>", $script);
            $returnScript["propertySql"] = $name . " " . trim(str_replace("@", "", $explode[0])) . ", ";

            //get the foreign key reference class and the reference field
            $foreignKeyClassExplodes = explode("->", $explode[1]);

            $foreignKeyClassName = trim($foreignKeyClassExplodes[0]);
            $foreignKeyClassField = trim($foreignKeyClassExplodes[1]);
            $foreignKeyOnDeleteOnUpdate = trim($explode[2]);


            //get the reference table name and execute update stracture if ___recursive allowed
            $referenceClass = new $foreignKeyClassName($this->pdo, ($this->___recursive) ? false : true);


            $returnScript["constraitsBuffer"] = "INDEX({$name}), FOREIGN KEY({$name}) REFERENCES {$referenceClass->getTableName()}({$foreignKeyClassField}) {$foreignKeyOnDeleteOnUpdate}, ";
            return $returnScript;
        }


        $returnScript["constraitsBuffer"] = "";
        $returnScript["propertySql"] = $name . " " . trim($script) . ", ";
        return $returnScript;
    }

    /**
     * @name updateStructure
     * @description update table structure
     * 
     * 
     * @return void
     */
    public function updateStructure() {

        //check if table exists

        try {
            $sql = "      
                SELECT 
                   table_name 
                FROM 
                   INFORMATION_SCHEMA.TABLES
                WHERE 
                   table_schema = :databaseName
                AND 
                   table_name = :table
               ";

            $statement = $this->pdo->prepare($sql);
            $statement->bindParam(':table', $this->getTableName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->bindParam(':databaseName', $this->pdo->getDbName(), \Mgr\DB\PDO\MgrPDO::PARAM_STR, 150);
            $statement->execute();

            $result = $statement->fetchAll();

            // if table found
            if ($result)
                $this->updateTable();
            else
                $this->createTable();

            return true;
        } catch (PDOException $Exception) {
            throw new MyDatabaseException($Exception->getMessage(), $Exception->getCode());
            return false;
        }
    }


}
