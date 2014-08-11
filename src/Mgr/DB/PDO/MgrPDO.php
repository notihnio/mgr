<?php

/**
  @author Panagiotis Mastrandrikos <pmastrandrikos@gmail.com>  https://github.com/notihnio

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

 */

namespace Mgr\DB\PDO;


/**
 * @name MgrPDO 
 * @description extends PDO
 * 
 */
class MgrPDO extends \PDO {

    private $dsn;
    

    public function __construct($dsn, $username = null, $password = null, $driver_options = null) {
        $this->dsn = $dsn;
        parent::__construct($dsn, $username, $password, $driver_options);
    }

    //returns db name
    public function getDbName() { 
        $explodes = explode(";", $this->dsn);
        
        foreach ($explodes as $explode) {
            $attr = explode("=", $explode);
            $key= $attr[0];
            $value=$attr[1];
            if($key=="dbname"){
                return $value;
            }
        }
        return $this->query('select database()')->fetchColumn();
    }

}
