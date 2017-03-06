<?php

class autoLoad{
    public static function loadClass($className){           
        if (file_exists(dirname(__FILE__). '/' .$className . '.php')) {
            require_once($className . '.php');
        }else{
            $filePath = (defined('EVENT_DIR')? EVENT_DIR : dirname(__FILE__) . '/event/') .  $className . '/' . $className . '.Class.php';
            if (file_exists($filePath)) {
              require_once($filePath);
            }            
        }                
    }
 
    public static function autoloadRegister()
    {
        spl_autoload_register('self::loadClass');
    }    
}
