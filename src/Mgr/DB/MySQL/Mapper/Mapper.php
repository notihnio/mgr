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

    public $pdo;

    public function __construct($pdo, $lock = false) {
        $this->pdo = $pdo;
        if (!$lock) {
            //extract table name from class Schema!
            $tableName = end(explode('\\', get_called_class()));
            $this->updateStructure($tableName);
        }
    }

    public function updateStructure($tableName) {

        //get ORM object proerties exept pdo
        $properties = get_object_vars($this);
        unset($properties["pdo"]);
        die(var_dump($tableName));


        $slq = "      
            -- First check if the table exists
                        IF EXISTS(SELECT table_name 
                                    FROM INFORMATION_SCHEMA.TABLES
                                   WHERE table_schema = 'db_name'
                                     AND table_name LIKE 'wild')

                        -- If exists, retreive columns information from that table
                        THEN
                           SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                             FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE table_name = 'tbl_name'
                              AND table_schema = 'db_name';

                           -- do some action, i.e. ALTER TABLE if some columns are missing 
                           ALTER TABLE ...

                        -- Table does not exist, create a new table
                        ELSE
                           CREATE TABLE ....

                        END IF;
               ";
    }

    private function convertPropertiesToSql($properties) {
         
        
    }

    public function select() {
        
    }

    public function selectFull() {
        
    }

    public function update() {
        
    }

    public function insert() {
        
    }

    public function delete() {
        
    }

}
