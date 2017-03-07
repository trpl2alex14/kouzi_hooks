<?php

require_once 'config.php';
require_once 'lib/autoLoad.php';
  
AutoLoad::autoloadRegister();

$event = new allEvent();
$event->route();
