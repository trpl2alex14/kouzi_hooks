<?php

class shopApi {
    var $dblogin = SHOP_DB_USER; 
    var $dbpass  = SHOP_DB_PASS; 
    var $db      = SHOP_DB_NAME; 
    var $dbhost  = SHOP_DB_HOST;

    var $link;
    
    protected static $_instance;

    public static function getInstance(){
        if( self::$_instance === NULL ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }    
        
    public function __construct() {
        $this->connect();
    }
    
    public function connect() {
        $this->link = new mysqli($this->dbhost, $this->dblogin, $this->dbpass, $this->db);
        if ($this->link->connect_error) {
            trigger_error('Connect Error (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
            die('Connect Error (' . $this->link->connect_errno . ') ' . $this->link->connect_error);
        }         
        $this->link->query('SET NAMES utf8');
    }    

    public function getProducts($id_list){
        if(!is_array($id_list)){
            $id_list = array($id_list);
        }
        //test        
        return array(
            '30211' => array("price" => 5200,"cost"  => 3500),
            '40411' => array("price" => 6900,"cost"  => 5100),
            '1' => array("price" => 0,"cost"  => 0)
        );
    }
}
