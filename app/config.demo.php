<?php

define('APP_DIR',__DIR__."/");
define('EVENT_DIR',APP_DIR."event/");

/// log files
define('ERROR_LOG','log/error.log');
define('ERROR_MAIL','av@itentaro.ru');
define('INFO_MAIL','av@itentaro.ru');
define('ALL_LOG','log/alllog.log');

define('BX24_DOMEN',"kouzi.bitrix24.ru");
define ('LEADSPAM_TOKEN','1');
define ('GANEWLEAD_TOKEN','1|2|3');

define('BX24_AUTH',0);
define('BX24_REST_URL',"restapi.bitrix24.ru");
define('BX24_PROTOCOL', 'https://');
define('BX24_TOKEN_REST',"1");
define('BX24_USER',"7");

define('GA_TRACER','UA-00000');


define('HOST_DEV', $_SERVER['REMOTE_ADDR'] == '127.0.0.1');
if(HOST_DEV){
    require_once 'config.local.php';
}else{
    require_once 'config.db.php';
}
