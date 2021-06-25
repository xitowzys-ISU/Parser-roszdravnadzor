<?php

namespace app\core;

use \PDO;
use \PDOException;

class Database extends PDO { 

    private function __clone(){}

    private function __construct() 
    { 
        try 
        { 
            parent::__construct(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
            ); 
            
            parent::setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        } 
        catch(PDOException $e) 
        { 
            echo $e->getMessage(); 
        } 
    } 
    
    public static function getInstance() 
    { 
        static $instance = false; 

        if(!$instance) 
            $instance = new self; 
            
        return $instance; 
    }
    
}