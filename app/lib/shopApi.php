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
        $data = array();
        $res = $this->link->query("SELECT articul,price,cost FROM products WHERE articul IN (".implode(',', $id_list).")");  
        if (!$this->link->errno){
            $res->data_seek(0);
            while ($row = $res->fetch_assoc()) {
                $data[$row['articul']] = array("price" => (int)$row['price'],"cost"  => (int)$row['cost']);
            }                         
        }
        return $data;
    }
}
