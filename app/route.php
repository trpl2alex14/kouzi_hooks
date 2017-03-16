<?php

require_once 'config.php';
require_once 'lib/autoLoad.php';
  
AutoLoad::autoloadRegister();

if(isset($_REQUEST['event'])){
    $event = new allEvent();
    $event->route();
}

if(isset($_REQUEST['code'])){
    $action = isset($_REQUEST["action"]) ? htmlspecialchars($_REQUEST["action"]) : "";        
    $biz = new allBizproc();    
    switch($action){
        case 'install':
            $biz->install();
        break;                    
        case 'delete':
            $biz->delete();
        break;        
    }   
    if(isset($_REQUEST['event_token'])){
        $biz->inputReq();
    }    
}


