<?php
define('HOST_DEV', $_SERVER['REMOTE_ADDR'] == '127.0.0.1');
if(HOST_DEV){
    require_once 'config.local.php';
    define('APP_SERVER', 'https://127.0.0.1/');
    define('APP_URL_FOLDER', APP_SERVER.'');        
}else{
    require_once 'config.db.php';
    define('APP_SERVER', 'https://wwww/');
    define('APP_URL_FOLDER', APP_SERVER.'addon/hooks/');    
}

define('APP_DIR',__DIR__."/");
define('EVENT_DIR',APP_DIR."event/");
define('BIZPROC_DIR',APP_DIR."bizproc/");
define('APP_URL_ROUTE', APP_URL_FOLDER.'route.php');

/// log files
define('ERROR_LOG','log/error.log');
define('ERROR_MAIL','av@');
define('INFO_MAIL','av@');
define('ALL_LOG','log/alllog.log');

////crm config BX24
define('BX24_DOMEN',"111.bitrix24.ru");
define ('LEADSPAM_TOKEN','');
define ('GANEWLEAD_TOKEN','');
//auth rest
define('BX24_AUTH',0);
define('BX24_REST_URL',"restapi.bitrix24.ru");
define('BX24_PROTOCOL', 'https://');
define('BX24_TOKEN_REST',"");
define('BX24_USER',"7");

//auth oauth2
define('BX24_OAUTH', 1);
define("CLIENT_ID", "");
define("CLIENT_SECRET", "");
define('LIB_BX_FOLDER', 'lib/bx24/');
define('APP_BX_FOLDER', APP_DIR.LIB_BX_FOLDER);
define('REDIRECT_URI', APP_SERVER.APP_URL_FOLDER.LIB_BX_FOLDER);
define('SCOPE', 'crm,entity,im,task,user,department,log,calendar,sonet_group,tasks_extended,mailservice,telephony,disk,bizproc,imbot,lists');
define('TOKEN_FILE', APP_BX_FOLDER.'bx.json');

//ga
define('GA_TRACER','UA--');


