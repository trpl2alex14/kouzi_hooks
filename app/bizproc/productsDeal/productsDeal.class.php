<?php
define('PD_SHIPPING',1908);
define('PD_COMISSION',1916);

class productsDeal {
    private $bx;
    private $log;
    private $no_send_id = array(PD_SHIPPING,PD_COMISSION);
    
    private $params = array(
       'CODE'=> 'productsDeal',
       'HANDLER'=> APP_URL_ROUTE,
       'AUTH_USER_ID'=> BX24_USER,
       'USE_SUBSCRIPTION'=> 'Y',
       'NAME'=> array('ru'=> 'Список товаров сделки'),
       'DESCRIPTION'=> array('ru'=> 'Действие возвращает список товаров'),
       'PROPERTIES'=> array(
          'inputString'=> array(
             'Name'=> array('ru'=> 'ID'),
             'Description'=> array('ru'=> 'Введите ID сделки'),
             'Type'=> 'string',
             'Required'=> 'Y',
             'Multiple'=> 'N',
             'Default'=> '{=Document:ID}',
          )
       ),
       'RETURN_PROPERTIES'=> array(
          'outputString'=> array(
             'Name'=> array('ru'=> 'text'),
             'Type'=> 'string',
             'Multiple'=> 'N',
             'Default'=> null
          )
       )
    );    
    
    public function __construct() {
        $this->bx = new bx24req(BX24_OAUTH);
        $this->log = new webLog();
    }
    
    public function log($msg) {
        $this->log->info($msg);
    }
    
    public function install(){        
        $this->log("BIZP_".get_class($this).': Install status:'.print_r($this->bx->call('bizproc.activity.add',$this->params,true)));        
    }
    
    public function delete(){
        $this->log("BIZP_".get_class($this).': delete status:'.print_r($this->bx->call('bizproc.activity.delete',array('code' => get_class($this))),true));                
    }
    
    public function inputReq(){        
        if(isset($_REQUEST['event_token']) && isset($_REQUEST['properties']) && isset($_REQUEST['properties']['inputString']) && $_REQUEST['properties']['inputString']!=''){
            $did = $_REQUEST['properties']['inputString'];
            $e_token = $_REQUEST['event_token'];
            $this->log("BIZP_".get_class($this).': input '.$did);            
            $data = $this->bx->call("crm.deal.productrows.get", array(                    
                    "id" => $did
            ));    
            $result='';
            $index=1;
            if(isset($data["result"])){
                foreach ($data["result"] as $item) {
                    if(!in_array($item['PRODUCT_ID'], $this->no_send_id)){
                        $result.= $index.'. '.$item['PRODUCT_NAME'].' - '.$item['QUANTITY'].'шт. <br>';
                        $index++;
                    }
                }
            }else{
                $this->log("BIZP_".get_class($this).': input '.$did.' Warning: no products to Deal');            
            }            
            $data = $this->bx->call("bizproc.event.send", array(                    
                    "event_token" => $e_token,
                    "return_values"=> array('outputString'=>$result)
            ));            
            $this->log("BIZP_".get_class($this).': input '.$did.' Items:'.$result);            
            $this->log("BIZP_".get_class($this).': input '.$did.' Result:'.print_r($data,true));
        }
    }
}
