<?php      
require_once './../../config.php';
require_once './../autoLoad.php';
  
AutoLoad::autoloadRegister();

$bx = new bx24req(1);
$data = $bx->route();
?>

<!DOCTYPE html>

<html>
    <head>
    <a href="../../bizproc.php"></a>
        <meta charset="UTF-8">
        <title>bx24</title>
    </head>
    <body>        
        <?php var_dump($data);?>
        <?php //var_dump($bx24->getCrmProduct(451));?>
        <?php //var_dump($bx24->getCrmProductIdToArticul('601'));?>
        <?php //var_dump($bx24->getCrmClientIdToPhone("79080700755"));?>
    </body>
</html>
